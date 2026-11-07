<?php

require_once 'config.php';
$email = 'admin@sportshub.local'; $pass = 'Admin@12345'; $name = 'Sports Office'; 
if (q('SELECT 1 FROM users WHERE email=?', [$email])->fetch()) die('Admin already exists. Delete this file.');
q('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)', [$name, $email, password_hash($pass, PASSWORD_DEFAULT), 'admin']);
echo 'Admin created. Login: '.e($email).' / the password in this file. Now DELETE create_admin.php.';
