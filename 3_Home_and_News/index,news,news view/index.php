<?php
require_once 'config.php';
$page = 'index'; $title = 'NSBM SportsHub';
$n = [q('SELECT COUNT(*) FROM clubs')->fetchColumn(),
      q("SELECT COUNT(*) FROM memberships WHERE status='approved'")->fetchColumn(),
      q('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn()];
$next = q('SELECT e.*, c.sport FROM events e LEFT JOIN clubs c ON c.id=e.club_id WHERE e.event_date >= CURDATE() ORDER BY e.event_date, e.event_time LIMIT 3')->fetchAll();
$slides = [];
foreach (q('SELECT * FROM banners WHERE active=1 ORDER BY sort_order, id DESC')->fetchAll() as $b)
    if ($u = img_url('banners', $b['image'])) $slides[] = ['img' => $u, 'title' => $b['title'], 'sub' => $b['subtitle'], 'link' => $b['link']];
if (!$slides) $slides[] = ['img' => is_file(__DIR__.'/images/hero.jpg') ? BASE.'/images/hero.jpg' : '', 'title' => 'Sports at NSBM Green University',
    'sub' => 'Join one of eleven official sports clubs, book university facilities and follow tournament results.', 'link' => 'clubs.php'];
$news = q('SELECT * FROM news ORDER BY created_at DESC, id DESC LIMIT 5')->fetchAll();
require 'includes/header.php';
?>
<section class="slider" id="slider" aria-roledescription="carousel" aria-label="Featured sports banners">
  <?php foreach ($slides as $i => $s): ?>
  <div class="slide<?= $i === 0 ? ' on' : '' ?>" <?= $s['img'] ? 'style="--img:url(\''.$s['img'].'\')"' : '' ?> aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>">
    <div class="slide-txt">
      <span class="slide-kicker">NSBM SportsHub</span>
      <h1><?= e($s['title']) ?></h1>
      <?php if ($s['sub']): ?><p><?= e($s['sub']) ?></p><?php endif; ?>
      <a class="btn" href="<?= preg_match('#^https?://#', (string)$s['link']) ? e($s['link']) : BASE.'/'.e($s['link'] ?: 'clubs.php') ?>">Explore</a>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="nums"><div><b><?= $n[0] ?></b><span>sports clubs</span></div><div><b><?= $n[1] ?></b><span>members</span></div><div><b><?= $n[2] ?></b><span>upcoming events</span></div></div>
  <?php if (count($slides) > 1): ?>
  <button class="sl-btn prev" type="button" aria-label="Previous banner">‹</button><button class="sl-btn next" type="button" aria-label="Next banner">›</button>
  <div class="sl-dots"><?php foreach ($slides as $i => $s) echo '<button type="button" class="'.($i === 0 ? 'on' : '').'" aria-label="Banner '.($i + 1).'"></button>'; ?></div>
  <?php endif; ?>
  <?php if (is_admin()): ?><a class="edit-chip" href="<?= BASE ?>/admin/banners.php">✎ Manage banners</a><?php endif; ?>
</section>

<div class="sec-head"><div><h2>Latest sports news</h2><p class="sub">Match reports, results and announcements from the sports office.</p></div>
  <span><?php if (is_admin()): ?><a class="btn sm" href="<?= BASE ?>/admin/news.php">+ Add news</a> <?php endif; ?><a class="btn alt sm" href="<?= BASE ?>/news.php">All news →</a></span></div>
<?php if ($news): $f = array_shift($news); ?>
<div class="news-wrap">
  <a class="news-feat" href="<?= BASE ?>/news-view.php?id=<?= (int)$f['id'] ?>" style="--img:url('<?= img_url('news', $f['image']) ?>')">
    <div><span class="tag"><?= e($f['sport'] ?: 'Sports') ?></span><h3><?= e($f['title']) ?></h3><p><?= e($f['summary']) ?></p><small><?= e(date('j M Y', strtotime($f['created_at']))) ?> · Read more →</small></div>
  </a>
  <div class="news-side">
  <?php foreach ($news as $x): ?>
    <a class="news-mini" href="<?= BASE ?>/news-view.php?id=<?= (int)$x['id'] ?>">
      <span class="nm-img" style="--img:url('<?= img_url('news', $x['image']) ?>')"></span>
      <span><span class="tag"><?= e($x['sport'] ?: 'Sports') ?></span><b><?= e($x['title']) ?></b><small><?= e(date('j M Y', strtotime($x['created_at']))) ?></small></span>
    </a>
  <?php endforeach; ?>
  </div>
</div>
<?php else: ?><p class="empty">No news yet.<?php if (is_admin()) echo ' <a href="'.BASE.'/admin/news.php">Add the first story</a>.'; ?></p><?php endif; ?>

<h2 style="margin-top:34px">Upcoming events</h2>
<p class="sub">Added by the sports office. Register on the Fixtures page.</p>
<div class="grid">
<?php foreach ($next as $x): ?>
  <div class="card"><span class="tag"><?= e($x['sport'] ?? ucfirst($x['type'])) ?></span>
  <h3 style="margin-top:10px"><?= e($x['title']) ?></h3><small><?= e(date('D j M', strtotime($x['event_date']))) ?> · <?= e(substr($x['event_time'], 0, 5)) ?> · <?= e($x['venue']) ?></small></div>
<?php endforeach; if (!$next) echo '<p>No upcoming events yet.</p>'; ?>
</div>
<?php require 'includes/footer.php'; ?>
