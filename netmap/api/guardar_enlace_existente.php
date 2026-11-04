<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function responder(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['estado' => 'error', 'mensaje' => 'Método no permitido.']);
}

$data = json_decode(file_get_contents('php://input'), true);
$originId = filter_var($data['id_puerto_origen'] ?? null, FILTER_VALIDATE_INT);
$destinationId = filter_var($data['id_puerto_destino'] ?? null, FILTER_VALIDATE_INT);

if (!$originId || !$destinationId || $originId === $destinationId) {
    responder(400, ['estado' => 'error', 'mensaje' => 'Selecciona dos puertos distintos.']);
}

$conexion->begin_transaction();

try {
    $check = $conexion->prepare(
        "SELECT p.id_puerto, p.id_equipo, p.estado,
                COALESCE(NULLIF(p.medio,''), IF(p.tipo_puerto='FIBRA','FIBRA','UTP')) AS medio,
                EXISTS(SELECT 1 FROM enlaces e
                       WHERE e.id_puerto_origen=p.id_puerto
                          OR e.id_puerto_destino=p.id_puerto) AS enlazado
         FROM puertos p
         WHERE p.id_puerto IN (?, ?)
         ORDER BY p.id_puerto
         FOR UPDATE"
    );
    $check->bind_param('ii', $originId, $destinationId);
    $check->execute();
    $ports = $check->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($ports) !== 2) {
        throw new RuntimeException('Uno de los puertos no existe.');
    }
    if ((int)$ports[0]['id_equipo'] === (int)$ports[1]['id_equipo']) {
        throw new RuntimeException('No se permite enlazar dos puertos del mismo equipo.');
    }
    foreach ($ports as $port) {
        if ($port['estado'] !== 'DISPONIBLE' || (int)$port['enlazado'] === 1) {
            throw new RuntimeException('Ambos puertos deben estar disponibles y sin enlaces.');
        }
    }
    $mediumA = strtoupper((string)$ports[0]['medio']);
    $mediumB = strtoupper((string)$ports[1]['medio']);
    if (($mediumA === 'FIBRA') !== ($mediumB === 'FIBRA')) {
        throw new RuntimeException('Los medios físicos no coinciden.');
    }

    $style = $mediumA === 'FIBRA' ? 'CONTINUA' : 'DISCONTINUA';
    $color = $mediumA === 'FIBRA' ? '#2563EB' : '#EF4444';
    $insert = $conexion->prepare(
        "INSERT INTO enlaces (id_puerto_origen, id_puerto_destino, estilo_linea, color_linea, observaciones)
         VALUES (?, ?, ?, ?, 'Enlace creado desde el editor de equipos')"
    );
    $insert->bind_param('iiss', $originId, $destinationId, $style, $color);
    $insert->execute();

    $update = $conexion->prepare("UPDATE puertos SET estado='NO_DISPONIBLE' WHERE id_puerto IN (?, ?)");
    $update->bind_param('ii', $originId, $destinationId);
    $update->execute();

    $conexion->commit();
    responder(200, ['estado'=>'ok', 'mensaje'=>'Enlace creado correctamente.', 'id_enlace'=>$insert->insert_id]);
} catch (Throwable $error) {
    $conexion->rollback();
    responder(400, ['estado'=>'error', 'mensaje'=>$error->getMessage()]);
}
?>