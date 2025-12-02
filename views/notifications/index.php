<?php require __DIR__.'/../../layout_top.php'; require_login_page(); require_once __DIR__.'/../../db.php'; $base=dynamic_base();
$stmt=pdo()->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC"); $stmt->execute([current_user_id()]); $rows=$stmt->fetchAll(); ?>
<h2>Notificaciones</h2>
<div class="grid">
<?php foreach($rows as $n): ?>
  <div class="card">
    <div><strong><?= htmlspecialchars($n['title']) ?></strong> <span class="badge"><?= htmlspecialchars($n['status']) ?></span></div>
    <p><?= nl2br(htmlspecialchars($n['body'])) ?></p>
    <div class="note"><?= htmlspecialchars($n['created_at']) ?></div>
  </div>
<?php endforeach; ?>
</div>
<?php require __DIR__.'/../../layout_bottom.php'; ?>

