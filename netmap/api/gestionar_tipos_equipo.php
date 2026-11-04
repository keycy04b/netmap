<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function responderTipo(int $status,array $data): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE); exit; }
$method=$_SERVER['REQUEST_METHOD'];
if($method==='GET'){
 $r=$conexion->query('SELECT id_tipo_equipo,nombre_tipo,ruta_icono FROM tipos_equipo ORDER BY nombre_tipo');
 if(!$r)responderTipo(500,['estado'=>'error','mensaje'=>$conexion->error]);
 responderTipo(200,['tipos'=>$r->fetch_all(MYSQLI_ASSOC)]);
}
$data=json_decode(file_get_contents('php://input'),true);
if(!is_array($data))responderTipo(400,['estado'=>'error','mensaje'=>'JSON inválido.']);
if($method==='POST'){
 $id=filter_var($data['id_tipo_equipo']??null,FILTER_VALIDATE_INT);
 $name=trim((string)($data['nombre_tipo']??''));
 $icon=trim((string)($data['ruta_icono']??''));
 $nameLength = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
 if($name===''||$nameLength>100)responderTipo(400,['estado'=>'error','mensaje'=>'El nombre debe tener entre 1 y 100 caracteres.']);
 if($id){
  $s=$conexion->prepare('UPDATE tipos_equipo SET nombre_tipo=?,ruta_icono=? WHERE id_tipo_equipo=?');
  $s->bind_param('ssi',$name,$icon,$id);
 }else{
  $s=$conexion->prepare('INSERT INTO tipos_equipo (nombre_tipo,ruta_icono) VALUES (?,?)');
  $s->bind_param('ss',$name,$icon);
 }
 if(!$s->execute())responderTipo(400,['estado'=>'error','mensaje'=>$s->error]);
 responderTipo(200,['estado'=>'ok','mensaje'=>'Tipo de equipo guardado.']);
}
if($method==='DELETE'){
 $id=filter_var($data['id_tipo_equipo']??null,FILTER_VALIDATE_INT);
 if(!$id)responderTipo(400,['estado'=>'error','mensaje'=>'Tipo inválido.']);
 $check=$conexion->prepare('SELECT COUNT(*) total FROM equipos WHERE id_tipo_equipo=?');
 $check->bind_param('i',$id);$check->execute();
 if((int)$check->get_result()->fetch_assoc()['total']>0)responderTipo(409,['estado'=>'error','mensaje'=>'No se puede eliminar: hay equipos registrados con este tipo.']);
 $del=$conexion->prepare('DELETE FROM tipos_equipo WHERE id_tipo_equipo=?');$del->bind_param('i',$id);
 if(!$del->execute())responderTipo(400,['estado'=>'error','mensaje'=>$del->error]);
 responderTipo(200,['estado'=>'ok','mensaje'=>'Tipo eliminado.']);
}
responderTipo(405,['estado'=>'error','mensaje'=>'Método no permitido.']);
?>