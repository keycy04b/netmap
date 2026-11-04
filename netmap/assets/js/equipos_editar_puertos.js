(() => {
  'use strict';
  const $ = (s, root=document) => root.querySelector(s);
  const data = window.NETMAP_EDIT_DATA || {equipos:[],tipos:[],sedes:[],pisos:[]};
  const select = $('#equipoSelect'), editor = $('#editor'), form = $('#formEquipo');
  const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const options = (items, id, label, selected='') => items.map(x => `<option value="${esc(x[id])}" ${String(x[id])===String(selected)?'selected':''}>${esc(x[label])}</option>`).join('');
  let currentId = null, ports = [];
  function fillCatalogs(item) {
    $('[name="id_tipo_equipo"]').innerHTML = options(data.tipos,'id_tipo_equipo','nombre_tipo',item.id_tipo_equipo);
    $('#sede').innerHTML = options(data.sedes,'id_sede','nombre_sede',item.id_sede);
    fillFloors(item.id_sede,item.id_piso);
  }
  function fillFloors(siteId, selected='') {
    const floors=data.pisos.filter(p=>String(p.id_sede)===String(siteId));
    $('#piso').innerHTML='<option value="">Seleccionar piso…</option>'+options(floors,'id_piso','numero_piso',selected);
  }
  $('#sede').addEventListener('change',()=>fillFloors($('#sede').value));
  select.addEventListener('change',()=>loadEquipment(select.value));
  async function loadEquipment(id) {
    currentId=Number(id)||null; editor.hidden=!currentId; if(!currentId)return;
    const item=data.equipos.find(e=>Number(e.id_equipo)===currentId); if(!item)return;
    for(const [key,val] of Object.entries(item)){const input=form.elements[key];if(input)input.value=val??'';}
    form.elements.id_equipo.value=currentId; fillCatalogs(item);
    $('#deviceIcon').src=item.icono||item.ruta_icono||'assets/img/iconos/switch.svg';
    $('#deviceIcon').onerror=()=>{$('#deviceIcon').style.display='none';}; $('#deviceIcon').style.display='block';
    await loadPorts();
  }
  async function loadPorts(){
    const body=$('#portsBody');body.innerHTML='<tr><td colspan="8">Cargando puertos…</td></tr>';
    try{const res=await fetch(`api/obtener_puertos_equipo.php?id_equipo=${currentId}`,{headers:{Accept:'application/json'}});const json=await res.json();if(!res.ok||!Array.isArray(json))throw new Error(json.mensaje||'Respuesta de puertos inválida.');ports=json;renderPorts();renderMiniPorts();}
    catch(e){body.innerHTML=`<tr><td colspan="8">${esc(e.message)}. Verifica la API de puertos.</td></tr>`;}
  }
  function stateClass(p){if(Number(p.esta_enlazado)===1||p.equipo_destino)return 'linked';const s=String(p.estado||'').toUpperCase();if(s==='RESERVADO')return 'reserved';if(s==='DANADO'||s==='DAÑADO')return 'damaged';if(s==='NO_DISPONIBLE')return 'occupied';return 'available';}
  function renderPorts(){const body=$('#portsBody');if(!ports.length){body.innerHTML='<tr><td colspan="8">Este equipo no tiene puertos registrados.</td></tr>';return;}body.innerHTML=ports.map(p=>{const cls=stateClass(p);const status=cls==='linked'?'ENLAZADO':cls==='occupied'?'OCUPADO (SIN ENLACE)':cls==='reserved'?'RESERVADO':cls==='damaged'?'DAÑADO':'DISPONIBLE';const linked=Boolean(p.equipo_destino)||Number(p.esta_enlazado)===1;return `<tr><td><b>${esc(p.numero_puerto)}</b></td><td>${esc(p.tipo_puerto||p.medio||'—')}${p.medio&&p.tipo_puerto?' · '+esc(p.medio):''}</td><td><span class="status ${cls}">${status}</span></td><td>${esc(p.equipo_destino||'—')}</td><td>${esc(p.puerto_destino||'—')}</td><td>${esc(p.fecha_creacion||'—')}</td><td>${esc(p.fecha_actualizacion||'—')}</td><td class="actions"><button data-action="state" data-id="${p.id_puerto}">Cambiar estado</button>${linked?`<button class="danger" data-action="unlink" data-id="${p.id_puerto}">Quitar enlace</button>`:''}</td></tr>`;}).join('');}
  function renderMiniPorts(){
    const box = $('#miniPorts');
    const groups = new Map();

    ports.forEach(port => {
      const groupId = Number(port.grupo_puerto) || 1;
      if (!groups.has(groupId)) groups.set(groupId, []);
      groups.get(groupId).push(port);
    });

    box.innerHTML = Array.from(groups.entries()).map(([groupId, groupPorts]) => {
      const first = groupPorts[0];
      const sectionCount = Math.max(1, Number(first.secciones_fisicas) || 1);
      const sectionSize = Math.ceil(groupPorts.length / sectionCount);
      const sections = [];

      for (let index = 0; index < sectionCount; index++) {
        const sectionPorts = groupPorts.slice(index * sectionSize, (index + 1) * sectionSize);
        if (!sectionPorts.length) continue;

        sections.push(`<div class="mini-section" aria-label="Sección física ${index + 1}">
          ${sectionPorts.map(port => `<span title="Puerto ${esc(port.numero_puerto)} · ${esc(port.estado)}" class="mini-port ${stateClass(port)}">${esc(port.numero_puerto)}</span>`).join('')}
        </div>`);
      }

      return `<div class="mini-group">
        <div class="mini-group-title">Grupo ${groupId} · ${esc(first.alcance || 'LAN')} · ${esc(first.medio || first.tipo_puerto || 'RED')}</div>
        <div class="mini-sections">${sections.join('')}</div>
      </div>`;
    }).join('');
  }
  $('#portsBody').addEventListener('click',async e=>{const b=e.target.closest('button[data-action]');if(!b)return;const id=Number(b.dataset.id);try{if(b.dataset.action==='state'){const p=ports.find(x=>Number(x.id_puerto)===id);if(!p)return;const choices=[['DISPONIBLE','Disponible'],['NO_DISPONIBLE','Ocupado'],['RESERVADO','Reservado'],['DANADO','Dañado']];const chosen=prompt('Estado nuevo:\n1 Disponible\n2 Ocupado\n3 Reservado\n4 Dañado',String(Math.max(1,choices.findIndex(x=>x[0]===p.estado)+1)));if(chosen===null)return;const opt=choices[Number(chosen)-1];if(!opt){alert('Selecciona 1, 2, 3 o 4.');return;}if((Number(p.esta_enlazado)===1||p.equipo_destino)&&opt[0]!=='NO_DISPONIBLE'){alert('Primero quita el enlace de este puerto.');return;}await post('api/cambiar_estado_puerto.php',{id_puerto:id,estado:opt[0]});}else{if(!confirm('¿Eliminar el enlace guardado y liberar ambos puertos?'))return;await post('api/eliminar_enlace_puerto.php',{id_puerto:id});}await loadPorts();}catch(err){alert(err.message);}});
  async function post(url,payload){const r=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json'},body:JSON.stringify(payload)});const d=await r.json();if(!r.ok||d.estado!=='ok')throw new Error(d.mensaje||'La operación falló.');return d;}
  form.addEventListener('input',e=>{if(e.target.name==='serial'){const pos=e.target.selectionStart;e.target.value=e.target.value.toUpperCase();e.target.setSelectionRange(pos,pos);}else if(['nombre_equipo','marca','modelo'].includes(e.target.name)&&e.target.value){const pos=e.target.selectionStart;e.target.value=e.target.value.charAt(0).toUpperCase()+e.target.value.slice(1);e.target.setSelectionRange(pos,pos);}});
  form.addEventListener('submit',async e=>{e.preventDefault();const payload=Object.fromEntries(new FormData(form));payload.id_equipo=Number(payload.id_equipo);payload.id_tipo_equipo=Number(payload.id_tipo_equipo);payload.id_piso=Number(payload.id_piso);payload.serial=String(payload.serial||'').toUpperCase();for(const k of ['nombre_equipo','marca','modelo'])if(payload[k])payload[k]=payload[k].charAt(0).toUpperCase()+payload[k].slice(1);try{const d=await post('api/actualizar_equipo_edicion.php',payload);$('#saveMessage').textContent=d.mensaje;const old=data.equipos.find(x=>Number(x.id_equipo)===currentId);Object.assign(old,payload);alert(d.mensaje);}catch(err){alert(err.message);}});
  const search=$('#searchEquipment');search.addEventListener('input',()=>{const q=search.value.trim().toLowerCase();const matches=data.equipos.filter(e=>Number(e.id_equipo)!==currentId&&[e.nombre_equipo,e.marca,e.modelo,e.serial,e.direccion_ip].some(v=>String(v||'').toLowerCase().includes(q))).slice(0,8);$('#searchResults').innerHTML=matches.map(e=>`<button class="result" data-id="${e.id_equipo}"><b>${esc(e.nombre_equipo||e.nombre_tipo)}</b><span>${esc(e.marca)} ${esc(e.modelo)} · ${esc(e.direccion_ip)}</span></button>`).join('');});
  $('#searchResults').addEventListener('click',e=>{const b=e.target.closest('[data-id]');if(!b)return;select.value=b.dataset.id;loadEquipment(b.dataset.id);});

  // Respeta el equipo enviado desde el listado mediante ?id=...
  const initialId = Number(data.equipoInicial) || 0;
  if (initialId && data.equipos.some(item => Number(item.id_equipo) === initialId)) {
    select.value = String(initialId);
    loadEquipment(initialId);
  }
})();
