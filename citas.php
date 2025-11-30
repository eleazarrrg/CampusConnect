<?php require __DIR__.'/layout_top.php'; require __DIR__.'/util.php'; require_login_page(); ?>
<h2>Citas</h2>
<div class="grid">
  <div class="card">
    <div class="row">
      <div class="note">Seleccione fecha (próximos 21 días)</div>
      <div id="cal" class="cal"></div>
      <div id="popup" class="toast" style="display:none">
        <div><strong>Gestión de Citas</strong></div>
        <div id="slots" class="row"></div>
        <div class="row">
          <button class="btn" id="btnConfirm" data-test="confirm-appointment">Agendar</button>
          <button class="btn secondary" id="btnCancelPopup">Cancelar</button>
        </div>
      </div>
    </div>
    <div class="err" id="err"></div>
    <div class="ok" id="ok"></div>
    <div class="toast" id="noevents" style="display:none"></div>
  </div>
</div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let selectedDate='', selectedTime='';
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token; render();})();

function render(){
  const cal = document.getElementById('cal'); cal.innerHTML='';
  const future=[...Array(21)].map((_,i)=>{const d=new Date(); d.setDate(d.getDate()+i); return d;});
  future.forEach(d=>{ const iso=d.toISOString().slice(0,10); const cell=document.createElement('div'); cell.className='day'; cell.dataset.available='true'; cell.textContent=iso; cell.setAttribute('data-test','calendar-day'); cell.onclick=()=>onDay(iso); cal.appendChild(cell); });
}

async function onDay(date){
  err.textContent=''; ok.textContent=''; selectedDate=date; selectedTime='';
  try{
    const r=await fetch(`${API}/appointments_availability.php?date=${date}`,{credentials:'include'});
    const list = await r.json();
    if(!r.ok) { err.textContent=list.error||'Error'; return; }
    slots.innerHTML='';
    list.forEach(it=>{ const b=document.createElement('button'); b.className='btn'; b.textContent=(it.time_label||it.time)+(it.available?'':' (ocupado)'); b.disabled=!it.available; b.onclick=()=>{selectedTime=it.time;}; b.setAttribute('data-test','time-option'); slots.appendChild(b); });
    popup.style.display='block';
  }catch(e){ err.textContent='Error de conexión'; }
}

btnCancelPopup.onclick=()=>{ popup.style.display='none'; }

btnConfirm.onclick=async()=>{
  if(!selectedDate||!selectedTime){ err.textContent='Seleccione día y hora'; return; }
  try{
    const r=await fetch(`${API}/appointments.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({date:selectedDate,time:selectedTime})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Cita creada'; popup.style.display='none';
  }catch(e){ err.textContent='Error de conexión'; }
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
