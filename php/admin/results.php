<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
<?php
require_once '../config.php'; need_admin();
$page = 'results';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM results WHERE id=?', [(int)$_POST['delete']]); flash('Result deleted.'); go('admin/results.php'); }
    $id = (int)($_POST['id'] ?? 0); $e = (int)$_POST['event_id']; $h = trim($_POST['home_team']); $a = trim($_POST['away_team']);
    $hs = $_POST['home_score'] ?? ''; $as = $_POST['away_score'] ?? '';
    if (!$e || !$h || !$a || !ctype_digit((string)$hs) || !ctype_digit((string)$as)) { flash('Enter both team names and scores of 0 or more.'); go('admin/results.php'); }
    if ($id) q('UPDATE results SET event_id=?, home_team=?, away_team=?, home_score=?, away_score=? WHERE id=?', [$e, $h, $a, $hs, $as, $id]);
    else q('INSERT INTO results(event_id, home_team, away_team, home_score, away_score) VALUES(?,?,?,?,?)', [$e, $h, $a, $hs, $as]);
    flash('Result saved.'); go('admin/results.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM results WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT r.*, e.title FROM results r JOIN events e ON e.id=r.event_id ORDER BY r.id DESC')->fetchAll();
$evs = q("SELECT id, title, event_date FROM events WHERE type<>'training' ORDER BY event_date DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Match results</h2><p class="sub">Record scores. Standings on the website update automatically.</p>
<div class="wrap"><table><thead><tr><th>Event</th><th>Result</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['home_team']) ?> <b><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></b> <?= e($r['away_team']) ?></td>
<td><a class="btn alt sm" href="?edit=<?= $r['id'] ?>">Edit</a> <form class="inline" method="post" data-confirm="Delete this result?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="3">No results yet.</td></tr>'; ?></tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit result' : 'Record a result' ?></h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Match or tournament<select name="event_id"><?php foreach ($evs as $x): ?><option value="<?= $x['id'] ?>" <?= ($edit['event_id'] ?? 0) == $x['id'] ? 'selected' : '' ?>><?= e($x['title']).' ('.e($x['event_date']).')' ?></option><?php endforeach; ?></select></label>
<label>Home team<input name="home_team" value="<?= e($edit['home_team'] ?? '') ?>" required></label>
<label>Home score<input type="number" name="home_score" min="0" value="<?= e($edit['home_score'] ?? '') ?>" required></label>
<label>Away team<input name="away_team" value="<?= e($edit['away_team'] ?? '') ?>" required></label>
<label>Away score<input type="number" name="away_score" min="0" value="<?= e($edit['away_score'] ?? '') ?>" required></label>
<button class="btn" type="submit">Save result</button></form>
<?php require '../includes/footer.php'; ?>
