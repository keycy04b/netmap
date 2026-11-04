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
})();