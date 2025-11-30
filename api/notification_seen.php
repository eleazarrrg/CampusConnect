<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $id=(int)($in['id']??0); $uid=current_user_id();
pdo()->prepare("UPDATE notifications SET status='seen', seen_at=NOW() WHERE id=? AND user_id=?")->execute([$id,$uid]);
json_response(200,['ok'=>true]);
