<?php require __DIR__.'/layout_top.php'; ?>
<h2>Iniciar sesion</h2>
<div class="grid">
  <div class="card">
    <div class="row">
      <input placeholder="Correo" data-test="login-email" id="email">
      <div class="row" style="width:100%">
        <input placeholder="Contrasena" type="password" data-test="login-password" id="password" style="flex:1">
        <button class="btn secondary" id="toggle">👁️</button>
      </div>
      <label><input type="checkbox" id="remember"> Recordarme</label>
      <button class="btn" id="btnLogin" data-test="login">Entrar</button>
      <button class="btn secondary" id="btnResend" style="display:none" data-test="resend-verification">Reenviar verificacion</button>
      <a href="#" id="forgot">¿Olvidaste tu contrasena?</a>
    </div>
    <div class="err" id="err"></div>
    <div class="ok" id="ok"></div>
  </div>
</div>
<script>
const API = '<?= dynamic_base() ?>/api';
let csrf=''; let pendingResend=false;
(async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token;})();
toggle.onclick=(e)=>{e.preventDefault(); password.type = password.type==='password'?'text':'password';};
btnLogin.onclick=async()=>{
  err.textContent=''; ok.textContent=''; pendingResend=false; btnResend.style.display='none';
  try{
    const r = await fetch(`${API}/auth_login.php`,{
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF':csrf}, credentials:'include',
      body:JSON.stringify({email:email.value.trim(),password:password.value,remember:remember.checked})
    });
    const j = await r.json().catch(()=>({}));
    if(r.ok) { location.href='<?= dynamic_base() ?>/panel.php'; return; }
    err.textContent = j.error||'Error';
    if(j.canResend){ pendingResend=true; btnResend.style.display='inline-block'; }
  }catch(e){ err.textContent='Error de conexion'; }
};
btnResend.onclick=async()=>{
  if(!pendingResend){ err.textContent='Debes iniciar sesion primero'; return; }
  try{
    const r=await fetch(`${API}/auth_resend.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({email:email.value.trim()})});
    const j=await r.json(); if(!r.ok){ err.textContent=j.error||'Error'; return; }
    ok.textContent='Verificacion reenviada. Revisa tu correo simulado.';
  }catch(e){ err.textContent='Error de conexion'; }
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
