<?php
require_once __DIR__ . '/../util.php'; require_once __DIR__ . '/../db.php'; check_session_timeout(); ensure_csrf(); require_login_api();

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$id = (int)($in['id'] ?? 0);
$uid = current_user_id();

$pdo = pdo();
$n = $pdo->prepare("SELECT * FROM notifications WHERE id=? AND user_id=?");
$n->execute([$id, $uid]);
$row = $n->fetch();
if (!$row) json_response(404, ['error' => 'No existe notificacion']);

$cfg = require __DIR__ . '/../config.php';
if (!is_dir($cfg['mail_dir'])) @mkdir($cfg['mail_dir'], 0777, true);
$fname = $cfg['mail_dir'] . '/' . date('Ymd_His') . '_resent_' . $id . '.txt';
$ok = (bool)@file_put_contents($fname, "To:user#$uid@utp.edu\nSubject:" . $row['title'] . "\n\n" . $row['body'] . "\n");
pdo()->prepare("UPDATE notifications SET status=? WHERE id=?")->execute([$ok ? 'resent' : 'failed', $id]);
json_response(200, ['ok' => $ok]);



