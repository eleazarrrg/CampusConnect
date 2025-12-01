<?php require __DIR__.'/../../layout_top.php'; require __DIR__.'/../../util.php'; require_login_page(); $id=(int)($_GET['id']??0); ?>
<div id="ev"></div>
<script>
fetch('<?= dynamic_base() ?>/api/event.php?id=<?= $id ?>',{credentials:'include'}).then(r=>r.json()).then(e=>{
  ev.innerHTML = `<div class="card"><h2>${e.title}</h2><div class="note">${e.date} ${e.time||''} - ${e.place||''}</div><p>${e.description||''}</p><div class="note">Cupos: ${e.seats??'N/D'}</div><div class="note">Requisitos: ${e.requirements||'-'}</div></div>`;
});
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>
