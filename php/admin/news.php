<?php
require_once '../config.php'; need_admin();
$page = 'news';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete']; del_image('news', q('SELECT image FROM news WHERE id=?', [$id])->fetchColumn());
        q('DELETE FROM news WHERE id=?', [$id]); flash('News deleted.'); go('admin/news.php');
    }
    $id = (int)($_POST['id'] ?? 0); $t = trim($_POST['title'] ?? ''); $sp = trim($_POST['sport'] ?? ''); $su = trim($_POST['summary'] ?? ''); $b = trim($_POST['body'] ?? '');
    if (!$t || !$su || !$b) { flash('Title, short summary and story are required.'); go('admin/news.php'.($id ? '?edit='.$id : '')); }
    $old = $id ? q('SELECT image FROM news WHERE id=?', [$id])->fetchColumn() : null; $img = $old; $err = '';
    $new = save_image('image', 'news', $err);
    if ($err) { flash($err); go('admin/news.php'.($id ? '?edit='.$id : '')); }
    if ($new) { $img = $new; if ($old) del_image('news', $old); }
    if ($id) q('UPDATE news SET title=?, sport=?, summary=?, body=?, image=? WHERE id=?', [$t, $sp ?: null, mb_substr($su, 0, 300), $b, $img, $id]);
    else q('INSERT INTO news(title, sport, summary, body, image, author_id) VALUES(?,?,?,?,?,?)', [$t, $sp ?: null, mb_substr($su, 0, 300), $b, $img, user()['id']]);
    flash('News saved. It is now on the home page.'); go('admin/news.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM news WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$list = q('SELECT * FROM news ORDER BY created_at DESC, id DESC')->fetchAll();
$sports = q('SELECT DISTINCT sport FROM clubs ORDER BY sport')->fetchAll(PDO::FETCH_COLUMN);
require '../includes/admin_header.php';
?>
<h2>Sports news</h2><p class="sub">Add stories with a photo. The newest stories show on the home page.</p>
<div class="wrap"><table><thead><tr><th>Photo</th><th>Story</th><th>Sport</th><th>Date</th><th></th></tr></thead><tbody>
<?php foreach ($list as $x): ?><tr><td><?php if ($u = img_url('news', $x['image'])) echo '<img class="thumb" src="'.$u.'" alt="">'; else echo '–'; ?></td>
<td><b><?= e($x['title']) ?></b><br><small><?= e($x['summary']) ?></small></td><td><?= e($x['sport'] ?: '-') ?></td><td><?= e(date('j M Y', strtotime($x['created_at']))) ?></td>
<td><a class="btn alt sm" href="<?= BASE ?>/news-view.php?id=<?= $x['id'] ?>">View</a> <a class="btn alt sm" href="?edit=<?= $x['id'] ?>#form">Edit</a>
<form class="inline" method="post" data-confirm="Delete this story?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $x['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$list) echo '<tr><td colspan="5">No news yet. Add the first story below.</td></tr>'; ?></tbody></table></div>
<h3 id="form" style="margin:24px 0 12px"><?= $edit ? 'Edit story' : 'Add a story' ?></h3>
<form class="f" method="post" enctype="multipart/form-data"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Title<input name="title" maxlength="160" value="<?= e($edit['title'] ?? '') ?>" required></label>
<label>Sport<select name="sport"><option value="">General</option><?php foreach ($sports as $sp): ?><option <?= ($edit['sport'] ?? '') === $sp ? 'selected' : '' ?>><?= e($sp) ?></option><?php endforeach; ?></select></label>
<label>Short summary (shown on cards)<input name="summary" maxlength="300" value="<?= e($edit['summary'] ?? '') ?>" required></label>
<label>Full story<textarea name="body" rows="8" required><?= e($edit['body'] ?? '') ?></textarea></label>
<?php if ($edit && ($u = img_url('news', $edit['image']))): ?><img class="thumb big" src="<?= $u ?>" alt="Current photo"><?php endif; ?>
<label>Photo (JPG, PNG or WebP, max 3 MB)<input type="file" name="image" accept="image/*"></label>
<div class="acts" style="justify-content:flex-start"><button class="btn" type="submit"><?= $edit ? 'Save changes' : 'Publish story' ?></button><?php if ($edit): ?><a class="btn alt" href="?">Cancel</a><?php endif; ?></div></form>
<?php require '../includes/footer.php'; ?>
