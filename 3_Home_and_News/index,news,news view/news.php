<?php
require_once 'config.php';
$page = 'news'; $title = 'Sports news';
$sport = $_GET['sport'] ?? ''; $s = trim($_GET['q'] ?? '');
$list = q('SELECT * FROM news WHERE (title LIKE ? OR summary LIKE ?)'.($sport ? ' AND sport=?' : '').' ORDER BY created_at DESC, id DESC',
    $sport ? ["%$s%", "%$s%", $sport] : ["%$s%", "%$s%"])->fetchAll();
$sports = q('SELECT DISTINCT sport FROM news WHERE sport IS NOT NULL AND sport<>"" ORDER BY sport')->fetchAll(PDO::FETCH_COLUMN);
require 'includes/header.php';
?>
<div class="sec-head"><div><h2>Sports news</h2><p class="sub">Stories and announcements from the NSBM sports office.</p></div>
<?php if (is_admin()): ?><a class="btn sm" href="<?= BASE ?>/admin/news.php">+ Add news</a><?php endif; ?></div>
<form class="filters" method="get">
  <input type="search" name="q" value="<?= e($s) ?>" placeholder="Search news" aria-label="Search news">
  <select name="sport" aria-label="Sport"><option value="">All sports</option>
  <?php foreach ($sports as $sp): ?><option <?= $sp === $sport ? 'selected' : '' ?>><?= e($sp) ?></option><?php endforeach; ?></select>
  <button class="btn" type="submit">Filter</button>
</form>
<div class="grid news-grid">
<?php foreach ($list as $x): ?>
  <a class="card news-card" href="<?= BASE ?>/news-view.php?id=<?= (int)$x['id'] ?>">
    <span class="nc-img" style="--img:url('<?= img_url('news', $x['image']) ?>')"></span>
    <span class="tag"><?= e($x['sport'] ?: 'Sports') ?></span>
    <h3 style="margin-top:10px"><?= e($x['title']) ?></h3>
    <p class="nc-sum"><?= e($x['summary']) ?></p><small><?= e(date('j M Y', strtotime($x['created_at']))) ?></small>
  </a>
<?php endforeach; if (!$list) echo '<p class="empty">No news found.</p>'; ?>
</div>
<?php require 'includes/footer.php'; ?>
