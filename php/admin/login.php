<?php
require_once '../config.php';
if (user() && user()['role'] === 'admin') go('admin/index.php');
$page = ''; $title = 'Admin login'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $u = q("SELECT * FROM users WHERE email=? AND role='admin'", [strtolower(trim($_POST['email'] ?? ''))])->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'role' => 'admin'];
        go('admin/index.php');
    }
    $err = 'Wrong email or password.';
}
require '../includes/header.php';
?>
<?php $authMode = 'admin'; require '../includes/auth_top.php'; ?>
<h2>Admin sign in</h2><p class="sub">For sports office staff only.</p>
<?php if ($err) echo '<p class="err" role="alert">'.e($err).'</p>'; ?>
<form class="f" method="post"><?= csrf() ?>
<label>Admin email<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="username" required></label>
<label>Password<input type="password" name="password" autocomplete="current-password" required></label>
<button class="btn" type="submit">Sign in</button>
<p class="auth-alt">New staff member? <a href="<?= BASE ?>/admin/register.php">Create admin account</a><br>Not staff? <a href="<?= BASE ?>/login.php">Go to student log in</a></p></form>
<?php require '../includes/auth_bottom.php'; require '../includes/footer.php'; ?>
