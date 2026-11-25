<?php
require_once '../config.php'; need_admin();
$page = 'index';
$c = fn($sql) => q($sql)->fetchColumn();
$stats = ['Students' => $c("SELECT COUNT(*) FROM users WHERE role='student'"), 'Clubs' => $c('SELECT COUNT(*) FROM clubs'),
  'Pending approvals' => $c("SELECT COUNT(*) FROM memberships WHERE status='pending'"), 'Pending bookings' => $c("SELECT COUNT(*) FROM bookings WHERE status='pending'"),
  'News stories' => $c('SELECT COUNT(*) FROM news'), 'Home banners' => $c('SELECT COUNT(*) FROM banners WHERE active=1'),
  'Upcoming events' => $c('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()'), 'Registrations' => $c('SELECT COUNT(*) FROM registrations')];
$month = q("SELECT DATE_FORMAT(created_at,'%b %Y') m, COUNT(*) n FROM registrations GROUP BY DATE_FORMAT(created_at,'%Y-%m'), m ORDER BY DATE_FORMAT(created_at,'%Y-%m') DESC LIMIT 6")->fetchAll();
$latestReq = q("SELECT m.status, m.created_at, u.name, u.email, c.name club FROM memberships m JOIN users u ON u.id=m.user_id JOIN clubs c ON c.id=m.club_id ORDER BY m.created_at DESC LIMIT 5")->fetchAll();
$latestUsers = q("SELECT name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Dashboard</h2><p class="sub">Overview of clubs, approvals, bookings and activity.</p>
<div class="stats"><?php foreach ($stats as $k => $v): ?><div class="card"><b><?= (int)$v ?></b><?= e($k) ?></div><?php endforeach; ?></div>
<h3 style="margin-bottom:12px">Quick actions</h3>
<div class="quick">
<a class="btn" href="<?= BASE ?>/admin/news.php#form">+ Add news</a><a class="btn" href="<?= BASE ?>/admin/banners.php#form">+ Add home banner</a><a class="btn" href="<?= BASE ?>/admin/events.php">+ Add event</a>
<a class="btn alt" href="<?= BASE ?>/admin/memberships.php">Review join requests</a><a class="btn alt" href="<?= BASE ?>/admin/users.php">See accounts</a><a class="btn alt" href="<?= BASE ?>/index.php">Open the home page</a>
</div>
<div class="split" style="margin:26px 0">
<div><h3 style="margin-bottom:10px">Latest join requests</h3>
<?php foreach ($latestReq as $r): ?><div class="card" style="margin:0 0 8px;padding:12px 14px"><b><?= e($r['name']) ?></b> <span class="tag <?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span><br><small><?= e($r['club']) ?> · <?= e($r['email']) ?> · <?= e(date('j M, H:i', strtotime($r['created_at']))) ?></small></div><?php endforeach; if (!$latestReq) echo '<p>No requests yet.</p>'; ?></div>
<div><h3 style="margin-bottom:10px">New accounts</h3>
<?php foreach ($latestUsers as $r): ?><div class="card" style="margin:0 0 8px;padding:12px 14px"><b><?= e($r['name']) ?></b> <span class="tag"><?= e(ucfirst($r['role'])) ?></span><br><small><?= e($r['email']) ?> · <?= e(date('j M Y', strtotime($r['created_at']))) ?></small></div><?php endforeach; ?></div>
</div>
<h3 style="margin-bottom:12px">Event registrations per month</h3>
<?php $max = max(1, ...array_column($month, 'n') ?: [1]); foreach ($month as $x): ?>
<div style="display:grid;grid-template-columns:90px 1fr 36px;gap:10px;align-items:center;max-width:560px;margin-bottom:8px"><span><?= e($x['m']) ?></span><div class="bar2"><i style="width:<?= $x['n'] / $max * 100 ?>%"></i></div><b><?= (int)$x['n'] ?></b></div>
<?php endforeach; if (!$month) echo '<p>No registrations yet.</p>'; ?>
<?php require '../includes/footer.php'; ?>
