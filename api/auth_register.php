<?php
require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); ensure_csrf();
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$full = trim($in['fullName'] ?? ''); $idc = trim($in['idCard'] ?? ''); $email = trim($in['email'] ?? ''); $phone = trim($in['phone'] ?? ''); $pass=$in['password'] ?? '';
if (!$full || !$idc || !$email || !$pass) json_response(400,['error'=>'Datos faltantes']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_response(422,['error'=>'Correo inválido']);
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',$pass)) json_response(422,['error'=>'Contraseña débil']);
$pdo=pdo();
$ex=$pdo->prepare("SELECT 1 FROM users WHERE email=? OR id_card=?"); $ex->execute([$email,$idc]);
if ($ex->fetch()) json_response(409,['error'=>'Correo o cédula ya registrados']);
$algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
$hash = password_hash($pass, $algo);
$token = bin2hex(random_bytes(6));
$stmt=$pdo->prepare("INSERT INTO users (full_name,id_card,email,phone,password_hash,status,verify_token) VALUES (?,?,?,?,?,'pending',?)");
$stmt->execute([$full,$idc,$email,$phone,$hash,$token]);
audit('user.register',['email'=>$email]);
notify((int)$pdo->lastInsertId(),'Verifica tu cuenta','Usa el OTP mostrado para activar tu cuenta.');
json_response(201,['message'=>'Registro creado','verificationToken'=>$token]);
