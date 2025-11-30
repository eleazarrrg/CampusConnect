<?php require __DIR__.'/layout_top.php'; ?>
<h2>Iniciar sesión</h2>
<div class="grid">
  <div class="card">
    <div class="row">
      <input placeholder="Correo" data-test="login-email" id="email">
      <div class="row" style="width:100%">
        <input placeholder="Contraseña" type="password" data-test="login-password" id="password" style="flex:1">
        <button class="btn secondary" id="toggle">👁️</button>
      </div>
      <label><input type="checkbox" id="remember"> Recordarme</label>
      <button class="btn" id="btnLogin" data-test="login">Entrar</button>
      <a href="#" id="forgot">¿Olvidaste tu contraseña?</a>
    </div>
    <div class="err" id="err"></div>
  </div>
</div>
<script>
const API = '<?= dynamic_base() ?>/api';
let csrf=''; (async()=>{csrf=(await (await fetch(`${API}/csrf.php`,{credentials:'include'})).json()).token;})();
toggle.onclick=(e)=>{e.preventDefault(); password.type = password.type==='password'?'text':'password';};
btnLogin.onclick=async()=>{
  err.textContent='';
  try{
    const r = await fetch(`${API}/auth_login.php`,{
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF':csrf}, credentials:'include',
      body:JSON.stringify({email:email.value.trim(),password:password.value,remember:remember.checked})
    });
    const j = await r.json().catch(()=>({}));
    if(r.ok) location.href='<?= dynamic_base() ?>/panel.php'; else err.textContent = j.error||'Error';
  }catch(e){ err.textContent='Error de conexión'; }
};
</script>
<?php require __DIR__.'/layout_bottom.php'; ?>
