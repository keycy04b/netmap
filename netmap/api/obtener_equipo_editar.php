<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(['estado'=>'error','mensaje'=>'ID de equipo inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$sql = 'SELECT e.id_equipo, e.id_piso, p.id_sede, e.id_tipo_equipo, e.nombre_equipo, e.marca, e.modelo, e.serial, e.direccion_ip, e.icono, e.observaciones FROM equipos e INNER JOIN pisos p ON p.id_piso=e.id_piso WHERE e.id_equipo=? LIMIT 1';
$stmt = $conexion->prepare($sql);
if (!$stmt) { http_response_code(500); echo json_encode(['estado'=>'error','mensaje'=>'No se pudo preparar la consulta.'], JSON_UNESCAPED_UNICODE); exit; }
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$equipo = $result->fetch_assoc();
if (!$equipo) { http_response_code(404); echo json_encode(['estado'=>'error','mensaje'=>'Equipo no encontrado.'], JSON_UNESCAPED_UNICODE); }
else echo json_encode($equipo, JSON_UNESCAPED_UNICODE);
$stmt->close(); $conexion->close();
