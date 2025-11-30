<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $books=$in['books']??[]; $days=(int)($in['days']??7); $uid=current_user_id();
if (!is_array($books) || count($books)==0) json_response(400,['error'=>'Sin libros']);
if (count($books)>3) json_response(422,['error'=>'Límite 3 libros']);
$pdo=pdo();
foreach($books as $bid){
  $b=$pdo->prepare("SELECT * FROM books WHERE id=?"); $b->execute([$bid]); $bk=$b->fetch();
  if(!$bk || $bk['status']!=='Disponible') json_response(422,['error'=>'Libro no disponible']);
}
foreach($books as $bid){
  $pdo->prepare("INSERT INTO loans(user_id,book_id,days,status,request_date) VALUES (?,?,?,'proceso',NOW())")->execute([$uid,$bid,$days]);
  $pdo->prepare("UPDATE books SET status='No disponible' WHERE id=?")->execute([$bid]);
  $lid = (int)pdo()->lastInsertId();
  pdo()->prepare("INSERT INTO loans_history(loan_id,user_id,action,meta) VALUES (?,?, 'create', JSON_OBJECT('days',?))")->execute([$lid,$uid,$days]);
}
audit('loans.create',['count'=>count($books)]); notify($uid,'Préstamo confirmado','Su solicitud de préstamo fue procesada.');
json_response(201,['ok'=>true]);
