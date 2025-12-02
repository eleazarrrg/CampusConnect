<?php
require_once __DIR__ . '/../util.php'; require_once __DIR__ . '/../db.php'; check_session_timeout(); require_login_api();

$id = (int)($_GET['id'] ?? 0);
$s = pdo()->prepare("SELECT id,title,category,date,time,place,seats,requirements FROM events WHERE id=?");
$s->execute([$id]);
$row = $s->fetch();
if (!$row) json_response(404, ['error' => 'Evento no encontrado']);

$row['description'] = $row['requirements'] ?: 'No hay descripcion disponible';
json_response(200, $row);



