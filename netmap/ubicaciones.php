<?php
require_once __DIR__ . '/api/conexion.php';
$conexion->close();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetMap · Ubicaciones</title>
    <link rel="stylesheet" href="assets/css/netmap_global.css">
    <style>
        body { margin:0; font-family:Inter,system-ui,sans-serif; }
        main { width:min(1180px,94%); margin:28px auto 60px; }
        .heading { display:flex; justify-content:space-between; align-items:end; gap:16px; flex-wrap:wrap; margin-bottom:22px; }
        .heading h1 { margin:0 0 6px; font-size:1.7rem; }
        .muted { color:var(--netmap-muted); margin:0; }
        .layout { display:grid; grid-template-columns: minmax(260px,.8fr) minmax(340px,1.2fr); gap:18px; align-items:start; }
        .panel { padding:20px; background:var(--netmap-surface); border:1px solid var(--netmap-border); border-radius:16px; }
        .panel h2 { margin:0 0 16px; font-size:1.12rem; }
        label { display:grid; gap:7px; margin:12px 0; color:var(--netmap-muted); font-size:.9rem; }
        input,select,textarea { width:100%; box-sizing:border-box; padding:11px 12px; border:1px solid var(--netmap-border); border-radius:9px; background:var(--netmap-bg); color:var(--netmap-text); font:inherit; }
        button { padding:9px 13px; border:1px solid #49617f; border-radius:9px; background:#1c3859; color:#fff; cursor:pointer; font:inherit; }
        button.danger { background:#632d36; border-color:#94515b; }
        .form-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .list { display:grid; gap:9px; margin-top:18px; }
        .item { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px; border:1px solid var(--netmap-border); border-radius:11px; background:var(--netmap-bg); }
        .item strong { display:block; }
        .item small { display:block; color:var(--netmap-muted); margin-top:4px; }
        .item-actions { display:flex; gap:6px; flex-wrap:wrap; }
        .item-actions button { padding:7px 9px; font-size:.82rem; }
        .empty { color:var(--netmap-muted); padding:12px 0; }
        #message { min-height:22px; margin:12px 0; color:var(--netmap-muted); }
        @media(max-width:760px){ .layout{grid-template-columns:1fr;} .item{align-items:flex-start; flex-direction:column;} }
    </style>
</head>
<body>
<header class="app-header"><strong>◈ NetMap</strong><span>Administración de ubicaciones</span></header>
<main>
    <div class="heading">
        <div><h1>Sedes y pisos</h1><p class="muted">Administra las ubicaciones sin afectar equipos que ya estén registrados.</p></div>
    </div>
    <div id="message" role="status" aria-live="polite">Cargando ubicaciones…</div>
    <div class="layout">
        <section class="panel">
            <h2 id="siteFormTitle">Agregar sede</h2>
            <form id="siteForm">
                <input type="hidden" name="id_sede">
                <label>Nombre de la sede<input name="nombre_sede" maxlength="100" required placeholder="Ej. Sede Central"></label>
                <label>Descripción (opcional)<textarea name="descripcion" rows="3" placeholder="Información de la ubicación"></textarea></label>
                <div class="form-actions"><button type="submit">Guardar sede</button><button type="button" id="cancelSiteEdit" hidden>Cancelar</button></div>
            </form>
            <div class="list" id="sitesList"></div>
        </section>
        <section class="panel">
            <h2 id="floorFormTitle">Agregar piso</h2>
            <form id="floorForm">
                <input type="hidden" name="id_piso">
                <label>Sede<select name="id_sede" required><option value="">Selecciona una sede…</option></select></label>
                <label>Nombre o número del piso<input name="numero_piso" maxlength="50" required placeholder="Ej. Planta baja, Piso 1"></label>
                <div class="form-actions"><button type="submit">Guardar piso</button><button type="button" id="cancelFloorEdit" hidden>Cancelar</button></div>
            </form>
            <div class="list" id="floorsList"></div>
        </section>
    </div>
</main>
<script>
(() => {
    const $ = (selector) => document.querySelector(selector);
    const siteForm = $('#siteForm');
    const floorForm = $('#floorForm');
    let sites = [];
    let floors = [];
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    function message(text, error = false) {
        $('#message').textContent = text;
        $('#message').style.color = error ? '#e07b83' : 'var(--netmap-muted)';
    }
    async function request(url, options = {}) {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok || data.estado !== 'ok') throw new Error(data.mensaje || 'No se pudo completar la operación.');
        return data;
    }
    async function load() {
        try {
            const data = await request('api/gestionar_ubicaciones.php');
            sites = data.sedes || [];
            floors = data.pisos || [];
            render();
            message('Ubicaciones actualizadas.');
        } catch (error) { message(error.message, true); }
    }
    function render() {
        const select = floorForm.elements.id_sede;
        const selected = select.value;
        select.innerHTML = '<option value="">Selecciona una sede…</option>' + sites.map(site =>
            '<option value="' + escapeHtml(site.id_sede) + '">' + escapeHtml(site.nombre_sede) + '</option>'
        ).join('');
        if (sites.some(site => String(site.id_sede) === selected)) select.value = selected;
        $('#sitesList').innerHTML = sites.length ? sites.map(site => `
            <article class="item">
                <div><strong>${escapeHtml(site.nombre_sede)}</strong><small>${Number(site.total_pisos)} pisos · ${Number(site.total_equipos)} equipos</small></div>
                <div class="item-actions">
                    <button type="button" data-edit-site="${site.id_sede}">Editar</button>
                    <button type="button" class="danger" data-delete-site="${site.id_sede}">Eliminar</button>
                </div>
            </article>`).join('') : '<p class="empty">Todavía no hay sedes registradas.</p>';
        $('#floorsList').innerHTML = floors.length ? floors.map(floor => `
            <article class="item">
                <div><strong>${escapeHtml(floor.nombre_sede)} · ${escapeHtml(floor.numero_piso)}</strong><small>${Number(floor.total_equipos)} equipos asociados</small></div>
                <div class="item-actions">
                    <button type="button" data-edit-floor="${floor.id_piso}">Editar</button>
                    <button type="button" class="danger" data-delete-floor="${floor.id_piso}">Eliminar</button>
                </div>
            </article>`).join('') : '<p class="empty">Todavía no hay pisos registrados.</p>';
    }
    async function save(form, action) {
        const body = new FormData(form);
        body.set('accion', action);
        try {
            const result = await request('api/gestionar_ubicaciones.php', { method:'POST', body });
            message(result.mensaje);
            resetForms();
            await load();
        } catch (error) { message(error.message, true); }
    }
    function resetForms() {
        siteForm.reset(); floorForm.reset();
        siteForm.elements.id_sede.value = '';
        floorForm.elements.id_piso.value = '';
        $('#siteFormTitle').textContent = 'Agregar sede';
        $('#floorFormTitle').textContent = 'Agregar piso';
        $('#cancelSiteEdit').hidden = true;
        $('#cancelFloorEdit').hidden = true;
    }
    siteForm.addEventListener('submit', event => {
        event.preventDefault();
        save(siteForm, siteForm.elements.id_sede.value ? 'editar_sede' : 'agregar_sede');
    });
    floorForm.addEventListener('submit', event => {
        event.preventDefault();
        save(floorForm, floorForm.elements.id_piso.value ? 'editar_piso' : 'agregar_piso');
    });
    $('#cancelSiteEdit').addEventListener('click', resetForms);
    $('#cancelFloorEdit').addEventListener('click', resetForms);
    $('#sitesList').addEventListener('click', async event => {
        const edit = event.target.closest('[data-edit-site]');
        const remove = event.target.closest('[data-delete-site]');
        if (edit) {
            const site = sites.find(item => Number(item.id_sede) === Number(edit.dataset.editSite));
            if (!site) return;
            siteForm.elements.id_sede.value = site.id_sede;
            siteForm.elements.nombre_sede.value = site.nombre_sede;
            siteForm.elements.descripcion.value = site.descripcion || '';
            $('#siteFormTitle').textContent = 'Editar sede';
            $('#cancelSiteEdit').hidden = false;
            siteForm.scrollIntoView({ behavior:'smooth', block:'center' });
        }
        if (remove) {
            if (!confirm('¿Eliminar esta sede? Solo se permitirá si no contiene pisos.')) return;
            const body = new FormData(); body.set('accion','eliminar_sede'); body.set('id_sede',remove.dataset.deleteSite);
            try { const result = await request('api/gestionar_ubicaciones.php',{method:'POST',body}); message(result.mensaje); await load(); }
            catch (error) { message(error.message,true); }
        }
    });
    $('#floorsList').addEventListener('click', async event => {
        const edit = event.target.closest('[data-edit-floor]');
        const remove = event.target.closest('[data-delete-floor]');
        if (edit) {
            const floor = floors.find(item => Number(item.id_piso) === Number(edit.dataset.editFloor));
            if (!floor) return;
            floorForm.elements.id_piso.value = floor.id_piso;
            floorForm.elements.id_sede.value = floor.id_sede;
            floorForm.elements.numero_piso.value = floor.numero_piso;
            $('#floorFormTitle').textContent = 'Editar piso';
            $('#cancelFloorEdit').hidden = false;
            floorForm.scrollIntoView({ behavior:'smooth', block:'center' });
        }
        if (remove) {
            if (!confirm('¿Eliminar este piso? Solo se permitirá si no tiene equipos asociados.')) return;
            const body = new FormData(); body.set('accion','eliminar_piso'); body.set('id_piso',remove.dataset.deleteFloor);
            try { const result = await request('api/gestionar_ubicaciones.php',{method:'POST',body}); message(result.mensaje); await load(); }
            catch (error) { message(error.message,true); }
        }
    });
    load();
})();
</script>
<script src="assets/js/netmap_global.js" defer></script>
</body>
</html>
