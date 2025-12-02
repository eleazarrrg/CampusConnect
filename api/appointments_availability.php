<?php require_once __DIR__.'/../util.php'; require_once __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$date = $_GET['date'] ?? ''; if (!$date) json_response(400,['error'=>'date requerido']);
$hours = ['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00'];
$stmt = pdo()->prepare("SELECT time FROM appointments WHERE date=? AND status='reservada'"); $stmt->execute([$date]);
$taken = array_column($stmt->fetchAll(), 'time');
$uid = current_user_id() ?? 0;
$out = array_map(function($h) use($taken,$date,$uid){
  $srv = $h . ':00';
  $available = !in_array($srv,$taken,true) && is_at_least_24h($date,$srv);
  return ['time'=>$h,'time_label'=>to_user_time_label($date,$srv,$uid),'available'=>$available];
}, $hours);
json_response(200,$out);



