<?php require __DIR__.'/layout_top.php'; require __DIR__.'/util.php'; require_login_page(); ?>
<h2>Solicitar Prestamo</h2>
<div class="grid">
  <div class="card">
    <div id="table"></div>
    <div id="detail" class="toast" style="display:none"></div>
    <div class="toast"><button class="btn" id="bagBtn">Mochila (<span id="bagCount">0</span>)</button></div>
    <div class="err" id="err"></div><div class="ok" id="ok"></div>
  </div>
</div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let bag=[]; let cacheBooks=[];
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token; load();})();
function load(){
  fetch(`${API}/books.php`,{credentials:'include'}).then(r=>r.json()).then(rows=>{
    cacheBooks = rows;
    const t = ['<div class="grid">']; rows.forEach(b=>{
      const dot = b.status==='Disponible'?'🟢':'🔴';
      t.push(`<div class="card" data-test="book-row"><div><strong>${b.title}</strong> - ${b.author||''} - ${dot}</div><div class="note">Disponibilidad: ${b.status}</div><div class="row"><button class="btn" data-id="${b.id}" ${b.status!=='Disponible'?'disabled':''} data-test="add-to-bag">Agregar a mochila</button><button class="btn secondary" data-view="${b.id}" data-test="view-book">Ver detalle</button></div></div>`);
    }); t.push('</div>'); table.innerHTML=t.join('');
    document.querySelectorAll('[data-test=add-to-bag]').forEach(btn=> btn.onclick=()=>addToBag(+btn.dataset.id));
    document.querySelectorAll('[data-view]').forEach(btn=> btn.onclick=()=>showDetail(+btn.dataset.view));
  });
}
function showDetail(id){
  const b = cacheBooks.find(x=>x.id==id); if(!b) return;
  detail.style.display='block';
  detail.innerHTML = `<div><strong>${b.title}</strong></div><div class="note">${b.author||''}</div><div>${b.description||'Sin descripcion'}</div><div class="note">Estado: ${b.status}</div>`;
  if(b.status==='Disponible'){ ok.textContent='Libro disponible. Puedes agregarlo.'; }
}
function addToBag(id){
  if(bag.length>=3){ err.textContent='Ha alcanzado el limite de 3 libros'; return; }
  if(!bag.includes(id)) bag.push(id); bagCount.textContent=bag.length; ok.textContent='¡El libro ha sido anadido a tu mochila de libros!';
}
bagBtn.onclick=async()=>{
  if(bag.length===0){ err.textContent='USTED NO CUENTA CON LIBROS EN LA MOCHILA'; return; }
  try{
    const r=await fetch(`${API}/loans.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({books:bag,days:7})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Prestamo confirmado'; bag=[]; bagCount.textContent='0'; load();
  }catch(e){ err.textContent='Error de conexion'; }
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
