<?php
require_once '../config.php'; need_admin();
$page = 'users';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    q("DELETE FROM users WHERE id=? AND role='student'", [(int)($_POST['delete'] ?? 0)]); flash('Student account removed.'); go('admin/users.php');
}
$rows = q("SELECT u.*, (SELECT COUNT(*) FROM memberships m WHERE m.user_id=u.id AND m.status='approved') clubs,
  (SELECT COUNT(*) FROM memberships m WHERE m.user_id=u.id AND m.status='pending') pend,
  (SELECT COUNT(*) FROM registrations r WHERE r.user_id=u.id) regs,
  (SELECT COUNT(*) FROM bookings b WHERE b.user_id=u.id) books FROM users u ORDER BY (u.role='admin') DESC, u.created_at DESC")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Accounts</h2><p class="sub">Every student and admin who created an account, with their activity.</p>
<div class="wrap"><table><thead><tr><th>Name</th><th>Role</th><th>Email</th><th>Student ID</th><th>Joined</th><th>Clubs</th><th>Pending</th><th>Events</th><th>Bookings</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><b><?= e($r['name']) ?></b></td><td><span class="tag <?= $r['role'] === 'admin' ? 'approved' : '' ?>"><?= e(ucfirst($r['role'])) ?></span></td>
<td><?= e($r['email']) ?></td><td><?= e($r['student_id'] ?: '-') ?></td><td><?= e(date('j M Y', strtotime($r['created_at']))) ?></td>
<td><?= (int)$r['clubs'] ?></td><td><?= (int)$r['pend'] ?></td><td><?= (int)$r['regs'] ?></td><td><?= (int)$r['books'] ?></td>
<td><?php if ($r['role'] === 'student'): ?><form class="inline" method="post" data-confirm="Remove this student account?"><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Remove</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require '../includes/footer.php'; ?>
