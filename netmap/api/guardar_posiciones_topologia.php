<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function posicionesRespuesta(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    posicionesRespuesta(405, ['estado' => 'error', 'mensaje' => 'Método no permitido.']);
}

$payload = json_decode(file_get_contents('php://input'), true);
$positions = $payload['posiciones'] ?? null;

if (!is_array($positions) || count($positions) > 1000) {
    posicionesRespuesta(400, ['estado' => 'error', 'mensaje' => 'La lista de posiciones no es válida.']);
}

try {
    $conexion->begin_transaction();
    $update = $conexion->prepare(
        'UPDATE equipos SET posicion_x = ?, posicion_y = ? WHERE id_equipo = ?'
    );
    if (!$update) {
        throw new RuntimeException('No se pudo preparar el guardado de posiciones.');
    }

    $saved = 0;
    foreach ($positions as $position) {
        if (!is_array($position)) {
            throw new RuntimeException('Hay una posición con formato inválido.');
        }

        $equipmentId = filter_var($position['id_equipo'] ?? null, FILTER_VALIDATE_INT);
        $x = filter_var($position['x'] ?? null, FILTER_VALIDATE_FLOAT);
        $y = filter_var($position['y'] ?? null, FILTER_VALIDATE_FLOAT);

        if (!$equipmentId || $x === false || $y === false) {
            throw new RuntimeException('Una posición contiene datos incompletos.');
        }
        if ($x < 0 || $y < 0 || $x > 50000 || $y > 50000) {
            throw new RuntimeException('Una posición está fuera del área permitida.');
        }

        $x = round((float)$x, 2);
        $y = round((float)$y, 2);
        $update->bind_param('ddi', $x, $y, $equipmentId);
        if (!$update->execute()) {
            throw new RuntimeException('No se pudo guardar la posición del equipo ' . $equipmentId . '.');
        }
        if ($update->affected_rows >= 0) {
            $saved++;
        }
    }

    $update->close();
    $conexion->commit();
    $conexion->close();

    posicionesRespuesta(200, [
        'estado' => 'ok',
        'mensaje' => 'Distribución guardada correctamente.',
        'posiciones_guardadas' => $saved
    ]);
} catch (Throwable $error) {
    $conexion->rollback();
    $conexion->close();
    posicionesRespuesta(400, ['estado' => 'error', 'mensaje' => $error->getMessage()]);
}
