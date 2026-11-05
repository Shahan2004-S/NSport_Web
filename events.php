<?php
require_once '../config.php'; need_admin();
$page = 'events';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM events WHERE id=?', [(int)$_POST['delete']]); flash('Event deleted.'); go('admin/events.php'); }
    $id = (int)($_POST['id'] ?? 0); $club = (int)$_POST['club_id'] ?: null; $t = trim($_POST['title']); $ty = $_POST['type'];
    $d = $_POST['event_date']; $tm = $_POST['event_time']; $v = trim($_POST['venue']); $mx = (int)$_POST['max_participants'] ?: null;
    if (!$t || !$d || !$tm || !$v || !in_array($ty, ['training', 'match', 'tournament'])) { flash('Fill in all required fields.'); go('admin/events.php'); }
    if ($id) q('UPDATE events SET club_id=?, title=?, type=?, event_date=?, event_time=?, venue=?, max_participants=? WHERE id=?', [$club, $t, $ty, $d, $tm, $v, $mx, $id]);
    else q('INSERT INTO events(club_id, title, type, event_date, event_time, venue, max_participants) VALUES(?,?,?,?,?,?,?)', [$club, $t, $ty, $d, $tm, $v, $mx]);
    flash('Event saved.'); go('admin/events.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM events WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$ev = q('SELECT e.*, c.name club FROM events e LEFT JOIN clubs c ON c.id=e.club_id ORDER BY e.event_date DESC')->fetchAll();
$clubs = q('SELECT id, name FROM clubs ORDER BY name')->fetchAll();
$venues = q("SELECT name FROM facilities WHERE kind='facility' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
require '../includes/admin_header.php';
?>
<h2>Events</h2><p class="sub">Training sessions, matches and tournaments.</p>
<div class="wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Event</th><th>Club</th><th>Venue</th><th></th></tr></thead><tbody>
<?php foreach ($ev as $x): ?><tr><td><?= e($x['event_date']) ?> <?= e(substr($x['event_time'], 0, 5)) ?></td><td><span class="tag"><?= e(ucfirst($x['type'])) ?></span></td><td><?= e($x['title']) ?></td><td><?= e($x['club'] ?? '-') ?></td><td><?= e($x['venue']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $x['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this event?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $x['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit event' : 'Add an event' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Title<input name="title" value="<?= e($edit['title'] ?? '') ?>" required></label>
<label>Type<select name="type"><?php foreach (['training', 'match', 'tournament'] as $t): ?><option value="<?= $t ?>" <?= ($edit['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option><?php endforeach; ?></select></label>
<label>Club<select name="club_id"><option value="0">No specific club</option><?php foreach ($clubs as $c): ?><option value="<?= $c['id'] ?>" <?= ($edit['club_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>Date<input type="date" name="event_date" value="<?= e($edit['event_date'] ?? '') ?>" required></label>
<label>Time<input type="time" name="event_time" value="<?= e(substr($edit['event_time'] ?? '', 0, 5)) ?>" required></label>
<label>Venue<select name="venue"><?php foreach ($venues as $v): ?><option <?= ($edit['venue'] ?? '') === $v ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></label>
<label>Maximum participants (optional)<input type="number" name="max_participants" min="1" value="<?= e($edit['max_participants'] ?? '') ?>"></label>
<button class="btn" type="submit">Save event</button></form>
<?php require '../includes/footer.php'; ?>
