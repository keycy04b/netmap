<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function salida(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $tipos = $conexion->query('SELECT id_tipo_equipo, nombre_tipo FROM tipos_equipo ORDER BY nombre_tipo');
    $presets = $conexion->query(
        'SELECT sp.id_sugerencia, sp.id_tipo_equipo, te.nombre_tipo,
                sp.alcance, sp.medio, sp.cantidad, sp.secciones, sp.activo
         FROM sugerencias_puertos sp
         INNER JOIN tipos_equipo te ON te.id_tipo_equipo=sp.id_tipo_equipo
         ORDER BY te.nombre_tipo, sp.alcance, sp.medio, sp.cantidad'
    );
    if (!$tipos || !$presets) salida(500, ['estado'=>'error','mensaje'=>'No se pudieron leer los presets. Ejecuta la migración 001_sugerencias_puertos.sql.']);
    salida(200, ['tipos'=>$tipos->fetch_all(MYSQLI_ASSOC),'presets'=>$presets->fetch_all(MYSQLI_ASSOC)]);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) salida(400, ['estado'=>'error','mensaje'=>'JSON inválido.']);

if ($method === 'POST') {
    $type = filter_var($data['id_tipo_equipo'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($data['cantidad'] ?? null, FILTER_VALIDATE_INT);
    $sections = filter_var($data['secciones'] ?? null, FILTER_VALIDATE_INT);
    $reach = strtoupper(trim((string)($data['alcance'] ?? 'LAN')));
    $medium = strtoupper(trim((string)($data['medio'] ?? 'UTP')));
    if (!$type || !$quantity || $quantity < 1 || $quantity > 512 || !$sections || $sections < 1 || $sections > $quantity ||
        !in_array($reach,['LAN','WAN'],true) || !in_array($medium,['UTP','FIBRA'],true)) {
        salida(400, ['estado'=>'error','mensaje'=>'Datos del preset inválidos.']);
    }
    $stmt = $conexion->prepare(
        'INSERT INTO sugerencias_puertos (id_tipo_equipo,alcance,medio,cantidad,secciones,activo)
         VALUES (?,?,?,?,?,1)
         ON DUPLICATE KEY UPDATE secciones=VALUES(secciones), activo=1'
    );
    $stmt->bind_param('issii',$type,$reach,$medium,$quantity,$sections);
    if (!$stmt->execute()) salida(400,['estado'=>'error','mensaje'=>$stmt->error]);
    salida(200,['estado'=>'ok','mensaje'=>'Preset guardado.']);
}

if ($method === 'DELETE') {
    $id = filter_var($data['id_sugerencia'] ?? null, FILTER_VALIDATE_INT);
    if (!$id) salida(400,['estado'=>'error','mensaje'=>'Preset inválido.']);
    $stmt=$conexion->prepare('DELETE FROM sugerencias_puertos WHERE id_sugerencia=?');
    $stmt->bind_param('i',$id);
    if (!$stmt->execute()) salida(400,['estado'=>'error','mensaje'=>$stmt->error]);
    salida(200,['estado'=>'ok','mensaje'=>'Preset eliminado.']);
}

salida(405,['estado'=>'error','mensaje'=>'Método no permitido.']);
?>