<?php
require_once '../config.php'; need_admin();
$page = 'memberships';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    q('UPDATE memberships SET status=? WHERE id=?', [$_POST['do'] === 'approve' ? 'approved' : 'rejected', (int)$_POST['id']]);
    flash('Request updated.'); go('admin/memberships.php');
}
$rows = q("SELECT m.*, u.name, u.email, c.name club FROM memberships m JOIN users u ON u.id=m.user_id JOIN clubs c ON c.id=m.club_id ORDER BY (m.status='pending') DESC, m.created_at DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Membership approvals</h2><p class="sub">Students who asked to join a club.</p>
<div class="wrap"><table><thead><tr><th>Student</th><th>Club</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['name']) ?><br><small><?= e($r['email']) ?></small></td><td><?= e($r['club']) ?></td><td><span class="tag"><?= e(ucfirst($r['status'])) ?></span></td>
<td><?php if ($r['status'] === 'pending'): ?><form class="inline" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
<button class="btn sm" name="do" value="approve">Approve</button> <button class="btn no sm" name="do" value="reject">Reject</button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="4">No requests yet.</td></tr>'; ?></tbody></table></div>
<?php require '../includes/footer.php'; ?>
