<?php
require_once 'config.php';
if (user()) go('index.php');
$page = ''; $title = 'Sign up'; $err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $n = trim($_POST['name'] ?? ''); $em = strtolower(trim($_POST['email'] ?? '')); $sid = trim($_POST['student_id'] ?? ''); $pw = $_POST['password'] ?? '';
    if (strlen($n) < 2) $err[] = 'Enter your full name.';
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if (strlen($pw) < 8) $err[] = 'Use at least 8 characters for the password.';
    if (!$err && q('SELECT 1 FROM users WHERE email=?', [$em])->fetch()) $err[] = 'That email is already registered.';
    if (!$err) {
        q('INSERT INTO users(name,email,student_id,password_hash,role) VALUES(?,?,?,?,?)', [$n, $em, $sid, password_hash($pw, PASSWORD_DEFAULT), 'student']);
        flash('Account created. You can log in now.'); go('login.php');
    }
}
require 'includes/header.php';
?>
<?php $authMode = 'student'; require 'includes/auth_top.php'; ?>
<h2>Create student account</h2><p class="sub">New to SportsHub? Join in a minute.</p>
<?php if ($err): ?><ul class="err" role="alert"><?php foreach ($err as $x) echo '<li>'.e($x).'</li>'; ?></ul><?php endif; ?>
<form class="f" method="post"><?= csrf() ?>
<label>Full name<input name="name" value="<?= e($_POST['name'] ?? '') ?>" autocomplete="name" required></label>
<label>University email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="username" required></label>
<label>Student ID (optional)<input name="student_id" value="<?= e($_POST['student_id'] ?? '') ?>"></label>
<label>Password (8+ characters)<input type="password" name="password" autocomplete="new-password" required></label>
<button class="btn" type="submit">Create account</button>
<p class="auth-alt">Already have an account? <a href="<?= BASE ?>/login.php">Log in</a></p></form>
<?php require 'includes/auth_bottom.php'; require 'includes/footer.php'; ?>
