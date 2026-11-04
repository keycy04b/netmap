<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

function iconResponse(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    iconResponse(405, ['estado' => 'error', 'mensaje' => 'Método no permitido.']);
}
if (!isset($_FILES['icono']) || !is_uploaded_file($_FILES['icono']['tmp_name'])) {
    iconResponse(400, ['estado' => 'error', 'mensaje' => 'No se recibió un archivo de imagen válido.']);
}

$file = $_FILES['icono'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    iconResponse(400, ['estado' => 'error', 'mensaje' => 'Falló la carga de la imagen.']);
}
if ((int)$file['size'] < 1 || (int)$file['size'] > 3 * 1024 * 1024) {
    iconResponse(413, ['estado' => 'error', 'mensaje' => 'La imagen debe pesar como máximo 3 MB.']);
}

$allowed = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
    'image/gif' => 'gif'
];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime])) {
    iconResponse(415, ['estado' => 'error', 'mensaje' => 'Formato no permitido. Usa PNG, JPG, WEBP o GIF.']);
}
if (@getimagesize($file['tmp_name']) === false) {
    iconResponse(415, ['estado' => 'error', 'mensaje' => 'El archivo no parece ser una imagen válida.']);
}

$directory = dirname(__DIR__) . '/assets/icons/custom';
if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
    iconResponse(500, ['estado' => 'error', 'mensaje' => 'No se pudo crear la carpeta de iconos.']);
}
if (!is_writable($directory)) {
    iconResponse(500, ['estado' => 'error', 'mensaje' => 'La carpeta assets/icons/custom no tiene permisos de escritura.']);
}

$filename = 'icon_' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
$destination = $directory . '/' . $filename;
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    iconResponse(500, ['estado' => 'error', 'mensaje' => 'No se pudo guardar la imagen en el servidor.']);
}

iconResponse(200, [
    'estado' => 'ok',
    'ruta' => 'assets/icons/custom/' . $filename,
    'mensaje' => 'Icono guardado.'
]);
