<?php
require_once '../config.php'; need_admin();
$page = 'clubs';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) { q('DELETE FROM clubs WHERE id=?', [(int)$_POST['delete']]); flash('Club deleted.'); go('admin/clubs.php'); }
    $id = (int)($_POST['id'] ?? 0); $n = trim($_POST['name']); $sp = trim($_POST['sport']); $d = trim($_POST['description']); $u = trim($_POST['page_url']);
    if (!$n || !$sp) { flash('Name and sport are required.'); go('admin/clubs.php'); }
    $img = $id ? q('SELECT image FROM clubs WHERE id=?', [$id])->fetchColumn() : null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['image']['size'] <= 2097152 && @getimagesize($_FILES['image']['tmp_name'])) {
            $img = bin2hex(random_bytes(8)).'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../images/clubs/'.$img);
        } else { flash('Photo must be a JPG, PNG or WebP image under 2 MB.'); go('admin/clubs.php'); }
    }
    if ($id) q('UPDATE clubs SET name=?, sport=?, description=?, page_url=?, image=? WHERE id=?', [$n, $sp, $d, $u, $img, $id]);
    else q('INSERT INTO clubs(name, sport, description, page_url, image) VALUES(?,?,?,?,?)', [$n, $sp, $d, $u, $img]);
    flash('Club saved.'); go('admin/clubs.php');
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM clubs WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$clubs = q('SELECT * FROM clubs ORDER BY name')->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Clubs</h2><p class="sub">Add, edit or delete sports clubs.</p>
<div class="wrap"><table><thead><tr><th>Club</th><th>Sport</th><th>Photo</th><th></th></tr></thead><tbody>
<?php foreach ($clubs as $c): ?><tr><td><?= e($c['name']) ?></td><td><?= e($c['sport']) ?></td><td><?= $c['image'] ? 'Yes' : 'No' ?></td>
<td><a class="btn alt sm" href="?edit=<?= $c['id'] ?>">Edit</a>
<form class="inline" method="post" data-confirm="Delete this club?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $c['id'] ?>">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<h3 style="margin:24px 0 12px"><?= $edit ? 'Edit club' : 'Add a club' ?></h3>
<form class="f" method="post" enctype="multipart/form-data"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<label>Club name<input name="name" value="<?= e($edit['name'] ?? '') ?>" required></label>
<label>Sport<input name="sport" value="<?= e($edit['sport'] ?? '') ?>" required></label>
<label>Official page link<input type="url" name="page_url" value="<?= e($edit['page_url'] ?? '') ?>"></label>
<label>Description<textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></label>
<label>Club photo (JPG, PNG or WebP, max 2 MB)<input type="file" name="image" accept="image/*"></label>
<button class="btn" type="submit">Save club</button></form>
<?php require '../includes/footer.php'; ?>
