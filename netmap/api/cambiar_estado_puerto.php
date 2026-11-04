<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';
function responder(int $code, array $data): never { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') responder(405, ['estado'=>'error','mensaje'=>'Método no permitido.']);
$data = json_decode(file_get_contents('php://input'), true);
$id = filter_var($data['id_puerto'] ?? null, FILTER_VALIDATE_INT);
$estado = strtoupper(trim((string)($data['estado'] ?? '')));
$permitidos = ['DISPONIBLE','NO_DISPONIBLE','RESERVADO','DANADO'];
if (!$id || !in_array($estado, $permitidos, true)) responder(400, ['estado'=>'error','mensaje'=>'Puerto o estado inválido.']);
$stmt = $conexion->prepare('SELECT estado, EXISTS(SELECT 1 FROM enlaces e WHERE e.id_puerto_origen=p.id_puerto OR e.id_puerto_destino=p.id_puerto) enlazado FROM puertos p WHERE id_puerto=? FOR UPDATE');
$conexion->begin_transaction();
try {
 $stmt->bind_param('i',$id); $stmt->execute(); $port=$stmt->get_result()->fetch_assoc();
 if (!$port) throw new RuntimeException('El puerto no existe.');
 if ((int)$port['enlazado']===1 && $estado!=='NO_DISPONIBLE') throw new RuntimeException('Quita el enlace antes de cambiar el estado de este puerto.');
 $update=$conexion->prepare('UPDATE puertos SET estado=? WHERE id_puerto=?'); $update->bind_param('si',$estado,$id); $update->execute();
 $conexion->commit(); responder(200,['estado'=>'ok','id_puerto'=>$id,'estado_puerto'=>$estado]);
} catch(Throwable $e) { $conexion->rollback(); responder(400,['estado'=>'error','mensaje'=>$e->getMessage()]); }
