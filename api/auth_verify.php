<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $token=trim($in['token']??'');
if(!$token) json_response(400,['error'=>'Token requerido']);
$pdo=pdo(); $u=$pdo->prepare("SELECT * FROM users WHERE verify_token=? AND status='pending'"); $u->execute([$token]); $row=$u->fetch();
if(!$row) json_response(400,['error'=>'Token inválido']);
$pdo->prepare("UPDATE users SET status='active', verify_token=NULL WHERE id=?")->execute([$row['id']]);
audit('user.verify',['uid'=>$row['id']]); json_response(200,['ok'=>true]);
