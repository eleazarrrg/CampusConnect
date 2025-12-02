<?php require __DIR__.'/../../layout_top.php'; require_login_page(); ?>
<h2>Prestamos activos</h2>
<div id="list" class="grid"></div>
<div class="card" data-test="loan-edit">
  <div>Editar prestamo</div>
  <div class="row">
    <input type="date" id="pickup" placeholder="Fecha entrega">
    <input type="date" id="due" placeholder="Fecha limite">
    <input type="date" id="ret" placeholder="Fecha devolucion" data-test="loan-date-return">
    <select id="statusSel">
      <option value="proceso">Proceso</option>
      <option value="activo">Activo</option>
      <option value="finalizado">Finalizado</option>
      <option value="cancelado">Cancelado</option>
    </select>
  </div>
  <div class="row">
    <button class="btn" id="save" data-test="save-loan">Guardar</button>
    <button class="btn warn" id="finalize">Finalizar</button>
    <button class="btn err" id="delete" data-test="delete-loan" style="display:none">Eliminar (proceso)</button>
  </div>
</div>
<div class="err" id="err" data-test="error"></div>
<div class="ok" id="ok"></div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let currentId=null, currentStatus=null;
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token; load();})();

async function load(){
  const r=await fetch(`${API}/loans_list.php`,{credentials:'include'}); const list=await r.json(); const c=document.getElementById('list'); c.innerHTML='';
  if(!Array.isArray(list) || list.length===0){ c.innerHTML='<div class="toast">No hay prestamos activos</div>'; return; }
  list.forEach(l=>{
    const div=document.createElement('div'); div.className='card';
    div.innerHTML=`<strong>${l.title}</strong> - estado: ${l.status} - id:${l.id} <span class="note">${l.request_date||''}</span>
      <div class="note">Entrega: ${l.pickup_date||'--'} | Limite: ${l.due_date||'--'} | Devolucion: ${l.return_date||'--'}</div>
      <button class='btn' data-id='${l.id}' data-status='${l.status}' data-pickup='${l.pickup_date||''}' data-due='${l.due_date||''}' data-ret='${l.return_date||''}'>âœï¸ Editar</button>`;
    c.appendChild(div);
  });
  [...c.querySelectorAll('button[data-id]')].forEach(b=> b.onclick = ()=> selectLoan(b.dataset));
}

function selectLoan(data){
  currentId=data.id; currentStatus=data.status; statusSel.value=data.status||'proceso';
  pickup.value=data.pickup||''; due.value=data.due||''; ret.value=data.ret||'';
  document.getElementById('delete').style.display = (data.status==='proceso') ? 'inline-block' : 'none';
  err.textContent=''; ok.textContent='';
}

function validateDates(){
  const today = new Date().toISOString().slice(0,10);
  if (pickup.value && pickup.value < today) return 'Fecha de entrega pasada';
  if (due.value && due.value < today) return 'Fecha limite pasada';
  if (ret.value && ret.value < today) return 'Fecha de devolucion pasada';
  return '';
}

save.onclick=async()=>{
  if(!currentId){ err.textContent='Seleccione un prestamo'; return; }
  const dateError = validateDates(); if(dateError){ err.textContent=dateError; return; }
  err.textContent=''; ok.textContent='';
  const r=await fetch(`${API}/loan_update.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId,status:statusSel.value,pickup:pickup.value,due:due.value,ret:ret.value})});
  const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else { ok.textContent='Prestamo actualizado'; load(); }
};

finalize.onclick=async()=>{
  if(!currentId){ err.textContent='Seleccione un prestamo'; return; }
  const r=await fetch(`${API}/loan_update.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId,status:'finalizado'})}); const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else { ok.textContent='Prestamo finalizado'; load(); }
};

delete.onclick=async()=>{
  if(!currentId){ err.textContent='Seleccione un prestamo'; return; }
  const r=await fetch(`${API}/loan_delete.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId})}); const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else { ok.textContent='Prestamo eliminado'; load(); }
};
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>

