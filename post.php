<?php require __DIR__.'/layout_top.php'; require __DIR__.'/util.php'; require_login_page(); $id=(int)($_GET['id']??0); ?>
<div id="post"></div>
<div class="card">
  <textarea id="comment" data-test="comment-input" placeholder="Escribe un comentario..."></textarea>
  <div class="row"><button class="btn" id="btnComment" data-test="publish-comment">Publicar</button><div class="err" id="err"></div></div>
</div>
<div id="comments" class="grid"></div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; (async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token;})();
fetch(`${API}/post.php?id=<?= $id ?>`,{credentials:'include'}).then(r=>r.json()).then(p=>{
  post.innerHTML = `<div class="card"><h2>${p.title}</h2><div class="note">${p.pub_date} — ${p.location}</div><img src="${p.image_url}" style="max-width:100%;border-radius:10px"><p>${p.body}</p></div>`;
  renderComments(p.comments||[]);
});
function renderComments(rows){ comments.innerHTML = rows.map(c=>`<div class="card">${c.text} — <span class="note">${c.created_at}</span></div>`).join(''); }
btnComment.onclick=async()=>{
  err.textContent=''; const text=comment.value.trim(); if(!text){ err.textContent='El comentario no puede estar vacío'; return; }
  const r=await fetch(`${API}/comment.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({post_id:<?= $id ?>, text})});
  const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; } else { comment.value=''; renderComments(j.comments); }
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
