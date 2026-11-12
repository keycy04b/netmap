<?php
require_once __DIR__ . '/api/conexion.php';
header('Content-Type: text/html; charset=utf-8');
$conexion->close();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetMap · Topología</title>
    <link rel="stylesheet" href="assets/css/netmap_global.css">
    <link rel="stylesheet" href="assets/css/topologia.css">
    <script src="assets/js/netmap_global.js" defer></script>
    <script src="assets/js/topologia.js" defer></script>
</head>
<body class="topology-page">
    <header class="app-header topology-header">
        <strong>◈ NetMap</strong>
        <span>Topología de red · Inventario real</span>
        <a href="index.php">Inicio</a>
    </header>

    <main class="topology-main">
        <section class="topology-heading">
            <div>
                <h1>Mapa de infraestructura</h1>
                <p>Organiza, consulta y prepara una vista exportable de tus equipos y conexiones.</p>
            </div>
            <div class="topology-filters">
                <label>Sede
                    <select id="filterSite"><option value="">Todas las sedes</option></select>
                </label>
                <label>Piso
                    <select id="filterFloor"><option value="">Todos los pisos</option></select>
                </label>
            </div>
        </section>

        <section class="topology-toolbar" aria-label="Herramientas del mapa">
            <div class="toolbar-group">
                <button type="button" class="primary" id="btnNewEquipment">＋ Registrar equipo</button>
                <button type="button" id="btnCanvasLink">↔ Enlazar equipos del mapa</button>
                <select id="canvasLinkType" aria-label="Tipo de enlace"><option value="any">Enlace (elegir medio)</option><option value="ethernet">Red / Ethernet</option><option value="fiber">Fibra óptica</option></select>
                <button type="button" id="btnCancelCanvasLink" hidden>Cancelar selección</button>
                <button type="button" id="btnAutoArrange" title="Distribuye los equipos y guarda sus posiciones">✣ Organizar automáticamente</button>
                <button type="button" id="btnSaveLayout">Guardar distribución</button>
            </div>
            <div class="toolbar-group">
                <button type="button" id="btnZoomOut" aria-label="Alejar">−</button>
                <button type="button" id="btnZoomReset">100%</button>
                <button type="button" id="btnZoomIn" aria-label="Acercar">＋</button>
                <button type="button" id="btnCenter">Centrar mapa</button>
                <button type="button" id="btnExportSvg">Exportar imagen SVG</button>
                <button type="button" id="btnExportPdf">Exportar PDF</button>
                <button type="button" id="btnRefresh">Actualizar</button>
            </div>
        </section>

        <section class="topology-summary" id="topologySummary" aria-live="polite">
            <span>Conectando con el inventario…</span>
        </section>

        <section class="topology-workspace">
            <div class="map-viewport" id="mapViewport">
                <div class="map-stage" id="mapStage">
                    <svg class="map-links" id="mapLinks" aria-label="Enlaces entre equipos"></svg>
                    <div class="map-nodes" id="mapNodes"></div>
                    <div class="map-empty" id="mapEmpty" hidden>
                        <strong>No hay equipos para mostrar</strong>
                        <p>Registra un equipo o cambia los filtros de sede y piso.</p>
                        <button type="button" id="btnEmptyRegister">Registrar primer equipo</button>
                    </div>
                </div>
            </div>
            <aside class="map-legend">
                <strong>Leyenda</strong>
                <span><i class="legend-line fiber"></i> Fibra óptica</span>
                <span><i class="legend-line ethernet"></i> Ethernet / UTP</span>
                <span><i class="legend-dot linked"></i> Equipo con enlaces</span>
                <span><i class="legend-dot free"></i> Sin enlaces registrados</span>
                <p>Arrastra las tarjetas para ajustar el mapa. La posición se guarda en el inventario.</p>
            </aside>
        </section>
        <p class="topology-status" id="topologyStatus" role="status" aria-live="polite">Listo.</p>
    </main>

    <div class="registration-overlay" id="registrationOverlay" hidden>
        <section class="registration-dialog" role="dialog" aria-modal="true" aria-labelledby="registrationTitle">
            <header class="registration-dialog-head">
                <div>
                    <strong id="registrationTitle">Registrar equipos y preparar enlaces</strong>
                    <p>Se utiliza el mismo formulario y proceso de guardado de equipos_agregar.php.</p>
                </div>
                <button type="button" id="btnCloseRegistration" aria-label="Cerrar ventana">×</button>
            </header>
            <p class="registration-mode-note" id="registrationModeNote" hidden>Conexión entre equipos existentes: selecciona los puertos de origen y destino en «Equipos existentes» y guarda el enlace.</p>
            <iframe id="registrationFrame" title="Formulario de registro de equipos" src="about:blank"></iframe>
        </section>
    </div>
</body>
</html>
