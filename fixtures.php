<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {            // "Register" button
    check_csrf(); need_login(); $id = (int)$_POST['event'];
    $ev = q('SELECT * FROM events WHERE id=?', [$id])->fetch();
    if ($ev) {
        $cnt = q('SELECT COUNT(*) FROM registrations WHERE event_id=?', [$id])->fetchColumn();
        if ($ev['max_participants'] && $cnt >= $ev['max_participants']) flash('Sorry, this event is full.');
        else { q('INSERT IGNORE INTO registrations(user_id, event_id) VALUES(?,?)', [user()['id'], $id]); flash('You are registered.'); }
    }
    go('fixtures.php');
}
$page = 'fixtures'; $title = 'Fixtures';
$view = $_GET['view'] ?? 'list'; $sport = $_GET['sport'] ?? ''; $s = trim($_GET['q'] ?? '');
$m = preg_match('/^\d{4}-\d{2}$/', $_GET['m'] ?? '') ? $_GET['m'] : date('Y-m');
$w = ['e.title LIKE ?']; $p = ["%$s%"];
if ($sport) { $w[] = 'c.sport=?'; $p[] = $sport; }
if ($view === 'cal') { $w[] = "DATE_FORMAT(e.event_date,'%Y-%m')=?"; $p[] = $m; } else $w[] = 'e.event_date >= CURDATE()';
$ev = q('SELECT e.*, c.sport FROM events e LEFT JOIN clubs c ON c.id=e.club_id WHERE '.implode(' AND ', $w).' ORDER BY e.event_date, e.event_time', $p)->fetchAll();
$sports = q('SELECT DISTINCT sport FROM clubs ORDER BY sport')->fetchAll(PDO::FETCH_COLUMN);
$reg = user() ? q('SELECT event_id FROM registrations WHERE user_id=?', [user()['id']])->fetchAll(PDO::FETCH_COLUMN) : [];
require 'includes/header.php';
?>
<h2>Training and match fixtures</h2><p class="sub">Register for matches, training sessions and tournaments. Spots can be limited.</p>
<form class="filters" method="get">
  <input type="search" name="q" value="<?= e($s) ?>" placeholder="Search fixtures" aria-label="Search fixtures">
  <select name="sport" aria-label="Sport"><option value="">All sports</option>
  <?php foreach ($sports as $sp): ?><option <?= $sp === $sport ? 'selected' : '' ?>><?= e($sp) ?></option><?php endforeach; ?></select>
  <select name="view" aria-label="View"><option value="list" <?= $view === 'list' ? 'selected' : '' ?>>List view</option><option value="cal" <?= $view === 'cal' ? 'selected' : '' ?>>Calendar view</option></select>
  <button class="btn" type="submit">Apply</button>
</form>
<?php if ($view === 'cal'):
  $first = strtotime("$m-01"); $by = [];
  foreach ($ev as $x) $by[(int)substr($x['event_date'], 8)][] = $x;
  $qs = '&view=cal&sport='.urlencode($sport).'&q='.urlencode($s);
?>
  <h3 style="margin-bottom:10px"><a href="?m=<?= date('Y-m', strtotime('-1 month', $first)).$qs ?>">‹</a> <?= date('F Y', $first) ?> <a href="?m=<?= date('Y-m', strtotime('+1 month', $first)).$qs ?>">›</a></h3>
  <div class="wrap"><div class="cal">
  <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d) echo "<div class=\"h\">$d</div>";
  echo str_repeat('<div></div>', (int)date('w', $first));
  for ($d = 1; $d <= (int)date('t', $first); $d++) {
      echo "<div><b>$d</b>";
      foreach ($by[$d] ?? [] as $x) echo '<span class="ev" title="'.e($x['title']).'">'.e($x['sport'] ?? $x['type']).'</span>';
      echo '</div>';
  } ?>
  </div></div>
<?php else: ?>
  <div class="wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Event</th><th>Venue</th><th></th></tr></thead><tbody>
  <?php foreach ($ev as $x): ?>
    <tr><td><?= e(date('D j M', strtotime($x['event_date']))) ?>, <?= e(substr($x['event_time'], 0, 5)) ?></td>
    <td><span class="tag"><?= e($x['sport'] ?? ucfirst($x['type'])) ?></span></td><td><?= e($x['title']) ?></td><td><?= e($x['venue']) ?></td>
    <td><?php if (in_array($x['id'], $reg)): ?><span class="tag">Registered</span>
    <?php else: ?><form method="post"><?= csrf() ?><input type="hidden" name="event" value="<?= $x['id'] ?>"><button class="btn sm">Register</button></form><?php endif; ?></td></tr>
  <?php endforeach; if (!$ev) echo '<tr><td colspan="5">No fixtures found.</td></tr>'; ?>
  </tbody></table></div>
<?php endif; require 'includes/footer.php'; ?>
