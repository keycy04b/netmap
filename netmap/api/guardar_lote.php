<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function jsonExit(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function netmapTitleCaseFirst(string $value): string
{
    $value = trim($value);
    if ($value === '') return '';
    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($value, 1, null, 'UTF-8');
    }
    return strtoupper(substr($value, 0, 1)) . substr($value, 1);
}

function endpointPortId(array $endpoint, array $newEquipmentIds, array $portIdsByClient): int
{
    if (($endpoint['tipo'] ?? '') === 'existente') {
        $portId = filter_var($endpoint['id_puerto'] ?? null, FILTER_VALIDATE_INT);
        if (!$portId) {
            throw new RuntimeException('El puerto de destino existente no es válido.');
        }
        return $portId;
    }

    if (($endpoint['tipo'] ?? '') !== 'nuevo') {
        throw new RuntimeException('Tipo de extremo de enlace desconocido.');
    }

    $clientId = filter_var($endpoint['client_id'] ?? null, FILTER_VALIDATE_INT);
    $group = filter_var($endpoint['grupo'] ?? null, FILTER_VALIDATE_INT);
    $portNumber = filter_var($endpoint['puerto'] ?? null, FILTER_VALIDATE_INT);

    if (!$clientId || !isset($newEquipmentIds[$clientId]) || $group === false || $portNumber === false) {
        throw new RuntimeException('Uno de los puertos nuevos del enlace no es válido.');
    }

    $key = $clientId . ':' . $group . ':' . $portNumber;
    if (!isset($portIdsByClient[$key])) {
        throw new RuntimeException('No se encontró el puerto indicado para el enlace.');
    }

    return $portIdsByClient[$key];
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    jsonExit(400, ['estado' => 'error', 'mensaje' => 'La solicitud no contiene JSON válido.']);
}

$equipmentData = $payload['equipos'] ?? $payload;
$linkData = $payload['enlaces'] ?? [];
if (!is_array($equipmentData)) {
    jsonExit(400, ['estado' => 'error', 'mensaje' => 'La lista de equipos no es válida.']);
}
if (!is_array($linkData)) {
    jsonExit(400, ['estado' => 'error', 'mensaje' => 'La lista de enlaces no es válida.']);
}
if (!$equipmentData && !$linkData) {
    jsonExit(400, ['estado' => 'error', 'mensaje' => 'No hay equipos ni enlaces para guardar.']);
}

$newEquipmentIds = [];
$portIdsByClient = [];
$portsUsedInBatch = [];
$conexion->begin_transaction();

try {
    $insertEquipment = $conexion->prepare(
        'INSERT INTO equipos
        (id_piso, id_tipo_equipo, icono, nombre_equipo, marca, modelo, serial, direccion_ip, numero_puertos, observaciones)
        VALUES (?, ?, NULLIF(?, \'\'), ?, ?, ?, ?, ?, ?, ?)'
    );
    $insertPort = $conexion->prepare(
        'INSERT INTO puertos
        (id_equipo, numero_puerto, tipo_puerto, estado, alcance, medio, grupo_puerto, secciones_fisicas)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $checkPort = $conexion->prepare(
        'SELECT p.id_puerto, p.id_equipo, p.estado, p.medio,
                EXISTS(SELECT 1 FROM enlaces e WHERE e.id_puerto_origen = p.id_puerto OR e.id_puerto_destino = p.id_puerto) AS enlazado
         FROM puertos p WHERE p.id_puerto = ? FOR UPDATE'
    );
    $insertLink = $conexion->prepare(
        "INSERT INTO enlaces (id_puerto_origen, id_puerto_destino, estilo_linea, color_linea, observaciones)
         VALUES (?, ?, ?, ?, 'Enlace creado desde el registro de equipos')"
    );
    $updatePort = $conexion->prepare("UPDATE puertos SET estado = 'NO_DISPONIBLE' WHERE id_puerto = ?");

    foreach ($equipmentData as $index => $equipment) {
        if (!is_array($equipment)) {
            throw new RuntimeException('Datos inválidos en la tarjeta ' . ($index + 1) . '.');
        }

        $clientId = (int)($equipment['client_id'] ?? ($index + 1));
        $floorId = (int)($equipment['id_piso'] ?? 0);
        $typeId = (int)($equipment['id_tipo_equipo'] ?? 0);
        $icon = trim((string)($equipment['icono'] ?? ''));
        $name = netmapTitleCaseFirst((string)($equipment['nombre_equipo'] ?? ''));
        $brand = netmapTitleCaseFirst((string)($equipment['marca'] ?? ''));
        $model = netmapTitleCaseFirst((string)($equipment['modelo'] ?? ''));
        $serial = strtoupper(trim((string)($equipment['serial'] ?? '')));
        $ip = trim((string)($equipment['direccion_ip'] ?? ''));
        $observations = trim((string)($equipment['observaciones'] ?? ''));
        $groups = $equipment['grupos_puertos'] ?? [];

        if (!$clientId || !$floorId || !$typeId || !$brand || !$model || !$serial || !$ip) {
            throw new RuntimeException('Completa los campos obligatorios del equipo ' . ($index + 1) . '.');
        }
        if (!is_array($groups) || !$groups) {
            throw new RuntimeException('Añade al menos un grupo de puertos al equipo ' . ($index + 1) . '.');
        }

        $totalPorts = 0;
        foreach ($groups as $group) {
            $quantity = filter_var($group['cantidad'] ?? null, FILTER_VALIDATE_INT);
            $sections = filter_var($group['secciones'] ?? 1, FILTER_VALIDATE_INT);
            $reach = strtoupper(trim((string)($group['alcance'] ?? 'LAN')));
            $medium = strtoupper(trim((string)($group['medio'] ?? 'UTP')));
            if (!$quantity || $quantity < 1 || $quantity > 128) {
                throw new RuntimeException('Cantidad de puertos inválida en equipo ' . ($index + 1) . '.');
            }
            if (!$sections || $sections < 1 || $sections > $quantity) {
                throw new RuntimeException('Número de secciones inválido en equipo ' . ($index + 1) . '.');
            }
            if (!in_array($reach, ['LAN', 'WAN'], true)) {
                throw new RuntimeException('El alcance solo puede ser LAN o WAN.');
            }
            if (!in_array($medium, ['UTP', 'FIBRA'], true)) {
                throw new RuntimeException('El medio solo puede ser UTP o FIBRA.');
            }
            $totalPorts += $quantity;
        }
        if ($totalPorts > 512) {
            throw new RuntimeException('El máximo permitido es de 512 puertos por equipo.');
        }

        $insertEquipment->bind_param(
            'iissssssis',
            $floorId, $typeId, $icon, $name, $brand, $model, $serial, $ip, $totalPorts, $observations
        );
        if (!$insertEquipment->execute()) {
            throw new RuntimeException('Error guardando equipo ' . ($index + 1) . ': ' . $insertEquipment->error);
        }
        $equipmentId = (int)$insertEquipment->insert_id;
        $newEquipmentIds[$clientId] = $equipmentId;

        $portNumber = 1;
        foreach ($groups as $groupIndex => $group) {
            $quantity = (int)$group['cantidad'];
            $reach = strtoupper((string)$group['alcance']);
            $medium = strtoupper((string)$group['medio']);
            $portType = $medium === 'FIBRA' ? 'FIBRA' : 'RED';
            $groupNumber = (int)$groupIndex + 1;
            $sections = (int)($group['secciones'] ?? 1);
            $states = is_array($group['estados'] ?? null) ? $group['estados'] : [];

            for ($localPort = 1; $localPort <= $quantity; $localPort++) {
                $stateKey = (string)$localPort;
                $state = (string)($states[$stateKey] ?? 'available');
                $state = match ($state) {
                    'occupied' => 'NO_DISPONIBLE',
                    'reserved' => 'RESERVADO',
                    'damaged' => 'DANADO',
                    default => 'DISPONIBLE'
                };

                $insertPort->bind_param(
                    'iissssii',
                    $equipmentId, $portNumber, $portType, $state, $reach, $medium, $groupNumber, $sections
                );
                if (!$insertPort->execute()) {
                    throw new RuntimeException('Error guardando el puerto ' . $portNumber . ': ' . $insertPort->error);
                }

                $portIdsByClient[$clientId . ':' . $groupIndex . ':' . $localPort] = (int)$insertPort->insert_id;
                $portNumber++;
            }
        }
    }

    // Valida e inserta los enlaces dentro de la misma transacción.
    foreach ($linkData as $linkIndex => $link) {
        if (!is_array($link) || !is_array($link['origen'] ?? null) || !is_array($link['destino'] ?? null)) {
            throw new RuntimeException('El enlace ' . ($linkIndex + 1) . ' tiene datos inválidos.');
        }

        $originId = endpointPortId($link['origen'], $newEquipmentIds, $portIdsByClient);
        $destinationId = endpointPortId($link['destino'], $newEquipmentIds, $portIdsByClient);
        foreach ([$originId, $destinationId] as $portId) {
            if (isset($portsUsedInBatch[$portId])) {
                throw new RuntimeException('Un puerto no puede tener más de un enlace. Revisa los puertos seleccionados.');
            }
            $portsUsedInBatch[$portId] = true;
        }
        if ($originId === $destinationId) {
            throw new RuntimeException('Un puerto no puede enlazarse consigo mismo.');
        }

        foreach ([$originId, $destinationId] as $portId) {
            $checkPort->bind_param('i', $portId);
            if (!$checkPort->execute()) {
                throw new RuntimeException('No se pudo validar uno de los puertos del enlace.');
            }
            $port = $checkPort->get_result()->fetch_assoc();
            if (!$port) {
                throw new RuntimeException('No existe uno de los puertos seleccionados.');
            }
            if ((string)$port['estado'] !== 'DISPONIBLE' || (int)$port['enlazado'] === 1) {
                throw new RuntimeException('Uno de los puertos ya no está disponible. Actualiza la búsqueda e inténtalo de nuevo.');
            }
        }

        $checkPort->bind_param('i', $originId);
        $checkPort->execute();
        $originPort = $checkPort->get_result()->fetch_assoc();
        $checkPort->bind_param('i', $destinationId);
        $checkPort->execute();
        $destinationPort = $checkPort->get_result()->fetch_assoc();
        $originMedium = strtoupper((string)($originPort['medio'] ?? 'UTP'));
        $destinationMedium = strtoupper((string)($destinationPort['medio'] ?? 'UTP'));
        if (($originMedium === 'FIBRA') !== ($destinationMedium === 'FIBRA')) {
            throw new RuntimeException('Enlace incompatible: no se puede conectar directamente Ethernet/UTP con fibra óptica.');
        }
        $lineStyle = $originMedium === 'FIBRA' ? 'CONTINUA' : 'DISCONTINUA';
        $lineColor = $originMedium === 'FIBRA' ? '#2563EB' : '#EF4444';
        $insertLink->bind_param('iiss', $originId, $destinationId, $lineStyle, $lineColor);
        if (!$insertLink->execute()) {
            throw new RuntimeException('Error guardando el enlace: ' . $insertLink->error);
        }

        $updatePort->bind_param('i', $originId);
        if (!$updatePort->execute()) throw new RuntimeException('No se pudo actualizar el estado del puerto de origen.');
        $updatePort->bind_param('i', $destinationId);
        if (!$updatePort->execute()) throw new RuntimeException('No se pudo actualizar el estado del puerto de destino.');
    }

    $insertEquipment->close();
    $insertPort->close();
    $checkPort->close();
    $insertLink->close();
    $updatePort->close();
    $conexion->commit();

    jsonExit(200, [
        'estado' => 'ok',
        'mensaje' => 'Equipos, iconos, estados y enlaces guardados correctamente.',
        'equipos_registrados' => count($equipmentData),
        'enlaces_registrados' => count($linkData)
    ]);
} catch (Throwable $error) {
    $conexion->rollback();
    jsonExit(400, ['estado' => 'error', 'mensaje' => $error->getMessage()]);
} finally {
    $conexion->close();
}
