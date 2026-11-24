<?php
require_once 'config.php';
if (user() && user()['role'] === 'student') q('UPDATE users SET last_seen=NULL WHERE id=?', [user()['id']]);
$_SESSION = [];
session_destroy();
header('Location: ' . BASE . '/index.php');
exit;
