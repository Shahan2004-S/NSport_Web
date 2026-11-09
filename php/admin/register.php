<?php
require_once '../config.php';
if (is_admin()) go('admin/index.php');
$page = ''; $title = 'Admin sign up'; $err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $n = trim($_POST['name'] ?? ''); $em = strtolower(trim($_POST['email'] ?? '')); $pw = $_POST['password'] ?? ''; $code = (string)($_POST['code'] ?? '');
    if (!hash_equals(ADMIN_CODE, $code)) $err[] = 'The admin access code is not correct. Ask the sports office.';
    if (strlen($n) < 2) $err[] = 'Enter your full name.';
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if (strlen($pw) < 8) $err[] = 'Use at least 8 characters for the password.';
    if (!$err && q('SELECT 1 FROM users WHERE email=?', [$em])->fetch()) $err[] = 'That email is already registered.';
    if (!$err) {
        q('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)', [$n, $em, password_hash($pw, PASSWORD_DEFAULT), 'admin']);
        flash('Admin account created. You can sign in now.'); go('admin/login.php');
    }
}
require '../includes/header.php';
?>
<?php $authMode = 'admin'; require '../includes/auth_top.php'; ?>
<h2>Create admin account</h2><p class="sub">For sports office staff. You need the admin access code.</p>
<?php if ($err): ?><ul class="err" role="alert"><?php foreach ($err as $x) echo '<li>'.e($x).'</li>'; ?></ul><?php endif; ?>
<form class="f" method="post"><?= csrf() ?>
<label>Full name<input name="name" value="<?= e($_POST['name'] ?? '') ?>" autocomplete="name" required></label>
<label>Email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="username" required></label>
<label>Password (8+ characters)<input type="password" name="password" autocomplete="new-password" required></label>
<label>Admin access code<input type="password" name="code" autocomplete="off" required></label>
<button class="btn" type="submit">Create admin account</button>
<p class="auth-alt">Already staff? <a href="<?= BASE ?>/admin/login.php">Admin sign in</a></p></form>
<?php require '../includes/auth_bottom.php'; require '../includes/footer.php'; ?>
