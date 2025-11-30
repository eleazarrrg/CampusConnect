<?php require __DIR__.'/layout_top.php'; require __DIR__.'/util.php'; require_login_page(); ?>
<h2>Préstamos activos</h2>
<div id="list" class="grid"></div>
<div class="card" data-test="loan-edit">
  <div>Editar préstamo</div>
  <div class="row">
    <input type="date" id="pickup" placeholder="Fecha entrega">
    <input type="date" id="due" placeholder="Fecha límite">
    <input type="date" id="ret" placeholder="Fecha devolución" data-test="loan-date-return">
  </div>
  <div class="row">
    <button class="btn" id="save" data-test="save-loan">Guardar</button>
    <button class="btn warn" id="finalize">Finalizar</button>
    <button class="btn err" id="delete" data-test="delete-loan" style="display:none">Eliminar (proceso)</button>
  </div>
</div>
<div class="err" id="err" data-test="error"></div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let currentId=null, currentStatus=null;
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token; load();})();
async function load(){
  const r=await fetch(`${API}/loans_list.php`,{credentials:'include'}); const list=await r.json(); const c=document.getElementById('list'); c.innerHTML='';
  list.forEach(l=>{ const div=document.createElement('div'); div.className='card'; div.innerHTML=`<strong>${l.title}</strong> — estado: ${l.status} — id:${l.id} <button class='btn' data-id='${l.id}' data-status='${l.status}'>✏️</button>`; c.appendChild(div); });
  [...c.querySelectorAll('button[data-id]')].forEach(b=> b.onclick = ()=> selectLoan(b.getAttribute('data-id'), b.getAttribute('data-status')) );
}
function selectLoan(id,status){ currentId=id; currentStatus=status; document.getElementById('delete').style.display = (status==='proceso') ? 'inline-block' : 'none'; }
save.onclick=async()=>{
  const today = new Date().toISOString().slice(0,10);
  if (ret.value && ret.value < today){ err.textContent='La fecha no puede ser pasada'; return; }
  err.textContent='';
  const r=await fetch(`${API}/loan_update.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId,status:'activo',pickup:pickup.value,due:due.value,ret:ret.value})});
  const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else load();
};
finalize.onclick=async()=>{
  const r=await fetch(`${API}/loan_update.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId,status:'finalizado'})}); const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else load();
};
delete.onclick=async()=>{
  const r=await fetch(`${API}/loan_delete.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',
    body:JSON.stringify({id:currentId})}); const j=await r.json(); if(!r.ok) err.textContent=j.error||'Error'; else load();
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
