<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    need_login(); check_csrf();
    $cid = (int)($_POST['club'] ?? 0);
    if (($_POST['do'] ?? '') === 'cancel') {
        q("DELETE FROM memberships WHERE user_id=? AND club_id=? AND status='pending'", [user()['id'], $cid]);
        flash('Join request cancelled.');
    } else {
        q('INSERT IGNORE INTO memberships(user_id, club_id) VALUES(?,?)', [user()['id'], $cid]);
        flash('Join request sent. The club will review it.');
    }
    go(($_POST['back'] ?? '') === 'my-activity' ? 'my-activity.php' : 'clubs.php');
}
$page = 'clubs'; $title = 'Clubs';
$sport = $_GET['sport'] ?? ''; $s = trim($_GET['q'] ?? '');
$clubs = q('SELECT * FROM clubs WHERE name LIKE ?'.($sport ? ' AND sport=?' : '').' ORDER BY name', $sport ? ["%$s%", $sport] : ["%$s%"])->fetchAll();
$sports = q('SELECT DISTINCT sport FROM clubs ORDER BY sport')->fetchAll(PDO::FETCH_COLUMN);
$mine = user() ? q('SELECT club_id, status FROM memberships WHERE user_id=?', [user()['id']])->fetchAll(PDO::FETCH_KEY_PAIR) : [];
require 'includes/header.php';
?>

<h2>Sports clubs</h2><p class="sub">The official NSBM sports clubs. Send a join request and the club approves it.</p>
<form class="filters" method="get">
  <input type="search" name="q" value="<?= e($s) ?>" placeholder="Search clubs" aria-label="Search clubs">
  <select name="sport" aria-label="Sport"><option value="">All sports</option>
  <?php foreach ($sports as $sp): ?><option <?= $sp === $sport ? 'selected' : '' ?>><?= e($sp) ?></option><?php endforeach; ?></select>
  <button class="btn" type="submit">Filter</button>
</form>

<div class="grid">
<?php foreach ($clubs as $c): ?>
  <div class="card">
    <?= club_img($c) ?>
    <span class="tag"><?= e($c['sport']) ?></span>
    <h3 style="margin-top:10px"><?= e($c['name']) ?></h3>
    <small><a href="<?= BASE ?>/club-<?= e(strtolower(str_replace(' ', '-', $c['sport']))) ?>.php">More about the <?= e($c['name']) ?></a></small>
    <?php $st = $mine[$c['id']] ?? null; ?>
    <div class="row"><?= $st ? '<span class="tag '.e($st).'">'.e(ucfirst($st)).'</span>' : '<span></span>' ?><div class="acts">
    <?php if ($st === 'pending'): ?><form class="inline" method="post" data-confirm="Cancel your join request for the <?= e($c['name']) ?>?"><?= csrf() ?><input type="hidden" name="club" value="<?= $c['id'] ?>"><input type="hidden" name="do" value="cancel"><button class="btn cancel sm" type="submit">Cancel request</button></form>
    <?php elseif (!$st && !user()): ?><button class="btn sm" type="button" data-login-prompt="Request to join the <?= e($c['name']) ?>">Request to join</button>
    <?php elseif (!$st): ?><form method="post"><?= csrf() ?><input type="hidden" name="club" value="<?= $c['id'] ?>"><button class="btn sm">Request to join</button></form><?php endif; ?>
    </div></div>
  </div>
<?php endforeach; if (!$clubs) echo '<p>No clubs found.</p>'; ?>
</div>
<?php require 'includes/footer.php'; ?>

