<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
$idEquipo = filter_input(INPUT_GET, 'id_equipo', FILTER_VALIDATE_INT);
if (!$idEquipo || $idEquipo < 1) {
    http_response_code(400);
    echo json_encode(['estado' => 'error', 'mensaje' => 'ID de equipo inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$sql = "SELECT p.id_puerto, p.numero_puerto, p.tipo_puerto, p.estado,
               p.alcance, p.medio, p.grupo_puerto, p.secciones_fisicas,
               e.id_enlace,
               CASE WHEN e.id_puerto_origen = p.id_puerto THEN e.id_puerto_destino
                    WHEN e.id_puerto_destino = p.id_puerto THEN e.id_puerto_origen
                    ELSE NULL END AS id_puerto_conectado,
               peer.id_equipo AS id_equipo_conectado,
               e.estilo_linea, e.color_linea,
               EXISTS (SELECT 1 FROM enlaces ex WHERE ex.id_puerto_origen=p.id_puerto OR ex.id_puerto_destino=p.id_puerto) AS esta_enlazado
        FROM puertos p
        LEFT JOIN enlaces e ON e.id_puerto_origen = p.id_puerto OR e.id_puerto_destino = p.id_puerto
        LEFT JOIN puertos peer ON peer.id_puerto = CASE
            WHEN e.id_puerto_origen = p.id_puerto THEN e.id_puerto_destino
            WHEN e.id_puerto_destino = p.id_puerto THEN e.id_puerto_origen
            ELSE NULL END
        WHERE p.id_equipo=? ORDER BY p.grupo_puerto, p.numero_puerto";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['estado'=>'error','mensaje'=>'No se pudo preparar la consulta de puertos.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$stmt->bind_param('i', $idEquipo);
$stmt->execute();
$result = $stmt->get_result();
echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
$stmt->close();
$conexion->close();
