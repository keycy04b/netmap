<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetMap - Listado General de Equipos</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 25px;
        }
        .container {
            max-width: 1250px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e1e4e8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.3rem;
        }
        .botones-accion {
            display: flex;
            gap: 10px;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background-color: #2980b9; }
        .btn-verde { background-color: #2ecc71; }
        .btn-verde:hover { background-color: #27ae60; }
        .btn-gris { background-color: #7f8c8d; }
        .btn-gris:hover { background-color: #616a6b; }
        .btn-rojo { background-color: #e74c3c; color: white !important; }
        .btn-rojo:hover { background-color: #c0392b; }

        .filtros-container {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .filtro-group {
            flex: 1;
            min-width: 160px;
        }
        .filtro-group.buscador-principal {
            flex: 2;
            min-width: 240px;
        }
        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #475569;
            text-transform: uppercase;
        }
        .input-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.85rem;
            box-sizing: border-box;
            background-color: #fff;
            transition: all 0.2s;
        }
        .input-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }
       
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e1e4e8;
        }
        th {
            background-color: #fafbfc;
            color: #2c3e50;
            font-weight: 600;
            user-select: none;
        }
        th.sortable {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        th.sortable:hover {
            background-color: #edf2f7;
            color: #2563eb;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-enlazado { background-color: #e8f8f5; color: #1abc9c; }
        .badge-libre { background-color: #fef9e7; color: #f39c12; }
       
        .fila-equipo { cursor: pointer; }
        .fila-equipo:hover { background-color: #f8fafc; }
       
        .puertos-detalles {
    display: block;
    width: 100%;
    box-sizing: border-box;
    background-color: #f8fafc;
    padding: 20px 25px;
    border-bottom: 2px solid #cbd5e1;
    overflow-x: auto;
    min-height: 60px;
}

.tabla-puertos {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    background: white;
    margin-top: 10px;
    border: 1px solid #e2e8f0;
}

.tabla-puertos th,
.tabla-puertos td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid #e1e4e8;
    vertical-align: middle;
}

.tabla-puertos th {
    background-color: #edf2f7;
    color: #2c3e50;
}
        .tabla-puertos {
            width: 100%;
            font-size: 0.85rem;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-top: 8px;
        }
        .tabla-puertos th { background-color: #edf2f7; }

        .acciones-td {
            display: flex;
            gap: 6px;
        }
        .btn-tabla {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: 600;
        }
       
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .container { border: none; box-shadow: none; padding: 0; }
            table { font-size: 0.8rem; }
            .puertos-detalles { overflow: visible; padding: 10px; }
            .fila-equipo { break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header-flex">
            <h2>Inventario y Listado de Equipos de Red</h2>
            <div class="botones-accion no-print">
                <a href="equipos_agregar.php" class="btn btn-verde">+ Agregar Equipo</a>
                <button onclick="expandirContraerTodos()" id="btn-expandir-todos" class="btn btn-gris">Expandir todos</button>
                <button onclick="window.print()" class="btn">Imprimir Vista Actual</button>
                <a href="index.html" class="btn btn-gris">Inicio</a>
            </div>
        </div>

        <div class="filtros-container no-print">
            <div class="filtro-group buscador-principal">
                <label>Buscador General</label>
                <input type="text" id="buscador" class="input-control" placeholder="🔍 IP, serial, marca, modelo o nombre..." onkeyup="filtrarYMostrar()">
            </div>
            <div class="filtro-group">
                <label>Sede</label>
                <select id="filtro-sede" class="input-control" onchange="filtrarYMostrar()">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="filtro-group">
                <label>Piso</label>
                <select id="filtro-piso" class="input-control" onchange="filtrarYMostrar()">
                    <option value="">Todos los pisos</option>
                </select>
            </div>

            <div class="filtro-group">
                <label>Tipo de Equipo</label>
                <select id="filtro-tipo" class="input-control" onchange="filtrarYMostrar()">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="filtro-group">
                <label>Estado</label>
                <select id="filtro-estado" class="input-control" onchange="filtrarYMostrar()">
                    <option value="">Todos los estados</option>
                    <option value="enlazado">Enlazado</option>
                    <option value="disponible">Disponible</option>
                    <option value="ocupado">Ocupado (sin enlace)</option>
                    <option value="reservado">Reservado</option>
                    <option value="danado">Dañado</option>
                </select>
            </div>
            <div class="filtro-group">
                <label>Fecha de creación desde</label>
                <input type="date" id="fecha-creacion-desde" class="input-control" onchange="filtrarYMostrar()">
            </div>
            <div class="filtro-group">
                <label>Fecha de creación hasta</label>
                <input type="date" id="fecha-creacion-hasta" class="input-control" onchange="filtrarYMostrar()">
            </div>
            <div class="filtro-group">
                <label>Fecha de modificación desde</label>
                <input type="date" id="fecha-modificacion-desde" class="input-control" onchange="filtrarYMostrar()">
            </div>
            <div class="filtro-group">
                <label>Fecha de modificación hasta</label>
                <input type="date" id="fecha-modificacion-hasta" class="input-control" onchange="filtrarYMostrar()">
            </div>
            <div class="filtro-group">
                <label>Ordenar por fecha</label>
                <select id="orden-fecha" class="input-control" onchange="aplicarOrdenFecha()">
                    <option value="id-desc">Orden original (más recientes primero)</option>
                    <option value="creacion-desc">Creación: más recientes primero</option>
                    <option value="creacion-asc">Creación: más antiguas primero</option>
                    <option value="modificacion-desc">Modificación: más recientes primero</option>
                    <option value="modificacion-asc">Modificación: más antiguas primero</option>
                </select>
            </div>
            <div class="filtro-group" style="align-self:end;">
                <button type="button" class="btn btn-gris" onclick="limpiarFiltrosFecha()">Limpiar fechas</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="sortable" onclick="ordenarPor('sede')">Ubicación (Sede / Piso) ↕</th>
                    <th class="sortable" onclick="ordenarPor('tipo')">Tipo / Nombre del Equipo ↕</th>
                    <th class="sortable" onclick="ordenarPor('marca')">Marca y Modelo / Serial ↕</th>
                    <th class="sortable" onclick="ordenarPor('ip')">Dirección IP ↕</th>
                    <th class="sortable" onclick="ordenarPor('puertos')">Puertos ↕</th>
                    <th>Observaciones</th>
                    <th>Estado</th>
                    <th>Fechas del equipo</th>
                    <th class="no-print">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-cuerpo">
                <tr>
                    <td colspan="9" style="text-align: center; color: #7f8c8d;">Cargando registros...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        let listaEquiposGlobal = []; 
        let ordenActual = { columna: 'id', asc: false };
        let ordenFechaSeleccionado = 'id-desc';

        async function init() {
            await cargarCatalogosFiltros();
            await cargarListadoEquipos();
        }

        async function cargarCatalogosFiltros() {
            try {
                let res = await fetch("api/obtener_catalogos.php");
                let catalogos = await res.json();

                let selectSede = document.getElementById("filtro-sede");
                (catalogos.sedes || []).forEach(s => {
                    selectSede.innerHTML += `<option value="${s.nombre_sede.toLowerCase()}">${s.nombre_sede}</option>`;
            
                }) 

                let selectPiso = document.getElementById("filtro-piso");
                (catalogos.pisos || []).forEach(p => {
                    const numeroPiso = String(p.numero_piso ?? "");
                    selectPiso.innerHTML += `<option value="${numeroPiso.toLowerCase()}">${numeroPiso}</option>`;
                });

                let selectTipo = document.getElementById("filtro-tipo");
                (catalogos.tipos_equipo || []).forEach(t => {
                    selectTipo.innerHTML += `<option value="${t.nombre_tipo.toLowerCase()}">${t.nombre_tipo}</option>`;
                });
            } catch (err) {
                console.error("Error al cargar catálogos para filtros:", err);
            }
        }

        async function cargarListadoEquipos() {
            try {
                let respuesta = await fetch("api/obtener_equipos_detallados.php");
                listaEquiposGlobal = await respuesta.json();
                filtrarYMostrar();
            } catch (error) {
                console.error("Error al cargar el listado:", error);
                document.getElementById("tabla-cuerpo").innerHTML = `<tr><td colspan="9" style="text-align: center; color: #e74c3c;">Error al conectar con el servidor.</td></tr>`;
            }
        }

        function filtrarYMostrar() {
            let texto = document.getElementById("buscador").value.toLowerCase().trim();
            let sedeSel = document.getElementById("filtro-sede").value;
            let pisoSel = document.getElementById("filtro-piso").value;
            let tipoSel = document.getElementById("filtro-tipo").value;
            let estadoSel = document.getElementById("filtro-estado").value;
            const creacionDesde = document.getElementById("fecha-creacion-desde").value;
            const creacionHasta = document.getElementById("fecha-creacion-hasta").value;
            const modificacionDesde = document.getElementById("fecha-modificacion-desde").value;
            const modificacionHasta = document.getElementById("fecha-modificacion-hasta").value;
            
            let filtrados = listaEquiposGlobal.filter(eq => {
                let matchTexto = !texto || (
                    (eq.nombre_equipo && eq.nombre_equipo.toLowerCase().includes(texto)) ||
                    (eq.nombre_tipo && eq.nombre_tipo.toLowerCase().includes(texto)) ||
                    (eq.marca && eq.marca.toLowerCase().includes(texto)) ||
                    (eq.modelo && eq.modelo.toLowerCase().includes(texto)) ||
                    (eq.serial && eq.serial.toLowerCase().includes(texto)) ||
                    (eq.direccion_ip && eq.direccion_ip.toLowerCase().includes(texto)) ||
                    (eq.observaciones && String(eq.observaciones).toLowerCase().includes(texto))
                );

                let matchSede = !sedeSel || (eq.nombre_sede && String(eq.nombre_sede).toLowerCase() === sedeSel);
                let matchPiso = !pisoSel || (eq.numero_piso !== null && eq.numero_piso !== undefined
                    && String(eq.numero_piso).toLowerCase() === pisoSel);
                let matchTipo = !tipoSel || (eq.nombre_tipo && String(eq.nombre_tipo).toLowerCase() === tipoSel);

                const estadosEquipo = {
                    enlazado: Number(eq.puertos_enlazados || 0),
                    disponible: Number(eq.puertos_disponibles || 0),
                    ocupado: Number(eq.puertos_ocupados || 0),
                    reservado: Number(eq.puertos_reservados || 0),
                    danado: Number(eq.puertos_danados || 0)
                };
                const matchEstado = !estadoSel || estadosEquipo[estadoSel] > 0;

                const fechaCreacion = obtenerFechaComparable(eq.fecha_creacion);
                const fechaModificacion = obtenerFechaComparable(eq.fecha_actualizacion);
                const matchFechaCreacion = (!creacionDesde || (fechaCreacion && fechaCreacion >= creacionDesde))
                    && (!creacionHasta || (fechaCreacion && fechaCreacion <= creacionHasta));
                const matchFechaModificacion = (!modificacionDesde || (fechaModificacion && fechaModificacion >= modificacionDesde))
                    && (!modificacionHasta || (fechaModificacion && fechaModificacion <= modificacionHasta));

                return matchTexto && matchSede && matchPiso && matchTipo && matchEstado
                    && matchFechaCreacion && matchFechaModificacion;
            });

            filtrados.sort((a, b) => {
                if (ordenFechaSeleccionado !== 'id-desc') {
                    const [campoFecha, direccion] = ordenFechaSeleccionado.split('-');
                    const campo = campoFecha === 'creacion' ? 'fecha_creacion' : 'fecha_actualizacion';
                    const fechaA = obtenerFechaComparable(a[campo]) || '';
                    const fechaB = obtenerFechaComparable(b[campo]) || '';
                    const comparacion = fechaA.localeCompare(fechaB);
                    return direccion === 'asc' ? comparacion : -comparacion;
                }

                let valA = obtenerValorColumna(a, ordenActual.columna);
                let valB = obtenerValorColumna(b, ordenActual.columna);
                if (valA < valB) return ordenActual.asc ? -1 : 1;
                if (valA > valB) return ordenActual.asc ? 1 : -1;
                return 0;
            });

            renderizarTabla(filtrados);
        }

        function obtenerValorColumna(eq, col) {
            switch(col) {
                case 'sede': return (eq.nombre_sede + " " + eq.numero_piso).toLowerCase();
                case 'tipo': return (eq.nombre_tipo + " " + (eq.nombre_equipo || '')).toLowerCase();
                case 'marca': return (eq.marca + " " + eq.modelo).toLowerCase();
                case 'ip': return eq.direccion_ip || '';
                case 'puertos': return parseInt(eq.numero_puertos) || 0;
                case 'fechas': return String(eq.fecha_creacion || '') + ' ' + String(eq.fecha_actualizacion || '');
                default: return eq.id_equipo;
            }
        }

        function ordenarPor(columna) {
            if (ordenActual.columna === columna) {
                ordenActual.asc = !ordenActual.asc;
            } else {
                ordenActual.columna = columna;
                ordenActual.asc = true;
            }
            filtrarYMostrar();
        }

        function renderizarTabla(equipos) {
            let tbody = document.getElementById("tabla-cuerpo");
            tbody.innerHTML = "";

            if (equipos.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: #7f8c8d;">No se encontraron registros que coincidan con los filtros seleccionados.</td></tr>`;
                return;
            }

            equipos.forEach(eq => {
                const resumenEstados = [
                    ['Enlazados', eq.puertos_enlazados],
                    ['Disponibles', eq.puertos_disponibles],
                    ['Ocupados', eq.puertos_ocupados],
                    ['Reservados', eq.puertos_reservados],
                    ['Dañados', eq.puertos_danados]
                ].filter(([, cantidad]) => Number(cantidad || 0) > 0);
                let estadoHtml = resumenEstados.length
                    ? resumenEstados.map(([nombre, cantidad]) =>
                        `<span class="badge badge-estado">${nombre}: ${cantidad}</span>`
                      ).join(' ')
                    : `<span class="badge badge-libre">Sin puertos registrados</span>`;

                let nombreDisplay = eq.nombre_equipo ? eq.nombre_equipo : 'Sin nombre asignado';

                tbody.innerHTML += `
                    <tr class="fila-equipo" onclick="togglePuertos(${eq.id_equipo}, event)">
                        <td>
                            <strong style="font-size: 1rem; color: #2c3e50;">${eq.nombre_sede}</strong><br>
                            <span class="badge" style="background:#e2e8f0; color:#334155;"> ${eq.numero_piso}</span>
                        </td>
                        <td>
                            <strong style="font-size: 1rem; color: #2563eb;">${eq.nombre_tipo}</strong><br>
                            <span style="font-size: 0.8rem; color: #64748b; font-style: ${eq.nombre_equipo ? 'normal' : 'italic'};">${nombreDisplay}</span>
                        </td>
                        <td>
                            ${eq.marca || 'N/D'} ${eq.modelo || ''}<br>
                            <span style="font-size: 0.75rem; color: #64748b;">Serial: ${eq.serial || 'N/D'}</span>
                        </td>
                        <td><span style="font-family: monospace; font-weight: 600;">${eq.direccion_ip || 'N/D'}</span></td>
                        <td>${eq.numero_puertos || 0}</td>
                        <td>${eq.observaciones || '<span style="color:#cbd5e1; font-style:italic;">Ninguna</span>'}</td>
                        <td>${estadoHtml}</td>
                        <td>
                            <div style="font-size:0.8rem; line-height:1.6;">
                                <div><strong>Creación:</strong> ${formatearFecha(eq.fecha_creacion)}</div>
                                <div><strong>Modificación:</strong> ${formatearFecha(eq.fecha_actualizacion)}</div>
                            </div>
                        </td>
                        <td class="no-print" onclick="event.stopPropagation()">
                            <div class="acciones-td">
                                <a href="equipos_editar_puertos.php?id=${eq.id_equipo}" class="btn-tabla btn">Editar</a>
                                <button onclick="eliminarEquipo(${eq.id_equipo})" class="btn-tabla btn-rojo" style="border:none; cursor:pointer;">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                    <tr id="desplegable-${eq.id_equipo}" style="display: none;">
                        <td colspan="9" style="padding: 0;">
                            <div class="puertos-detalles" id="contenido-puertos-${eq.id_equipo}">
                                <p style="margin:0; font-size: 0.85rem; color: #64748b;">Cargando puertos y auditoría...</p>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function obtenerFechaComparable(valor) {
            if (!valor) return '';
            const texto = String(valor).trim();
            if (!texto || texto.startsWith('0000-00-00')) return '';
            // Las fechas SQL usan YYYY-MM-DD HH:MM:SS; ISO YYYY-MM-DD permite comparar rangos.
            return texto.replace(' ', 'T').slice(0, 10);
        }

        function aplicarOrdenFecha() {
            ordenFechaSeleccionado = document.getElementById('orden-fecha').value;
            filtrarYMostrar();
        }

        function limpiarFiltrosFecha() {
            ['fecha-creacion-desde', 'fecha-creacion-hasta', 'fecha-modificacion-desde', 'fecha-modificacion-hasta']
                .forEach(id => { document.getElementById(id).value = ''; });
            filtrarYMostrar();
        }

        function formatearFecha(valor) {
            if (!valor) return "No disponible";
            const fecha = String(valor).trim();
            if (!fecha || fecha === "0000-00-00 00:00:00" || fecha === "0000-00-00") return "No disponible";
            return fecha;
        }

        async function togglePuertos(idEquipo, event) {
            if (event && event.target && event.target.closest(".acciones-td")) return;

            const filaDesplegable = document.getElementById(`desplegable-${idEquipo}`);
            const contenedor = document.getElementById(`contenido-puertos-${idEquipo}`);
            if (!filaDesplegable || !contenedor) return;

            if (filaDesplegable.style.display !== "none") {
                filaDesplegable.style.display = "none";
                return;
            }

            // Cada equipo se abre de forma independiente; no cerramos los demás.
            filaDesplegable.style.display = "table-row";
            if (filaDesplegable.dataset.cargado === "true") return;

            contenedor.innerHTML = `<p style="margin:0; font-size:0.85rem; color:#64748b;">Cargando puertos y fechas...</p>`;
            const equipoActual = listaEquiposGlobal.find(e => e.id_equipo == idEquipo);
            const fechasHtml = equipoActual ? `
                <div style="font-size:0.75rem; color:#64748b; margin-bottom:10px; display:flex; flex-wrap:wrap; gap:8px 20px;">
                    <span>📅 <strong>Registrado:</strong> ${formatearFecha(equipoActual.fecha_creacion)}</span>
                    <span>🔄 <strong>Última modificación:</strong> ${formatearFecha(equipoActual.fecha_actualizacion)}</span>
                </div>` : "";

            try {
                const res = await fetch(`api/obtener_puertos_equipo.php?id_equipo=${encodeURIComponent(idEquipo)}`);
                if (!res.ok) throw new Error(`Error HTTP ${res.status}`);
                const puertos = await res.json();

                if (!Array.isArray(puertos) || puertos.length === 0) {
                    contenedor.innerHTML = fechasHtml + `<p style="margin:0; color:#e74c3c;">Este equipo no tiene puertos registrados.</p>`;
                    filaDesplegable.dataset.cargado = "true";
                    return;
                }

                let htmlPuertos = `${fechasHtml}
                    <strong style="font-size:0.9rem; color:#2c3e50; display:block; margin-bottom:6px;">Puertos y Enlaces Físicos:</strong>
                    <table class="tabla-puertos"><thead><tr>
                        <th>N° Puerto / Tipo</th><th>Estado</th>
                        <th>Conectado a (Equipo Destino)</th><th>Puerto Destino</th>
                        <th>Creación del puerto</th><th>Modificación del puerto</th>
                    </tr></thead><tbody>`;

                puertos.forEach(p => {
                    const destinoInfo = p.equipo_destino
                        ? `<span style="color:#27ae60; font-weight:600;">${p.equipo_destino}</span>`
                        : `<span style="color:#94a3b8; font-style:italic;">Sin enlace (Libre)</span>`;
                    const puertoDestinoInfo = p.puerto_destino ? `Puerto #${p.puerto_destino}` : "-";
                    htmlPuertos += `<tr>
                        <td><strong>Puerto #${p.numero_puerto}</strong> <span class="badge" style="background:#e2e8f0; font-size:0.7rem;">${p.tipo_puerto || "N/D"}</span></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#334155;">${p.estado || "N/D"}</span></td>
                        <td>${destinoInfo}</td><td>${puertoDestinoInfo}</td>
                        <td>${formatearFecha(p.fecha_creacion) === "No disponible" ? "Pendiente de implementar" : formatearFecha(p.fecha_creacion)}</td>
                        <td>${formatearFecha(p.fecha_actualizacion) === "No disponible" ? "Pendiente de implementar" : formatearFecha(p.fecha_actualizacion)}</td>
                    </tr>`;
                });

                contenedor.innerHTML = htmlPuertos + `</tbody></table>`;
                filaDesplegable.dataset.cargado = "true";
            } catch (err) {
                console.error("Error al cargar los puertos:", err);
                contenedor.innerHTML = fechasHtml + `<p style="color:red; margin:0;">Error al cargar los puertos del servidor. Cierra y vuelve a abrir este equipo para intentarlo de nuevo.</p>`;
            }
        }

        async function expandirContraerTodos() {
            const boton = document.getElementById("btn-expandir-todos");
            const filas = Array.from(document.querySelectorAll("tr[id^='desplegable-']"));
            const hayCerrados = filas.some(fila => fila.style.display === "none");

            if (hayCerrados) {
                boton.disabled = true;
                boton.textContent = "Expandiendo...";
                try {
                    for (const fila of filas) {
                        if (fila.style.display === "none") {
                            await togglePuertos(fila.id.replace("desplegable-", ""));
                        }
                    }
                } finally {
                    boton.disabled = false;
                    boton.textContent = "Contraer todos";
                }
            } else {
                filas.forEach(fila => { fila.style.display = "none"; });
                boton.textContent = "Expandir todos";
            }
        }

        async function eliminarEquipo(id) {
            if (confirm("¿Estás seguro de eliminar este equipo? Se borrarán también sus puertos y conexiones asociadas.")) {
                try {
                    let res = await fetch("api/eliminar_equipo.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "Accept": "application/json" },
                        body: JSON.stringify({ id_equipo: Number(id) })
                    });
                    let resultado = await res.json();
                    if (!res.ok || resultado.estado !== "ok") {
                        throw new Error(resultado.mensaje || "No se pudo eliminar el equipo.");
                    }
                    if (resultado.estado === "ok") {
                        alert(resultado.mensaje);
                        cargarListadoEquipos();
                    } else {
                        alert("Error: " + resultado.mensaje);
                    }
                } catch (err) {
                    alert("No se pudo conectar con el servidor.");
                }
            }
        }

        init();
    </script>
</body>
</html>


