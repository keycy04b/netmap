<?php
header('Content-Type: application/json; charset=utf-8');

// Incluir la conexión
require_once "conexion.php";

if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode(["estado" => "error", "mensaje" => "Error de conexión a la base de datos"]);
    exit;
}

$datos = [
    "tipos_equipo" => [],
    "sedes" => [],
    "pisos" => []
];

// 1. Obtener Tipos de Equipo
if ($res1 = $conexion->query("SELECT id_tipo_equipo, nombre_tipo FROM tipos_equipo")) {
    $datos['tipos_equipo'] = $res1->fetch_all(MYSQLI_ASSOC);
    $res1->free();
}

// 2. Obtener Sedes
if ($res2 = $conexion->query("SELECT id_sede, nombre_sede FROM sedes")) {
    $datos['sedes'] = $res2->fetch_all(MYSQLI_ASSOC);
    $res2->free();
}

// 3. Obtener Pisos
if ($res3 = $conexion->query("SELECT id_piso, id_sede, numero_piso FROM pisos")) {
    $datos['pisos'] = $res3->fetch_all(MYSQLI_ASSOC);
    $res3->free();
}

// Enviar el JSON limpio sin errores
echo json_encode($datos);
?>

