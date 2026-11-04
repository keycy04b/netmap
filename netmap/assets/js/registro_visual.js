(() => {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
    const states = ['available', 'occupied', 'reserved', 'damaged'];
    const stateLabels = {
        available: 'Disponible',
        occupied: 'Ocupado',
        reserved: 'Reservado',
        damaged: 'Dañado'
    };
    const stateDb = {
        available: 'DISPONIBLE',
        occupied: 'NO_DISPONIBLE',
        reserved: 'RESERVADO',
        damaged: 'DANADO'
    };

    let catalogos = { tipos_equipo: [], sedes: [], pisos: [] };
    let presetsPorTipo = {};
    let nextId = 1;
    let dirty = false;
    let joint = false;
    let existingEquipment = [];
    let selectedExisting = null;
    const selectedExistingList = new Map();
    let globalMode = 'link';
    let pendingSource = null;
    let pendingExistingSource = null;
    let links = [];
    let toastTimer = null;

    const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));

    function toast(message) {
        const box = $('#toast');
        box.textContent = message;
        box.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => box.classList.remove('show'), 4200);
    }

    function dirtyMark(message = 'Hay cambios sin guardar.') {
        dirty = true;
        $('#formStatus').textContent = message;
    }

    function makeGroup(medio = 'UTP', alcance = 'LAN', cantidad = 24, secciones = 2) {
        return {
            medio, alcance, cantidad, secciones,
            id: `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
            states: {}
        };
    }

    function suggestedPortCounts(card) {
        const typeName = $('[name="id_tipo_equipo"]', card)?.selectedOptions[0]?.textContent || '';
        const typeId = $('[name="id_tipo_equipo"]', card)?.value;
        const configured = presetsPorTipo[typeId] || [];
        if (configured.length) {
            return configured.map(item => ({
                cantidad: Number(item.cantidad),
                secciones: Number(item.secciones),
                alcance: item.alcance,
                medio: item.medio
            }));
        }
        let counts = [8, 16, 24, 48];
        if (/computador|computadora|pc|laptop|portátil|estación/i.test(typeName)) counts = [1, 2, 3, 4];
        else if (/router|enrutador/i.test(typeName)) counts = [4, 8, 12, 16];
        else if (/servidor/i.test(typeName)) counts = [2, 4, 6, 8];
        const current = getGroups(card)[0] || {alcance:'LAN',medio:'UTP'};
        return counts.map(cantidad => ({
            cantidad,
            secciones: cantidad < 12 ? 1 : Math.ceil(cantidad / 12),
            alcance: current.alcance,
            medio: current.medio
        }));
    }

    function titleCaseFirst(value) {
        const text = String(value ?? '').trim();
        if (!text) return '';
        return text.charAt(0).toLocaleUpperCase('es') + text.slice(1);
    }

    function normalizeCardText(card, final = false) {
        const serial = $('[name="serial"]', card);
        if (serial) serial.value = serial.value.toLocaleUpperCase('es');
        if (final) {
            ['nombre_equipo', 'marca', 'modelo'].forEach(name => {
                const input = $(`[name="${name}"]`, card);
                if (input) input.value = titleCaseFirst(input.value);
            });
        }
    }

    function options(items, valueKey, labelKey) {
        return items.map(item =>
            `<option value="${esc(item[valueKey])}">${esc(item[labelKey])}</option>`
        ).join('');
    }

    async function init() {
        try {
            const response = await fetch('api/obtener_catalogos.php', { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            catalogos = await response.json();
            if (!Array.isArray(catalogos.tipos_equipo)) throw new Error(catalogos.mensaje || 'Respuesta de catálogos inválida.');
            try {
                const presetResponse = await fetch('api/gestionar_presets_puertos.php', {headers:{Accept:'application/json'}});
                if (presetResponse.ok) {
                    const presetData = await presetResponse.json();
                    (presetData.presets || []).filter(item => Number(item.activo) === 1).forEach(item => {
                        (presetsPorTipo[item.id_tipo_equipo] ||= []).push(item);
                    });
                }
            } catch (presetError) {
                console.warn('Se usarán sugerencias predeterminadas: ', presetError);
            }
            $('#existingSite').insertAdjacentHTML('beforeend', options(catalogos.sedes || [], 'id_sede', 'nombre_sede'));
            $('#existingFloor').insertAdjacentHTML('beforeend', options(catalogos.pisos || [], 'id_piso', 'numero_piso'));
            addCard();
            $('#formStatus').textContent = 'Listo para registrar.';
        } catch (error) {
            console.error(error);
            toast(`No se pudieron cargar los catálogos: ${error.message}`);
            $('#formStatus').textContent = 'Error al cargar catálogos.';
        }
    }

    function addCard() {
        const id = nextId++;
        const card = document.createElement('article');
        card.className = 'device-card';
        card.dataset.id = id;
        card.dataset.groups = JSON.stringify([makeGroup()]);
        card.dataset.icon = '';
        card.dataset.iconMode = 'default';
        card.dataset.mode = globalMode;
        card.innerHTML = `
            <div class="cardhead">
                <span>Equipo nuevo #${id}</span>
                <button type="button" data-action="remove-card" ${id === 1 ? 'hidden' : ''}>× Quitar equipo</button>
            </div>
            <div class="cardbody">
                <div class="fields">
                    <div class="field">
                        <label>Sede *</label>
                        <select class="sede" required><option value="">Seleccione…</option>${options(catalogos.sedes || [], 'id_sede', 'nombre_sede')}</select>
                    </div>
                    <div class="field">
                        <label>Piso *</label>
                        <select class="piso" required disabled><option value="">Seleccione…</option></select>
                    </div>
                    <div class="field">
                        <label>Tipo de equipo *</label>
                        <select name="id_tipo_equipo" required><option value="">Seleccione…</option>${options(catalogos.tipos_equipo || [], 'id_tipo_equipo', 'nombre_tipo')}</select>
                    </div>
                    <div class="field">
                        <label>Nombre (opcional)</label>
                        <input name="nombre_equipo" placeholder="Ej. Switch-Core">
                    </div>
                    <div class="field">
                        <label>Marca *</label>
                        <input name="marca" required placeholder="Cisco">
                    </div>
                    <div class="field">
                        <label>Modelo *</label>
                        <input name="modelo" required placeholder="Catalyst 2960">
                    </div>
                    <div class="field">
                        <label>Serial *</label>
                        <input name="serial" required>
                    </div>
                    <div class="field">
                        <label>Dirección IP *</label>
                        <input name="direccion_ip" required placeholder="192.168.1.10">
                    </div>
                    <div class="field w4">
                        <label>Observaciones</label>
                        <input name="observaciones" placeholder="Notas del equipo">
                    </div>

                    <div class="identity-summary">
                        <strong>Ubicación:</strong> <span class="identity-location">Sede y piso por seleccionar</span>
                        <span>·</span><strong>Equipo:</strong> <span class="identity-name">Nombre automático por tipo</span>
                        <span>·</span><strong>Alcance/medio:</strong> <span class="identity-media">LAN · Ethernet</span>
                    </div>

                    <div class="icon-editor">
                        <div class="icon-preview"><span class="icon-placeholder">◈</span></div>
                        <div class="icon-tools">
                            <strong>Icono del equipo</strong>
                            <div class="icon-actions">
                                
                                <button type="button" data-action="use-suggestion">Predeterminado</button>
                                <button type="button" data-action="upload-icon">Subir archivo</button>
                                
                            </div>
                            <div class="icon-suggestions" aria-label="Iconos sugeridos">
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/switch.svg" title="Switch"><img src="assets/icons/switch.svg" alt="Switch" onerror="this.style.display='none'"></button>
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/computer.svg" title="Computadora"><img src="assets/icons/computer.svg" alt="Computadora" onerror="this.style.display='none'"></button>
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/router.svg" title="Router"><img src="assets/icons/router.svg" alt="Router" onerror="this.style.display='none'"></button>
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/server.svg" title="Servidor"><img src="assets/icons/server.svg" alt="Servidor" onerror="this.style.display='none'"></button>
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/network.svg" title="Transporte"><img src="assets/icons/network.svg" alt="Transporte" onerror="this.style.display='none'"></button>
                                <button class="icon-suggestion" type="button" data-icon="assets/icons/phone.svg" title="Teléfono IP"><img src="assets/icons/phone.svg" alt="Teléfono IP" onerror="this.style.display='none'"></button>
                            </div>
                            <input class="icon-url" type="url" placeholder="Pega una URL web o una ruta local (assets/icons/mi-icono.png)" aria-label="URL o ruta del icono">
                            <div class="icon-actions">
                            
                                
                                <input type="file" class="icon-file" accept="image/png,image/jpeg,image/webp,image/gif" hidden>
                            </div>
                            <div class="icon-drop" tabindex="0">Arrastra y suelta una imagen aquí, o cópiala y pégala en esta zona.</div>
                            <small class="icon-status muted">Se aceptan PNG, JPG, WEBP o GIF. Máximo 3 MB al subir.</small>
                        </div>
                    </div>
                </div>

                <section class="preview">
                    <div class="previewhead">
                        <span>Vista previa del equipo</span>
                        <span class="identity-short">LAN · Ethernet</span>
                        <span class="total">24 puertos</span>
                    </div>
                    <div class="rack"><div class="groups"></div></div>
                    <div class="legend">
                        <span><i style="background:#22c55e"></i>Disponible</span>
                        <span><i style="background:#2563eb"></i>Ocupado / enlazado</span>
                        <span><i style="background:#f59e0b"></i>Reservado</span>
                        <span><i style="background:#dc2626"></i>Dañado</span>
                    </div>
                    <div class="previewactions">
                        <button type="button" data-action="link-mode">↔ Enlazar</button>
                        <button type="button" data-action="state-mode">● Cambiar estado</button>
                    </div>
                    <div class="group-add-row"><button type="button" data-action="add-group">＋ Añadir otro grupo de puertos</button></div>
                    <div class="group-settings"></div>
                </section>
            </div>`;
        $('#contenedor-tarjetas').append(card);
        bindIconDrop(card);
        renderCard(card);
        updateIdentity(card);
        dirtyMark();
    }

    function getGroups(card) {
        try { return JSON.parse(card.dataset.groups || '[]'); }
        catch { return []; }
    }

    function setGroups(card, groups) {
        card.dataset.groups = JSON.stringify(groups);
    }

    function portState(group, portNumber) {
        return group.states?.[portNumber] || 'available';
    }

    function renderPortRows(group, groupIndex) {
        const count = Number(group.cantidad);
        const sectionCount = Number(group.secciones);
        const base = Math.floor(count / sectionCount);
        const extra = count % sectionCount;
        let portNumber = 1;
        let output = '';

        for (let sectionIndex = 0; sectionIndex < sectionCount; sectionIndex++) {
            const amount = base + (sectionIndex < extra ? 1 : 0);
            if (amount <= 0) continue;
            const rowOne = Math.ceil(amount / 2);
            const rows = [Array.from({ length: rowOne }, (_, i) => i + 1)];
            if (amount - rowOne > 0) rows.push(Array.from({ length: amount - rowOne }, (_, i) => rowOne + i + 1));
            output += `<div class="section"><div class="section-rows">`;
            for (const row of rows) {
                output += `<div class="prow">`;
                for (const localPort of row) {
                    const current = portNumber++;
                    const state = portState(group, current);
                    const linked = links.some(link =>
                [link.source, link.destination].some(endpoint =>
                    endpoint.kind === 'new' &&
                    endpoint.cardId === Number(groupIndex.cardId) &&
                    endpoint.group === groupIndex.index &&
                    endpoint.port === current
                )
            );
                    output += `<button type="button" class="port ${esc(state)}${linked ? ' linked' : ''}"
                        data-action="port" data-group="${groupIndex.index}" data-port="${current}"
                        title="Puerto ${current}: ${stateLabels[state]}"><span>${current}</span><i class="dot"></i></button>`;
                }
                output += `</div>`;
            }
            output += `</div></div>`;
        }
        return output;
    }

    function renderCard(card) {
        const groups = getGroups(card);
        const total = groups.reduce((sum, group) => sum + Number(group.cantidad || 0), 0);
        $('.total', card).textContent = `${total} puertos`;
        const visualGroups = groups.map((group, groupIndex) => {
            const mediumClass = group.medio === 'FIBRA' ? 'medium-fiber' : 'medium-utp';
            const mediumLabel = group.medio === 'FIBRA' ? 'Fibra óptica' : 'Ethernet / UTP';
            const ports = renderPortRows(group, { cardId: card.dataset.id, index: groupIndex });
            return `<section class="group ${mediumClass}">
                <div class="gtitle"><span>${esc(group.alcance)} · ${mediumLabel}</span><small>${Number(group.cantidad)} puertos</small></div>
                <div class="sections">${ports}</div>
            </section>`;
        }).join('');
        $('.groups', card).innerHTML = visualGroups;
        const presets = suggestedPortCounts(card);
        $('.group-settings', card).innerHTML = `<div class="port-presets"><strong>Sugerencias de puertos para este equipo:</strong><div>${presets.map(preset => `<button type="button" data-action="preset-ports" data-count="${preset.cantidad}" data-sections="${preset.secciones}" data-reach="${esc(preset.alcance)}" data-medium="${esc(preset.medio)}" title="${esc(preset.alcance)} · ${esc(preset.medio)} · ${preset.secciones} sección(es)">${preset.cantidad} · ${preset.medio === 'FIBRA' ? 'Fibra' : 'UTP'} · ${preset.alcance}</button>`).join('')}</div></div>` + groups.map((group, index) => `
            <div class="group-settings-item" data-settings-group="${index}">
                <div class="group-settings-head">
                    <strong>Grupo ${index + 1} · ${group.medio === 'FIBRA' ? 'Fibra óptica' : 'Ethernet / UTP'}</strong>
                    <button type="button" class="group-remove" data-action="remove-group" data-group="${index}" title="Eliminar este grupo" aria-label="Eliminar grupo ${index + 1}">×</button>
                </div>
                <div class="group-settings-grid">
                    <div class="field"><label>Alcance</label><select data-g="${index}" data-f="alcance"><option value="LAN" ${group.alcance === 'LAN' ? 'selected' : ''}>LAN</option><option value="WAN" ${group.alcance === 'WAN' ? 'selected' : ''}>WAN</option></select></div>
                    <div class="field"><label>Medio físico</label><select data-g="${index}" data-f="medio"><option value="UTP" ${group.medio === 'UTP' ? 'selected' : ''}>Ethernet / UTP</option><option value="FIBRA" ${group.medio === 'FIBRA' ? 'selected' : ''}>Fibra óptica</option></select></div>
                    <div class="field"><label>Cantidad de puertos</label><input type="number" min="1" max="128" data-g="${index}" data-f="cantidad" value="${Number(group.cantidad)}"></div>
                    <div class="field"><label>Secciones físicas</label><input type="number" min="1" max="${Number(group.cantidad)}" data-g="${index}" data-f="secciones" value="${Number(group.secciones)}"></div>
                </div>
            </div>`).join('');
        updateIdentity(card);
        drawWires();
    }

    function updateIdentity(card) {
        const site = $('.sede', card);
        const floor = $('.piso', card);
        const type = $('[name="id_tipo_equipo"]', card);
        const name = $('[name="nombre_equipo"]', card);
        const siteName = site?.selectedOptions[0]?.textContent || 'Sede por seleccionar';
        const floorName = floor?.selectedOptions[0]?.textContent || 'piso por seleccionar';
        const typeName = type?.selectedOptions[0]?.textContent || 'Tipo por seleccionar';
        const groups = getGroups(card);
        const mediaSummary = [...new Set(groups.map(group => `${group.alcance} · ${group.medio === 'FIBRA' ? 'Fibra' : 'Ethernet'}`))].join(' / ') || 'Sin puertos';
        $('.identity-location', card).textContent = `${siteName} · ${floorName}`;
        $('.identity-name', card).textContent = name?.value.trim() || typeName;
        $('.identity-media', card).textContent = mediaSummary;
        $('.identity-short', card).textContent = mediaSummary;
        const cardTitle = $('.cardhead span', card);
        cardTitle.textContent = `${name?.value.trim() || typeName} · ${siteName} · ${floorName}`;
    }

    function updateFloors(siteSelect) {
        const card = siteSelect.closest('.device-card');
        const floorSelect = $('.piso', card);
        const oldValue = floorSelect.value;
        floorSelect.innerHTML = `<option value="">Seleccione…</option>`;
        (catalogos.pisos || []).filter(floor => String(floor.id_sede) === siteSelect.value).forEach(floor => {
            floorSelect.insertAdjacentHTML('beforeend',
                `<option value="${esc(floor.id_piso)}">${esc(floor.numero_piso)}</option>`);
        });
        floorSelect.disabled = !siteSelect.value;
        if ([...floorSelect.options].some(option => option.value === oldValue)) floorSelect.value = oldValue;
        updateIdentity(card);
    }

    function suggestedIcon(card) {
        const typeSelect = $('[name="id_tipo_equipo"]', card);
        const type = typeSelect?.selectedOptions[0]?.textContent.toLowerCase() || '';
        if (type.includes('switch') || type.includes('swiche') || type.includes('conmutador')) return 'assets/icons/switch.svg';
        if (type.includes('comput') || type.includes('pc')) return 'assets/icons/computer.svg';
        if (type.includes('router') || type.includes('enrut')) return 'assets/icons/router.svg';
        if (type.includes('servidor')) return 'assets/icons/server.svg';
        if (type.includes('teléfono') || type.includes('telefono') || type.includes('ip')) return 'assets/icons/phone.svg';
        if (type.includes('transporte')) return 'assets/icons/network.svg';
        return '';
    }

    function setIcon(card, src, mode = 'custom') {
        card.dataset.icon = src || '';
        card.dataset.iconMode = mode;
        const preview = $('.icon-preview', card);
        preview.innerHTML = src
            ? `<img src="${esc(src)}" alt="Icono seleccionado" onerror="this.style.display='none'; this.parentElement.querySelector('.icon-placeholder').hidden=false"><span class="icon-placeholder" hidden>◈</span>`
            : `<span class="icon-placeholder">◈</span>`;
        $('.icon-url', card).value = src || '';
        $('.icon-status', card).textContent = src ? `Icono seleccionado: ${src}` : 'Se usará el icono predeterminado del tipo de equipo.';
        updateIdentity(card);
        dirtyMark();
    }

    function bindIconDrop(card) {
        const drop = $('.icon-drop', card);
        const fileInput = $('.icon-file', card);
        const processFile = async file => {
            if (!file) return;
            if (!/^image\/(png|jpeg|webp|gif)$/.test(file.type)) {
                toast('Formato no permitido. Usa PNG, JPG, WEBP o GIF.');
                return;
            }
            if (file.size > 3 * 1024 * 1024) {
                toast('La imagen supera el límite de 3 MB.');
                return;
            }
            const formData = new FormData();
            formData.append('icono', file);
            $('.icon-status', card).textContent = 'Subiendo imagen…';
            try {
                const response = await fetch('api/subir_icono.php', { method: 'POST', body: formData, headers: { Accept: 'application/json' } });
                const result = await response.json();
                if (!response.ok || result.estado !== 'ok') throw new Error(result.mensaje || 'No se pudo subir el icono.');
                setIcon(card, result.ruta, 'upload');
                toast('Icono subido correctamente.');
            } catch (error) {
                $('.icon-status', card).textContent = error.message;
                toast(error.message);
            }
        };
        card._processIconFile = processFile;
        drop.addEventListener('dragover', event => {
            event.preventDefault();
            drop.classList.add('is-over');
        });
        drop.addEventListener('dragleave', () => drop.classList.remove('is-over'));
        drop.addEventListener('drop', event => {
            event.preventDefault();
            drop.classList.remove('is-over');
            processFile(event.dataTransfer.files?.[0]);
        });
        drop.addEventListener('paste', event => {
            const item = [...(event.clipboardData?.items || [])].find(entry => entry.type.startsWith('image/'));
            if (item) {
                event.preventDefault();
                processFile(item.getAsFile());
            }
        });
        drop.addEventListener('click', () => drop.focus());
        fileInput.addEventListener('change', () => processFile(fileInput.files?.[0]));
    }

    function portButton(cardId, groupIndex, portNumber) {
        return $(`.device-card[data-id="${cardId}"] .port[data-group="${groupIndex}"][data-port="${portNumber}"]`);
    }

    function setPortState(card, groupIndex, portNumber, nextState) {
        const groups = getGroups(card);
        const group = groups[groupIndex];
        group.states = group.states || {};
        group.states[portNumber] = nextState;
        setGroups(card, groups);
    }

    function clickNewPort(button) {
        const card = button.closest('.device-card');
        const groupIndex = Number(button.dataset.group);
        const portNumber = Number(button.dataset.port);
        const groups = getGroups(card);
        const group = groups[groupIndex];
        const currentState = portState(group, portNumber);
        const attachedLink = links.find(link => [link.source, link.destination].some(endpoint =>
            endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) &&
            endpoint.group === groupIndex && endpoint.port === portNumber));
        if (attachedLink && card.dataset.mode !== 'state') {
            if (confirm('¿Eliminar este enlace preparado y liberar ambos puertos?')) removeDraftLink(attachedLink);
            return;
        }

        if (card.dataset.mode === 'state') {
            const isLinked = links.some(link => [link.source, link.destination].some(endpoint =>
                endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) &&
                endpoint.group === groupIndex && endpoint.port === portNumber));
            if (isLinked) {
                const link = links.find(item => [item.source, item.destination].some(endpoint =>
                    endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) &&
                    endpoint.group === groupIndex && endpoint.port === portNumber));
                if (confirm('Este puerto tiene un enlace preparado. ¿Quieres quitar el enlace y liberar ambos puertos?')) {
                    removeDraftLink(link);
                }
                return;
            }
            const nextState = states[(states.indexOf(currentState) + 1) % states.length];
            setPortState(card, groupIndex, portNumber, nextState);
            renderCard(card);
            dirtyMark(`Puerto ${portNumber}: ${stateLabels[nextState]}.`);
            return;
        }

        if (currentState !== 'available') {
            toast('Solo un puerto disponible puede iniciar un enlace.');
            return;
        }

        if (!pendingSource) {
            pendingSource = { cardId: Number(card.dataset.id), group: groupIndex, port: portNumber };
            button.classList.add('selected');
            toast('Origen elegido. Ahora pulsa un puerto de otro equipo nuevo o del equipo existente seleccionado.');
            return;
        }

        const source = pendingSource;
        if (source.cardId === Number(card.dataset.id)) {
            if (source.group === groupIndex && source.port === portNumber) {
                pendingSource = null;
                button.classList.remove('selected');
                toast('Selección de origen cancelada.');
            } else {
                toast('El destino debe ser otro equipo. Para enlazar con un equipo existente, selecciónalo en el buscador.');
            }
            return;
        }

        const sourceCard = $(`.device-card[data-id="${source.cardId}"]`);
        const sourceGroup = getGroups(sourceCard)[source.group];
        if (sourceGroup.medio !== group.medio) {
            toast('Los medios no coinciden: Ethernet/UTP y fibra se enlazan a través de un equipo compatible, no directamente.');
            return;
        }
        addLink({ kind: 'new', cardId: source.cardId, group: source.group, port: source.port },
                { kind: 'new', cardId: Number(card.dataset.id), group: groupIndex, port: portNumber });
    }

    function addLink(source, destination) {
        const duplicate = links.some(link =>
            (sameEndpoint(link.source, source) && sameEndpoint(link.destination, destination)) ||
            (sameEndpoint(link.source, destination) && sameEndpoint(link.destination, source))
        );
        if (duplicate) {
            toast('Ese enlace ya está preparado.');
            return;
        }
        links.push({ source, destination });
        pendingSource = null;
        $$('.port.selected').forEach(port => port.classList.remove('selected'));
        links.forEach(link => {
            if (link.source.kind === 'new') {
                const button = portButton(link.source.cardId, link.source.group, link.source.port);
                if (button) { button.classList.remove('available', 'selected'); button.classList.add('occupied', 'linked'); }
            }
            if (link.destination.kind === 'new') {
                const button = portButton(link.destination.cardId, link.destination.group, link.destination.port);
                if (button) { button.classList.remove('available', 'selected'); button.classList.add('occupied', 'linked'); }
            }
        });
        drawWires();
        dirtyMark('Enlace preparado; se guardará junto con los equipos.');
        toast('Enlace preparado para guardarse.');
    }

    function removeDraftLink(link) {
        if (!link) return;
        const index = links.indexOf(link);
        if (index < 0) return;
        links.splice(index, 1);
        pendingSource = null;
        $$('.port.selected').forEach(port => port.classList.remove('selected'));
        for (const endpoint of [link.source, link.destination]) {
            if (endpoint.kind !== 'new') continue;
            const card = $(`.device-card[data-id="${endpoint.cardId}"]`);
            if (!card) continue;
            const group = getGroups(card)[endpoint.group];
            if (group?.states) delete group.states[endpoint.port];
            setGroups(card, getGroups(card));
            renderCard(card);
        }
        drawWires();
        renderExistingPorts();
        dirtyMark('Enlace preparado eliminado. Ambos puertos quedan disponibles.');
        toast('Enlace quitado.');
    }

    function sameEndpoint(a, b) {
        return a.kind === b.kind && a.cardId === b.cardId && a.group === b.group &&
            a.port === b.port && a.equipmentId === b.equipmentId && a.portId === b.portId;
    }

    function drawWires() {
        const root = $('.page') || document.querySelector('main') || document.body;
        if (!root) return;
        let svg = $('#draftWires');
        if (!svg) {
            svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.id = 'draftWires';
            svg.classList.add('draft-wires');
            root.prepend(svg);
        }
        const rootRect = root.getBoundingClientRect();
        const width = Math.max(root.scrollWidth, root.clientWidth);
        const height = Math.max(root.scrollHeight, root.clientHeight);
        svg.setAttribute('width', width);
        svg.setAttribute('height', height);
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svg.innerHTML = '';
        const findPort = endpoint => endpoint.kind === 'new'
            ? portButton(endpoint.cardId, endpoint.group, endpoint.port)
            : $(`#existingPorts .port[data-port-id="${endpoint.portId}"]`);
        for (const link of links) {
            const a = findPort(link.source), b = findPort(link.destination);
            if (!a || !b) continue;
            const ar = a.getBoundingClientRect(), br = b.getBoundingClientRect();
            const x1 = ar.left + ar.width / 2 - rootRect.left + root.scrollLeft;
            const y1 = ar.top + ar.height / 2 - rootRect.top + root.scrollTop;
            const x2 = br.left + br.width / 2 - rootRect.left + root.scrollLeft;
            const y2 = br.top + br.height / 2 - rootRect.top + root.scrollTop;
            const mid = (x1 + x2) / 2;
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${x1} ${y1} C ${mid} ${y1}, ${mid} ${y2}, ${x2} ${y2}`);
            let medium = 'UTP';
            if (link.source.kind === 'new') {
                const card = $(`.device-card[data-id="${link.source.cardId}"]`);
                medium = getGroups(card)[link.source.group]?.medio || 'UTP';
            } else {
                const port = selectedExistingList.get(Number(link.source.equipmentId))?.ports.find(p => Number(p.id_puerto) === Number(link.source.portId));
                medium = port?.medio || port?.tipo_puerto || 'UTP';
            }
            path.setAttribute('class', `draft-wire ${String(medium).toUpperCase().includes('FIBRA') ? 'wire-fiber' : 'wire-ethernet'}`);
            path.style.pointerEvents = 'stroke';
            path.style.cursor = 'pointer';
            path.addEventListener('click', () => removeDraftLink(link));
            svg.append(path);
        }
    }

    async function searchExisting() {
        const box = $('#existingResults');
        box.textContent = 'Buscando equipos…';
        $('#existingPorts').replaceChildren();
        try {
            const response = await fetch('api/buscar_equipos_enlace.php', { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const result = await response.json();
            if (!Array.isArray(result)) throw new Error(result.mensaje || 'La respuesta de equipos no es una lista.');
            existingEquipment = result;
            renderExistingResults();
        } catch (error) {
            box.textContent = `No se pudo buscar: ${error.message}`;
        }
    }

    function renderExistingResults() {
        const query = $('#existingSearch').value.trim().toLowerCase();
        const siteId = $('#existingSite').value;
        const floorId = $('#existingFloor').value;
        const results = existingEquipment.filter(item => {
            const searchable = [item.nombre_equipo, item.serial, item.direccion_ip, item.modelo, item.marca, item.nombre_tipo]
                .some(value => String(value || '').toLowerCase().includes(query));
            const siteOk = !siteId || String(item.id_sede || '') === siteId;
            const floorOk = !floorId || String(item.id_piso || '') === floorId;
            return searchable && siteOk && floorOk;
        });
        const box = $('#existingResults');
        box.innerHTML = results.length ? results.map(item => `
            <button type="button" class="existing-item ${selectedExistingList.has(Number(item.id_equipo)) ? 'is-selected' : ''}" data-action="select-existing" data-id="${esc(item.id_equipo)}" aria-pressed="${selectedExistingList.has(Number(item.id_equipo))}">
                <strong>${esc(item.nombre_equipo || item.modelo || 'Equipo')}</strong>
                <span>${esc(item.nombre_sede || 'Sede —')} · Piso ${esc(item.numero_piso || '—')} · ${esc(item.direccion_ip || 'Sin IP')}<br><small>${esc(item.nombre_tipo || '')} · ${esc(item.marca || '')} ${esc(item.modelo || '')} · Serial ${esc(item.serial || '—')}</small></span>
                <b>${selectedExistingList.has(Number(item.id_equipo)) ? '✓ Seleccionado' : '+ Seleccionar'}</b>
            </button>`).join('') : '<p>No se encontraron equipos con esos filtros.</p>';
    }

    async function selectExisting(equipmentId) {
        const item = existingEquipment.find(equipment => Number(equipment.id_equipo) === Number(equipmentId));
        if (!item) return;
        const id = Number(item.id_equipo);
        if (selectedExistingList.has(id)) {
            selectedExistingList.delete(id);
            if (selectedExisting?.id === id) selectedExisting = [...selectedExistingList.values()].at(-1) || null;
            renderExistingResults();
            renderExistingPorts();
            return;
        }
        const entry = { id, name: item.nombre_equipo || item.modelo || `Equipo ${id}`, ports: [], loading: true };
        selectedExistingList.set(id, entry);
        selectedExisting = entry;
        renderExistingResults();
        renderExistingPorts();
        try {
            const response = await fetch(`api/obtener_puertos_enlace.php?id_equipo=${encodeURIComponent(id)}`, {
                headers: { Accept: 'application/json' }
            });
            const result = await response.json();
            if (!response.ok || result.estado === 'error') throw new Error(result.mensaje || 'No se pudieron cargar los puertos.');
            entry.ports = Array.isArray(result) ? result : (result.puertos || []);
            entry.loading = false;
            renderExistingPorts();
        } catch (error) {
            selectedExistingList.delete(id);
            selectedExisting = [...selectedExistingList.values()].at(-1) || null;
            toast(`No se pudo cargar ${entry.name}: ${error.message}`);
            renderExistingResults();
            renderExistingPorts();
        }
    }

    function renderExistingPorts() {
        const box = $('#existingPorts');
        if (!selectedExistingList.size) { box.replaceChildren(); return; }
        box.innerHTML = [...selectedExistingList.values()].map(entry => {
            const item = existingEquipment.find(e => Number(e.id_equipo) === entry.id) || {};
            if (entry.loading) return `<article class="existing-device-card"><header class="existing-device-head"><div><strong>${esc(entry.name)}</strong></div><span class="locked-tag">Cargando puertos…</span></header></article>`;
            if (!entry.ports.length) return `<article class="existing-device-card"><header class="existing-device-head"><div><strong>${esc(entry.name)}</strong><small>${esc(item.nombre_tipo || 'Equipo registrado')} · ${esc(item.nombre_sede || 'Sede —')} · Piso ${esc(item.numero_piso || '—')}</small></div><span class="locked-tag">Equipo bloqueado</span></header><p>Este equipo no tiene puertos registrados.</p></article>`;
            const groups = new Map();
            entry.ports.forEach(port => {
                const key = `${port.grupo_puerto ?? port.medio ?? port.tipo_puerto ?? 'RED'}|${port.alcance ?? 'LAN'}|${port.medio ?? port.tipo_puerto ?? 'UTP'}`;
                if (!groups.has(key)) groups.set(key, {key, medio: port.medio || (String(port.tipo_puerto).toUpperCase().includes('FIBRA') ? 'FIBRA' : 'UTP'), alcance: port.alcance || 'LAN', ports: []});
                groups.get(key).ports.push(port);
            });
            const groupMarkup = [...groups.values()].map(group => {
                const medium = String(group.medio).toUpperCase().includes('FIBRA') ? 'FIBRA' : 'UTP';
                const count = group.ports.length;
                const storedSections = Number(group.ports[0]?.secciones_fisicas || 0);
                const sectionCount = Math.max(1, Math.min(count || 1, storedSections || (count < 24 ? 1 : Math.ceil(count / 12))));
                const sectionSize = Math.ceil(count / sectionCount);
                const sectionMarkup = [];
                for (let sectionIndex = 0; sectionIndex < sectionCount; sectionIndex++) {
                    const sectionPorts = group.ports.slice(sectionIndex * sectionSize, (sectionIndex + 1) * sectionSize);
                    if (!sectionPorts.length) continue;
                    const buttons = sectionPorts.map(port => {
                        const state = normalizeExistingState(port.estado, !!port.esta_enlazado);
                        const linked = !!port.esta_enlazado;
                        const draftLinked = links.some(link => [link.source, link.destination].some(endpoint => endpoint.kind === 'existing' && endpoint.equipmentId === entry.id && endpoint.portId === Number(port.id_puerto)));
                        const canLink = state === 'available' && !linked && !draftLinked;
                        const pending = pendingExistingSource?.equipmentId === entry.id && pendingExistingSource?.portId === Number(port.id_puerto);
                        return `<button type="button" class="port ${state}${linked || draftLinked ? ' linked' : ''}${pending ? ' selected' : ''}" data-action="choose-existing-port" data-equipment-id="${entry.id}" data-port-id="${esc(port.id_puerto)}" data-port-number="${esc(port.numero_puerto)}" data-medium="${esc(medium)}" data-state="${esc(state)}" data-linked="${linked ? '1' : '0'}" data-peer-port="${esc(port.id_puerto_conectado || '')}" data-peer-equipment="${esc(port.id_equipo_conectado || '')}" data-link-color="${esc(port.color_linea || '')}" data-link-style="${esc(port.estilo_linea || '')}" ${globalMode === 'link' && !canLink ? 'disabled' : ''} title="Puerto ${esc(port.numero_puerto)} · ${esc(stateLabels[state] || state)}${linked ? ' · Enlace existente' : ''}"><span>${esc(port.numero_puerto)}</span><i class="dot"></i></button>`;
                    });
                    const rows = [];
                    for (let i = 0; i < buttons.length; i += 6) rows.push(`<div class="prow">${buttons.slice(i, i + 6).join('')}</div>`);
                    sectionMarkup.push(`<div class="section"><div class="section-label"></div><div class="section-rows">${rows.join('')}</div></div>`);
                }
                return `<section class="group ${medium === 'FIBRA' ? 'medium-fiber' : 'medium-utp'}"><div class="gtitle"><span>${esc(group.alcance)} · ${medium === 'FIBRA' ? 'Fibra óptica' : 'Ethernet / UTP'}</span><small>${group.ports.length} puertos · ${sectionCount} sección(es)</small></div><div class="sections existing-sections">${sectionMarkup.join('')}</div></section>`;
            }).join('');
            return `<article class="existing-device-card" data-existing-equipment="${entry.id}"><header class="existing-device-head"><div><strong>${esc(entry.name)}</strong><small>${esc(item.nombre_tipo || 'Equipo registrado')} · ${esc(item.nombre_sede || 'Sede —')} · Piso ${esc(item.numero_piso || '—')} · ${esc(item.direccion_ip || 'Sin IP')}</small></div><button type="button" class="existing-remove" data-action="remove-existing" data-id="${entry.id}" aria-label="Quitar equipo">×</button><span class="locked-tag">Equipo bloqueado</span></header><div class="existing-device-rack"><div class="groups">${groupMarkup}</div></div></article>`;
        }).join('');
        const stateFilter = $('#existingStateFilter')?.value || 'all';
        if (stateFilter !== 'all') {
            box.querySelectorAll('.existing-device-card').forEach(card => {
                card.querySelectorAll('.port[data-action="choose-existing-port"]').forEach(port => {
                    port.hidden = port.dataset.state !== stateFilter;
                });
                card.querySelectorAll('.group').forEach(group => {
                    group.hidden = !group.querySelector('.port:not([hidden])');
                });
                card.hidden = !card.querySelector('.port:not([hidden])');
            });
        }
        drawSavedExistingLinks();
        drawWires();
    }

    function drawSavedExistingLinks() {
        const box = $('#existingPorts');
        if (!box) return;
        box.querySelector('.saved-links-overlay')?.remove();
        const visibleCards = [...box.querySelectorAll('.existing-device-card:not([hidden])')];
        if (visibleCards.length < 2) return;
        const boxRect = box.getBoundingClientRect();
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'saved-links-overlay');
        svg.setAttribute('aria-hidden', 'true');
        svg.setAttribute('width', String(box.scrollWidth));
        svg.setAttribute('height', String(box.scrollHeight));
        svg.setAttribute('viewBox', `0 0 ${box.scrollWidth} ${box.scrollHeight}`);
        const drawn = new Set();
        box.querySelectorAll('.port[data-action="choose-existing-port"][data-peer-port]:not([hidden])').forEach(port => {
            const peerId = port.dataset.peerPort;
            const peerEquipment = port.dataset.peerEquipment;
            if (!peerId || !peerEquipment || !selectedExistingList.has(Number(peerEquipment))) return;
            const peer = box.querySelector(`.port[data-port-id="${CSS.escape(peerId)}"]`);
            if (!peer || peer.hidden || peer.closest('.existing-device-card')?.hidden) return;
            const key = [port.dataset.portId, peerId].sort().join(':');
            if (drawn.has(key)) return;
            drawn.add(key);
            const a = port.getBoundingClientRect();
            const b = peer.getBoundingClientRect();
            const x1 = a.left - boxRect.left + box.scrollLeft + a.width / 2;
            const y1 = a.top - boxRect.top + box.scrollTop + a.height / 2;
            const x2 = b.left - boxRect.left + box.scrollLeft + b.width / 2;
            const y2 = b.top - boxRect.top + box.scrollTop + b.height / 2;
            const midX = (x1 + x2) / 2;
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`);
            path.setAttribute('class', `saved-link-line ${String(port.dataset.linkStyle).toUpperCase() === 'CONTINUA' ? 'is-solid' : 'is-dashed'}`);
            path.setAttribute('stroke', /^#[0-9a-f]{3,8}$/i.test(port.dataset.linkColor || '') ? port.dataset.linkColor : (String(port.dataset.medium).toUpperCase().includes('FIBRA') ? '#2563eb' : '#e11d48'));
            svg.appendChild(path);
        });
        if (svg.childElementCount) box.prepend(svg);
    }

    function normalizeExistingState(dbState, linked) {
        if (linked) return 'occupied';
        const state = String(dbState || '').toUpperCase();
        if (state === 'RESERVADO') return 'reserved';
        if (['DANADO', 'DAÑADO', 'DANADA', 'DAÑADA'].includes(state)) return 'damaged';
        if (['NO_DISPONIBLE', 'OCUPADO', 'ENLAZADO'].includes(state)) return 'occupied';
        return 'available';
    }

    async function chooseExistingPort(button) {
        const equipmentId = Number(button.dataset.equipmentId);
        const entry = selectedExistingList.get(equipmentId);
        const portId = Number(button.dataset.portId);
        if (window.netmapExistingPairMode && globalMode === 'link' && !pendingSource && !pendingExistingSource) {
            if (button.dataset.state !== 'available' || button.disabled) { toast('Solo puedes iniciar desde un puerto disponible.'); return; }
            pendingExistingSource = {equipmentId, portId};
            renderExistingPorts();
            toast('Origen seleccionado. Elige un puerto disponible de otro equipo existente.');
            return;
        }
        if (globalMode === 'state') {
            if (button.dataset.linked === '1') {
                if (!confirm('Este puerto tiene un enlace guardado. ¿Quieres eliminar el enlace y liberar ambos puertos?')) return;
                try {
                    const response = await fetch('api/eliminar_enlace_puerto.php', { method: 'POST', headers: {'Content-Type':'application/json', Accept:'application/json'}, body: JSON.stringify({id_puerto: portId}) });
                    const result = await response.json();
                    if (!response.ok || result.estado !== 'ok') throw new Error(result.mensaje || 'No se pudo quitar el enlace.');
                    for (const selectedId of selectedExistingList.keys()) await reloadExistingPorts(selectedId);
                    toast('Enlace eliminado; puertos liberados.');
                } catch (error) { toast(error.message); }
                return;
            }
            const current = button.dataset.state;
            const next = states[(states.indexOf(current) + 1) % states.length];
            try {
                const response = await fetch('api/cambiar_estado_puerto.php', { method:'POST', headers:{'Content-Type':'application/json',Accept:'application/json'}, body:JSON.stringify({id_puerto:portId,estado:stateDb[next]}) });
                const result = await response.json();
                if (!response.ok || result.estado !== 'ok') throw new Error(result.mensaje || 'No se pudo cambiar el estado.');
                await reloadExistingPorts(equipmentId);
                toast(`Puerto ${button.dataset.portNumber}: ${stateLabels[next]}.`);
            } catch (error) { toast(error.message); }
            return;
        }
        if (pendingExistingSource) {
            const state = button.dataset.state;
            if (state !== 'available' || button.dataset.linked === '1' || button.disabled) { toast('El puerto destino debe estar disponible y sin enlace.'); return; }
            if (pendingExistingSource.equipmentId === equipmentId && pendingExistingSource.portId === portId) { pendingExistingSource = null; renderExistingPorts(); toast('Selección cancelada.'); return; }
            const sourcePort = selectedExistingList.get(pendingExistingSource.equipmentId)?.ports.find(p => Number(p.id_puerto) === pendingExistingSource.portId);
            const sourceMedium = String(sourcePort?.medio || sourcePort?.tipo_puerto || 'UTP').toUpperCase();
            const targetMedium = String(button.dataset.medium || 'UTP').toUpperCase();
            if (sourceMedium.includes('FIBRA') !== targetMedium.includes('FIBRA')) { toast('Los medios físicos deben coincidir.'); return; }
            addLink({kind:'existing',equipmentId:pendingExistingSource.equipmentId,portId:pendingExistingSource.portId},{kind:'existing',equipmentId,portId,port:Number(button.dataset.portNumber)});
            pendingExistingSource = null;
            renderExistingPorts();
            return;
        }
        if (!pendingSource) { toast('Primero pulsa un puerto nuevo o activa «Enlazar equipos existentes».'); return; }
        const state = button.dataset.state;
        if (state !== 'available' || button.dataset.linked === '1' || button.disabled) {
            toast('Ese puerto no está disponible. Solo se pueden enlazar puertos disponibles y sin enlace.');
            return;
        }
        const sourceCard = $(`.device-card[data-id="${pendingSource.cardId}"]`);
        if (!sourceCard) { pendingSource = null; toast('El equipo de origen ya no existe. Selecciona el puerto otra vez.'); return; }
        const sourceGroup = getGroups(sourceCard)[pendingSource.group];
        const destinationMedium = button.dataset.medium.toUpperCase();
        if ((sourceGroup.medio === 'FIBRA' ? 'FIBRA' : 'UTP') !== (destinationMedium === 'FIBRA' ? 'FIBRA' : 'UTP')) {
            toast('El medio físico del puerto de origen y destino debe coincidir.');
            return;
        }
        addLink(
            { kind: 'new', cardId: pendingSource.cardId, group: pendingSource.group, port: pendingSource.port },
            { kind: 'existing', equipmentId, portId, port: Number(button.dataset.portNumber) }
        );
        button.classList.add('linked');
        button.disabled = true;
        renderExistingPorts();
    }

    async function reloadExistingPorts(equipmentId) {
        const entry = selectedExistingList.get(Number(equipmentId));
        if (!entry) return;
        const response = await fetch(`api/obtener_puertos_enlace.php?id_equipo=${encodeURIComponent(equipmentId)}`, {headers:{Accept:'application/json'}});
        const result = await response.json();
        if (!response.ok || result.estado === 'error') throw new Error(result.mensaje || 'No se pudieron actualizar los puertos.');
        entry.ports = Array.isArray(result) ? result : (result.puertos || []);
        renderExistingPorts();
    }

    $('#contenedor-tarjetas').addEventListener('change', event => {
        const card = event.target.closest('.device-card');
        if (!card) return;
        if (event.target.matches('.sede')) {
            updateFloors(event.target);
            dirtyMark();
            return;
        }
        if (event.target.dataset.g !== undefined) {
            const groups = getGroups(card);
            const index = Number(event.target.dataset.g);
            const field = event.target.dataset.f;
            const group = groups[index];
            if (!group) return;
            if (field === 'alcance' || field === 'medio') {
                if (field === 'medio' && links.some(link => [link.source, link.destination].some(endpoint =>
                    endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) && endpoint.group === index))) {
                    if (!confirm('Cambiar el medio afectará enlaces de este grupo. ¿Quitar esos enlaces y continuar?')) {
                        event.target.value = group.medio;
                        return;
                    }
                    links = links.filter(link => ![link.source, link.destination].some(endpoint =>
                        endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) && endpoint.group === index));
                    pendingSource = null;
                }
                group[field] = event.target.value;
            } else {
                group[field] = Math.max(1, Number(event.target.value) || 1);
            }
            group.secciones = Math.min(Number(group.secciones), Number(group.cantidad));
            if (field === 'cantidad') {
                const maxPort = Number(group.cantidad);
                Object.keys(group.states || {}).forEach(port => {
                    if (Number(port) > maxPort) delete group.states[port];
                });
            }
            setGroups(card, groups);
            renderCard(card);
            dirtyMark();
            return;
        }
        if (event.target.matches('[name="id_tipo_equipo"]')) {
            renderCard(card); // Actualiza las sugerencias según el tipo de equipo.
            dirtyMark();
        }
    });

    $('#contenedor-tarjetas').addEventListener('input', event => {
        const card = event.target.closest('.device-card');
        if (!card) return;
        if (event.target.matches('[name="serial"]')) event.target.value = event.target.value.toLocaleUpperCase('es');
        if (event.target.matches('[name="nombre_equipo"], [name="marca"], [name="modelo"]')) updateIdentity(card);
        if (event.target.matches('input[name]')) dirtyMark();
    });

    $('#contenedor-tarjetas').addEventListener('focusout', event => {
        const card = event.target.closest('.device-card');
        if (!card || !event.target.matches('[name="nombre_equipo"], [name="marca"], [name="modelo"], [name="serial"]')) return;
        normalizeCardText(card, true);
        updateIdentity(card);
    });

    $('#contenedor-tarjetas').addEventListener('click', event => {
        const button = event.target.closest('button');
        if (!button) return;
        const card = button.closest('.device-card');
        const action = button.dataset.action;

        if (action === 'preset-ports') {
            const groups = getGroups(card);
            const groupIndex = Number(button.closest('[data-settings-group]')?.dataset.settingsGroup ?? 0);
            const group = groups[groupIndex];
            if (!group) return;
            const count = Math.max(1, Math.min(128, Number(button.dataset.count) || 1));
            group.cantidad = count;
            group.secciones = Math.max(1, Math.min(count, Number(button.dataset.sections) || (count < 12 ? 1 : Math.ceil(count / 12))));
            if (['LAN','WAN'].includes(button.dataset.reach)) group.alcance = button.dataset.reach;
            if (['UTP','FIBRA'].includes(button.dataset.medium)) group.medio = button.dataset.medium;
            Object.keys(group.states || {}).forEach(port => { if (Number(port) > count) delete group.states[port]; });
            setGroups(card, groups);
            renderCard(card);
            dirtyMark(`Sugerencia aplicada: ${count} puertos.`);
        } else if (action === 'add-group') {
            const groups = getGroups(card);
            groups.push(makeGroup('FIBRA', 'LAN', 2, 1));
            setGroups(card, groups);
            renderCard(card);
            dirtyMark();
        } else if (action === 'remove-group') {
            const groups = getGroups(card);
            const groupIndex = Number(button.dataset.group);
            if (groups.length <= 1) {
                toast('Cada equipo debe tener al menos un grupo. Puedes reducirlo a un puerto si hace falta.');
                return;
            }
            if (links.some(link =>
                (link.source.kind === 'new' && link.source.cardId === Number(card.dataset.id) && link.source.group === groupIndex) ||
                (link.destination.kind === 'new' && link.destination.cardId === Number(card.dataset.id) && link.destination.group === groupIndex))) {
                toast('Quita primero los enlaces preparados que usan este grupo.');
                return;
            }
            groups.splice(groupIndex, 1);
            links = links.map(link => {
                for (const endpoint of [link.source, link.destination]) {
                    if (endpoint.kind === 'new' && endpoint.cardId === Number(card.dataset.id) && endpoint.group > groupIndex) endpoint.group--;
                }
                return link;
            });
            setGroups(card, groups);
            renderCard(card);
            dirtyMark();
        } else if (action === 'remove-card') {
            if (links.some(link =>
                (link.source.kind === 'new' && link.source.cardId === Number(card.dataset.id)) ||
                (link.destination.kind === 'new' && link.destination.cardId === Number(card.dataset.id)))) {
                toast('Quita primero los enlaces preparados para este equipo.');
                return;
            }
            card.remove();
            dirtyMark();
            drawWires();
        } else if (action === 'link-mode') {
            if (pendingSource) { pendingSource = null; $$('.port.selected').forEach(port => port.classList.remove('selected')); }
            globalMode = 'link';
            $$('.device-card').forEach(item => item.dataset.mode = 'link');
            renderExistingPorts();
            toast('Modo Enlazar: selecciona un puerto disponible y luego su destino.');
        } else if (action === 'state-mode') {
            if (pendingSource) { pendingSource = null; $$('.port.selected').forEach(port => port.classList.remove('selected')); toast('Se canceló el enlace pendiente al cambiar de modo.'); }
            globalMode = 'state';
            $$('.device-card').forEach(item => item.dataset.mode = 'state');
            renderExistingPorts();
            toast('Modo Cambiar estado: pulsa un puerto para avanzar de estado.');
        } else if (action === 'port') {
            clickNewPort(button);
        } else if (action === 'choose-icon') {
            $('.icon-file', card).click();
        } else if (action === 'upload-icon') {
            $('.icon-file', card).click();
        } else if (action === 'use-suggestion') {
            const icon = suggestedIcon(card);
            if (!icon) {
                toast('Todavía no hay una sugerencia configurada para este tipo. Puedes elegir una imagen de las sugerencias o pegar una ruta.');
                return;
            }
            setIcon(card, icon, 'suggestion');
        } else if (action === 'clear-icon') {
            setIcon(card, '', 'default');
        } else if (action === 'apply-icon-url') {
            const value = $('.icon-url', card).value.trim();
            if (!value) { setIcon(card, '', 'default'); return; }
            const isWeb = /^https?:\/\//i.test(value);
            const isLocal = /^(assets\/|\/|\.\/|\.\.\/)/i.test(value);
            if (!isWeb && !isLocal) {
                toast('Usa una URL http(s) o una ruta local como assets/icons/mi-icono.png.');
                return;
            }
            setIcon(card, value, isWeb ? 'web' : 'local');
        } else if (button.classList.contains('icon-suggestion')) {
            setIcon(card, button.dataset.icon, 'suggestion');
        }
    });

    $('#existingResults').addEventListener('click', event => {
        const button = event.target.closest('[data-action="select-existing"]');
        if (button) selectExisting(Number(button.dataset.id));
    });

    $('#existingPorts').addEventListener('click', event => {
        const remove = event.target.closest('[data-action="remove-existing"]');
        if (remove) {
            const id = Number(remove.dataset.id);
            if (links.some(link => [link.source,link.destination].some(ep => ep.kind === 'existing' && ep.equipmentId === id))) { toast('Quita primero los enlaces preparados de este equipo.'); return; }
            selectedExistingList.delete(id);
            selectedExisting = [...selectedExistingList.values()].at(-1) || null;
            renderExistingResults(); renderExistingPorts(); drawWires();
            return;
        }
        const button = event.target.closest('[data-action="choose-existing-port"]');
        if (button) chooseExistingPort(button);
    });

    $('#existingSearch').addEventListener('input', renderExistingResults);
    $('#existingSearch').addEventListener('keydown', event => {
        if (event.key === 'Enter') { event.preventDefault(); searchExisting(); }
    });
    $('#existingSite').addEventListener('change', renderExistingResults);
    $('#existingFloor').addEventListener('change', renderExistingResults);
    $('#existingStateFilter')?.addEventListener('change', renderExistingPorts);
    document.addEventListener('click', event => {
        if (!event.target.closest('#linkExistingPair')) return;
        window.netmapExistingPairMode = !window.netmapExistingPairMode;
        pendingExistingSource = null;
        pendingSource = null;
        $('#linkExistingPair').classList.toggle('active', window.netmapExistingPairMode);
        $('#linkExistingPair').textContent = window.netmapExistingPairMode ? 'Cancelar enlace entre existentes' : '↔ Enlazar equipos existentes';
        renderExistingPorts();
        toast(window.netmapExistingPairMode ? 'Selecciona origen y destino entre los equipos registrados.' : 'Modo desactivado.');
    });
    $('#searchExisting').addEventListener('click', searchExisting);
    $('#toggleExisting').addEventListener('click', () => {
        $('#existingPanel').scrollIntoView({behavior:'smooth', block:'start'});
        if (!existingEquipment.length) searchExisting();
    });
    $('#btn-add-card').addEventListener('click', addCard);
    const sharedPanel = document.createElement('section');
    sharedPanel.id = 'sharedNetControls';
    sharedPanel.className = 'shared-net-controls';
    sharedPanel.innerHTML = `<div class="shared-actions">
        <button type="button" data-action="link-mode">↔ Enlazar</button>
        <button type="button" data-action="state-mode">● Cambiar estado</button>
        <button type="button" id="linkExistingPair">↔ Enlazar equipos existentes</button>
    </div><div class="legend"><span><i style="background:#22c55e"></i>Disponible</span><span><i style="background:#2563eb"></i>Ocupado / enlazado</span><span><i style="background:#f59e0b"></i>Reservado</span><span><i style="background:#dc2626"></i>Dañado</span></div>`;
    $('#existingPorts').before(sharedPanel);
    sharedPanel.hidden = false;

    $('#toggleView').addEventListener('click', () => {
        joint = !joint;
        $('#contenedor-tarjetas').classList.toggle('combined', joint);
        if (joint) ensureSharedControls();
        const shared = $('#sharedNetControls');
        if (shared) shared.hidden = !joint;
        $('#toggleView').textContent = joint ? 'Vista por tarjetas' : 'Vista previa conjunta';
        drawWires();
    });

    function ensureSharedControls() { return; }
    document.addEventListener('click', event => {
        const button = event.target.closest('#sharedNetControls [data-action]');
        if (!button) return;
        const action = button.dataset.action;
        const firstCard = $('.device-card');
        if (firstCard) firstCard.querySelector(`[data-action="${action}"]`)?.click();
        else {
            if (action === 'link-mode') { globalMode = 'link'; toast('Modo Enlazar activado.'); }
            if (action === 'state-mode') { globalMode = 'state'; pendingSource = null; toast('Modo Cambiar estado activado.'); }
            $$('.device-card').forEach(item => item.dataset.mode = globalMode);
            renderExistingPorts();
        }
    });

    $('#form-lote').addEventListener('submit', async event => {
        event.preventDefault();
        const cards = $('.device-card');
        if (!cards.length && !links.some(link => link.source.kind === 'existing' && link.destination.kind === 'existing')) {
            toast('Añade un equipo nuevo o prepara un enlace entre equipos existentes.');
            return;
        }
        const payload = { equipos: [], enlaces: [] };
        for (const card of cards) {
            const value = name => $(`[name="${name}"]`, card)?.value.trim() || '';
            const required = ['id_tipo_equipo', 'marca', 'modelo', 'serial', 'direccion_ip'];
            if (!$('.sede', card).value || !$('.piso', card).value || required.some(name => !value(name))) {
                toast('Completa sede, piso, tipo, marca, modelo, serial e IP en cada tarjeta.');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            normalizeCardText(card, true);
            const groups = getGroups(card);
            if (!groups.length) { toast('Cada equipo necesita al menos un grupo de puertos.'); return; }
            payload.equipos.push({
                client_id: Number(card.dataset.id),
                id_piso: $('.piso', card).value,
                id_tipo_equipo: value('id_tipo_equipo'),
                icono: card.dataset.icon || '',
                marca: value('marca'),
                modelo: value('modelo'),
                serial: value('serial'),
                direccion_ip: value('direccion_ip'),
                nombre_equipo: value('nombre_equipo'),
                observaciones: value('observaciones'),
                grupos_puertos: groups.map(group => ({
                    cantidad: Number(group.cantidad),
                    alcance: group.alcance,
                    medio: group.medio,
                    secciones: Number(group.secciones),
                    estados: group.states || {}
                }))
            });
        }

        payload.enlaces = links.map(link => ({
            origen: link.source.kind === 'new'
                ? { tipo: 'nuevo', client_id: link.source.cardId, grupo: link.source.group, puerto: link.source.port }
                : { tipo: 'existente', id_puerto: link.source.portId },
            destino: link.destination.kind === 'new'
                ? { tipo: 'nuevo', client_id: link.destination.cardId, grupo: link.destination.group, puerto: link.destination.port }
                : { tipo: 'existente', id_puerto: link.destination.portId }
        }));

        const saveButton = $('#btnSave');
        saveButton.disabled = true;
        saveButton.textContent = 'Guardando…';
        try {
            const response = await fetch('api/guardar_lote.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (!response.ok || result.estado !== 'ok') throw new Error(result.mensaje || 'No se pudieron guardar los equipos.');
            dirty = false;
            $('#formStatus').textContent = 'Guardado correctamente.';
            alert(result.mensaje || 'Equipos registrados correctamente.');
            location.href = 'equipos_lista.php';
        } catch (error) {
            toast(`No se guardó: ${error.message}`);
            console.error(error);
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = 'Guardar equipos';
        }
    });

    window.addEventListener('beforeunload', event => {
        if (dirty) {
            event.preventDefault();
            event.returnValue = 'Hay cambios sin guardar.';
        }
    });
    window.addEventListener('resize', drawWires);
    window.addEventListener('scroll', drawWires, true);
    $('#scrollToPorts')?.addEventListener('click', () => $('#portsAnchor')?.scrollIntoView({behavior:'smooth',block:'start'}));
    $('#scrollToEditor')?.addEventListener('click', () => $('#contenedor-tarjetas')?.scrollIntoView({behavior:'smooth',block:'start'}));
    init();
})();

/* Mantiene alineadas las conexiones guardadas cuando cambia el tamaño de la vista. */
window.addEventListener('resize', () => {
    if (typeof drawSavedExistingLinks === 'function') drawSavedExistingLinks();
});
