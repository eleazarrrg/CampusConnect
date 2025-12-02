<?php require_once __DIR__.'/../util.php'; require_once __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $id=(int)($in['id']??0); $status=$in['status']??'activo'; $pickup=$in['pickup']??null; $due=$in['due']??null; $ret=$in['ret']??null; $uid=current_user_id();
$pickup = $pickup ?: null; $due = $due ?: null; $ret = $ret ?: null;
$pdo=pdo(); $s=$pdo->prepare("SELECT * FROM loans WHERE id=? AND user_id=?"); $s->execute([$id,$uid]); $l=$s->fetch(); if(!$l) json_response(404,['error'=>'No existe prestamo']);
$today=date('Y-m-d'); if($pickup && $pickup<$today) json_response(422,['error'=>'Fecha de entrega pasada']); if($due && $due<$today) json_response(422,['error'=>'Fecha limite pasada']); if($ret && $ret<$today) json_response(422,['error'=>'Fecha de devolucion pasada']);
$pdo->prepare("UPDATE loans SET pickup_date=?, due_date=?, return_date=?, status=? WHERE id=?")->execute([$pickup,$due,$ret,$status,$id]);
if($status==='finalizado'){ $pdo->prepare("UPDATE books SET status='Disponible' WHERE id=?")->execute([$l['book_id']]); }
$action = $status==='finalizado' ? 'finalize' : 'update';
$pdo->prepare("INSERT INTO loans_history(loan_id,user_id,action,meta) VALUES (?,?, ?, JSON_OBJECT('status',?))")->execute([$id,$uid,$action,$status]);
audit('loans.update',['loan_id'=>$id]); notify($uid,'Prestamo actualizado','Los datos del prestamo fueron modificados');
json_response(200,['ok'=>true]);



