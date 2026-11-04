<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function respuesta(int $status, array $data): never { http_response_code($status); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respuesta(405, ['estado'=>'error','mensaje'=>'Método no permitido.']);
$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) respuesta(400, ['estado'=>'error','mensaje'=>'JSON inválido.']);
$id = filter_var($data['id_equipo'] ?? null, FILTER_VALIDATE_INT);
$idTipo = filter_var($data['id_tipo_equipo'] ?? null, FILTER_VALIDATE_INT);
$idPiso = filter_var($data['id_piso'] ?? null, FILTER_VALIDATE_INT);
$nombre = trim((string)($data['nombre_equipo'] ?? ''));
$marca = trim((string)($data['marca'] ?? ''));
$modelo = trim((string)($data['modelo'] ?? ''));
$serial = strtoupper(trim((string)($data['serial'] ?? '')));
$ip = trim((string)($data['direccion_ip'] ?? ''));
$icono = trim((string)($data['icono'] ?? ''));
$obs = trim((string)($data['observaciones'] ?? ''));
if (!$id || !$idTipo || !$idPiso || $nombre === '' || $marca === '' || $modelo === '' || $serial === '' || $ip === '') respuesta(422, ['estado'=>'error','mensaje'=>'Completa todos los campos obligatorios.']);
if (strlen($nombre)>150 || strlen($marca)>100 || strlen($modelo)>100 || strlen($serial)>120 || strlen($ip)>45 || strlen($icono)>500 || strlen($obs)>2000) respuesta(422, ['estado'=>'error','mensaje'=>'Uno de los campos supera la longitud permitida.']);
$check = $conexion->prepare('SELECT id_equipo FROM equipos WHERE id_equipo=?');
$check->bind_param('i', $id); $check->execute();
if (!$check->get_result()->fetch_assoc()) { $check->close(); respuesta(404, ['estado'=>'error','mensaje'=>'Equipo no encontrado.']); }
$check->close();
$check = $conexion->prepare('SELECT id_piso FROM pisos WHERE id_piso=?');
$check->bind_param('i', $idPiso); $check->execute();
if (!$check->get_result()->fetch_assoc()) { $check->close(); respuesta(422, ['estado'=>'error','mensaje'=>'El piso seleccionado no existe.']); }
$check->close();
$check = $conexion->prepare('SELECT id_tipo_equipo FROM tipos_equipo WHERE id_tipo_equipo=?');
$check->bind_param('i', $idTipo); $check->execute();
if (!$check->get_result()->fetch_assoc()) { $check->close(); respuesta(422, ['estado'=>'error','mensaje'=>'El tipo de equipo seleccionado no existe.']); }
$check->close();
$sql = 'UPDATE equipos SET id_piso=?, id_tipo_equipo=?, nombre_equipo=?, marca=?, modelo=?, serial=?, direccion_ip=?, icono=NULLIF(?,\'\'), observaciones=?, fecha_actualizacion=CURRENT_TIMESTAMP WHERE id_equipo=?';
$stmt = $conexion->prepare($sql);
if (!$stmt) respuesta(500, ['estado'=>'error','mensaje'=>'No se pudo preparar la actualización. Verifica que equipos tenga la columna fecha_actualizacion.']);
$stmt->bind_param('iisssssssi', $idPiso, $idTipo, $nombre, $marca, $modelo, $serial, $ip, $icono, $obs, $id);
if (!$stmt->execute()) { $error = $stmt->error; $stmt->close(); respuesta(500, ['estado'=>'error','mensaje'=>'No se pudieron guardar los cambios: '.$error]); }
$stmt->close(); $conexion->close();
echo json_encode(['estado'=>'ok','mensaje'=>'Datos generales actualizados. Puertos y enlaces conservados.'], JSON_UNESCAPED_UNICODE);
