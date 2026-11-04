<?php
require_once "conexion.php";

$sql = "SELECT 
    e.id_enlace,
    e.estilo_linea,
    e.color_linea,
    e.caracteristicas AS caracteristicas_enlace,
    e.observaciones AS observaciones_enlace,
    
    -- Datos del Extremo Origen
    eq_origen.nombre_equipo AS origen_nombre,
    eq_origen.marca AS origen_marca,
    eq_origen.modelo AS origen_modelo,
    eq_origen.serial AS origen_serial,
    eq_origen.direccion_ip AS origen_ip,
    eq_origen.tipo_red AS origen_tipo_red,
    s_origen.nombre_sede AS origen_sede,
    p_origen.numero_piso AS origen_piso,
    pt_origen.numero_puerto AS origen_numero_puerto,
    pt_origen.etiqueta_puerto AS origen_etiqueta_puerto,
    pt_origen.tipo_puerto AS origen_tipo_puerto,

    -- Datos del Extremo Destino
    eq_destino.nombre_equipo AS destino_nombre,
    eq_destino.marca AS destino_marca,
    eq_destino.modelo AS destino_modelo,
    eq_destino.serial AS destino_serial,
    eq_destino.direccion_ip AS destino_ip,
    eq_destino.tipo_red AS destino_tipo_red,
    s_destino.nombre_sede AS destino_sede,
    p_destino.numero_piso AS destino_piso,
    pt_destino.numero_puerto AS destino_numero_puerto,
    pt_destino.etiqueta_puerto AS destino_etiqueta_puerto,
    pt_destino.tipo_puerto AS destino_tipo_puerto

FROM enlaces e
INNER JOIN puertos pt_origen ON e.id_puerto_origen = pt_origen.id_puerto
INNER JOIN equipos eq_origen ON pt_origen.id_equipo = eq_origen.id_equipo
INNER JOIN pisos p_origen ON eq_origen.id_piso = p_origen.id_piso
INNER JOIN sedes s_origen ON p_origen.id_sede = s_origen.id_sede

INNER JOIN puertos pt_destino ON e.id_puerto_destino = pt_destino.id_puerto
INNER JOIN equipos eq_destino ON pt_destino.id_equipo = eq_destino.id_equipo
INNER JOIN pisos p_destino ON eq_destino.id_piso = p_destino.id_piso
INNER JOIN sedes s_destino ON p_destino.id_sede = s_destino.id_sede";

$resultado = $conexion->query($sql);
$enlaces = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $enlaces[] = $fila;
    }
}

echo json_encode($enlaces);
$conexion->close();
