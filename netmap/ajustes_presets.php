<?php
// NetMap: administración de sugerencias de puertos.
require_once __DIR__ . '/api/conexion.php';
$tipos = [];
$resultado = $conexion->query('SELECT id_tipo_equipo,nombre_tipo FROM tipos_equipo ORDER BY nombre_tipo');
if ($resultado) $tipos = $resultado->fetch_all(MYSQLI_ASSOC);
$conexion->close();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>NetMap · Presets de puertos</title>
<link rel="stylesheet" href="assets/css/netmap_global.css">
<style>
body{margin:0;font-family:system-ui,sans-serif}.wrap{max-width:1050px;margin:28px auto;padding:0 16px}
.panel{background:var(--netmap-surface,#fff);color:var(--netmap-text,#172033);border:1px solid var(--netmap-border,#dce3ec);border-radius:14px;padding:18px;margin:14px 0}
form{display:grid;grid-template-columns:repeat(5,minmax(120px,1fr));gap:10px;align-items:end}
label{display:grid;gap:5px;font-size:.82rem;font-weight:700}
input,select{min-width:0;padding:9px;border:1px solid #aab6c6;border-radius:8px}
button{padding:9px 12px;border:0;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer}
table{width:100%;border-collapse:collapse}td,th{padding:10px;text-align:left;border-bottom:1px solid var(--netmap-border,#dce3ec)}
.delete{background:#b42318}.notice{color:var(--netmap-muted,#64748b)}
@media(max-width:750px){form{grid-template-columns:1fr 1fr}}
</style>
<script src="assets/js/netmap_global.js" defer></script>
<script src="assets/js/gestionar_presets.js" defer></script>
</head>
<body>
<header><strong>NetMap</strong><span>Administración de sugerencias de puertos</span><a href="equipos_agregar.php">Volver al registro</a></header>
<main class="wrap">
<h1>Presets de puertos</h1>
<p class="notice">Configura cantidades sugeridas por tipo de equipo, alcance, medio físico y secciones. Estos valores aparecerán en las tarjetas al registrar equipos.</p>
<section class="panel">
<h2>Tipos de equipo</h2>
<form id="typeForm" style="display:grid;grid-template-columns:2fr 2fr auto;gap:10px;align-items:end">
<label>Nombre del tipo<input name="nombre_tipo" maxlength="100" required placeholder="Ej. Punto de acceso"></label>
<label>Ruta del icono<input name="ruta_icono" placeholder="assets/icons/access-point.svg"></label>
<button type="submit">Agregar tipo</button>
</form>
<div style="overflow:auto;margin-top:12px"><table><thead><tr><th>Tipo</th><th>Icono</th><th></th></tr></thead><tbody id="typeRows"><tr><td colspan="3">Cargando…</td></tr></tbody></table></div>
<p id="typeMessage" class="notice" aria-live="polite"></p>
</section>
<section class="panel">
<h2>Agregar o actualizar sugerencia</h2>
<form id="presetForm">
<label>Tipo de equipo<select name="id_tipo_equipo" required><?php foreach($tipos as $tipo): ?><option value="<?= (int)$tipo['id_tipo_equipo'] ?>"><?= htmlspecialchars($tipo['nombre_tipo'],ENT_QUOTES,'UTF-8') ?></option><?php endforeach ?></select></label>
<label>Alcance<select name="alcance"><option>LAN</option><option>WAN</option></select></label>
<label>Medio físico<select name="medio"><option value="UTP">Ethernet / UTP</option><option value="FIBRA">Fibra óptica</option></select></label>
<label>Cantidad<input name="cantidad" type="number" min="1" max="512" value="24" required></label>
<label>Secciones<input name="secciones" type="number" min="1" max="512" value="2" required></label>
<button type="submit">Guardar preset</button>
</form>
<p id="presetMessage" class="notice" aria-live="polite"></p>
</section>
<section class="panel">
<h2>Sugerencias configuradas</h2>
<div style="overflow:auto"><table><thead><tr><th>Tipo</th><th>Alcance</th><th>Medio</th><th>Puertos</th><th>Secciones</th><th></th></tr></thead><tbody id="presetRows"><tr><td colspan="6">Cargando…</td></tr></tbody></table></div>
</section>
</main>
</body></html>