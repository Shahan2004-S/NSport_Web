<?php
require_once 'config.php'; need_login();
$page = 'booking'; $title = 'Book a facility';
$fac = q('SELECT * FROM facilities ORDER BY kind, name')->fetchAll(); $err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $f = (int)$_POST['f']; $d = $_POST['d'] ?? ''; $sl = (int)($_POST['t'] ?? -1); $pu = trim($_POST['p'] ?? '');
    if (!q('SELECT 1 FROM facilities WHERE id=?', [$f])->fetch()) $err[] = 'Choose a facility or equipment.';
    if (!$d || $d < date('Y-m-d')) $err[] = 'Choose today or a later date.';
    if (!isset($SLOTS[$sl])) $err[] = 'Choose a time slot.';
    if (strlen($pu) < 5) $err[] = 'Describe the purpose in a few words.';
    if (!$err && q("SELECT 1 FROM bookings WHERE facility_id=? AND booking_date=? AND slot=? AND status<>'rejected'", [$f, $d, $sl])->fetch())
        $err[] = 'That slot is already booked. Pick another time.';
    if (!$err) {
        q('INSERT INTO bookings(user_id, facility_id, booking_date, slot, purpose) VALUES(?,?,?,?,?)', [user()['id'], $f, $d, $sl, $pu]);
        flash('Booking request sent. The sports office will review it.'); go('my-activity.php');
    }
}
require 'includes/header.php';
?>
<h2>Book a facility or equipment</h2><p class="sub">Requests are reviewed by the sports office. You can see the decision under My activity.</p>
<?php if ($err): ?><ul class="err"><?php foreach ($err as $x) echo '<li>'.e($x).'</li>'; ?></ul><?php endif; ?>
<form class="f" method="post"><?= csrf() ?>
<label>Facility or equipment<select name="f"><?php foreach ($fac as $x): ?><option value="<?= $x['id'] ?>" <?= (int)($_POST['f'] ?? 0) === (int)$x['id'] ? 'selected' : '' ?>><?= ($x['kind'] === 'equipment' ? 'Equipment: ' : '').e($x['name']) ?></option><?php endforeach; ?></select></label>
<label>Date<input type="date" name="d" min="<?= date('Y-m-d') ?>" value="<?= e($_POST['d'] ?? '') ?>" required></label>
<label>Time slot<select name="t"><?php foreach ($SLOTS as $i => $x) echo "<option value=\"$i\">".e($x).'</option>'; ?></select></label>
<label>Purpose<textarea name="p" rows="3" placeholder="e.g. Faculty team practice" required><?= e($_POST['p'] ?? '') ?></textarea></label>
<button class="btn" type="submit">Send booking request</button></form>
<?php require 'includes/footer.php'; ?>
