<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function topologiaJsonError(string $message, int $status = 500): never
{
    http_response_code($status);
    echo json_encode(['estado' => 'error', 'mensaje' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

$sqlEquipos = "
    SELECT
        e.id_equipo,
        e.id_piso,
        e.id_tipo_equipo,
        e.nombre_equipo,
        e.marca,
        e.modelo,
        e.serial,
        e.direccion_ip,
        e.icono,
        e.numero_puertos,
        COALESCE(e.posicion_x, 0) AS posicion_x,
        COALESCE(e.posicion_y, 0) AS posicion_y,
        t.nombre_tipo,
        t.ruta_icono,
        p.numero_piso,
        s.id_sede,
        s.nombre_sede,
        (SELECT COUNT(*)
         FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo) AS total_puertos,
        (SELECT COUNT(*)
         FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND EXISTS (
               SELECT 1 FROM enlaces en
               WHERE en.id_puerto_origen = pt.id_puerto
                  OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_enlazados
    FROM equipos e
    LEFT JOIN tipos_equipo t ON t.id_tipo_equipo = e.id_tipo_equipo
    LEFT JOIN pisos p ON p.id_piso = e.id_piso
    LEFT JOIN sedes s ON s.id_sede = p.id_sede
    ORDER BY s.nombre_sede, p.numero_piso, e.id_equipo
";

$sqlEnlaces = "
    SELECT
        en.id_enlace,
        en.estilo_linea,
        en.color_linea,
        po.id_equipo AS id_equipo_origen,
        po.numero_puerto AS puerto_origen,
        po.medio AS medio_origen,
        pd.id_equipo AS id_equipo_destino,
        pd.numero_puerto AS puerto_destino,
        pd.medio AS medio_destino
    FROM enlaces en
    INNER JOIN puertos po ON po.id_puerto = en.id_puerto_origen
    INNER JOIN puertos pd ON pd.id_puerto = en.id_puerto_destino
    ORDER BY en.id_enlace
";

$resultEquipos = $conexion->query($sqlEquipos);
if (!$resultEquipos) {
    topologiaJsonError('No se pudieron consultar los equipos: ' . $conexion->error);
}

$equipos = [];
while ($row = $resultEquipos->fetch_assoc()) {
    $type = strtolower((string)($row['nombre_tipo'] ?? ''));
    $icon = trim((string)($row['icono'] ?: $row['ruta_icono']));
    if ($icon === '') {
        if (strpos($type, 'comput') !== false) {
            $icon = 'assets/icons/computer.svg';
        } elseif (strpos($type, 'router') !== false) {
            $icon = 'assets/icons/router.svg';
        } elseif (strpos($type, 'servidor') !== false) {
            $icon = 'assets/icons/server.svg';
        } elseif (strpos($type, 'teléfono') !== false || strpos($type, 'telefono') !== false) {
            $icon = 'assets/icons/phone.svg';
        } elseif (strpos($type, 'switch') !== false || strpos($type, 'swiche') !== false) {
            $icon = 'assets/icons/switch.svg';
        } else {
            $icon = 'assets/icons/network.svg';
        }
    }

    $row['icono_mostrar'] = $icon;
    $row['nombre_mostrar'] = trim((string)$row['nombre_equipo']) !== ''
        ? $row['nombre_equipo']
        : (($row['nombre_tipo'] ?: 'Equipo') . ' #' . $row['id_equipo']);
    $row['posicion_x'] = (float)$row['posicion_x'];
    $row['posicion_y'] = (float)$row['posicion_y'];
    $row['total_puertos'] = (int)$row['total_puertos'];
    $row['puertos_enlazados'] = (int)$row['puertos_enlazados'];
    $equipos[] = $row;
}

$resultEnlaces = $conexion->query($sqlEnlaces);
if (!$resultEnlaces) {
    topologiaJsonError('No se pudieron consultar los enlaces: ' . $conexion->error);
}

$enlaces = [];
while ($row = $resultEnlaces->fetch_assoc()) {
    $row['id_enlace'] = (int)$row['id_enlace'];
    $row['id_equipo_origen'] = (int)$row['id_equipo_origen'];
    $row['id_equipo_destino'] = (int)$row['id_equipo_destino'];
    $row['puerto_origen'] = (int)$row['puerto_origen'];
    $row['puerto_destino'] = (int)$row['puerto_destino'];
    $enlaces[] = $row;
}

$conexion->close();
echo json_encode([
    'estado' => 'ok',
    'equipos' => $equipos,
    'enlaces' => $enlaces
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
