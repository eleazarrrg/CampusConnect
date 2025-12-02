<?php
require_once __DIR__ . '/../util.php'; require_once __DIR__ . '/../db.php'; check_session_timeout(); require_login_api();

$id = (int)($_GET['id'] ?? 0);
$p = pdo()->prepare("SELECT * FROM posts WHERE id=? AND is_active=1");
$p->execute([$id]);
$post = $p->fetch();
if (!$post) json_response(404, ['error' => 'Publicacion no encontrada']);

$c = pdo()->prepare("SELECT text,created_at FROM comments WHERE post_id=? ORDER BY id DESC");
$c->execute([$id]);
$post['comments'] = $c->fetchAll();

json_response(200, $post);



