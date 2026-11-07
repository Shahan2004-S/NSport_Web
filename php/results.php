<?php
require_once 'config.php';
$page = 'results';
$title = 'Results';
$res = q('SELECT r.*, e.title, e.event_date FROM results r JOIN events e ON e.id=r.event_id ORDER BY e.event_date DESC, r.id DESC')->fetchAll();
$t = []; // standings worked out from all recorded results (win = 3 points, draw = 1)
foreach ($res as $r)
  foreach ([[$r['home_team'], $r['home_score'], $r['away_score']], [$r['away_team'], $r['away_score'], $r['home_score']]] as [$n, $f, $a]) {
    $t[$n] ??= ['p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'pts' => 0];
    $t[$n]['p']++;

    if ($f > $a) {
      $t[$n]['w']++;
      $t[$n]['pts'] += 3;
    } elseif ($f == $a) {
      $t[$n]['d']++;
      $t[$n]['pts']++;
    } else $t[$n]['l']++;
}
uasort($t, fn($a, $b) => $b['pts'] <=> $a['pts']);
require 'includes/header.php';
?>
<h2>Results and standings</h2>
<p class="sub">Recorded by the sports office after each match.</p>

<div class="split">
  <div>
    <h3 style="margin-bottom:10px">Latest results</h3>
    <?php foreach ($res as $r): ?>
      <div class="card row" style="margin:0 0 10px"><b><?= e($r['home_team']) ?></b><span style="font:800 22px Merriweather,serif"><?= (int)$r['home_score'] ?> – <?= (int)$r['away_score'] ?></span><b><?= e($r['away_team']) ?></b></div>
    <?php endforeach; if (!$res) echo '<p>No results recorded yet.</p>'; ?>
  </div>

  <div>
    <h3 style="margin-bottom:10px">Standings</h3>
    <div class="wrap">
      <table style="min-width:0">
        <thead>
          <tr><th>Team</th><th>P</th><th>W</th><th>D</th><th>L</th><th>Pts</th></tr>
        </thead>
        <tbody>
          <?php foreach ($t as $n => $x): ?><tr><td><?= e($n) ?></td><td><?= $x['p'] ?></td><td><?= $x['w'] ?></td><td><?= $x['d'] ?></td><td><?= $x['l'] ?></td><td><b><?= $x['pts'] ?></b></td></tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
