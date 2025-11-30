<?php
require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $email=trim($in['email']??''); $password=$in['password']??''; $remember=!empty($in['remember']);
if(!$email||!$password) json_response(400,['error'=>'Correo y contraseña son requeridos']);
$pdo=pdo();
/* rate limit básico por IP: máx 15 en 1 min */
$lim=$pdo->prepare("SELECT COUNT(*) c FROM login_audit WHERE ip=? AND created_at> (NOW()-INTERVAL 1 MINUTE)"); $lim->execute([user_ip()]); if((int)$lim->fetch()['c']>15) json_response(429,['error'=>'Demasiados intentos, intenta en un minuto']);
$stmt=$pdo->prepare("SELECT * FROM users WHERE email=?"); $stmt->execute([$email]); $u=$stmt->fetch();
$now = new DateTimeImmutable();
if ($u && $u['locked_until'] && new DateTimeImmutable($u['locked_until']) > $now){ json_response(423,['error'=>'Cuenta bloqueada temporalmente']); }
if (!$u || !password_verify($password, $u['password_hash']) || $u['status']!=='active'){
  if ($u){
    $att=(int)$u['attempts']+1;
    if ($att>=3){ $lock=$now->modify('+30 minutes')->format('Y-m-d H:i:s'); $pdo->prepare("UPDATE users SET attempts=0, locked_until=? WHERE id=?")->execute([$lock,$u['id']]); }
    else { $pdo->prepare("UPDATE users SET attempts=? WHERE id=?")->execute([$att,$u['id']]); }
    $pdo->prepare("INSERT INTO login_audit(user_id,email,ip,success) VALUES (?,?,?,0)")->execute([$u['id'],$email,user_ip()]);
  }
  json_response(401,['error'=>'Credenciales inválidas o cuenta no activa']);
}
$pdo->prepare("UPDATE users SET attempts=0, locked_until=NULL WHERE id=?")->execute([$u['id']]);
$_SESSION['uid']=(int)$u['id']; $_SESSION['last']=time(); if(!isset($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16));
audit('auth.login',['uid'=>$u['id']]); $pdo->prepare("INSERT INTO login_audit(user_id,email,ip,success) VALUES (?,?,?,1)")->execute([$u['id'],$email,user_ip()]);
$opts=['httponly'=>true,'samesite'=>'Lax','path'=>'/']; if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') $opts['secure']=true; if ($remember) $opts['expires']=time()+60*60*24*30;
setcookie('sid', session_id(), $opts);
json_response(200,['message'=>'Login ok']);
