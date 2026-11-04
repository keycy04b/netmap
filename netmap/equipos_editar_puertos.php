<?php
// NetMap - Editor de equipo y gestión individual de puertos.
require_once __DIR__ . '/api/conexion.php';
header('Content-Type: text/html; charset=utf-8');
$equipos = [];
$sql = "SELECT e.id_equipo, e.nombre_equipo, e.marca, e.modelo, e.serial, e.direccion_ip,
               e.observaciones, e.id_piso, e.id_tipo_equipo, e.icono,
               t.nombre_tipo, p.numero_piso, p.id_sede, s.nombre_sede
        FROM equipos e
        LEFT JOIN tipos_equipo t ON t.id_tipo_equipo=e.id_tipo_equipo
        LEFT JOIN pisos p ON p.id_piso=e.id_piso
        LEFT JOIN sedes s ON s.id_sede=p.id_sede
        ORDER BY e.nombre_equipo";
if ($r = $conexion->query($sql)) { while ($row = $r->fetch_assoc()) $equipos[] = $row; }
$tipos=[]; if ($r=$conexion->query("SELECT id_tipo_equipo,nombre_tipo FROM tipos_equipo ORDER BY nombre_tipo")) while($x=$r->fetch_assoc()) $tipos[]=$x;
$sedes=[]; if ($r=$conexion->query("SELECT id_sede,nombre_sede FROM sedes ORDER BY nombre_sede")) while($x=$r->fetch_assoc()) $sedes[]=$x;
$pisos=[]; if ($r=$conexion->query("SELECT id_piso,id_sede,numero_piso FROM pisos ORDER BY id_sede,numero_piso")) while($x=$r->fetch_assoc()) $pisos[]=$x;
$conexion->close();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>NetMap · Editar equipo</title>
<link rel="stylesheet" href="assets/css/equipos_editar_puertos.css"></head><body>
<header><b>◈ NetMap</b><span>Edición de equipo y puertos</span><a href="equipos_lista.php">Volver al listado</a></header>
<main><h1>Editar equipo</h1><p class="muted">Selecciona un equipo para editar sus datos y administrar cada puerto.</p>
<section class="panel"><label for="equipoSelect">Equipo</label><select id="equipoSelect"><option value="">Seleccionar equipo…</option><?php foreach($equipos as $e): ?><option value="<?= (int)$e['id_equipo'] ?>"><?= htmlspecialchars(($e['nombre_equipo'] ?: $e['nombre_tipo'].' '.$e['id_equipo']).' · '.($e['nombre_sede']??'Sin sede').' · Piso '.($e['numero_piso']??'—'),ENT_QUOTES,'UTF-8') ?></option><?php endforeach ?></select></section>
<div id="editor" hidden>
<section class="topgrid"><div class="panel device"><div class="device-image"><img id="deviceIcon" alt="Icono del equipo"><div id="miniPorts" class="mini-ports"></div></div><div class="legend"><span><i class="available"></i>Disponible</span><span><i class="linked"></i>Enlazado/ocupado</span><span><i class="reserved"></i>Reservado</span><span><i class="damaged"></i>Dañado</span></div></div>
<form id="formEquipo" class="panel fields"><h2>Datos del equipo</h2><input type="hidden" name="id_equipo"><label>Nombre<input name="nombre_equipo" required></label><div class="twocol"><label>Tipo<select name="id_tipo_equipo" required></select></label><label>Marca<input name="marca"></label><label>Modelo<input name="modelo"></label><label>Serial<input name="serial"></label><label>Dirección IP<input name="direccion_ip"></label><label>Sede<select id="sede" required></select></label><label>Piso<select name="id_piso" id="piso" required></select></label></div><label>Observaciones<textarea name="observaciones" rows="2"></textarea></label><label>Ruta del icono<input name="icono" id="iconoPath" placeholder="assets/img/iconos/switch.svg"></label><button type="submit">Guardar datos</button><span id="saveMessage" class="message"></span></form></section>
<section class="panel"><h2>Buscar otro equipo para enlazar</h2><input id="searchEquipment" placeholder="Buscar por nombre, marca, IP o serial"><div id="searchResults" class="search-results"></div><p class="muted">Esta sección permite localizar equipos; la creación/eliminación de enlaces se mantiene en el flujo de enlaces existente.</p></section>
<section class="panel"><h2>Puertos del equipo</h2><div class="table-wrap"><table><thead><tr><th>Puerto</th><th>Tipo / medio</th><th>Estado</th><th>Equipo destino</th><th>Puerto destino</th><th>Creación</th><th>Actualización</th><th>Acciones</th></tr></thead><tbody id="portsBody"><tr><td colspan="8">Selecciona un equipo.</td></tr></tbody></table></div></section>
</div></main>
<script>window.NETMAP_EDIT_DATA={equipoInicial:<?= isset($_GET['id']) ? (int)$_GET['id'] : 0 ?>,equipos:<?= json_encode($equipos,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>,tipos:<?= json_encode($tipos,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>,sedes:<?= json_encode($sedes,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>,pisos:<?= json_encode($pisos,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>};</script><script src="assets/js/equipos_editar_puertos.js" defer></script></body></html>
