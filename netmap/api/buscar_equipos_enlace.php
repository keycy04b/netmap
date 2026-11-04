<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$sql = "
    SELECT
        e.id_equipo,
        e.id_piso,
        p.id_sede,
        e.id_tipo_equipo,
        e.nombre_equipo,
        e.marca,
        e.modelo,
        e.serial,
        e.direccion_ip,
        t.nombre_tipo,
        s.nombre_sede,
        p.numero_piso
    FROM equipos e
    INNER JOIN tipos_equipo t ON t.id_tipo_equipo = e.id_tipo_equipo
    INNER JOIN pisos p ON p.id_piso = e.id_piso
    INNER JOIN sedes s ON s.id_sede = p.id_sede
    ORDER BY s.nombre_sede, p.numero_piso, e.nombre_equipo
";

$result = $conexion->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['estado' => 'error', 'mensaje' => 'No se pudo consultar el inventario de equipos.'], JSON_UNESCAPED_UNICODE);
    $conexion->close();
    exit;
}

echo json_encode($result->fetch_all(MYSQLI_ASSOC), JSON_UNESCAPED_UNICODE);
$result->free();
$conexion->close();
