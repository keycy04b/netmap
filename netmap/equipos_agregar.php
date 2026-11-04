<?php
// NetMap: registro de equipos, iconos, grupos de puertos y enlaces.
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
    <script src="assets/js/registro_visual.js" defer></script>
</head>
<body>
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
        <div class="toolbar">
            <button type="button" id="toggleView">Vista previa conjunta</button>
            <button type="button" id="toggleExisting">Buscar equipo para enlazar</button>
        </div>
    </section>

    <details id="existingPanel" class="existing-panel">
        <summary>Buscar un equipo ya registrado para enlazarlo</summary>
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
        <p class="muted">Selecciona un equipo y después pulsa un puerto nuevo y un puerto de ese equipo para preparar el enlace. Se registrará al guardar.</p>
        <div id="existingResults" aria-live="polite"></div>
        <div id="existingPorts" class="existing-ports"></div>
    </details>

    <form id="form-lote" novalidate>
        <div id="contenedor-tarjetas" class="cards"></div>
        <div class="bottom">
            <button type="button" id="btn-add-card">＋ Añadir otro equipo</button>
            <span id="formStatus" aria-live="polite">Cargando catálogos…</span>
            <button type="submit" class="primary" id="btnSave">Guardar equipos</button>
        </div>
    </form>
</main>
<div id="toast" role="status" aria-live="polite"></div>
</body>
</html>
