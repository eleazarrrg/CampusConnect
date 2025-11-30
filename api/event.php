<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$id=(int)($_GET['id']??0); $s=pdo()->prepare("SELECT id,title,category,date,time,place,seats,requirements FROM events WHERE id=?"); $s->execute([$id]); json_response(200,$s->fetch()?:[]);
