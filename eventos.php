<?php require __DIR__.'/layout_top.php'; require __DIR__.'/util.php'; require_login_page(); ?>
<h2>Eventos y Programas</h2>
<div class="row">
  <input id="q" placeholder="Buscar... (p. ej., Taller de Estudio)" data-test="search">
  <select id="cat" data-test="filter-category"><option value="">Todas</option><option>Talleres</option><option>Seminarios</option><option>Feria</option></select>
  <button class="btn" id="apply">Aplicar</button>
</div>
<div id="list" style="margin-top:12px"></div>
<div class="toast" id="noevents" style="display:none">No hay eventos programados para esta fecha.</div>
<div id="cal" class="cal" style="margin-top:12px"></div>
<div class="toast" id="empty" style="display:none">No hay eventos o programas disponibles en este momento. Por favor, revisa más tarde.</div>
<div class="toast" id="emptySearch" style="display:none">No se encontraron resultados para su búsqueda</div>
<script>
const API='<?= dynamic_base() ?>/api'; let events=[];
async function load(q='',cat=''){ const r=await fetch(`${API}/events.php?q=${encodeURIComponent(q)}&category=${encodeURIComponent(cat)}`,{credentials:'include'}); events=await r.json(); render(); }
function render(){
  list.innerHTML=''; empty.style.display='none'; emptySearch.style.display='none';
  if(!events || events.length===0) { empty.style.display='block'; }
  events.forEach(e=>{ const div=document.createElement('div'); div.className='card'; div.setAttribute('data-test','event-item');
    div.innerHTML=`<strong>${e.title}</strong> — ${e.category} — ${e.date} ${e.time||''} — Cupos: ${e.seats??'N/D'} — <a href='<?= dynamic_base() ?>/evento.php?id=${e.id}'>Ver detalles</a>`; list.appendChild(div); });
  cal.innerHTML=''; const future=[...Array(21)].map((_,i)=>{const d=new Date(); d.setDate(d.getDate()+i); return d;});
  future.forEach(d=>{ const iso=d.toISOString().slice(0,10); const has = events.some(e=>e.date===iso);
    const cell=document.createElement('div'); cell.className='day'; cell.setAttribute('data-test','calendar-day'); cell.setAttribute('data-has-events', String(has)); cell.textContent=iso;
    cell.onclick=()=>{ noevents.style.display = has? 'none':'block'; };
    cal.appendChild(cell);
  });
}
document.getElementById('apply').onclick=async()=>{
  await load(q.value,cat.value);
  if (events.length===0) { document.getElementById('emptySearch').style.display='block'; }
};
load();
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
