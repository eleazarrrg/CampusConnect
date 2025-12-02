<?php
require_once __DIR__ . '/../util.php'; require_once __DIR__ . '/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$pid = (int)($in['post_id'] ?? 0);
$text = trim($in['text'] ?? '');
if (!$pid) json_response(400, ['error' => 'post_id requerido']);
if ($text === '') json_response(422, ['error' => 'El comentario no puede estar vacio']);

$pdo = pdo();
$pdo->prepare("INSERT INTO comments(post_id,user_id,text) VALUES (?,?,?)")->execute([$pid, current_user_id(), $text]);
$rows = $pdo->prepare("SELECT text,created_at FROM comments WHERE post_id=? ORDER BY id DESC");
$rows->execute([$pid]);
json_response(201, ['comments' => $rows->fetchAll()]);



