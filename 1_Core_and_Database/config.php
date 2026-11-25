<?php
define('BASE', '/sportshub');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportshub');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ADMIN_CODE', 'NSBM-ADMIN-2026');
session_start();
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $x) { die('Database connection failed. Check the settings in config.php'); }

$SLOTS = ['6:00 am – 8:00 am', '4:00 pm – 6:00 pm', '6:00 pm – 8:00 pm'];

function q($sql, $p = []) { global $pdo; $s = $pdo->prepare($sql); $s->execute($p); return $s; }
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function user() { return $_SESSION['user'] ?? null; }
function go($path) { header('Location: '.BASE.'/'.$path); exit; }
function flash($m = null) { if ($m !== null) { $_SESSION['flash'] = $m; return null; } $x = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $x; }
function csrf() { $_SESSION['t'] ??= bin2hex(random_bytes(16)); return '<input type="hidden" name="t" value="'.$_SESSION['t'].'">'; }
function check_csrf() { if (!hash_equals($_SESSION['t'] ?? '', $_POST['t'] ?? '')) { http_response_code(400); die('Invalid request'); } }
function need_login() { if (!user()) { flash('Please log in or create an account first.'); go('login.php'); } }
function need_admin() { if (!user() || user()['role'] !== 'admin') go('admin/login.php'); }
function club_img($c) {
    $f = 'images/clubs/'.$c['image'];
    if ($c['image'] && is_file(__DIR__.'/'.$f)) return '<img class="ph" src="'.BASE.'/'.e($f).'" alt="'.e($c['name']).'">';
    return '<div class="ph">'.e(strtoupper(substr($c['sport'], 0, 2))).'</div>';
}

function is_admin() { return user() && user()['role'] === 'admin'; }
function need_login_msg() { if (!user()) { flash('Please log in or create an account first.'); go('login.php'); } }
function save_image($field, $folder, &$err, $maxMB = 3) {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if ($f['error'] !== 0 || !in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) || $f['size'] > $maxMB * 1048576 || !@getimagesize($f['tmp_name'])) {
        $err = "Photo must be a JPG, PNG or WebP image under $maxMB MB."; return null;
    }
    $dir = __DIR__.'/images/'.$folder; if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(8)).'.'.$ext;
    return move_uploaded_file($f['tmp_name'], $dir.'/'.$name) ? $name : null;
}
function del_image($folder, $name) { $f = __DIR__.'/images/'.$folder.'/'.basename((string)$name); if ($name && is_file($f)) @unlink($f); }
function img_url($folder, $name) { return ($name && is_file(__DIR__.'/images/'.$folder.'/'.$name)) ? BASE.'/images/'.$folder.'/'.e($name) : ''; }

// ---- v4: campus student list + "students online" ------------------------------------------
// Online = the student's browser pinged the site in the last 3 minutes (see heartbeat.php).
const ONLINE_MINUTES = 3;

// Adds the new column / table by itself the first time (so an existing database keeps working).
// If your host does not allow it, run database_update_v4.sql in phpMyAdmin instead.
function ensure_v4() {
    if (!empty($_SESSION['v4ok'])) return;
    try {
        if (!q("SHOW COLUMNS FROM users LIKE 'last_seen'")->fetch()) q('ALTER TABLE users ADD COLUMN last_seen DATETIME NULL');
        q("CREATE TABLE IF NOT EXISTS student_roster(id INT AUTO_INCREMENT PRIMARY KEY, student_id VARCHAR(30) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        $_SESSION['v4ok'] = 1;
    } catch (PDOException $x) {
        die('The database needs one small update. In phpMyAdmin select your database, open the SQL tab and run database_update_v4.sql, then reload this page.');
    }
}
function in_campus_list($email) { return (bool)q('SELECT 1 FROM student_roster WHERE email=?', [strtolower(trim((string)$email))])->fetch(); }
function online_count() { return (int)q("SELECT COUNT(*) FROM users WHERE role='student' AND last_seen >= NOW() - INTERVAL ".ONLINE_MINUTES." MINUTE")->fetchColumn(); }
function online_students() {
    return q("SELECT name, student_id, TIMESTAMPDIFF(SECOND, last_seen, NOW()) AS ago FROM users WHERE role='student'
              AND last_seen >= NOW() - INTERVAL ".ONLINE_MINUTES." MINUTE ORDER BY last_seen DESC LIMIT 100")->fetchAll();
}
// Runs on every page: refreshes "last seen" for a logged-in student (at most every 25 seconds)
// and signs the student out if they are no longer in the campus list.
function touch_user() {
    $u = user(); if (!$u || $u['role'] !== 'student') return;
    if (time() - ($_SESSION['seen'] ?? 0) < 25) return;
    $_SESSION['seen'] = time();
    $ok = q('SELECT 1 FROM users u JOIN student_roster r ON r.email = u.email WHERE u.id=?', [$u['id']])->fetch();
    if (!$ok) {
        q('UPDATE users SET last_seen=NULL WHERE id=?', [$u['id']]);
        unset($_SESSION['user'], $_SESSION['seen']);
        flash('You were signed out because your account is not in the NSBM student list. Please contact the sports office.');
        go('login.php');
    }
    q('UPDATE users SET last_seen=NOW() WHERE id=?', [$u['id']]);
}
ensure_v4();
touch_user();
