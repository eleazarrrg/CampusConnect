<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/util.php';
start_secure_session();
set_security_headers();
$base = dynamic_base();
$cfg = require __DIR__.'/config.php';
if (!isset($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
?><!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?= htmlspecialchars($cfg['app_name']) ?></title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css?v=6">
  </head>
  <body>
    <header>
      <div class="brand"><?= htmlspecialchars($cfg['app_name']) ?></div>
      <nav>
        <a href="<?= $base ?>/index.php">Inicio</a>
        <a href="<?= $base ?>/citas.php" data-test="menu-appointments">Citas</a>
        <a href="<?= $base ?>/prestamos.php" data-test="menu-request-loan">Préstamos</a>
        <a href="<?= $base ?>/eventos.php" data-test="menu-events">Eventos</a>
        <a href="<?= $base ?>/publicaciones.php" data-test="menu-posts">Publicaciones</a>
        <?php if (!empty($_SESSION['uid'])): ?>
          <a class="right" href="<?= $base ?>/notificaciones.php">🔔</a>
          <a class="right" href="<?= $base ?>/logout.php" data-test="logout">Salir</a>
        <?php else: ?>
          <a class="right" href="<?= $base ?>/register.php" data-test="create-account">Crear cuenta</a>
          <a class="right" href="<?= $base ?>/login.php">Login</a>
        <?php endif; ?>
      </nav>
    </header>
    <main class="container">
