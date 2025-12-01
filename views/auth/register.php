<?php require __DIR__.'/../../layout_top.php'; ?>
<h2>Crear cuenta</h2>
<div class="grid">
  <div class="card">
    <div class="row">
      <input placeholder="Nombre completo" data-test="full-name" id="fullName">
      <input placeholder="Cedula" data-test="id-card" id="idCard">
      <input placeholder="Correo" data-test="email" id="email">
      <input placeholder="Telefono (opcional)" id="phone">
      <input type="password" placeholder="Contrasena" data-test="password" id="password">
      <input type="password" placeholder="Confirmar" data-test="confirm-password" id="confirm">
      <label class="row"><input type="checkbox" id="tos" data-test="tos"> Acepto terminos y politicas</label>
      <div class="row">
        <span class="badge" id="captchaQ"></span>
        <input placeholder="Respuesta" id="captchaA" data-test="captcha">
      </div>
      <div class="row">
        <meter id="pwdMeter" min="0" max="4" low="2" high="3" optimum="4" value="0" style="width:200px"></meter>
        <span class="note" id="pwdHint"></span>
      </div>
      <button class="btn" id="btnRegister" data-test="register">Registrar</button>
    </div>
    <div class="err" id="err"></div>
    <div class="ok" id="ok"></div>
    <hr/>
    <div class="toast" id="verifyBox" style="display:none">
      <div>OTP: <code id="tokenOut"></code></div>
      <input placeholder="OTP" data-test="otp" id="otp">
      <button class="btn" id="btnVerify" data-test="verify">Verificar</button>
    </div>
  </div>
</div>
<script>
const API = '<?= dynamic_base() ?>/api';
let csrf = '';
async function loadCSRF(){ const r=await fetch(`${API}/csrf.php`,{credentials:'include'}); csrf=(await r.json()).token; }
function makeCaptcha(){ const a=Math.floor(Math.random()*9)+1,b=Math.floor(Math.random()*9)+1; window.captchaAns=a+b; captchaQ.textContent=`CAPTCHA: ${a} + ${b} = ?`; }
function pwdScore(s){ let n=0; if(s.length>=8) n++; if(/[A-Z]/.test(s)) n++; if(/[a-z]/.test(s)) n++; if(/\d/.test(s)) n++; return n; }
password.oninput=()=>{ const n=pwdScore(password.value); pwdMeter.value=n; pwdHint.textContent = ['Muy debil','Debil','Aceptable','Fuerte','Muy fuerte'][n]; };
makeCaptcha(); loadCSRF();

function validateForm(){
  err.textContent=''; ok.textContent=''; [fullName,idCard,email,password,confirm].forEach(i=>i.classList.remove('error'));
  const errors=[];
  if(!tos.checked) errors.push('Debe aceptar TOS');
  const p1=password.value, p2=confirm.value;
  if(p1!==p2){ errors.push('Las contrasenas no coinciden'); password.classList.add('error'); confirm.classList.add('error'); }
  if(pwdScore(p1)<3) { errors.push('La contrasena es debil'); password.classList.add('error'); }
  if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value.trim())){ errors.push('Correo invalido'); email.classList.add('error'); }
  if(Number(captchaA.value)!==window.captchaAns){ errors.push('CAPTCHA incorrecto'); }
  if(!fullName.value.trim()) { errors.push('Nombre es obligatorio'); fullName.classList.add('error'); }
  if(!idCard.value.trim()) { errors.push('Cedula es obligatoria'); idCard.classList.add('error'); }
  if(errors.length){ err.textContent = errors.join(' | '); return false; }
  return true;
}

btnRegister.onclick=async()=>{
  if(!validateForm()) return;
  const body={fullName:fullName.value.trim(),idCard:idCard.value.trim(),email:email.value.trim(),phone:phone.value.trim(),password:password.value};
  try{
    const r=await fetch(`${API}/auth_register.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify(body)});
    const j=await r.json();
    if(!r.ok){ err.textContent=j.error||'Error'; return; }
    verifyBox.style.display='block'; tokenOut.textContent=j.verificationToken||''; ok.textContent='Registro creado. Revisa el OTP.';
  }catch(e){ err.textContent='Error de conexion'; }
};

btnVerify.onclick=async()=>{
  try{
    const r=await fetch(`${API}/auth_verify.php`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF':csrf},credentials:'include',body:JSON.stringify({token:otp.value.trim()})});
    const j=await r.json().catch(()=>({}));
    if(!r.ok){ err.textContent=j.error||'Token invalido'; return; }
    location.href='<?= dynamic_base() ?>/login.php';
  }catch(e){ err.textContent='Error de conexion'; }
};
</script>
<?php require __DIR__.'/../../layout_bottom.php'; ?>
