<?php require_once __DIR__.'/../util.php'; require_once __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();
$in=json_decode(file_get_contents('php://input'),true)?:[]; $pid=(int)($in['post_id']??0);
if(!$pid) json_response(400,['error'=>'post_id requerido']);
$pdo=pdo();
$pdo->prepare("UPDATE posts SET likes=likes+1 WHERE id=?")->execute([$pid]);
$likes=$pdo->prepare("SELECT likes FROM posts WHERE id=?"); $likes->execute([$pid]); $count=(int)$likes->fetch()['likes'];
json_response(200,['likes'=>$count]);



