<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root"; // Usuario por defecto de XAMPP
$password = ""; // Contraseña por defecto (vacía en XAMPP)
$database = "netmap";

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "Error de conexión a la base de datos: " . $conexion->connect_error
    ]);
    exit();
}

$conexion->set_charset("utf8mb4");
