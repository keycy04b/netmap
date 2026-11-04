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
$idPuerto = filter_var($datos['id_puerto'] ?? null, FILTER_VALIDATE_INT);
if (!$idPuerto) {
    responder(400, ['estado' => 'error', 'mensaje' => 'Puerto inválido.']);
}

$conexion->begin_transaction();
try {
    $buscar = $conexion->prepare(
        'SELECT id_enlace, id_puerto_origen, id_puerto_destino
         FROM enlaces
         WHERE id_puerto_origen = ? OR id_puerto_destino = ?
         FOR UPDATE'
    );
    $buscar->bind_param('ii', $idPuerto, $idPuerto);
    $buscar->execute();
    $enlace = $buscar->get_result()->fetch_assoc();

    if (!$enlace) {
        throw new RuntimeException('Este puerto no tiene un enlace guardado.');
    }

    $idEnlace = (int)$enlace['id_enlace'];
    $eliminar = $conexion->prepare('DELETE FROM enlaces WHERE id_enlace = ?');
    $eliminar->bind_param('i', $idEnlace);
    $eliminar->execute();

    $liberar = $conexion->prepare(
        "UPDATE puertos SET estado = 'DISPONIBLE'
         WHERE id_puerto = ?
           AND NOT EXISTS (SELECT 1 FROM enlaces WHERE id_puerto_origen = ? OR id_puerto_destino = ?)"
    );
    foreach ([(int)$enlace['id_puerto_origen'], (int)$enlace['id_puerto_destino']] as $id) {
        $liberar->bind_param('iii', $id, $id, $id);
        $liberar->execute();
    }

    $conexion->commit();
    responder(200, ['estado' => 'ok', 'mensaje' => 'Enlace eliminado y ambos puertos liberados.']);
} catch (Throwable $error) {
    $conexion->rollback();
    responder(400, ['estado' => 'error', 'mensaje' => $error->getMessage()]);
}
