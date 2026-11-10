<?php
// NetMap: registro de equipos, iconos, grupos de puertos y enlaces.
$modoInsertado = isset($_GET['embed']) && $_GET['embed'] === '1';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetMap · Registrar equipos</title>
    <link rel="stylesheet" href="assets/css/registro_visual.css">
    <link rel="stylesheet" href="assets/css/registro_visual_mejoras.css">
    <link rel="stylesheet" href="assets/css/registro_visual_ajustes_fase2.css">
    <link rel="stylesheet" href="assets/css/ajustes_sugerencias.css">
    <link rel="stylesheet" href="assets/css/ajustes_fase_siguiente.css">
    <link rel="stylesheet" href="assets/css/netmap_global.css">
    <script src="assets/js/netmap_global.js" defer></script>
    <script src="assets/js/registro_visual.js" defer></script>
    <style>
        .is-embedded .netmap-nav,
        .is-embedded .app-header,
        .is-embedded .floating-navigation { display: none !important; }
        .is-embedded .page { width: 100%; max-width: none; margin: 0; padding: 12px; }
        .is-embedded .page-heading { margin-bottom: 10px; }
        .is-embedded .page-heading h1 { font-size: 19px; }
        .is-embedded .page-heading p { font-size: 12px; }
        .is-embedded .selection-toolbar { margin: 10px 0; }
        .is-embedded .bottom { position: sticky; bottom: 0; z-index: 15; padding: 10px; margin: 10px -12px 0; background: rgba(8, 15, 29, .96); border-top: 1px solid #334155; }
    </style>
</head>
<body class="<?= $modoInsertado ? 'is-embedded' : '' ?>">
<header class="app-header">
    <strong>◈ NetMap</strong>
    <span>Registro de equipos</span>
    <a href="equipos_lista.php">← Volver al listado</a>
</header>

<main class="page">
    <section class="page-heading">
        <div>
            <h1>Registrar equipos</h1>
            <p>Completa los datos, elige el icono y configura los puertos.</p>
        </div>
    </section>

    <form id="form-lote" novalidate>
        <div id="contenedor-tarjetas" class="cards"></div>

        <div class="selection-toolbar" aria-label="Acciones para los equipos seleccionados">
            <span class="selection-toolbar-title">Acciones de equipos y conexiones</span>
            <div class="toolbar">
                <button type="button" id="toggleView">Vista previa conjunta</button>
                <button type="button" id="toggleExisting">Enlazar equipos existentes</button>
            </div>
        </div>

        <div class="bottom">
            <button type="button" id="btn-add-card">＋ Añadir otro equipo</button>
            <span id="formStatus" aria-live="polite">Cargando catálogos…</span>
            <button type="submit" class="primary" id="btnSave">Guardar equipos y enlaces</button>
        </div>
    </form>

    <section id="existingPanel" class="existing-panel">
        <h2>Equipos existentes y enlaces</h2>
        <div class="searchrow">
            <input id="existingSearch" type="search" placeholder="Nombre, serial, IP, marca o modelo">
            <select id="existingSite">
                <option value="">Todas las sedes</option>
            </select>
            <select id="existingFloor">
                <option value="">Todos los pisos</option>
            </select>
            <select id="existingStateFilter" aria-label="Filtrar puertos por estado">
                <option value="all">Todos los estados</option>
                <option value="available">Disponible</option>
                <option value="occupied">Ocupado / enlazado</option>
                <option value="reserved">Reservado</option>
                <option value="damaged">Dañado</option>
            </select>
            <button type="button" id="searchExisting">Buscar</button>
        </div>
        <p class="muted">Selecciona uno o varios equipos. Puedes enlazar puertos de equipos nuevos con equipos existentes o conectar entre sí equipos que ya estaban registrados. También puedes guardar un equipo nuevo sin crear enlaces.</p>
        <div id="existingResults" aria-live="polite"></div>
        <div id="existingPorts" class="existing-ports"></div>
    </section>
</main>
<div class="floating-navigation" aria-label="Navegación rápida">
    <button type="button" id="scrollToPorts" title="Bajar al listado de puertos">↓ Puertos</button>
    <button type="button" id="scrollToEditor" title="Subir al editor">↑ Editor</button>
</div>
<div id="toast" role="status" aria-live="polite"></div>
</body>
</html>
