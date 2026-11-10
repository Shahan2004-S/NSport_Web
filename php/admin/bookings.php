<?php
require_once '../config.php'; need_admin();
$page = 'bookings';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $id = (int)$_POST['id']; $st = $_POST['do'] === 'approve' ? 'approved' : 'rejected';
    $b = q('SELECT * FROM bookings WHERE id=?', [$id])->fetch();
    if ($b && $st === 'approved' && q("SELECT 1 FROM bookings WHERE facility_id=? AND booking_date=? AND slot=? AND status='approved' AND id<>?", [$b['facility_id'], $b['booking_date'], $b['slot'], $id])->fetch())
        flash('Another booking is already approved for that slot.');
    else { q('UPDATE bookings SET status=? WHERE id=?', [$st, $id]); flash('Booking '.$st.'.'); }
    go('admin/bookings.php');
}
$rows = q("SELECT b.*, u.name, f.name facility FROM bookings b JOIN users u ON u.id=b.user_id JOIN facilities f ON f.id=b.facility_id ORDER BY (b.status='pending') DESC, b.booking_date")->fetchAll();
require '../includes/admin_header.php';
?>
<h2>Booking requests</h2><p class="sub">Facility and equipment requests from students.</p>
<div class="wrap"><table><thead><tr><th>Student</th><th>Facility or equipment</th><th>When</th><th>Purpose</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['facility']) ?></td><td><?= e($r['booking_date']) ?><br><small><?= e($SLOTS[$r['slot']]) ?></small></td><td><?= e($r['purpose']) ?></td><td><span class="tag"><?= e(ucfirst($r['status'])) ?></span></td>
<td><?php if ($r['status'] === 'pending'): ?><form class="inline" method="post"><?= csrf() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn sm" name="do" value="approve">Approve</button> <button class="btn no sm" name="do" value="reject">Reject</button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="6">No booking requests yet.</td></tr>'; ?></tbody></table></div>
<?php require '../includes/footer.php'; ?>
