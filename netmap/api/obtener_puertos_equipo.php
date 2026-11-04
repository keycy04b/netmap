<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$idEquipo = filter_input(INPUT_GET, 'id_equipo', FILTER_VALIDATE_INT);
if (!$idEquipo || $idEquipo < 1) {
    http_response_code(400);
    echo json_encode(['estado' => 'error', 'mensaje' => 'ID de equipo inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* id_enlace pertenece a enlaces, no a puertos. Se obtiene mediante LEFT JOIN. */
$sql = "
    SELECT
        pt.id_puerto,
        pt.id_equipo,
        pt.numero_puerto,
        pt.etiqueta_puerto,
        pt.tipo_puerto,
        pt.alcance,
        pt.medio,
        pt.grupo_puerto,
        pt.secciones_fisicas,
        pt.estado,
        pt.fecha_creacion,
        pt.fecha_actualizacion,
        en.id_enlace,
        CASE
            WHEN en.id_puerto_origen = pt.id_puerto THEN eq_dest.nombre_equipo
            WHEN en.id_puerto_destino = pt.id_puerto THEN eq_orig.nombre_equipo
            ELSE NULL
        END AS equipo_destino,
        CASE
            WHEN en.id_puerto_origen = pt.id_puerto THEN pt_dest.numero_puerto
            WHEN en.id_puerto_destino = pt.id_puerto THEN pt_orig.numero_puerto
            ELSE NULL
        END AS puerto_destino,
        CASE WHEN en.id_enlace IS NULL THEN 0 ELSE 1 END AS esta_enlazado
    FROM puertos AS pt
    LEFT JOIN enlaces AS en
        ON en.id_puerto_origen = pt.id_puerto
        OR en.id_puerto_destino = pt.id_puerto
    LEFT JOIN puertos AS pt_dest ON pt_dest.id_puerto = en.id_puerto_destino
    LEFT JOIN equipos AS eq_dest ON eq_dest.id_equipo = pt_dest.id_equipo
    LEFT JOIN puertos AS pt_orig ON pt_orig.id_puerto = en.id_puerto_origen
    LEFT JOIN equipos AS eq_orig ON eq_orig.id_equipo = pt_orig.id_equipo
    WHERE pt.id_equipo = ?
    ORDER BY pt.grupo_puerto, pt.numero_puerto
";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'No se pudo preparar la consulta de puertos: ' . $conexion->error], JSON_UNESCAPED_UNICODE);
    exit;
}
$stmt->bind_param('i', $idEquipo);
$stmt->execute();
$resultado = $stmt->get_result();
$puertos = $resultado->fetch_all(MYSQLI_ASSOC);
echo json_encode($puertos, JSON_UNESCAPED_UNICODE);
$stmt->close();
$conexion->close();
?>
