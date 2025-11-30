<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uid = current_user_id();
if ($method === 'GET'){
  $stmt = pdo()->prepare("SELECT id,date,time,status FROM appointments WHERE user_id=? AND status='reservada' ORDER BY id DESC LIMIT 1");
  $stmt->execute([$uid]); $row = $stmt->fetch();
  json_response(200,['appointment'=>$row ?: null]);
}
ensure_csrf();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $date=$in['date']??''; $time=$in['time']??'';
if(!$date||!$time) json_response(400,['error'=>'Fecha y hora requeridas']);
if(!is_at_least_24h($date, $time.':00')) json_response(422,['error'=>'Debe agendar con ≥24 horas de antelación']);
$pdo=pdo();
$exists=$pdo->prepare("SELECT 1 FROM appointments WHERE user_id=? AND status='reservada'"); $exists->execute([$uid]); if($exists->fetch()) json_response(409,['error'=>'Ya tiene una cita activa']);
$slot=$pdo->prepare("SELECT 1 FROM appointments WHERE date=? AND time=? AND status='reservada'"); $slot->execute([$date,$time.':00']); if($slot->fetch()) json_response(409,['error'=>'El día o la hora ya no está disponible']);
$pdo->beginTransaction();
$pdo->prepare("INSERT INTO appointments(user_id,date,time,status) VALUES (?,?,?,'reservada')")->execute([$uid,$date,$time.':00']);
$aid=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO appointment_history(appointment_id,user_id,new_date,new_time,action) VALUES (?,?,?,?, 'create')")->execute([$aid,$uid,$date,$time.':00']);
$pdo->commit();
audit('appointment.create',['id'=>$aid]); notify($uid,'Cita confirmada',"Fecha: $date $time");
json_response(201,['ok'=>true,'id'=>$aid]);
