<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

function responder(array $datos, int $codigo = 200): void {
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function texto(string $campo, int $maximo): string {
    $valor = trim((string)($_POST[$campo] ?? ''));
    $longitud = function_exists('mb_strlen') ? mb_strlen($valor, 'UTF-8') : strlen($valor);
    if ($valor === '' || $longitud > $maximo) {
        responder(['estado' => 'error', 'mensaje' => "El campo {$campo} es obligatorio y admite hasta {$maximo} caracteres."], 422);
    }
    return $valor;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sedes = [];
    $pisos = [];

    $resultado = $conexion->query(
        "SELECT s.id_sede, s.nombre_sede, s.descripcion,
                COUNT(DISTINCT p.id_piso) AS total_pisos,
                COUNT(DISTINCT e.id_equipo) AS total_equipos
         FROM sedes s
         LEFT JOIN pisos p ON p.id_sede = s.id_sede
         LEFT JOIN equipos e ON e.id_piso = p.id_piso
         GROUP BY s.id_sede
         ORDER BY s.nombre_sede"
    );

    if (!$resultado) {
        responder(['estado' => 'error', 'mensaje' => 'No se pudieron cargar las sedes.'], 500);
    }
    while ($fila = $resultado->fetch_assoc()) {
        $sedes[] = $fila;
    }

    $resultado = $conexion->query(
        "SELECT p.id_piso, p.id_sede, p.numero_piso,
                s.nombre_sede, COUNT(e.id_equipo) AS total_equipos
         FROM pisos p
         INNER JOIN sedes s ON s.id_sede = p.id_sede
         LEFT JOIN equipos e ON e.id_piso = p.id_piso
         GROUP BY p.id_piso
         ORDER BY s.nombre_sede, p.numero_piso"
    );

    if (!$resultado) {
        responder(['estado' => 'error', 'mensaje' => 'No se pudieron cargar los pisos.'], 500);
    }
    while ($fila = $resultado->fetch_assoc()) {
        $pisos[] = $fila;
    }

    responder(['estado' => 'ok', 'sedes' => $sedes, 'pisos' => $pisos]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(['estado' => 'error', 'mensaje' => 'Método no permitido.'], 405);
}

$accion = (string)($_POST['accion'] ?? '');

try {
    if ($accion === 'agregar_sede') {
        $nombre = texto('nombre_sede', 100);
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        $stmt = $conexion->prepare("INSERT INTO sedes (nombre_sede, descripcion) VALUES (?, ?)");
        $stmt->bind_param('ss', $nombre, $descripcion);
        $stmt->execute();
        responder(['estado' => 'ok', 'mensaje' => 'Sede agregada correctamente.', 'id' => $conexion->insert_id]);
    }

    if ($accion === 'editar_sede') {
        $id = (int)($_POST['id_sede'] ?? 0);
        $nombre = texto('nombre_sede', 100);
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        if ($id < 1) responder(['estado' => 'error', 'mensaje' => 'Sede no válida.'], 422);
        $stmt = $conexion->prepare("UPDATE sedes SET nombre_sede = ?, descripcion = ? WHERE id_sede = ?");
        $stmt->bind_param('ssi', $nombre, $descripcion, $id);
        $stmt->execute();
        if ($stmt->affected_rows === 0 && $stmt->errno) {
            responder(['estado' => 'error', 'mensaje' => 'No se pudo actualizar la sede.'], 500);
        }
        responder(['estado' => 'ok', 'mensaje' => 'Sede actualizada correctamente.']);
    }

    if ($accion === 'eliminar_sede') {
        $id = (int)($_POST['id_sede'] ?? 0);
        if ($id < 1) responder(['estado' => 'error', 'mensaje' => 'Sede no válida.'], 422);
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM pisos WHERE id_sede = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];
        if ($total > 0) {
            responder(['estado' => 'error', 'mensaje' => 'No se puede eliminar: esta sede todavía contiene pisos. Elimina o reasigna primero esos pisos.'], 409);
        }
        $stmt = $conexion->prepare("DELETE FROM sedes WHERE id_sede = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows < 1) responder(['estado' => 'error', 'mensaje' => 'La sede no existe o ya fue eliminada.'], 404);
        responder(['estado' => 'ok', 'mensaje' => 'Sede eliminada correctamente.']);
    }

    if ($accion === 'agregar_piso') {
        $idSede = (int)($_POST['id_sede'] ?? 0);
        $numero = texto('numero_piso', 50);
        if ($idSede < 1) responder(['estado' => 'error', 'mensaje' => 'Selecciona una sede válida.'], 422);
        $check = $conexion->prepare("SELECT id_piso FROM pisos WHERE id_sede = ? AND LOWER(numero_piso) = LOWER(?)");
        $check->bind_param('is', $idSede, $numero);
        $check->execute();
        if ($check->get_result()->num_rows > 0) responder(['estado' => 'error', 'mensaje' => 'Ese piso ya existe en la sede seleccionada.'], 409);
        $stmt = $conexion->prepare("INSERT INTO pisos (id_sede, numero_piso) VALUES (?, ?)");
        $stmt->bind_param('is', $idSede, $numero);
        $stmt->execute();
        responder(['estado' => 'ok', 'mensaje' => 'Piso agregado correctamente.', 'id' => $conexion->insert_id]);
    }

    if ($accion === 'editar_piso') {
        $id = (int)($_POST['id_piso'] ?? 0);
        $idSede = (int)($_POST['id_sede'] ?? 0);
        $numero = texto('numero_piso', 50);
        if ($id < 1 || $idSede < 1) responder(['estado' => 'error', 'mensaje' => 'Piso o sede no válidos.'], 422);
        $check = $conexion->prepare("SELECT id_piso FROM pisos WHERE id_sede = ? AND LOWER(numero_piso) = LOWER(?) AND id_piso <> ?");
        $check->bind_param('isi', $idSede, $numero, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) responder(['estado' => 'error', 'mensaje' => 'Ya existe ese nombre de piso en la sede.'], 409);
        $stmt = $conexion->prepare("UPDATE pisos SET id_sede = ?, numero_piso = ? WHERE id_piso = ?");
        $stmt->bind_param('isi', $idSede, $numero, $id);
        $stmt->execute();
        responder(['estado' => 'ok', 'mensaje' => 'Piso actualizado correctamente.']);
    }

    if ($accion === 'eliminar_piso') {
        $id = (int)($_POST['id_piso'] ?? 0);
        if ($id < 1) responder(['estado' => 'error', 'mensaje' => 'Piso no válido.'], 422);
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM equipos WHERE id_piso = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];
        if ($total > 0) {
            responder(['estado' => 'error', 'mensaje' => 'No se puede eliminar: hay equipos registrados en este piso. Reasigna esos equipos primero.'], 409);
        }
        $stmt = $conexion->prepare("DELETE FROM pisos WHERE id_piso = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->affected_rows < 1) responder(['estado' => 'error', 'mensaje' => 'El piso no existe o ya fue eliminado.'], 404);
        responder(['estado' => 'ok', 'mensaje' => 'Piso eliminado correctamente.']);
    }

    responder(['estado' => 'error', 'mensaje' => 'Acción no reconocida.'], 422);
} catch (Throwable $error) {
    responder(['estado' => 'error', 'mensaje' => 'No se pudo completar la operación. Verifica los datos e inténtalo nuevamente.'], 500);
}
