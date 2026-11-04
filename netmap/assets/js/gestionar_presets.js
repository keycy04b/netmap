(() => {
'use strict';
const form=document.getElementById('presetForm');
if(!form)return;
const rows=document.getElementById('presetRows');
const message=document.getElementById('presetMessage');
async function load(){
 const r=await fetch('api/gestionar_presets_puertos.php',{headers:{Accept:'application/json'}});
 const d=await r.json();
 if(!r.ok||d.estado==='error')throw new Error(d.mensaje||'No se pudieron cargar presets.');
 rows.innerHTML=d.presets.length?d.presets.map(p=>`<tr><td>${esc(p.nombre_tipo)}</td><td>${esc(p.alcance)}</td><td>${esc(p.medio)}</td><td>${p.cantidad}</td><td>${p.secciones}</td><td><button class="delete" data-id="${p.id_sugerencia}">Eliminar</button></td></tr>`).join(''):'<tr><td colspan="6">No hay presets configurados.</td></tr>';
}
function esc(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
form.addEventListener('submit',async e=>{
 e.preventDefault();const d=Object.fromEntries(new FormData(form));
 try{const r=await fetch('api/gestionar_presets_puertos.php',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(d)});const j=await r.json();if(!r.ok||j.estado!=='ok')throw new Error(j.mensaje||'No se pudo guardar.');message.textContent=j.mensaje;await load();}
 catch(err){message.textContent=err.message;}
});
rows.addEventListener('click',async e=>{
 const b=e.target.closest('button[data-id]');if(!b)return;
 if(!confirm('¿Eliminar este preset?'))return;
 try{const r=await fetch('api/gestionar_presets_puertos.php',{method:'DELETE',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({id_sugerencia:Number(b.dataset.id)})});const j=await r.json();if(!r.ok||j.estado!=='ok')throw new Error(j.mensaje||'No se pudo eliminar.');message.textContent=j.mensaje;await load();}
 catch(err){message.textContent=err.message;}
});
load().catch(e=>{rows.innerHTML=`<tr><td colspan="6">${esc(e.message)}</td></tr>`;});

const typeForm=document.getElementById('typeForm');
if(typeForm){
 const typeRows=document.getElementById('typeRows'),typeMessage=document.getElementById('typeMessage');
 async function loadTypes(){
  const r=await fetch('api/gestionar_tipos_equipo.php',{headers:{Accept:'application/json'}});
  const d=await r.json();if(!r.ok||!Array.isArray(d.tipos))throw new Error(d.mensaje||'No se pudieron cargar tipos.');
  typeRows.innerHTML=d.tipos.map(t=>`<tr><td>${esc(t.nombre_tipo)}</td><td>${esc(t.ruta_icono||'—')}</td><td><button data-edit="${t.id_tipo_equipo}" data-name="${esc(t.nombre_tipo)}" data-icon="${esc(t.ruta_icono||'')}">Editar</button> <button class="delete" data-delete="${t.id_tipo_equipo}">Eliminar</button></td></tr>`).join('');
 }
 typeForm.addEventListener('submit',async e=>{
  e.preventDefault();const data=Object.fromEntries(new FormData(typeForm));
  try{const r=await fetch('api/gestionar_tipos_equipo.php',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(data)});const d=await r.json();if(!r.ok||d.estado!=='ok')throw new Error(d.mensaje||'No se pudo guardar.');typeMessage.textContent=d.mensaje;typeForm.reset();await loadTypes();}
  catch(err){typeMessage.textContent=err.message;}
 });
 typeRows.addEventListener('click',async e=>{
  const edit=e.target.closest('[data-edit]');
  if(edit){const name=prompt('Nombre del tipo:',edit.dataset.name);if(name===null)return;const icon=prompt('Ruta del icono:',edit.dataset.icon);if(icon===null)return;
   try{const r=await fetch('api/gestionar_tipos_equipo.php',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({id_tipo_equipo:Number(edit.dataset.edit),nombre_tipo:name,ruta_icono:icon})});const d=await r.json();if(!r.ok||d.estado!=='ok')throw new Error(d.mensaje||'No se pudo actualizar.');typeMessage.textContent=d.mensaje;await loadTypes();}catch(err){typeMessage.textContent=err.message;}return;}
  const del=e.target.closest('[data-delete]');if(!del)return;
  if(!confirm('¿Eliminar este tipo? Solo será posible si ningún equipo lo utiliza.'))return;
  try{const r=await fetch('api/gestionar_tipos_equipo.php',{method:'DELETE',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({id_tipo_equipo:Number(del.dataset.delete)})});const d=await r.json();if(!r.ok||d.estado!=='ok')throw new Error(d.mensaje||'No se pudo eliminar.');typeMessage.textContent=d.mensaje;await loadTypes();}catch(err){typeMessage.textContent=err.message;}
 });
 loadTypes().catch(e=>{typeRows.innerHTML=`<tr><td colspan="3">${esc(e.message)}</td></tr>`;});
}
})();