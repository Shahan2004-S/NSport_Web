<?php
require_once '../config.php'; need_admin();
$page = 'reports';
$rows = q('SELECT c.name, COUNT(DISTINCT r.id) regs, COALESCE(SUM(a.present),0) attended FROM clubs c LEFT JOIN events e ON e.club_id=c.id LEFT JOIN registrations r ON r.event_id=e.id LEFT JOIN attendance a ON a.event_id=r.event_id AND a.user_id=r.user_id GROUP BY c.id, c.name ORDER BY regs DESC')->fetchAll();
if (isset($_GET['csv'])) {
    header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="participation-report.csv"');
    $o = fopen('php://output', 'w'); fputcsv($o, ['Club', 'Registrations', 'Attended']);
    foreach ($rows as $r) fputcsv($o, [$r['name'], $r['regs'], $r['attended']]);
    exit;
}
$bk = q('SELECT status, COUNT(*) n FROM bookings GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$max = max(1, ...array_column($rows, 'regs') ?: [1]);
require '../includes/admin_header.php';
?>
<h2>Reports</h2><p class="sub">Participation and booking summary.</p>
<h3 style="margin-bottom:12px">Participation by club</h3>
<?php foreach ($rows as $r): ?>
<div style="display:grid;grid-template-columns:150px 1fr 90px;gap:10px;align-items:center;max-width:640px;margin-bottom:8px"><span><?= e($r['name']) ?></span><div class="bar2"><i style="width:<?= $r['regs'] / $max * 100 ?>%"></i></div><b><?= (int)$r['regs'] ?> / <?= (int)$r['attended'] ?></b></div>
<?php endforeach; ?>
<p class="sub">Numbers show registrations / attended.</p>
<h3 style="margin-bottom:8px">Bookings</h3>
<p>Pending: <b><?= (int)($bk['pending'] ?? 0) ?></b> · Approved: <b><?= (int)($bk['approved'] ?? 0) ?></b> · Rejected: <b><?= (int)($bk['rejected'] ?? 0) ?></b></p>
<p><a class="btn" href="?csv=1">Download CSV</a> <button class="btn alt" onclick="print()">Print report</button></p>
<?php require '../includes/footer.php'; ?>
