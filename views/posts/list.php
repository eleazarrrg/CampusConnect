<?php require __DIR__.'/../../layout_top.php'; require_login_page(); ?>
<h2>Publicaciones</h2>
<div id="list" class="grid"></div>
<script>
fetch('<?= dynamic_base() ?>/api/posts.json',{credentials:'include'}).then(r=>r.json()).then(rows=>{
  const out = rows.map(p=>`<div class="card"><div><strong>${p.title}</strong> - ${p.pub_date} - ${p.location}</div><img src="${p.image_url}" alt="" style="max-width:100%;border-radius:10px"><div class="row"><a class="btn" data-test="view-details" href="post.php?id=${p.id}">Ver detalles</a></div></div>`).join('');
  list.innerHTML = out;
});
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>

