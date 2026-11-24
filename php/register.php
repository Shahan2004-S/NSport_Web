<?php
require_once 'config.php';
if (user()) go('index.php');
$page = '';
$title = 'Sign up';
$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $n = trim($_POST['name'] ?? '');
    $em = strtolower(trim($_POST['email'] ?? ''));
    $sid = trim($_POST['student_id'] ?? '');
    $pw = $_POST['password'] ?? '';
    if (strlen($n) < 2) $err[] = 'Enter your full name.';
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err[] = 'Enter a valid email address.';
    if ($sid === '') $err[] = 'Enter your student ID.';
    if (strlen($pw) < 8) $err[] = 'Use at least 8 characters for the password.';
    $row = null;
    if (!$err) {
        $row = q('SELECT * FROM student_roster WHERE student_id=? AND email=?', [$sid, $em])->fetch();
        if (!$row) $err[] = 'We could not find this student ID and university email in the NSBM student list. Check both, or ask the sports office to add you.';
    }
    if (!$err && q('SELECT 1 FROM users WHERE email=?', [$em])->fetch()) $err[] = 'That email is already registered.';
    if (!$err && q("SELECT 1 FROM users WHERE student_id=? AND role='student'", [$row['student_id']])->fetch()) $err[] = 'An account for this student ID already exists. Try logging in.';
    if (!$err) {
        q('INSERT INTO users(name,email,student_id,password_hash,role) VALUES(?,?,?,?,?)', [$n, $em, $row['student_id'], password_hash($pw, PASSWORD_DEFAULT), 'student']);
        flash('Account created. You can log in now.');
        go('login.php');
    }
}
require 'includes/header.php';
?>
<?php $authMode = 'student';
require 'includes/auth_top.php'; ?>
<h2>Create student account</h2>
<p class="sub">Only NSBM students in the campus student list can create an account.</p>
<?php if ($err): ?><ul class="err" role="alert"><?php foreach ($err as $x) echo '<li>' . e($x) . '</li>'; ?></ul><?php endif; ?>
<form class="f" method="post"><?= csrf() ?>
    <label>Full name<input name="name" value="<?= e($_POST['name'] ?? '') ?>" autocomplete="name" required></label>
    <label>University email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="username" required></label>
    <label>Student ID<input name="student_id" value="<?= e($_POST['student_id'] ?? '') ?>" autocomplete="off" required></label>
    <label>Password (8+ characters)<input type="password" name="password" autocomplete="new-password" required></label>
    <button class="btn" type="submit">Create account</button>
    <p class="auth-alt">Already have an account? <a href="<?= BASE ?>/login.php">Log in</a></p>
</form>
<?php require 'includes/auth_bottom.php';
require 'includes/footer.php'; ?>
