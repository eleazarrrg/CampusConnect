<?php require_once __DIR__.'/../util.php'; require_once __DIR__.'/../db.php'; check_session_timeout(); require_login_api();
$q=trim($_GET['q']??''); $cat=trim($_GET['category']??''); $sql="SELECT id,title,category,date,time,place,seats FROM events WHERE 1"; $args=[];
if($q!==''){ $sql.=" AND title LIKE ?"; $args[]='%'.$q.'%'; }
if($cat!==''){ $sql.=" AND category=?"; $args[]=$cat; }
$sql.=" ORDER BY date ASC"; $stmt=pdo()->prepare($sql); $stmt->execute($args); $rows=$stmt->fetchAll();
pdo()->prepare("INSERT INTO search_stats(user_id,q,category,date) VALUES (?,?,?,CURDATE())")->execute([current_user_id(),$q,$cat]);
json_response(200,$rows);



