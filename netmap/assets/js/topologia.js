(() => {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const state = {
        equipos: [],
        enlaces: [],
        zoom: 1,
        drag: null,
        saveTimer: null
    };

    const stage = $('#mapStage');
    const viewport = $('#mapViewport');
    const nodesBox = $('#mapNodes');
    const linksSvg = $('#mapLinks');

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));

    function status(message) {
        $('#topologyStatus').textContent = message;
    }

    function filteredEquipment() {
        const site = $('#filterSite').value;
        const floor = $('#filterFloor').value;
        return state.equipos.filter(item =>
            (!site || String(item.id_sede) === site) &&
            (!floor || String(item.id_piso) === floor)
        );
    }

    function filteredLinks(visibleIds) {
        return state.enlaces.filter(link =>
            visibleIds.has(Number(link.id_equipo_origen)) &&
            visibleIds.has(Number(link.id_equipo_destino))
        );
    }

    async function loadTopology() {
        status('Consultando equipos y enlaces…');
        try {
            const response = await fetch('api/obtener_topologia.php', {
                headers: { Accept: 'application/json' },
                cache: 'no-store'
            });
            const data = await response.json();
            if (!response.ok || data.estado !== 'ok') {
                throw new Error(data.mensaje || 'No se pudo cargar la topología.');
            }
            state.equipos = Array.isArray(data.equipos) ? data.equipos : [];
            state.enlaces = Array.isArray(data.enlaces) ? data.enlaces : [];
            populateFilters();
            renderTopology();
            status('Inventario actualizado.');
        } catch (error) {
            status('Error: ' + error.message);
        }
    }

    function populateFilters() {
        const siteSelect = $('#filterSite');
        const floorSelect = $('#filterFloor');
        const oldSite = siteSelect.value;
        const oldFloor = floorSelect.value;
        const sites = new Map();
        const floors = new Map();

        state.equipos.forEach(item => {
            if (item.id_sede) sites.set(String(item.id_sede), item.nombre_sede || 'Sede');
            if (item.id_piso) floors.set(String(item.id_piso), {
                name: item.numero_piso || 'Piso',
                site: String(item.id_sede || '')
            });
        });

        siteSelect.innerHTML = '<option value="">Todas las sedes</option>' +
            [...sites.entries()].map(([id, name]) =>
                '<option value="' + escapeHtml(id) + '">' + escapeHtml(name) + '</option>'
            ).join('');
        if (sites.has(oldSite)) siteSelect.value = oldSite;

        const visibleFloors = [...floors.entries()].filter(([, floor]) =>
            !siteSelect.value || floor.site === siteSelect.value
        );
        floorSelect.innerHTML = '<option value="">Todos los pisos</option>' +
            visibleFloors.map(([id, floor]) =>
                '<option value="' + escapeHtml(id) + '">' + escapeHtml(floor.name) + '</option>'
            ).join('');
        if (visibleFloors.some(([id]) => id === oldFloor)) floorSelect.value = oldFloor;
    }

    function renderTopology() {
        const equipment = filteredEquipment();
        const ids = new Set(equipment.map(item => Number(item.id_equipo)));
        const links = filteredLinks(ids);
        const totalPorts = equipment.reduce((sum, item) => sum + Number(item.total_puertos || 0), 0);
        const linkedPorts = equipment.reduce((sum, item) => sum + Number(item.puertos_enlazados || 0), 0);

        $('#topologySummary').innerHTML =
            '<span><strong>' + equipment.length + '</strong> equipos visibles</span>' +
            '<span><strong>' + links.length + '</strong> enlaces</span>' +
            '<span><strong>' + totalPorts + '</strong> puertos registrados</span>' +
            '<span><strong>' + linkedPorts + '</strong> puertos enlazados</span>';

        $('#mapEmpty').hidden = equipment.length !== 0;
        nodesBox.replaceChildren();
        equipment.forEach(item => nodesBox.appendChild(createNode(item)));
        sizeStage(equipment);
        drawLinks(links);
    }

    function createNode(item) {
        const node = document.createElement('article');
        node.className = 'topo-node';
        node.dataset.id = item.id_equipo;
        node.style.left = Math.max(25, Number(item.posicion_x) || 0) + 'px';
        node.style.top = Math.max(25, Number(item.posicion_y) || 0) + 'px';

        const icon = String(item.icono_mostrar || '');
        const safeIcon = /^(https?:\/\/|assets\/|\/|\.\/|\.\.\/)/i.test(icon) ? icon : '';
        node.innerHTML =
            '<div class="topo-node-visual"><img src="' + escapeHtml(safeIcon) + '" alt="" onerror="this.style.display=\'none\'"></div>' +
            '<div class="topo-node-copy">' +
                '<strong title="' + escapeHtml(item.nombre_mostrar) + '">' + escapeHtml(item.nombre_mostrar) + '</strong>' +
                '<span>' + escapeHtml([item.nombre_tipo, item.marca, item.modelo].filter(Boolean).join(' · ')) + '</span>' +
                '<span>' + escapeHtml(item.nombre_sede || 'Sede sin asignar') + ' · Piso ' + escapeHtml(item.numero_piso || '—') + '</span>' +
                '<span>' + escapeHtml(item.direccion_ip || 'Sin IP') + '</span>' +
            '</div>' +
            '<div class="topo-node-meta"><span>' + Number(item.total_puertos || 0) + ' puertos</span>' +
            '<span class="' + (Number(item.puertos_enlazados) > 0 ? 'has-links' : '') + '">' +
            Number(item.puertos_enlazados || 0) + ' enlazados</span></div>';

        node.addEventListener('pointerdown', beginDrag);
        node.addEventListener('dblclick', () => {
            window.location.href = 'equipos_editar_puertos.php?id=' + encodeURIComponent(item.id_equipo);
        });
        node.title = 'Arrastra para mover · Doble clic para editar puertos';
        return node;
    }

    function sizeStage(equipment) {
        const liveNodes = [...nodesBox.querySelectorAll('.topo-node')];
        const maxX = liveNodes.reduce((max, node) => Math.max(max, (parseFloat(node.style.left) || 0) + 300), 0);
        const maxY = liveNodes.reduce((max, node) => Math.max(max, (parseFloat(node.style.top) || 0) + 180), 0);
        const width = Math.max(viewport.clientWidth, maxX + 420, 1200);
        const height = Math.max(viewport.clientHeight, maxY + 260, 900);
        stage.style.width = width + 'px';
        stage.style.height = height + 'px';
        linksSvg.setAttribute('width', width);
        linksSvg.setAttribute('height', height);
        linksSvg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
    }

    function drawLinks(links) {
        linksSvg.replaceChildren();
        const NS = 'http://www.w3.org/2000/svg';
        const nodes = new Map([...nodesBox.querySelectorAll('.topo-node')].map(node =>
            [Number(node.dataset.id), node]
        ));

        links.forEach((link, index) => {
            const origin = nodes.get(Number(link.id_equipo_origen));
            const destination = nodes.get(Number(link.id_equipo_destino));
            if (!origin || !destination) return;

            const a = {
                left: parseFloat(origin.style.left) || 0,
                top: parseFloat(origin.style.top) || 0
            };
            const b = {
                left: parseFloat(destination.style.left) || 0,
                top: parseFloat(destination.style.top) || 0
            };
            let x1, y1, x2, y2, pathData;

            if (Math.abs(a.left - b.left) < 80) {
                const upper = a.top <= b.top ? { node: origin, pos: a } : { node: destination, pos: b };
                const lower = upper.node === origin ? { node: destination, pos: b } : { node: origin, pos: a };
                x1 = upper.pos.left + 119;
                y1 = upper.pos.top + 112;
                x2 = lower.pos.left + 119;
                y2 = lower.pos.top;
                const laneX = Math.max(a.left, b.left) + 270 + (index % 4) * 14;
                pathData = 'M ' + x1 + ' ' + y1 + ' H ' + laneX + ' V ' + y2 + ' H ' + x2;
            } else {
                const aIsLeft = a.left < b.left;
                x1 = aIsLeft ? a.left + 238 : a.left;
                x2 = aIsLeft ? b.left : b.left + 238;
                y1 = a.top + 56;
                y2 = b.top + 56;
                const midX = (x1 + x2) / 2;
                pathData = 'M ' + x1 + ' ' + y1 + ' H ' + midX + ' V ' + y2 + ' H ' + x2;
            }

            const medium = String(link.medio_origen || link.medio_destino || '').toUpperCase();
            const path = document.createElementNS(NS, 'path');
            path.setAttribute('d', pathData);
            path.setAttribute('class', medium.includes('FIBRA') ? 'fiber' : 'ethernet');
            path.dataset.linkId = link.id_enlace;
            linksSvg.appendChild(path);

            const label = document.createElementNS(NS, 'text');
            label.setAttribute('x', String((a.x + b.x) / 2));
            label.setAttribute('y', String((a.y + b.y) / 2 - 8));
            label.setAttribute('text-anchor', 'middle');
            label.textContent = 'P' + link.puerto_origen + ' ↔ P' + link.puerto_destino;
            linksSvg.appendChild(label);
        });
    }

    function nodeCenter(node) {
        const left = parseFloat(node.style.left) || 0;
        const top = parseFloat(node.style.top) || 0;
        const width = node.offsetWidth || 238;
        const height = node.offsetHeight || 112;
        return { x: left + width / 2, y: top + height / 2 };
    }

    function beginDrag(event) {
        if (event.button !== 0) return;
        const node = event.currentTarget;
        const rect = node.getBoundingClientRect();
        const stageRect = stage.getBoundingClientRect();
        state.drag = {
            node,
            pointerId: event.pointerId,
            offsetX: (event.clientX - rect.left) / state.zoom,
            offsetY: (event.clientY - rect.top) / state.zoom,
            stageLeft: stageRect.left,
            stageTop: stageRect.top
        };
        node.classList.add('is-dragging');
        node.setPointerCapture(event.pointerId);
        node.addEventListener('pointermove', moveDrag);
        node.addEventListener('pointerup', endDrag, { once: true });
        node.addEventListener('pointercancel', endDrag, { once: true });
    }

    function moveDrag(event) {
        if (!state.drag || event.pointerId !== state.drag.pointerId) return;
        const node = state.drag.node;
        const stageRect = stage.getBoundingClientRect();
        const x = Math.max(20, (event.clientX - stageRect.left) / state.zoom - state.drag.offsetX);
        const y = Math.max(20, (event.clientY - stageRect.top) / state.zoom - state.drag.offsetY);
        node.style.left = x + 'px';
        node.style.top = y + 'px';
        drawLinks(filteredLinks(new Set(filteredEquipment().map(item => Number(item.id_equipo)))));
    }

    async function endDrag(event) {
        if (!state.drag || event.pointerId !== state.drag.pointerId) return;
        const node = state.drag.node;
        node.classList.remove('is-dragging');
        node.removeEventListener('pointermove', moveDrag);
        state.drag = null;
        await saveVisiblePositions();
        status('Posición actualizada.');
    }

    function autoArrange() {
        const equipment = filteredEquipment();
        if (!equipment.length) {
            status('No hay equipos visibles para organizar.');
            return;
        }

        const ids = new Set(equipment.map(item => Number(item.id_equipo)));
        const links = filteredLinks(ids);
        const adjacency = new Map(equipment.map(item => [Number(item.id_equipo), new Set()]));
        links.forEach(link => {
            const a = Number(link.id_equipo_origen);
            const b = Number(link.id_equipo_destino);
            adjacency.get(a)?.add(b);
            adjacency.get(b)?.add(a);
        });

        const byId = new Map(equipment.map(item => [Number(item.id_equipo), item]));
        const remaining = new Set(ids);
        const components = [];

        while (remaining.size) {
            const root = [...remaining].sort((a, b) =>
                adjacency.get(b).size - adjacency.get(a).size ||
                String(byId.get(a).nombre_mostrar).localeCompare(String(byId.get(b).nombre_mostrar), 'es')
            )[0];
            const queue = [root];
            const visited = new Set([root]);
            const layers = [];

            while (queue.length) {
                const currentLayer = [];
                const nextQueue = [];
                queue.forEach(id => {
                    currentLayer.push(id);
                    remaining.delete(id);
                    [...adjacency.get(id)].sort((a, b) =>
                        String(byId.get(a).nombre_mostrar).localeCompare(String(byId.get(b).nombre_mostrar), 'es')
                    ).forEach(peer => {
                        if (!visited.has(peer)) {
                            visited.add(peer);
                            nextQueue.push(peer);
                        }
                    });
                });
                layers.push(currentLayer);
                queue.splice(0, queue.length, ...nextQueue);
            }
            components.push(layers);
        }

        let componentTop = 55;
        components.forEach(layers => {
            const maxLayerSize = Math.max(...layers.map(layer => layer.length));
            layers.forEach((layer, layerIndex) => {
                layer.forEach((id, rowIndex) => {
                    const node = nodesBox.querySelector('.topo-node[data-id="' + id + '"]');
                    if (!node) return;
                    const y = componentTop + rowIndex * 170;
                    const x = 55 + layerIndex * 300;
                    node.style.left = x + 'px';
                    node.style.top = y + 'px';
                });
            });
            componentTop += Math.max(1, maxLayerSize) * 170 + 100;
        });

        sizeStage(equipment);
        drawLinks(links);
        saveVisiblePositions().then(() => status('Equipos organizados y distribución guardada.'));
    }

    async function saveVisiblePositions() {
        const positions = [...nodesBox.querySelectorAll('.topo-node')].map(node => ({
            id_equipo: Number(node.dataset.id),
            x: Math.round(parseFloat(node.style.left) || 0),
            y: Math.round(parseFloat(node.style.top) || 0)
        }));
        if (!positions.length) return;

        try {
            const response = await fetch('api/guardar_posiciones_topologia.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ posiciones: positions })
            });
            const result = await response.json();
            if (!response.ok || result.estado !== 'ok') {
                throw new Error(result.mensaje || 'No se pudo guardar la distribución.');
            }
            positions.forEach(position => {
                const equipment = state.equipos.find(item => Number(item.id_equipo) === position.id_equipo);
                if (equipment) {
                    equipment.posicion_x = position.x;
                    equipment.posicion_y = position.y;
                }
            });
        } catch (error) {
            status('No se pudo guardar la distribución: ' + error.message);
            throw error;
        }
    }

    function setZoom(nextZoom) {
        state.zoom = Math.min(1.5, Math.max(.55, nextZoom));
        nodesBox.style.transform = 'scale(' + state.zoom + ')';
        nodesBox.style.transformOrigin = 'top left';
        linksSvg.style.transform = 'scale(' + state.zoom + ')';
        linksSvg.style.transformOrigin = 'top left';
        $('#btnZoomReset').textContent = Math.round(state.zoom * 100) + '%';
    }

    function centerMap() {
        viewport.scrollTo({
            left: Math.max(0, (stage.scrollWidth - viewport.clientWidth) / 2),
            top: Math.max(0, (stage.scrollHeight - viewport.clientHeight) / 2),
            behavior: 'smooth'
        });
    }

    function openRegistration() {
        $('#registrationOverlay').hidden = false;
        const frame = $('#registrationFrame');
        if (frame.src === 'about:blank' || !frame.src.includes('equipos_agregar.php')) {
            frame.src = 'equipos_agregar.php?embed=1';
        }
        document.body.classList.add('modal-open');
    }

    function closeRegistration(reload = false) {
        $('#registrationOverlay').hidden = true;
        document.body.classList.remove('modal-open');
        if (reload) {
            $('#registrationFrame').src = 'about:blank';
            loadTopology();
        }
    }

    function exportSvg() {
        const equipment = filteredEquipment();
        const ids = new Set(equipment.map(item => Number(item.id_equipo)));
        const links = filteredLinks(ids);
        const livePosition = new Map([...nodesBox.querySelectorAll('.topo-node')].map(node => [
            Number(node.dataset.id),
            { x: parseFloat(node.style.left) || 0, y: parseFloat(node.style.top) || 0 }
        ]));
        const width = Math.max(1200, ...equipment.map(item => (livePosition.get(Number(item.id_equipo))?.x || 0) + 330));
        const height = Math.max(700, ...equipment.map(item => (livePosition.get(Number(item.id_equipo))?.y || 0) + 190));
        const nodeById = new Map(equipment.map(item => [Number(item.id_equipo), item]));
        const nodeMarkup = equipment.map(item => {
            const live = livePosition.get(Number(item.id_equipo)) || { x: Number(item.posicion_x) || 0, y: Number(item.posicion_y) || 0 };
            const x = live.x;
            const y = live.y;
            const title = escapeHtml(item.nombre_mostrar);
            const meta = escapeHtml([item.nombre_tipo, item.direccion_ip].filter(Boolean).join(' · '));
            return '<g transform="translate(' + x + ' ' + y + ')">' +
                '<rect width="238" height="112" rx="12" fill="#142239" stroke="#3b536f"/>' +
                '<text x="14" y="30" fill="#f1f7ff" font-size="14" font-weight="700">' + title + '</text>' +
                '<text x="14" y="53" fill="#a9bdd6" font-size="10">' + meta + '</text>' +
                '<text x="14" y="83" fill="#67e8f9" font-size="10">' +
                    Number(item.total_puertos || 0) + ' puertos · ' + Number(item.puertos_enlazados || 0) + ' enlazados</text>' +
                '</g>';
        }).join('');

        const edgeMarkup = links.map((link, index) => {
            const a = nodeById.get(Number(link.id_equipo_origen));
            const b = nodeById.get(Number(link.id_equipo_destino));
            if (!a || !b) return '';
            const pa = livePosition.get(Number(a.id_equipo)) || { x: Number(a.posicion_x) || 0, y: Number(a.posicion_y) || 0 };
            const pb = livePosition.get(Number(b.id_equipo)) || { x: Number(b.posicion_x) || 0, y: Number(b.posicion_y) || 0 };
            let pathData;
            if (Math.abs(pa.x - pb.x) < 80) {
                const upper = pa.y <= pb.y ? pa : pb;
                const lower = upper === pa ? pb : pa;
                const laneX = Math.max(pa.x, pb.x) + 270 + (index % 4) * 14;
                pathData = 'M ' + (upper.x + 119) + ' ' + (upper.y + 112) +
                    ' H ' + laneX + ' V ' + lower.y + ' H ' + (lower.x + 119);
            } else {
                const aIsLeft = pa.x < pb.x;
                const x1 = aIsLeft ? pa.x + 238 : pa.x;
                const x2 = aIsLeft ? pb.x : pb.x + 238;
                const y1 = pa.y + 56;
                const y2 = pb.y + 56;
                const mid = (x1 + x2) / 2;
                pathData = 'M ' + x1 + ' ' + y1 + ' H ' + mid + ' V ' + y2 + ' H ' + x2;
            }
            const fiber = String(link.medio_origen || '').toUpperCase().includes('FIBRA');
            const color = fiber ? '#38bdf8' : '#fb7185';
            const dash = fiber ? '' : ' stroke-dasharray="8 6"';
            return '<path d="' + pathData +
                '" fill="none" stroke="' + color + '" stroke-width="3"' + dash + '/>';
        }).join('');

        const svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' + width + '" height="' + height +
            '" viewBox="0 0 ' + width + ' ' + height + '">' +
            '<rect width="100%" height="100%" fill="#080f1d"/>' +
            '<defs><pattern id="grid" width="24" height="24" patternUnits="userSpaceOnUse">' +
            '<circle cx="1" cy="1" r="1" fill="#26364e"/></pattern></defs>' +
            '<rect width="100%" height="100%" fill="url(#grid)"/>' + edgeMarkup + nodeMarkup + '</svg>';
        const blob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = 'netmap-topologia.svg';
        anchor.click();
        URL.revokeObjectURL(url);
        status('Imagen SVG preparada.');
    }

    $('#btnNewEquipment').addEventListener('click', openRegistration);
    $('#btnEmptyRegister').addEventListener('click', openRegistration);
    $('#btnCloseRegistration').addEventListener('click', () => closeRegistration(false));
    $('#registrationOverlay').addEventListener('click', event => {
        if (event.target === $('#registrationOverlay')) closeRegistration(false);
    });
    $('#btnAutoArrange').addEventListener('click', autoArrange);
    $('#btnSaveLayout').addEventListener('click', () =>
        saveVisiblePositions().then(() => status('Distribución guardada.')).catch(() => {})
    );
    $('#btnZoomIn').addEventListener('click', () => setZoom(state.zoom + .1));
    $('#btnZoomOut').addEventListener('click', () => setZoom(state.zoom - .1));
    $('#btnZoomReset').addEventListener('click', () => setZoom(1));
    $('#btnCenter').addEventListener('click', centerMap);
    $('#btnRefresh').addEventListener('click', loadTopology);
    $('#btnExportSvg').addEventListener('click', exportSvg);
    $('#btnExportPdf').addEventListener('click', () => window.print());
    $('#filterSite').addEventListener('change', () => {
        populateFilters();
        renderTopology();
    });
    $('#filterFloor').addEventListener('change', renderTopology);

    window.addEventListener('message', event => {
        if (event.origin !== window.location.origin) return;
        if (event.data?.type === 'netmap:registro-guardado') {
            closeRegistration(true);
            status(event.data.mensaje || 'Registro guardado. Inventario actualizado.');
        }
    });

    window.addEventListener('resize', () => {
        sizeStage(filteredEquipment());
        drawLinks(filteredLinks(new Set(filteredEquipment().map(item => Number(item.id_equipo)))));
    });

    loadTopology();
})();
