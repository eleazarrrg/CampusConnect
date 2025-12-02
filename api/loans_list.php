<?php
require_once __DIR__ . '/../util.php'; require_once __DIR__ . '/../db.php'; check_session_timeout(); require_login_api();

$uid = current_user_id();
$sql = "SELECT l.id,l.status,l.request_date,l.pickup_date,l.due_date,l.return_date,b.title 
        FROM loans l JOIN books b ON b.id=l.book_id 
        WHERE l.user_id=? ORDER BY l.id DESC";
$stmt = pdo()->prepare($sql);
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

json_response(200, $rows);



