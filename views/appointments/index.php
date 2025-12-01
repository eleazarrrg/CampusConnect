<?php require __DIR__.'/../../layout_top.php'; require __DIR__.'/../../util.php'; require_login_page(); ?>
<h2>Citas</h2>
<div class="grid">
  <div class="card">
    <div class="row">
      <div class="note">Seleccione fecha (proximos 21 dias) - verde = disponible</div>
      <div id="cal" class="cal"></div>
      <div id="popup" class="toast" style="display:none">
        <div><strong>Gestion de Citas</strong></div>
        <div class="note" id="selectedLabel"></div>
        <div id="slots" class="row"></div>
        <div class="row">
          <button class="btn" id="btnConfirm" data-test="confirm-appointment">Agendar</button>
          <button class="btn secondary" id="btnReschedule" style="display:none" data-test="reschedule-appointment">Modificar</button>
          <button class="btn secondary" id="btnCancelPopup">Cerrar</button>
        </div>
      </div>
    </div>
    <div class="toast" id="activeBox" style="display:none"></div>
    <div class="row">
      <button class="btn warn" id="btnCancel" style="display:none" data-test="cancel-appointment">Cancelar cita</button>
    </div>
    <div class="err" id="err"></div>
    <div class="ok" id="ok"></div>
    <div class="toast" id="noevents" style="display:none"></div>
  </div>
</div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let selectedDate='', selectedTime='', activeId=null;
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token; await loadActive(); await renderDays();})();

async function loadActive(){
  const r=await fetch(`${API}/appointments.php`,{credentials:'include'}); const j=await r.json(); const a=j.appointment;
  if(a){ activeId=a.id; activeBox.style.display='block'; activeBox.textContent=`Cita activa: ${a.date} ${a.time?.slice(0,5)||''}`; btnCancel.style.display='inline-block'; btnReschedule.style.display='inline-block'; }
  else { activeId=null; activeBox.style.display='none'; btnCancel.style.display='none'; btnReschedule.style.display='none'; }
}

async function renderDays(){
  const cal = document.getElementById('cal'); cal.innerHTML='';
  const r=await fetch(`${API}/appointments_days.php`,{credentials:'include'}); const days=await r.json();
  days.forEach(d=>{ const cell=document.createElement('div'); cell.className='day'; cell.dataset.available=String(d.available); cell.textContent=d.date; cell.setAttribute('data-test','calendar-day');
    if(d.available) cell.style.background='#d1fae5'; else cell.style.opacity='.5';
    cell.onclick=()=> onDay(d.date,d.available);
    cal.appendChild(cell);
  });
}

async function onDay(date,isAvailable){
  err.textContent=''; ok.textContent=''; selectedDate=date; selectedTime=''; selectedLabel.textContent=`Fecha seleccionada: ${date}`;
  if(!isAvailable){ noevents.style.display='block'; return; } else noevents.style.display='none';
  try{
    const r=await fetch(`${API}/appointments_availability.php?date=${date}`,{credentials:'include'});
    const list = await r.json();
    if(!r.ok) { err.textContent=list.error||'Error'; return; }
    slots.innerHTML='';
    list.forEach(it=>{ const b=document.createElement('button'); b.className='btn'; b.textContent=(it.time_label||it.time)+(it.available?'':' (ocupado)'); b.disabled=!it.available; b.onclick=()=>{selectedTime=it.time;}; b.setAttribute('data-test','time-option'); slots.appendChild(b); });
    popup.style.display='block';
  }catch(e){ err.textContent='Error de conexion'; }
}

btnCancelPopup.onclick=()=>{ popup.style.display='none'; }

btnConfirm.onclick=async()=>{
  if(!selectedDate||!selectedTime){ err.textContent='Seleccione dia y hora'; return; }
  try{
    const r=await fetch(`${API}/appointments.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({date:selectedDate,time:selectedTime})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Cita creada'; popup.style.display='none'; await loadActive(); await renderDays();
  }catch(e){ err.textContent='Error de conexion'; }
};

btnReschedule.onclick=async()=>{
  if(!activeId){ err.textContent='No hay cita activa'; return; }
  if(!selectedDate||!selectedTime){ err.textContent='Seleccione dia y hora'; return; }
  try{
    const r=await fetch(`${API}/appointments_update.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({id:activeId,date:selectedDate,time:selectedTime})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Cita modificada'; popup.style.display='none'; await loadActive(); await renderDays();
  }catch(e){ err.textContent='Error de conexion'; }
};

btnCancel.onclick=async()=>{
  if(!activeId) return;
  try{
    const r=await fetch(`${API}/appointments_cancel.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({id:activeId})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Cita cancelada'; activeId=null; popup.style.display='none'; await loadActive(); await renderDays();
  }catch(e){ err.textContent='Error de conexion'; }
};
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>
