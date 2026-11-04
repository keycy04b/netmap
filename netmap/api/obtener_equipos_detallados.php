<?php
header('Content-Type: application/json; charset=utf-8');
require_once "conexion.php";

$sql = "SELECT e.id_equipo, e.nombre_equipo, e.marca, e.modelo, e.serial,
               e.direccion_ip, e.numero_puertos, e.observaciones,
               e.fecha_creacion, e.fecha_actualizacion,
               t.nombre_tipo,
               p.numero_piso,
               s.nombre_sede,
               (SELECT COUNT(*) FROM puertos pt
                INNER JOIN enlaces en ON pt.id_puerto = en.id_puerto_origen OR pt.id_puerto = en.id_puerto_destino
                WHERE pt.id_equipo = e.id_equipo) AS total_enlaces
        FROM equipos e
        INNER JOIN tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo
        INNER JOIN pisos p ON e.id_piso = p.id_piso
        INNER JOIN sedes s ON p.id_sede = s.id_sede
        ORDER BY e.id_equipo DESC";

$resultado = $conexion->query($sql);
$equipos = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $equipos[] = $fila;
    }
}

echo json_encode($equipos);
$conexion->close();
?>


