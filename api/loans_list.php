<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$uid=current_user_id(); $rows=pdo()->query("SELECT l.id,l.status,l.request_date,b.title FROM loans l JOIN books b ON b.id=l.book_id WHERE l.user_id={$uid} ORDER BY l.id DESC")->fetchAll();
json_response(200,$rows);
