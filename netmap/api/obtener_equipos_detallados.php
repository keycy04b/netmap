<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion.php';

$sql = "
    SELECT
        e.id_equipo,
        e.nombre_equipo,
        e.marca,
        e.modelo,
        e.serial,
        e.direccion_ip,
        e.numero_puertos,
        e.observaciones,
        e.fecha_creacion,
        e.fecha_actualizacion,
        t.nombre_tipo,
        p.numero_piso,
        s.nombre_sede,
        (SELECT COUNT(*)
         FROM puertos pt
         INNER JOIN enlaces en
            ON en.id_puerto_origen = pt.id_puerto
            OR en.id_puerto_destino = pt.id_puerto
         WHERE pt.id_equipo = e.id_equipo) AS total_enlaces,
        (SELECT COUNT(*) FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND EXISTS (
             SELECT 1 FROM enlaces en
             WHERE en.id_puerto_origen = pt.id_puerto
                OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_enlazados,
        (SELECT COUNT(*) FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND pt.estado = 'DISPONIBLE'
           AND NOT EXISTS (
             SELECT 1 FROM enlaces en
             WHERE en.id_puerto_origen = pt.id_puerto
                OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_disponibles,
        (SELECT COUNT(*) FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND pt.estado = 'NO_DISPONIBLE'
           AND NOT EXISTS (
             SELECT 1 FROM enlaces en
             WHERE en.id_puerto_origen = pt.id_puerto
                OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_ocupados,
        (SELECT COUNT(*) FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND pt.estado = 'RESERVADO'
           AND NOT EXISTS (
             SELECT 1 FROM enlaces en
             WHERE en.id_puerto_origen = pt.id_puerto
                OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_reservados,
        (SELECT COUNT(*) FROM puertos pt
         WHERE pt.id_equipo = e.id_equipo
           AND pt.estado IN ('DANADO', 'DAÑADO')
           AND NOT EXISTS (
             SELECT 1 FROM enlaces en
             WHERE en.id_puerto_origen = pt.id_puerto
                OR en.id_puerto_destino = pt.id_puerto
           )) AS puertos_danados
    FROM equipos e
    INNER JOIN tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo
    INNER JOIN pisos p ON e.id_piso = p.id_piso
    INNER JOIN sedes s ON p.id_sede = s.id_sede
    ORDER BY e.id_equipo DESC
";

$resultado = $conexion->query($sql);
$equipos = [];

if (!$resultado) {
    http_response_code(500);
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'No se pudo obtener el inventario: ' . $conexion->error
    ], JSON_UNESCAPED_UNICODE);
    $conexion->close();
    exit;
}

while ($fila = $resultado->fetch_assoc()) {
    $equipos[] = $fila;
}

echo json_encode($equipos, JSON_UNESCAPED_UNICODE);
$conexion->close();
?>