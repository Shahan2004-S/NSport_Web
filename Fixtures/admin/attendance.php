<?php
require_once '../config.php'; need_admin();
$page = 'attendance'; $eid = (int)($_GET['event'] ?? 0);
$users = $eid ? q('SELECT u.id, u.name, COALESCE(a.present,0) present FROM registrations r JOIN users u ON u.id=r.user_id LEFT JOIN attendance a ON a.event_id=r.event_id AND a.user_id=u.id WHERE r.event_id=? ORDER BY u.name', [$eid])->fetchAll() : [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $eid) {
    check_csrf();
    foreach ($users as $u) q('REPLACE INTO attendance(event_id, user_id, present) VALUES(?,?,?)', [$eid, $u['id'], isset($_POST['present'][$u['id']]) ? 1 : 0]);
    flash('Attendance saved.'); go('admin/attendance.php?event='.$eid);
}
$evs = q('SELECT id, title, event_date FROM events ORDER BY event_date DESC')->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Attendance</h2><p class="sub">Mark who attended a session. Only registered students are listed.</p>
<form class="filters" method="get"><select name="event" aria-label="Event"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= $eid === (int)$x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select><button class="btn" type="submit">Open</button></form>
<?php if ($eid): ?><form method="post"><?= csrf() ?><div class="wrap"><table><thead><tr><th>Student</th><th>Present</th></tr></thead><tbody>
<?php foreach ($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><input type="checkbox" name="present[<?= $u['id'] ?>]" <?= $u['present'] ? 'checked' : '' ?> style="width:auto" aria-label="<?= e($u['name']) ?> present"></td></tr><?php endforeach; ?>
<?php if (!$users) echo '<tr><td colspan="2">No registrations for this event.</td></tr>'; ?></tbody></table></div><p><button class="btn" type="submit">Save attendance</button></p></form><?php endif; ?>
<?php require '../includes/footer.php'; ?>
