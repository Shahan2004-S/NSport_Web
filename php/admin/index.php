<?php
require_once '../config.php'; need_admin();
$page = 'index';
$c = fn($sql) => q($sql)->fetchColumn();
$stats = ['Students' => $c("SELECT COUNT(*) FROM users WHERE role='student'"), 'Clubs' => $c('SELECT COUNT(*) FROM clubs'),
  'Pending approvals' => $c("SELECT COUNT(*) FROM memberships WHERE status='pending'"), 'Pending bookings' => $c("SELECT COUNT(*) FROM bookings WHERE status='pending'"),
  'Upcoming events' => $c('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()'), 'Registrations' => $c('SELECT COUNT(*) FROM registrations')];
$month = q("SELECT DATE_FORMAT(created_at,'%b %Y') m, COUNT(*) n FROM registrations GROUP BY DATE_FORMAT(created_at,'%Y-%m'), m ORDER BY DATE_FORMAT(created_at,'%Y-%m') DESC LIMIT 6")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Dashboard</h2><p class="sub">Overview of clubs, approvals, bookings and activity.</p>
<div class="stats"><?php foreach ($stats as $k => $v): ?><div class="card"><b><?= (int)$v ?></b><?= e($k) ?></div><?php endforeach; ?></div>
<h3 style="margin-bottom:12px">Event registrations per month</h3>
<?php $max = max(1, ...array_column($month, 'n') ?: [1]); foreach ($month as $x): ?>
<div style="display:grid;grid-template-columns:90px 1fr 36px;gap:10px;align-items:center;max-width:560px;margin-bottom:8px"><span><?= e($x['m']) ?></span><div class="bar2"><i style="width:<?= $x['n'] / $max * 100 ?>%"></i></div><b><?= (int)$x['n'] ?></b></div>
<?php endforeach; if (!$month) echo '<p>No registrations yet.</p>'; ?>
<?php require '../includes/footer.php'; ?>
