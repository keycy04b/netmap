<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function respuesta(int $c,array $d): never { http_response_code($c); echo json_encode($d,JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD']!=='POST') respuesta(405,['estado'=>'error','mensaje'=>'Método no permitido.']);
$d=json_decode(file_get_contents('php://input'),true);
$id=filter_var($d['id_equipo']??null,FILTER_VALIDATE_INT);
if(!$id) respuesta(400,['estado'=>'error','mensaje'=>'Equipo inválido.']);
$nombre=ucfirst(trim((string)($d['nombre_equipo']??'')));
$marca=ucfirst(trim((string)($d['marca']??'')));
$modelo=ucfirst(trim((string)($d['modelo']??'')));
$serial=strtoupper(trim((string)($d['serial']??'')));
$ip=trim((string)($d['direccion_ip']??''));
$obs=trim((string)($d['observaciones']??''));
$icono=trim((string)($d['icono']??''));
$tipo=filter_var($d['id_tipo_equipo']??null,FILTER_VALIDATE_INT);
$piso=filter_var($d['id_piso']??null,FILTER_VALIDATE_INT);
if($nombre===''||!$tipo||!$piso) respuesta(400,['estado'=>'error','mensaje'=>'Nombre, tipo y piso son obligatorios.']);
$q=$conexion->prepare('UPDATE equipos SET nombre_equipo=?, marca=?, modelo=?, serial=?, direccion_ip=?, observaciones=?, icono=?, id_tipo_equipo=?, id_piso=? WHERE id_equipo=?');
if(!$q) respuesta(500,['estado'=>'error','mensaje'=>'No se pudo preparar la actualización.']);
$q->bind_param('sssssssiii',$nombre,$marca,$modelo,$serial,$ip,$obs,$icono,$tipo,$piso,$id);
if(!$q->execute()) respuesta(500,['estado'=>'error','mensaje'=>'No se pudieron guardar los datos del equipo.']);
respuesta(200,['estado'=>'ok','mensaje'=>'Datos del equipo actualizados.']);
