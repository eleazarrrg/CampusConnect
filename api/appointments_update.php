<?php
require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $id=(int)($in['id']??0); $date=$in['date']??''; $time=$in['time']??'';
if(!$id||!$date||!$time) json_response(400,['error'=>'Datos incompletos']);
if(!is_at_least_24h($date, $time.':00')) json_response(422,['error'=>'Debe modificar con ≥24 horas de antelación']);
$uid=current_user_id(); $pdo=pdo();
$curr=$pdo->prepare("SELECT * FROM appointments WHERE id=? AND user_id=? AND status='reservada'"); $curr->execute([$id,$uid]); $a=$curr->fetch();
if(!$a) json_response(404,['error'=>'No existe la cita']);
$slot=$pdo->prepare("SELECT 1 FROM appointments WHERE date=? AND time=? AND status='reservada' AND id<>?"); $slot->execute([$date,$time.':00',$id]); if($slot->fetch()) json_response(409,['error'=>'El horario no está disponible']);
$pdo->beginTransaction();
$pdo->prepare("UPDATE appointments SET date=?, time=? WHERE id=?")->execute([$date,$time.':00',$id]);
$pdo->prepare("INSERT INTO appointment_history(appointment_id,user_id,old_date,old_time,new_date,new_time,action) VALUES (?,?,?,?,?,?, 'modify')")
  ->execute([$id,$uid,$a['date'],$a['time'],$date,$time.':00']);
$pdo->commit();
audit('appointment.modify',['id'=>$id]); notify($uid,'Cita reprogramada',"Nueva fecha: $date $time");
json_response(200,['ok'=>true]);
