<?php require __DIR__.'/../../layout_top.php'; require __DIR__.'/../../util.php'; require_login_page(); $id=(int)($_GET['id']??0); ?>
<div id="post"></div>
<div class="card">
  <textarea id="comment" data-test="comment-input" placeholder="Escribe un comentario..."></textarea>
  <div class="row"><button class="btn" id="btnComment" data-test="publish-comment">Publicar</button><div class="err" id="err"></div></div>
</div>
<div id="comments" class="grid"></div>
<script>
const API='<?= dynamic_base() ?>/api'; let csrf=''; let likes=0;
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token;})();
fetch(`${API}/post.php?id=<?= $id ?>`,{credentials:'include'}).then(r=>r.json()).then(p=>{
  likes=p.likes||0;
  post.innerHTML = `<div class="card"><h2>${p.title}</h2><div class="note">${p.pub_date} - ${p.location}</div><img src="${p.image_url}" style="max-width:100%;border-radius:10px"><p>${p.body}</p><button class="btn" id="likeBtn" data-test="like-post">Me gusta (${likes})</button></div>`;
  document.getElementById('likeBtn').onclick=likePost;
  renderComments(p.comments||[]);
});
function renderComments(rows){
  comments.innerHTML = rows.map((c,idx)=>`<div class="card"><div>${c.text} - <span class="note">${c.created_at}</span></div><div class="row"><button class="btn secondary" data-reply="${idx}">Responder</button></div></div>`).join('');
  comments.querySelectorAll('button[data-reply]').forEach(b=>{
    b.onclick=()=>{ const text = rows[+b.dataset.reply].text; comment.value = `> ${text}\n`; comment.focus(); };
  });
}
btnComment.onclick=async()=>{
  err.textContent=''; const text=comment.value.trim(); if(!text){ err.textContent='El comentario no puede estar vacio'; return; }
  const r=await fetch(`${API}/comment.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({post_id:<?= $id ?>, text})});
  const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; } else { comment.value=''; renderComments(j.comments); }
};
async function likePost(){
  const r=await fetch(`${API}/post_like.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({post_id:<?= $id ?>})});
  const j=await r.json(); if(r.ok){ likes=j.likes; document.getElementById('likeBtn').textContent=`Me gusta (${likes})`; }
}
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>
