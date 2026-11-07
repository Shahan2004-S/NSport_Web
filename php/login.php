<?php
require_once 'config.php';
if (user()) {
     go('index.php');
      }
$page = ''; 
$title = 'Log in'; 
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $u = q('SELECT * FROM users WHERE email=?', [strtolower(trim($_POST['email'] ?? ''))])->fetch();

    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $u['id'], 
            'name' => $u['name'], 
            'role' => $u['role']
            ];
        go($u['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    $err = 'Wrong email or password.';
}
require 'includes/header.php';
?>
<h2>Log in</h2>
<p class="sub">Use the email you signed up with.</p>
<?php if ($err):?>
    <p class="err"><?=e($err)?>'</p>'
    <?php endif; ?> 
<form class="f" method="post">
    <?= csrf() ?>
<label>University email
    <input type="email" name="email" required></label>
<label>Password
    <input type="password" name="password" required></label>
<button class="btn" type="submit">Log in</button>

<small>
    No account? <a href="<?= BASE ?>/register.php">Sign up</a> ·
    Sports office? <a href="<?= BASE ?>/admin/login.php">Admin login</a>
</small>
</form>
<?php require 'includes/footer.php'; ?>
