<?php
require_once 'config.php';
need_login();
$page = 'my-activity';
$title = 'My activity';
$id = user()['id'];
$clubs = q('SELECT c.id, c.name, m.status FROM memberships m JOIN clubs c ON c.id=m.club_id WHERE m.user_id=?', [$id])->fetchAll();
$regs = q('SELECT e.title, e.event_date FROM registrations r JOIN events e ON e.id=r.event_id WHERE r.user_id=? ORDER BY e.event_date DESC', [$id])->fetchAll();
$books = q('SELECT b.*, f.name FROM bookings b JOIN facilities f ON f.id=b.facility_id WHERE b.user_id=? ORDER BY b.booking_date DESC', [$id])->fetchAll();
$att = q('SELECT COUNT(*) FROM attendance WHERE user_id=? AND present=1', [$id])->fetchColumn();
require 'includes/header.php';
?>
<h2>My activity</h2>
<p class="sub">Your clubs, registrations and booking requests.</p>
<div class="grid">
  <div class="card">
    <h3>My clubs</h3><?php foreach ($clubs as $c): ?><div class="clubline"><?= e($c['name']) ?> <span class="tag <?= e($c['status']) ?>"><?= e(ucfirst($c['status'])) ?></span>
        <?php if ($c['status'] === 'pending'): ?><form class="inline" method="post" action="<?= BASE ?>/clubs.php" data-confirm="Cancel your join request for the <?= e($c['name']) ?>?"><?= csrf() ?><input type="hidden" name="club" value="<?= (int)$c['id'] ?>"><input type="hidden" name="do" value="cancel"><input type="hidden" name="back" value="my-activity"><button class="btn cancel sm" type="submit">Cancel</button></form><?php endif; ?></div><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                                                            if (!$clubs) echo '<p>No clubs yet.</p>'; ?>
  </div>
  <div class="card">
    <h3>Registrations</h3><?php foreach ($regs as $r): ?><p><?= e($r['title']) ?><br><small><?= e(date('D j M Y', strtotime($r['event_date']))) ?></small></p><?php endforeach;
                                                                                                                                                      if (!$regs) echo '<p>None yet.</p>'; ?>
  </div>
  <div class="card">
    <h3>Booking requests</h3><?php foreach ($books as $b): ?><p><?= e($b['name']) ?>, <?= e($b['booking_date']) ?>, <?= e($SLOTS[$b['slot']]) ?> <span class="tag"><?= e(ucfirst($b['status'])) ?></span></p><?php endforeach;
                                                                                                                                                                                                            if (!$books) echo '<p>None yet.</p>'; ?>
  </div>
  <div class="card">
    <h3>Participation</h3>
    <p style="font:800 32px Merriweather,serif;margin:0"><?= (int)$att ?> sessions</p><small>attended so far</small>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
