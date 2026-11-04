<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function responder(int $code, array $data): never { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405,['estado'=>'error','mensaje'=>'Método no permitido.']);
$data=json_decode(file_get_contents('php://input'),true);
$id=filter_var($data['id_enlace']??null,FILTER_VALIDATE_INT);
if (!$id) responder(400,['estado'=>'error','mensaje'=>'ID de enlace inválido.']);
$conexion->begin_transaction();
try {
 $q=$conexion->prepare('SELECT id_puerto_origen,id_puerto_destino FROM enlaces WHERE id_enlace=? FOR UPDATE');
 $q->bind_param('i',$id); $q->execute(); $link=$q->get_result()->fetch_assoc();
 if (!$link) throw new RuntimeException('El enlace ya no existe.');
 $del=$conexion->prepare('DELETE FROM enlaces WHERE id_enlace=?'); $del->bind_param('i',$id); $del->execute();
 foreach ([(int)$link['id_puerto_origen'],(int)$link['id_puerto_destino']] as $portId) {
  $free=$conexion->prepare("UPDATE puertos SET estado='DISPONIBLE' WHERE id_puerto=? AND NOT EXISTS(SELECT 1 FROM enlaces WHERE id_puerto_origen=? OR id_puerto_destino=?)");
  $free->bind_param('iii',$portId,$portId,$portId); $free->execute();
 }
 $conexion->commit(); responder(200,['estado'=>'ok','mensaje'=>'Enlace eliminado y puertos liberados.','puertos'=>[(int)$link['id_puerto_origen'],(int)$link['id_puerto_destino']]]);
} catch(Throwable $e) { $conexion->rollback(); responder(400,['estado'=>'error','mensaje'=>$e->getMessage()]); }
