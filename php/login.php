<?php
require_once 'config.php';
if (user()) go('index.php');
$page = ''; $title = 'Log in'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $u = q('SELECT * FROM users WHERE email=?', [strtolower(trim($_POST['email'] ?? ''))])->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'role' => $u['role']];
        go($u['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    $err = 'Wrong email or password.';
}
require 'includes/header.php';
?>
<?php $authMode = 'student'; require 'includes/auth_top.php'; ?>
<h2>Student log in</h2><p class="sub">Welcome back! Use the email you signed up with.</p>
<?php if ($err) echo '<p class="err" role="alert">'.e($err).'</p>'; ?>
<form class="f" method="post"><?= csrf() ?>
<label>University email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="username" required></label>
<label>Password<input type="password" name="password" autocomplete="current-password" required></label>
<button class="btn" type="submit">Log in</button>
<p class="auth-alt">New to SportsHub? <a href="<?= BASE ?>/register.php">Create an account</a></p></form>
<?php require 'includes/auth_bottom.php'; require 'includes/footer.php'; ?>
