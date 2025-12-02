<?php
require_once __DIR__.'/../util.php'; require_once __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$daysAhead = 21; $hours = ['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00'];
$today = new DateTimeImmutable('now', new DateTimeZone('America/Panama'));
$pdo = pdo();
$out = [];
for ($i=0; $i<$daysAhead; $i++){
  $d = $today->modify("+$i days")->format('Y-m-d');
  $stmt = $pdo->prepare("SELECT time FROM appointments WHERE date=? AND status='reservada'");
  $stmt->execute([$d]); $taken = array_column($stmt->fetchAll(),'time');
  $availableSlots = array_filter($hours, function($h) use($taken,$d){ $srv=$h.':00'; return !in_array($srv,$taken,true) && is_at_least_24h($d,$srv); });
  $out[] = ['date'=>$d,'available'=>count($availableSlots)>0];
}
json_response(200,$out);



