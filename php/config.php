<?php

define('BASE', '/sportshub');   
define('DB_HOST', 'localhost');
define('DB_NAME', 'sportshub');
define('DB_USER', 'root');      
define('DB_PASS', '');

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
function need_login() { if (!user()) go('login.php'); }
function need_admin() { if (!user() || user()['role'] !== 'admin') go('admin/login.php'); }
function club_img($c) { 
    $f = 'images/clubs/'.$c['image'];
    if ($c['image'] && is_file(__DIR__.'/'.$f)) return '<img class="ph" src="'.BASE.'/'.e($f).'" alt="'.e($c['name']).'">';
    return '<div class="ph">'.e(strtoupper(substr($c['sport'], 0, 2))).'</div>';
}
