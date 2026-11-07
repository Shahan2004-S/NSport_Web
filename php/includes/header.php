<?php require_once __DIR__.'/../config.php'; $u = user(); ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'NSBM SportsHub') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
<script src="<?= BASE ?>/assets/js/app.js" defer></script></head>
<body>
<div class="top"><div><a href="tel:+94115445000">+94 11 544 5000</a><a href="mailto:inquiries@nsbm.ac.lk">inquiries@nsbm.ac.lk</a><a href="https://www.nsbm.ac.lk" target="_blank" rel="noopener">nsbm.ac.lk</a></div></div>
<header><div class="bar">
<a class="logo" href="<?= BASE ?>/index.php"><span>NSBM Green University</span><b>SportsHub</b></a>
<nav>
<?php foreach (['index'=>'Home','clubs'=>'Clubs','fixtures'=>'Fixtures','results'=>'Results','booking'=>'Book','my-activity'=>'My activity'] as $f => $l): ?>
<a href="<?= BASE ?>/<?= $f ?>.php" class="<?= ($page ?? '') === $f ? 'on' : '' ?>"><?= $l ?></a>
<?php endforeach; ?>
</nav>
<?php include __DIR__.'/theme_button.php'; ?>
<?php if ($u): ?>
  <?php if ($u['role'] === 'admin'): ?><a class="btn sm" href="<?= BASE ?>/admin/index.php">Admin</a><?php endif; ?>
  <a class="btn alt sm" href="<?= BASE ?>/logout.php">Log out (<?= e($u['name']) ?>)</a>
<?php else: ?>
  <a class="btn alt sm" href="<?= BASE ?>/login.php">Log in</a><a class="btn sm" href="<?= BASE ?>/register.php">Sign up</a>
<?php endif; ?>
</div></header>
<main>
<?php if ($m = flash()): ?><div class="flash" role="status"><?= e($m) ?></div><?php endif; ?>
