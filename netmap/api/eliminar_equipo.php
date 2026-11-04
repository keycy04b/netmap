<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function responder(int $codigo, array $datos): never
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['estado' => 'error', 'mensaje' => 'Método no permitido.']);
}

$datos = json_decode(file_get_contents('php://input'), true);
$idEquipo = filter_var($datos['id_equipo'] ?? null, FILTER_VALIDATE_INT);

if (!$idEquipo || $idEquipo < 1) {
    responder(400, ['estado' => 'error', 'mensaje' => 'ID de equipo inválido.']);
}

$conexion->begin_transaction();

try {
    $buscar = $conexion->prepare(
        'SELECT id_puerto
         FROM puertos
         WHERE id_equipo = ?
         FOR UPDATE'
    );
    $buscar->bind_param('i', $idEquipo);
    $buscar->execute();

    $resultado = $buscar->get_result();
    $idsPuertos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $idsPuertos[] = (int) $fila['id_puerto'];
    }

    $existe = $conexion->prepare(
        'SELECT id_equipo FROM equipos WHERE id_equipo = ? FOR UPDATE'
    );
    $existe->bind_param('i', $idEquipo);
    $existe->execute();

    if (!$existe->get_result()->fetch_assoc()) {
        throw new RuntimeException('El equipo no existe.');
    }

    if ($idsPuertos) {
        $eliminarEnlaces = $conexion->prepare(
            'DELETE FROM enlaces
             WHERE id_puerto_origen IN (
                 SELECT id_puerto FROM puertos WHERE id_equipo = ?
             )
             OR id_puerto_destino IN (
                 SELECT id_puerto FROM puertos WHERE id_equipo = ?
             )'
        );
        $eliminarEnlaces->bind_param('ii', $idEquipo, $idEquipo);
        $eliminarEnlaces->execute();

        $eliminarPuertos = $conexion->prepare(
            'DELETE FROM puertos WHERE id_equipo = ?'
        );
        $eliminarPuertos->bind_param('i', $idEquipo);
        $eliminarPuertos->execute();
    }

    $eliminarEquipo = $conexion->prepare(
        'DELETE FROM equipos WHERE id_equipo = ?'
    );
    $eliminarEquipo->bind_param('i', $idEquipo);
    $eliminarEquipo->execute();

    if ($eliminarEquipo->affected_rows !== 1) {
        throw new RuntimeException('No se pudo eliminar el equipo.');
    }

    $conexion->commit();

    responder(200, [
        'estado' => 'ok',
        'mensaje' => 'Equipo, puertos y enlaces asociados eliminados correctamente.'
    ]);
} catch (Throwable $error) {
    $conexion->rollback();

    responder(400, [
        'estado' => 'error',
        'mensaje' => $error->getMessage()
    ]);
}
?>