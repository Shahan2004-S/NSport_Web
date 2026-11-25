<?php
$authMode = $authMode ?? 'student'; ?>
<div class="auth">
  <aside class="auth-side">
    <div>
      <span class="auth-kicker">NSBM Green University</span>
      <h1>Your sports life,<br>all in one place.</h1>
      <p>Join clubs, book facilities and follow every fixture and result.</p>
    </div>
    <ul>
      <li>Join the official NSBM sports clubs</li>
      <li>Book grounds, courts and equipment</li>
      <li>Follow fixtures, results and standings</li>
    </ul>
  </aside>
  <div class="auth-main">
    <div class="seg<?= $authMode === 'admin' ? ' fromstudent' : '' ?>" data-on="<?= e($authMode) ?>" aria-label="Account type">
      <a href="<?= BASE ?>/login.php" class="<?= $authMode === 'student' ? 'on' : '' ?>" <?= $authMode === 'student' ? 'aria-current="page"' : '' ?>>Student</a>
      <a href="<?= BASE ?>/admin/login.php" class="<?= $authMode === 'admin' ? 'on' : '' ?>" <?= $authMode === 'admin' ? 'aria-current="page"' : '' ?>>Admin</a>
    </div>
