<?php require_once __DIR__.'/../config.php'; need_admin(); ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | NSBM SportsHub</title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
<script src="<?= BASE ?>/assets/js/app.js" defer></script></head>
<body>
<div class="top"><div><span>Admin panel</span><a href="<?= BASE ?>/index.php">View site</a><a href="<?= BASE ?>/logout.php">Log out</a></div></div>
<header><div class="bar">
<a class="logo" href="<?= BASE ?>/admin/index.php"><span>NSBM SportsHub</span><b>Admin</b></a>
<nav>
<?php foreach (['index'=>'Dashboard','clubs'=>'Clubs','memberships'=>'Approvals','events'=>'Events','bookings'=>'Bookings','results'=>'Results','attendance'=>'Attendance','reports'=>'Reports'] as $f => $l): ?>
<a href="<?= BASE ?>/admin/<?= $f ?>.php" class="<?= ($page ?? '') === $f ? 'on' : '' ?>"><?= $l ?></a>
<?php endforeach; ?>
</nav>
<?php include __DIR__.'/theme_button.php'; ?>
</div></header>
<main>
<?php if ($m = flash()): ?><div class="flash" role="status"><?= e($m) ?></div><?php endif; ?>
