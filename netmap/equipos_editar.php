<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de equipo inválido.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetMap · Editar equipo</title>
    <style>
        :root{font-family:system-ui,-apple-system,Segoe UI,sans-serif;color:#172033;background:#f3f6fb}
        *{box-sizing:border-box}body{margin:0}.top{background:#14213d;color:white;padding:16px 22px;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap}
        a{color:inherit}.wrap{max-width:1000px;margin:24px auto;padding:0 16px}.panel{background:white;border:1px solid #dce4ef;border-radius:16px;padding:22px;box-shadow:0 8px 28px #15294b0d}
        h1{margin:0 0 6px;font-size:1.6rem}.muted{color:#64748b}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:20px}
        label{display:block;font-size:.9rem;font-weight:650}input,select,textarea{width:100%;margin-top:6px;border:1px solid #cbd5e1;border-radius:9px;padding:11px;font:inherit;background:white}textarea{min-height:90px;resize:vertical}.wide{grid-column:1/-1}
        .actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:22px}.btn{border:0;border-radius:9px;padding:11px 16px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-block}.primary{background:#2563eb;color:white}.secondary{background:#e2e8f0;color:#172033}.notice{margin-top:14px;padding:12px;border-radius:9px;display:none;white-space:pre-wrap}.notice.error{display:block;background:#fee2e2;color:#991b1b}.notice.ok{display:block;background:#dcfce7;color:#166534}
        .ports{margin-top:22px;border-top:1px solid #e2e8f0;padding-top:18px}.warning{background:#fff7ed;color:#9a3412;padding:12px;border-radius:10px;margin-top:12px;font-size:.9rem}
        @media(max-width:650px){.grid{grid-template-columns:1fr}.wide{grid-column:auto}.panel{padding:16px}}
    </style>
</head>
<body>
<header class="top"><strong>◈ NetMap · Editar equipo</strong><a href="equipos_lista.php">← Volver al listado</a></header>
<main class="wrap"><section class="panel">
    <h1 id="titulo">Editar equipo</h1><p class="muted">Modifica los datos generales. Los puertos y enlaces existentes se conservan sin cambios en esta pantalla.</p>
    <form id="formEditar">
        <input type="hidden" id="id_equipo" value="<?= (int)$id ?>">
        <div class="grid">
            <label>Nombre del equipo *<input id="nombre_equipo" required maxlength="150"></label>
            <label>Tipo de equipo *<select id="id_tipo_equipo" required></select></label>
            <label>Marca *<input id="marca" required maxlength="100"></label>
            <label>Modelo *<input id="modelo" required maxlength="100"></label>
            <label>Serial *<input id="serial" required maxlength="120"></label>
            <label>Dirección IP *<input id="direccion_ip" required maxlength="45" placeholder="IPv4 o IPv6"></label>
            <label>Sede<select id="id_sede" required></select></label>
            <label>Piso *<select id="id_piso" required></select></label>
            <label class="wide">Icono (ruta o URL)<input id="icono" maxlength="500" placeholder="assets/icons/... o https://..."></label>
            <label class="wide">Observaciones<textarea id="observaciones" maxlength="2000"></textarea></label>
        </div>
        <div class="warning">No se permite cambiar aquí la cantidad ni la estructura física de puertos. Así evitamos borrar o desordenar puertos que ya tengan enlaces.</div>
        <div class="ports"><strong>Puertos y conexiones</strong><p class="muted" id="resumenPuertos">Cargando resumen…</p></div>
        <div id="mensaje" class="notice" role="status" aria-live="polite"></div>
        <div class="actions"><a class="btn secondary" href="equipos_lista.php">Cancelar</a><button class="btn primary" id="guardar" type="submit">Guardar cambios</button></div>
    </form>
</section></main>
<script>
(() => {
    const id = Number(document.getElementById('id_equipo').value);
    const $ = (id) => document.getElementById(id);
    const msg = (text, kind='error') => { $('mensaje').className = `notice ${kind}`; $('mensaje').textContent = text; };
    const jsonFetch = async (url, options={}) => {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok || data.estado === 'error') throw new Error(data.mensaje || 'Error de comunicación con el servidor.');
        return data;
    };
    const fill = (select, rows, valueKey, labelFn, placeholder) => {
        select.replaceChildren(new Option(placeholder, ''));
        rows.forEach(row => select.add(new Option(labelFn(row), row[valueKey])));
    };
    const cargar = async () => {
        try {
            const [catalogos, equipo, puertos] = await Promise.all([
                jsonFetch('api/obtener_catalogos.php'),
                jsonFetch(`api/obtener_equipo_editar.php?id=${id}`),
                jsonFetch(`api/obtener_puertos_equipo.php?id_equipo=${id}`)
            ]);
            if (!equipo || !equipo.id_equipo) throw new Error('No se encontró el equipo solicitado.');
            fill($('id_tipo_equipo'), catalogos.tipos_equipo || [], 'id_tipo_equipo', r=>r.nombre_tipo, 'Selecciona un tipo');
            fill($('id_sede'), catalogos.sedes || [], 'id_sede', r=>r.nombre_sede, 'Selecciona una sede');
            $('id_tipo_equipo').value = equipo.id_tipo_equipo;
            $('id_sede').value = equipo.id_sede;
            const cargarPisos = () => {
                const pisos = (catalogos.pisos || []).filter(p => String(p.id_sede) === $('id_sede').value);
                fill($('id_piso'), pisos, 'id_piso', p=>`Piso ${p.numero_piso}`, 'Selecciona un piso');
            };
            $('id_sede').addEventListener('change', cargarPisos);
            cargarPisos(); $('id_piso').value = equipo.id_piso;
            ['nombre_equipo','marca','modelo','serial','direccion_ip','observaciones','icono'].forEach(k => { if ($(k)) $(k).value = equipo[k] || ''; });
            $('titulo').textContent = `Editar: ${equipo.nombre_equipo}`;
            const enlazados = puertos.filter(p => p.equipo_destino || p.id_enlace).length;
            $('resumenPuertos').textContent = `${puertos.length} puertos registrados · ${enlazados} con indicios de enlace. Esta edición no altera los puertos.`;
        } catch (error) { msg(error.message); $('guardar').disabled = true; }
    };
    $('formEditar').addEventListener('submit', async event => {
        event.preventDefault();
        if (!$('formEditar').reportValidity()) return;
        if (!confirm('¿Guardar los cambios de los datos generales de este equipo? Los puertos y enlaces no se modificarán.')) return;
        const payload = Object.fromEntries(['id_equipo','nombre_equipo','id_tipo_equipo','marca','modelo','serial','direccion_ip','id_sede','id_piso','icono','observaciones'].map(k=>[k, $(k).value.trim()]));
        $('guardar').disabled = true;
        try {
            const result = await jsonFetch('api/actualizar_equipo.php', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
            msg(result.mensaje || 'Cambios guardados.', 'ok');
            setTimeout(()=>location.href='equipos_lista.php', 700);
        } catch(error) { msg(error.message); }
        finally { $('guardar').disabled = false; }
    });
    cargar();
})();
</script>
</body>
</html>
