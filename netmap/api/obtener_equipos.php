<?php
require_once "conexion.php";

// Consultamos los equipos uniendo sus tablas relacionadas para traer también el nombre del tipo, piso y sede
$sql = "SELECT e.id_equipo, e.nombre_equipo, e.marca, e.modelo, e.serial, 
               e.direccion_ip, e.tipo_red, e.posicion_x, e.posicion_y, e.observaciones,
               t.nombre_tipo, t.ruta_icono, 
               p.numero_piso, 
               s.nombre_sede
        FROM equipos e
        INNER JOIN tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo
        INNER JOIN pisos p ON e.id_piso = p.id_piso
        INNER JOIN sedes s ON p.id_sede = s.id_sede";

$resultado = $conexion->query($sql);
$equipos = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $equipos[] = $fila;
    }
}

echo json_encode($equipos);
$conexion->close();
