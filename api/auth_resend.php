<?php
require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $email=trim($in['email']??'');
if(!$email) json_response(400,['error'=>'Correo requerido']);
$pdo=pdo(); $u=$pdo->prepare("SELECT id,email,verify_token,status FROM users WHERE email=?"); $u->execute([$email]); $row=$u->fetch();
if(!$row || $row['status']!=='pending') json_response(400,['error'=>'No hay verificacion pendiente']);
$token = $row['verify_token'] ?: bin2hex(random_bytes(6));
if(!$row['verify_token']){ $pdo->prepare("UPDATE users SET verify_token=? WHERE id=?")->execute([$token,$row['id']]); }
$body = "OTP de verificacion: {$token}\nIngresa el codigo para activar tu cuenta.";
notify((int)$row['id'],'Reenvio de verificacion',$body);
json_response(200,['ok'=>true]);
