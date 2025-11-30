<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $id=(int)($in['id']??0); $uid=current_user_id();
$pdo=pdo(); $stmt=$pdo->prepare("SELECT * FROM appointments WHERE id=? AND user_id=?"); $stmt->execute([$id,$uid]); $a=$stmt->fetch();
if(!$a) json_response(404,['error'=>'No existe']);
$lessThan24 = !is_at_least_24h($a['date'], $a['time']);
if($lessThan24) { json_response(422,['error'=>'No se libera para otros si faltan <24h; contacte soporte']); }
$pdo->beginTransaction();
$pdo->prepare("UPDATE appointments SET status='cancelada' WHERE id=?")->execute([$id]);
$pdo->prepare("INSERT INTO appointment_history(appointment_id,user_id,old_date,old_time,action) VALUES (?,?,?,?, 'cancel')")->execute([$id,$uid,$a['date'],$a['time']]);
$pdo->commit(); audit('appointment.cancel',['id'=>$id]); notify($uid,'Cita cancelada',"Fecha: {$a['date']} " . substr($a['time'],0,5));
json_response(200,['ok'=>true]);
