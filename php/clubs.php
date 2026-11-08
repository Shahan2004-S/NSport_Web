<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    need_login();

    q(
        'INSERT IGNORE INTO memberships(user_id, club_id) VALUES(?,?)',
        [user()['id'], (int)$_POST['club']]
    );

    flash('Join request sent. The club will review it.');
    go('clubs.php');
}

$page = 'clubs';
$title = 'Clubs';

$sport = $_GET['sport'] ?? '';
$s = trim($_GET['q'] ?? '');

$clubs = q(
    'SELECT * FROM clubs WHERE name LIKE ?' . ($sport ? ' AND sport=?' : '') . ' ORDER BY name',
    $sport ? ["%$s%", $sport] : ["%$s%"]
)->fetchAll();

$sports = q(
    'SELECT DISTINCT sport FROM clubs ORDER BY sport'
)->fetchAll(PDO::FETCH_COLUMN);

$mine = user()
    ? q(
        'SELECT club_id, status FROM memberships WHERE user_id=?',
        [user()['id']]
    )->fetchAll(PDO::FETCH_KEY_PAIR)
    : [];

require 'includes/header.php';
?>

<h2>Sports clubs</h2>

<p class="sub">
    The official NSBM sports clubs. Send a join request and the club approves it.
</p>

<form class="filters" method="get">

    <input
        type="search"
        name="q"
        value="<?= e($s) ?>"
        placeholder="Search clubs"
        aria-label="Search clubs"
    >

    <select name="sport" aria-label="Sport">
        <option value="">All sports</option>

        <?php foreach ($sports as $sp): ?>
            <option <?= $sp === $sport ? 'selected' : '' ?>>
                <?= e($sp) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <button class="btn" type="submit">Filter</button>

</form>

<div class="grid">

    <?php foreach ($clubs as $c): ?>

        <div class="card">

            <!-- ################################################################ -->
            <!-- ##  📷 PHOTO #2 : CLUB PHOTO (one per club)                    ## -->
            <!-- ##  Do NOT edit here. Upload it in Admin > Clubs > Edit club.  ## -->
            <!-- ##  It is saved in images/clubs/ and shown automatically.      ## -->
            <!-- ################################################################ -->

            <?= club_img($c) ?>

            <span class="tag"><?= e($c['sport']) ?></span>

            <h3 style="margin-top:10px">
                <?= e($c['name']) ?>
            </h3>

            <small>
                <a href="<?= BASE ?>/club-<?= e(strtolower(str_replace(' ', '-', $c['sport']))) ?>.php">
                    More about the <?= e($c['name']) ?>
                </a>
            </small>

            <div class="row">
                <span></span>

                <?php if (isset($mine[$c['id']])): ?>

                    <span class="tag">
                        <?= e(ucfirst($mine[$c['id']])) ?>
                    </span>

                <?php else: ?>

                    <form method="post">
                        <?= csrf() ?>

                        <input
                            type="hidden"
                            name="club"
                            value="<?= $c['id'] ?>"
                        >

                        <button class="btn sm">Request to join</button>
                    </form>

                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

    <?php if (!$clubs) echo '<p>No clubs found.</p>'; ?>

</div>

<?php require 'includes/footer.php'; ?>
