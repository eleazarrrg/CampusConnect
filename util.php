<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

function start_secure_session(): void {
  static $started=false;
  if ($started) return;
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  if (PHP_VERSION_ID >= 70300 && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => $secure,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
  }
  if (session_status() === PHP_SESSION_NONE) session_start();
  $started=true;
}

start_secure_session();

function set_security_headers(): void {
  header("X-Frame-Options: SAMEORIGIN");
  header("X-Content-Type-Options: nosniff");
  header("Referrer-Policy: no-referrer-when-downgrade");
  header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
  header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https://picsum.photos; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; object-src 'none'; base-uri 'self'; frame-ancestors 'self';");
}

function dynamic_base(): string {
  $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
  return $dir === '/' ? '' : $dir;
}

function user_ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }

function json_response(int $code, $data): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function check_session_timeout(): void {
  $now = time();
  if (!isset($_SESSION['last'])) { $_SESSION['last'] = $now; return; }
  if (($now - $_SESSION['last']) > 1800) { // 30 min
    session_unset(); session_destroy();
    http_response_code(440); echo json_encode(['error'=>'Sesión expirada']); exit;
  }
  $_SESSION['last'] = $now;
}

function ensure_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'GET') return;
  $hdr = $_SERVER['HTTP_X_CSRF'] ?? '';
  if (!isset($_SESSION['csrf']) || !$hdr || !hash_equals($_SESSION['csrf'], $hdr)) {
    http_response_code(419); echo json_encode(['error'=>'CSRF inválido']); exit;
  }
}

function current_user_id(): ?int { return $_SESSION['uid'] ?? null; }
function require_login_api(): void { if (!current_user_id()) json_response(401, ['error'=>'No autenticado']); }
function require_login_page(): void { if (!current_user_id()) { header('Location: '.dynamic_base().'/login.php'); exit; } }

function audit(string $action, array $meta=[]): void {
  try {
    $pdo = pdo();
    $stmt = $pdo->prepare("INSERT INTO audit_log(user_id, action, ip, meta) VALUES (?,?,?,?)");
    $stmt->execute([current_user_id(), $action, user_ip(), json_encode($meta, JSON_UNESCAPED_UNICODE)]);
  } catch (Throwable $e) { /* no-op */ }
}

function notify(int $user_id, string $title, string $body): void {
  $pdo = pdo();
  $pdo->prepare("INSERT INTO notifications (user_id,title,body) VALUES (?,?,?)")->execute([$user_id,$title,$body]);
  $nid = (int)$pdo->lastInsertId();
  $ok = true; $cfg = require __DIR__.'/config.php';
  if (!is_dir($cfg['mail_dir'])) $ok = @mkdir($cfg['mail_dir'],0777,true);
  $file = $cfg['mail_dir'].'/'.date('Ymd_His')."_{$nid}.txt";
  $content = "To: user{$user_id}@utp.edu\nSubject: {$title}\n\n{$body}\n";
  $ok = $ok && (bool)@file_put_contents($file, $content);
  $pdo->prepare("UPDATE notifications SET status=? WHERE id=?")->execute([$ok?'new':'failed',$nid]);
}

function is_at_least_24h(string $date, string $time): bool {
  return (strtotime("$date $time") - time()) >= 24*3600;
}

function user_tz(int $uid): string {
  try { $stmt = pdo()->prepare('SELECT tz FROM users WHERE id=?'); $stmt->execute([$uid]); $tz = $stmt->fetch()['tz'] ?? 'America/Panama'; return $tz ?: 'America/Panama'; }
  catch (Throwable $e) { return 'America/Panama'; }
}

function to_user_time_label(string $date, string $time, ?int $uid=null): string {
  $uid = $uid ?? (current_user_id() ?? 0);
  try {
    $srv = new DateTimeZone('America/Panama');
    $usr = new DateTimeZone(user_tz($uid));
    $dt = new DateTime("$date $time", $srv);
    $dt->setTimezone($usr);
    return $dt->format('H:i');
  } catch (Throwable $e) { return substr($time,0,5); }
}
