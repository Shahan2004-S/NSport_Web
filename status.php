<?php
// Admin-only page: shows whether the v4 update is working on this server (database, uploads, Excel reading).
require_once '../config.php'; need_admin();
$page = 'roster';
$ok = fn($b) => $b ? '<span class="tag approved">OK</span>' : '<span class="tag rejected">Problem</span>';
$checks = [];
$checks[] = ['Database connection', true, 'Connected to "'.DB_NAME.'".'];
$col = (bool)q("SHOW COLUMNS FROM users LIKE 'last_seen'")->fetch();
$checks[] = ['Column users.last_seen (students online)', $col, $col ? 'Present.' : 'Missing. Run database_update_v4.sql in phpMyAdmin.'];
$tbl = (bool)q("SHOW TABLES LIKE 'student_roster'")->fetch();
$checks[] = ['Table student_roster (campus student list)', $tbl, $tbl ? ((int)q('SELECT COUNT(*) FROM student_roster')->fetchColumn().' students in the list.') : 'Missing. Run database_update_v4.sql in phpMyAdmin.'];
$checks[] = ['Excel (.xlsx) upload', class_exists('ZipArchive'), class_exists('ZipArchive') ? 'This server can read Excel files.' : 'This server cannot read .xlsx. Save the sheet as .csv instead.'];
$checks[] = ['Live count (heartbeat.php)', is_file(__DIR__.'/../heartbeat.php'), 'Students online now: '.online_count().'.'];
$checks[] = ['BASE setting in config.php', true, 'BASE is "'.BASE.'". On InfinityFree it should be an empty value: \'\'.'];
$checks[] = ['PHP version', version_compare(PHP_VERSION, '7.4', '>='), PHP_VERSION];
$checks[] = ['v4 files', is_file(__DIR__.'/roster.php') && is_file(__DIR__.'/online.php'), 'roster.php and online.php are uploaded.'];
require '../includes/admin_header.php';
?>
<h2>Setup check</h2><p class="sub">If everything says OK, the campus student list and the live online count are working. You can delete admin/status.php afterwards.</p>
<div class="wrap"><table><thead><tr><th>Check</th><th>Result</th><th>Details</th></tr></thead><tbody>
<?php foreach ($checks as $c): ?><tr><td><?= e($c[0]) ?></td><td><?= $ok($c[1]) ?></td><td><?= e($c[2]) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require '../includes/footer.php'; ?>
