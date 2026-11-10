<?php
require_once 'config.php';
$x = q('SELECT n.*, u.name author FROM news n LEFT JOIN users u ON u.id=n.author_id WHERE n.id=?', [(int)($_GET['id'] ?? 0)])->fetch();
if (!$x) { http_response_code(404); flash('That news story was not found.'); go('news.php'); }
$page = 'news'; $title = $x['title'];
$more = q('SELECT id,title,image,created_at FROM news WHERE id<>? ORDER BY created_at DESC, id DESC LIMIT 3', [$x['id']])->fetchAll();
require 'includes/header.php';
?>
<div class="crumb"><a href="<?= BASE ?>/index.php">Home</a> / <a href="<?= BASE ?>/news.php">News</a> / <?= e($x['sport'] ?: 'Sports') ?></div>
<article class="article">
  <?php if ($u = img_url('news', $x['image'])): ?><div class="art-img" style="--img:url('<?= $u ?>')" role="img" aria-label="<?= e($x['title']) ?>"></div><?php endif; ?>
  <span class="tag"><?= e($x['sport'] ?: 'Sports') ?></span>
  <h1><?= e($x['title']) ?></h1>
  <small><?= e(date('j F Y', strtotime($x['created_at']))) ?><?= $x['author'] ? ' · by '.e($x['author']) : '' ?></small>
  <div class="art-body"><?php foreach (preg_split('/\n{2,}/', trim(str_replace("\r", '', $x['body']))) as $p) echo '<p>'.nl2br(e($p)).'</p>'; ?></div>
  <p><a class="btn alt" href="<?= BASE ?>/news.php">← All news</a>
  <?php if (is_admin()): ?> <a class="btn sm" href="<?= BASE ?>/admin/news.php?edit=<?= (int)$x['id'] ?>">✎ Edit this story</a><?php endif; ?></p>
</article>
<?php if ($more): ?><h3 style="margin:30px 0 12px">More news</h3><div class="grid">
<?php foreach ($more as $m): ?><a class="card news-card" href="<?= BASE ?>/news-view.php?id=<?= (int)$m['id'] ?>"><span class="nc-img" style="--img:url('<?= img_url('news', $m['image']) ?>')"></span><b><?= e($m['title']) ?></b><br><small><?= e(date('j M Y', strtotime($m['created_at']))) ?></small></a>
<?php endforeach; ?></div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
