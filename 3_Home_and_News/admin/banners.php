<?php
require_once '../config.php'; need_admin();
$page = 'banners';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete']; del_image('banners', q('SELECT image FROM banners WHERE id=?', [$id])->fetchColumn());
        q('DELETE FROM banners WHERE id=?', [$id]); flash('Banner deleted.'); go('admin/banners.php');
    }
    if (isset($_POST['toggle'])) { q('UPDATE banners SET active = 1 - active WHERE id=?', [(int)$_POST['toggle']]); flash('Banner updated.'); go('admin/banners.php'); }
    $id = (int)($_POST['id'] ?? 0); $t = trim($_POST['title'] ?? ''); $su = trim($_POST['subtitle'] ?? ''); $l = trim($_POST['link'] ?? ''); $o = (int)($_POST['sort_order'] ?? 0);
    if (!$t) { flash('A banner needs a title.'); go('admin/banners.php'); }
    $old = $id ? q('SELECT image FROM banners WHERE id=?', [$id])->fetchColumn() : null; $img = $old; $err = '';
    $new = save_image('image', 'banners', $err, 4);
    if ($err) { flash($err); go('admin/banners.php'.($id ? '?edit='.$id : '')); }
    if ($new) { $img = $new; if ($old) del_image('banners', $old); }
    if (!$img) { flash('Please choose a photo for the banner.'); go('admin/banners.php'); }
    if ($id) q('UPDATE banners SET title=?, subtitle=?, link=?, sort_order=?, image=? WHERE id=?', [$t, $su, $l, $o, $img, $id]);
    else q('INSERT INTO banners(title, subtitle, link, sort_order, image) VALUES(?,?,?,?,?)', [$t, $su, $l, $o, $img]);
    flash('Banner saved. It is now on the home page.'); go('admin/banners.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM banners WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$list = q('SELECT * FROM banners ORDER BY sort_order, id DESC')->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Home page banners</h2><p class="sub">The big photo slider at the top of the home page. Add several photos and they slide automatically. Lowest order number shows first.</p>
<div class="grid banner-grid">
<?php foreach ($list as $b): ?>
  <div class="card<?= $b['active'] ? '' : ' off' ?>">
    <div class="b-img" style="--img:url('<?= img_url('banners', $b['image']) ?>')"></div>
    <h3 style="margin-top:12px"><?= e($b['title']) ?></h3><small><?= e($b['subtitle']) ?></small>
    <div class="row"><span class="tag <?= $b['active'] ? 'approved' : 'rejected' ?>"><?= $b['active'] ? 'Showing' : 'Hidden' ?> · #<?= (int)$b['sort_order'] ?></span>
    <div class="acts"><form class="inline" method="post"><?= csrf() ?><button class="btn alt sm" name="toggle" value="<?= $b['id'] ?>"><?= $b['active'] ? 'Hide' : 'Show' ?></button></form>
    <a class="btn alt sm" href="?edit=<?= $b['id'] ?>#form">Edit</a>
    <form class="inline" method="post" data-confirm="Delete this banner?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $b['id'] ?>">Delete</button></form></div></div>
  </div>
<?php endforeach; if (!$list) echo '<p>No banners yet. The home page shows the default photo until you add one.</p>'; ?>
</div>
<h3 id="form" style="margin:24px 0 12px"><?= $edit ? 'Edit banner' : 'Add a banner' ?></h3>
<form class="f" method="post" enctype="multipart/form-data"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Title (big text on the photo)<input name="title" maxlength="120" value="<?= e($edit['title'] ?? '') ?>" required></label>
<label>Small text under the title (optional)<input name="subtitle" maxlength="220" value="<?= e($edit['subtitle'] ?? '') ?>"></label>
<label>Button link (optional, e.g. clubs.php or news.php)<input name="link" value="<?= e($edit['link'] ?? '') ?>"></label>
<label>Order (1 shows first)<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? count($list) + 1) ?>"></label>
<?php if ($edit && ($u = img_url('banners', $edit['image']))): ?><img class="thumb big" src="<?= $u ?>" alt="Current photo"><?php endif; ?>
<label>Banner photo (JPG, PNG or WebP, max 4 MB, wide photo)<input type="file" name="image" accept="image/*" <?= $edit ? '' : 'required' ?>></label>
<div class="acts" style="justify-content:flex-start"><button class="btn" type="submit"><?= $edit ? 'Save changes' : 'Add banner' ?></button><?php if ($edit): ?><a class="btn alt" href="?">Cancel</a><?php endif; ?></div></form>
<?php require '../includes/footer.php'; ?>
