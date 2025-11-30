<?php require __DIR__.'/../util.php'; require __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$rows = pdo()->query("SELECT id,title,author,published_date,description,cover_url,status FROM books ORDER BY id DESC")->fetchAll();
json_response(200,$rows);
