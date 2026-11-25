<?php
// The NSBM campus student list. Only students listed here can create an account and log in.
require_once '../config.php'; need_admin();
$page = 'roster';

// "Download example sheet": a small file the admin can open in Excel, fill in and upload again
if (isset($_GET['template'])) {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="student-list-example.csv"');
    echo "student_id,full_name,email\r\n26.1-IFSL-001,Nimal Perera,nimal@example.com\r\n26.1-IFSL-002,Kumari Silva,kumari@example.com\r\n"; exit;
}

// Reads the first sheet of an Excel (.xlsx) file and returns its rows as arrays. Returns null if the file cannot be read.
function xlsx_rows($path) {
    if (!class_exists('ZipArchive')) return null;
    $z = new ZipArchive(); if ($z->open($path) !== true) return null;
    $shared = [];
    if (($x = $z->getFromName('xl/sharedStrings.xml')) !== false && ($sx = @simplexml_load_string($x))) {
        foreach ($sx->si as $si) { $t = ''; if (isset($si->t)) $t = (string)$si->t; else foreach ($si->r as $r) $t .= (string)$r->t; $shared[] = $t; }
    }
    $sheet = $z->getFromName('xl/worksheets/sheet1.xml');
    if ($sheet === false) for ($i = 0; $i < $z->numFiles; $i++) { if (preg_match('#^xl/worksheets/sheet\d+\.xml$#', $z->getNameIndex($i))) { $sheet = $z->getFromIndex($i); break; } }
    $z->close();
    if ($sheet === false || !($sx = @simplexml_load_string($sheet))) return null;
    $rows = [];
    foreach ($sx->sheetData->row as $row) {
        $r = [];
        foreach ($row->c as $c) {
            preg_match('/^([A-Z]+)/', (string)$c['r'], $m); $col = 0;
            foreach (str_split($m[1] ?? 'A') as $ch) $col = $col * 26 + (ord($ch) - 64);
            $t = (string)$c['t'];
            $v = $t === 's' ? ($shared[(int)$c->v] ?? '') : ($t === 'inlineStr' ? (string)$c->is->t : (string)$c->v);
            $r[$col - 1] = trim($v);
        }
        if ($r) { $out = []; for ($i = 0; $i <= max(array_keys($r)); $i++) $out[] = $r[$i] ?? ''; $rows[] = $out; }
    }
    return $rows;
}

// add one student, or update the name / email of an existing student ID
function roster_save($sid, $name, $em) {
    $sid = trim($sid); $name = trim($name); $em = strtolower(trim($em));
    if ($sid === '' || $name === '' || !filter_var($em, FILTER_VALIDATE_EMAIL) || strlen($sid) > 30 || strlen($name) > 100 || strlen($em) > 120) return 'skipped';
    try {
        $n = q('INSERT INTO student_roster(student_id, full_name, email) VALUES(?,?,?) ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), email=VALUES(email)', [$sid, $name, $em])->rowCount();
        return $n === 1 ? 'added' : 'updated';
    } catch (PDOException $x) { return 'skipped'; }      // e.g. that email already belongs to another student ID
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (isset($_POST['delete'])) {
        q('DELETE FROM student_roster WHERE id=?', [(int)$_POST['delete']]);
        flash('Removed from the student list. That student can no longer log in.'); go('admin/roster.php');
    }
    if (isset($_POST['adopt'])) {   // let students who already have an account keep logging in
        q("INSERT IGNORE INTO student_roster(student_id, full_name, email)
           SELECT IF(u.student_id IS NULL OR u.student_id = '', CONCAT('ACC-', u.id), u.student_id), u.name, u.email FROM users u
           WHERE u.role='student' AND NOT EXISTS (SELECT 1 FROM student_roster r WHERE r.email = u.email)");
        flash('Existing student accounts were added to the student list.'); go('admin/roster.php');
    }
    if (isset($_POST['one'])) {
        $r = roster_save($_POST['student_id'] ?? '', $_POST['full_name'] ?? '', $_POST['email'] ?? '');
        flash($r === 'skipped' ? 'Fill in all three fields with a valid email. The email or ID may already be used by another student.' : ($r === 'added' ? 'Student added to the list.' : 'Student details updated.'));
        go('admin/roster.php');
    }
    // import many: an Excel (.xlsx) file, a .csv file, or pasted lines. Columns: student_id, full name, email
    $all = [];
    if (!empty($_FILES['sheet']['name']) && $_FILES['sheet']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['sheet']['name'], PATHINFO_EXTENSION));
        if ($_FILES['sheet']['size'] > 2097152) { flash('That file is too big (max 2 MB).'); go('admin/roster.php'); }
        if ($ext === 'xlsx') {
            $all = xlsx_rows($_FILES['sheet']['tmp_name']);
            if ($all === null) { flash('Could not read that Excel file. Save it as .xlsx (Excel Workbook) or as .csv and try again.'); go('admin/roster.php'); }
        } elseif ($ext === 'csv' || $ext === 'txt') {
            foreach (preg_split('/\r\n|\r|\n/', preg_replace('/^\xEF\xBB\xBF/', '', (string)file_get_contents($_FILES['sheet']['tmp_name']))) as $line) if (trim($line) !== '') $all[] = array_map('trim', str_getcsv($line));
        } else { flash('Please upload an Excel (.xlsx) or .csv file. Old .xls files: open them in Excel and use Save As .xlsx.'); go('admin/roster.php'); }
    } else {
        foreach (preg_split('/\r\n|\r|\n/', (string)($_POST['rows'] ?? '')) as $line) if (trim($line) !== '') $all[] = array_map('trim', str_getcsv($line));
    }
    $c = ['added' => 0, 'updated' => 0, 'skipped' => 0]; $n = 0;
    foreach ($all as $f) {
        if (!array_filter($f, fn($x) => $x !== '')) continue;
        if (++$n > 5000) break;
        if ($n === 1 && (count($f) < 3 || !filter_var($f[2], FILTER_VALIDATE_EMAIL))) continue;   // header row
        if (count($f) < 3) { $c['skipped']++; continue; }
        $c[roster_save($f[0], $f[1], $f[2])]++;
    }
    flash("Import finished: {$c['added']} added, {$c['updated']} updated, {$c['skipped']} skipped."); go('admin/roster.php');
}

$qs = trim($_GET['q'] ?? '');
$sql = "SELECT r.*, u.id AS uid, (u.last_seen >= NOW() - INTERVAL ".ONLINE_MINUTES." MINUTE) AS online
        FROM student_roster r LEFT JOIN users u ON u.email = r.email AND u.role='student'";
$p = [];
if ($qs !== '') { $sql .= ' WHERE r.student_id LIKE ? OR r.full_name LIKE ? OR r.email LIKE ?'; $like = '%'.$qs.'%'; $p = [$like, $like, $like]; }
$rows = q($sql.' ORDER BY r.student_id LIMIT 300', $p)->fetchAll();
$total = (int)q('SELECT COUNT(*) FROM student_roster')->fetchColumn();
$made = (int)q("SELECT COUNT(*) FROM student_roster r JOIN users u ON u.email = r.email AND u.role='student'")->fetchColumn();
$orphans = (int)q("SELECT COUNT(*) FROM users u WHERE u.role='student' AND NOT EXISTS (SELECT 1 FROM student_roster r WHERE r.email = u.email)")->fetchColumn();
require '../includes/admin_header.php';
?>
<h2>Student list</h2>
<p class="sub">Only students in this list can create an account and log in. Add your campus students here (one by one, or many at once).</p>
<div class="stats"><div class="card"><b><?= $total ?></b>In the student list</div><div class="card"><b><?= $made ?></b>Accounts created</div><div class="card"><b><?= max(0, $total - $made) ?></b>Not signed up yet</div></div>
<?php if ($orphans): ?><div class="card" style="margin:0 0 20px"><b><?= $orphans ?> existing student account<?= $orphans > 1 ? 's are' : ' is' ?> not in the list</b>, so <?= $orphans > 1 ? 'they cannot' : 'it cannot' ?> log in.
<form class="inline" method="post" data-confirm="Add all existing student accounts to the list?" style="margin-left:8px"><?= csrf() ?><button class="btn sm" name="adopt" value="1">Add them to the list</button></form></div><?php endif; ?>

<form class="f" method="get" style="max-width:none;display:flex;gap:10px;margin-bottom:12px"><input name="q" value="<?= e($qs) ?>" placeholder="Search by student ID, name or email" aria-label="Search the student list" style="flex:1;max-width:420px"><button class="btn alt" type="submit">Search</button><?php if ($qs !== ''): ?><a class="btn alt" href="roster.php">Clear</a><?php endif; ?></form>
<div class="wrap"><table><thead><tr><th>Student ID</th><th>Name</th><th>University email</th><th>Account</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><b><?= e($r['student_id']) ?></b></td><td><?= e($r['full_name']) ?></td><td><?= e($r['email']) ?></td>
<td><?php if ($r['online']): ?><span class="live-on"><i class="dot"></i>Online now</span><?php elseif ($r['uid']): ?><span class="tag approved">Signed up</span><?php else: ?><span class="tag">Not yet</span><?php endif; ?></td>
<td><form class="inline" method="post" data-confirm="Remove this student from the list? They will not be able to log in."><?= csrf() ?><button class="btn no sm" name="delete" value="<?= $r['id'] ?>">Remove</button></form></td></tr>
<?php endforeach; if (!$rows) echo '<tr><td colspan="5">'.($qs !== '' ? 'No student matches your search.' : 'The list is empty. Add your first student below.').'</td></tr>'; ?>
</tbody></table></div>
<?php if ($total > 300 && $qs === ''): ?><p><small>Showing the first 300 students. Use the search box to find others.</small></p><?php endif; ?>

<h3 id="form" style="margin:26px 0 12px">Add one student</h3>
<form class="f" method="post"><?= csrf() ?><input type="hidden" name="one" value="1">
<label>Student ID<input name="student_id" maxlength="30" required></label>
<label>Full name<input name="full_name" maxlength="100" required></label>
<label>University email<input type="email" name="email" maxlength="120" required></label>
<button class="btn" type="submit">Add to list</button></form>

<h3 style="margin:26px 0 12px">Add many students from an Excel sheet</h3>
<p class="sub" style="margin-bottom:12px">Make an Excel sheet with 3 columns: <code>student_id</code>, <code>full_name</code>, <code>email</code> (one student per row, the first row can be the heading). Save it and upload it here.
<a href="roster.php?template=1">Download an example sheet</a></p>
<form class="f" method="post" enctype="multipart/form-data"><?= csrf() ?>
<label>Excel file (.xlsx) or .csv file (max 2 MB)<input type="file" name="sheet" accept=".xlsx,.csv,.txt"></label>
<label>or paste rows here (student_id, full name, email)<textarea name="rows" rows="4" placeholder="26.1-IFSL-014, Nimal Perera, nimal@students.nsbm.ac.lk"></textarea></label>
<button class="btn" type="submit">Import</button></form>
<p><small><a href="status.php">Check that everything is set up</a></small></p>
<?php require '../includes/footer.php'; ?>
