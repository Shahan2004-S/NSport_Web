<?php
require_once 'config.php';
$sportName = $clubSport ?? '';
$club = q('SELECT * FROM clubs WHERE sport=? LIMIT 1', [$sportName])->fetch();
if (!$club) { http_response_code(404); die('Club not found. Please check the club data in the database.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    need_login(); check_csrf();
    if (($_POST['do'] ?? '') === 'cancel') {
        q("DELETE FROM memberships WHERE user_id=? AND club_id=? AND status='pending'", [user()['id'], (int)$club['id']]);
        flash('Join request cancelled.');
    }
    else {
        q('INSERT IGNORE INTO memberships(user_id, club_id) VALUES(?,?)', [user()['id'], (int)$club['id']]);
        flash('Join request sent. The club will review it.');
    }
    go('club-'.strtolower(str_replace(' ', '-', $sportName)).'.php');
}
$page = 'clubs'; $title = $club['name'];
$mine = user() ? q('SELECT status FROM memberships WHERE user_id=? AND club_id=?', [user()['id'], $club['id']])->fetchColumn() : false;
require 'includes/header.php';
$activities = [
'Athletics' => ['Sprint and distance running','Long jump and high jump','Throws and general fitness training'],
'Badminton' => ['Singles and doubles practice','Serving, footwork and rally drills','Friendly matches and tournaments'],
'Chess' => ['Practice games','Opening and endgame sessions','Friendly chess tournaments'],
'Cricket' => ['Batting and bowling practice','Fielding and fitness drills','Team matches and tournaments'],
'Martial Arts' => ['Basic techniques and movement','Balance and coordination practice','Guided fitness sessions'],
'Netball' => ['Passing and catching drills','Footwork and positional practice','Team games and competitions'],
'Rugby' => ['Passing and ball-handling skills','Movement and fitness training','Team tactics and supervised practice'],
'Swimming' => ['Swimming technique practice','Stroke development','Fitness and endurance sessions'],
'Table Tennis' => ['Serving and rally practice','Forehand and backhand drills','Friendly matches and tournaments'],
'Tennis' => ['Serving and groundstroke practice','Footwork and court movement','Singles and doubles games'],
'Volleyball' => ['Serving, passing and setting drills','Attacking and defensive skills','Team practice and friendly matches']
];

$about = [
'Athletics'=>'Athletics develops speed, stamina, strength and coordination through track and field activities.',
'Badminton'=>'Badminton helps students improve agility, coordination, concentration and racket skills.',
'Chess'=>'Chess encourages strategic thinking, patience, concentration and problem-solving.',
'Cricket'=>'Cricket brings students together to develop batting, bowling, fielding and teamwork.',
'Martial Arts'=>'Martial arts training builds discipline, balance, focus and physical fitness in a supervised setting.',
'Netball'=>'Netball develops teamwork, agility, communication and court awareness.',
'Rugby'=>'Rugby develops teamwork, discipline, fitness and game awareness.',
'Swimming'=>'Swimming supports fitness, water confidence and the development of swimming techniques.',
'Table Tennis'=>'Table tennis improves reflexes, coordination, precision and concentration.',
'Tennis'=>'Tennis combines movement, coordination and strategy through individual and doubles play.',
'Volleyball'=>'Volleyball builds communication, coordination and teamwork through court play.'
];

?>
<div class="crumb"><a href="<?= BASE ?>/index.php">Home</a> / <a href="<?= BASE ?>/clubs.php">Clubs</a> / <?= e($club['sport']) ?></div>
<div class="club-detail-hero">
  <?= club_img($club) ?>
  <div><span class="tag"><?= e($club['sport']) ?></span><h1><?= e($club['name']) ?></h1>
  <p><?= e($about[$sportName] ?? ($club['description'] ?: 'Learn new skills, take part in club activities and represent the university.')) ?></p></div>
</div>
<div class="club-detail-grid">
  <section class="card club-detail-box">
    <h2>About the club</h2>
    <p><?= e($about[$sportName] ?? ($club['description'] ?: 'Students can take part in club activities, learn new skills and represent the university at suitable events.')) ?></p>
    <h2 style="margin-top:24px">Club activities</h2>
    <ul><?php foreach (($activities[$sportName] ?? []) as $activity): ?><li><?= e($activity) ?></li><?php endforeach; ?></ul>
  </section>
  <aside class="card club-detail-box">
    <h2>Join the club</h2>
    <p>Interested in joining the <?= e($club['name']) ?>? Send a join request. The club will review your request.</p>
    <?php if ($mine): ?><div class="acts" style="justify-content:flex-start"><span class="tag <?= e($mine) ?>"><?= e(ucfirst($mine)) ?></span>
      <?php if ($mine === 'pending'): ?><form class="inline" method="post" data-confirm="Cancel your join request for the <?= e($club['name']) ?>?"><?= csrf() 
    ?><input type="hidden" name="do" value="cancel"><button class="btn cancel sm" type="submit">Cancel request</button></form><?php endif; ?></div>
    <?php elseif (!user()): ?><button class="btn" type="button" data-login-prompt="Request to join the <?= e($club['name']) ?>">Request to join</button>
    <?php else: ?><form method="post"><?= csrf() ?><button class="btn" type="submit">Request to join</button></form><?php endif; ?>
    <p style="margin-top:18px"><a class="btn alt" href="<?= BASE ?>/clubs.php">Back to all clubs</a></p>
  </aside>
</div>
<style>
.club-detail-hero{display:grid;grid-template-columns:minmax(180px,280px) 1fr;gap:22px;align-items:center;background:var(--brs);border-bottom:5px solid var(--am);padding:22px;margin:18px 0}
.club-detail-hero .ph{width:100%;height:170px;margin:0;object-fit:cover}
.club-detail-hero h1{font-size:clamp(25px,4vw,36px);margin:10px 0}
.club-detail-hero p{color:var(--mut);margin:0}
.club-detail-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:16px}
.club-detail-box h2{font-size:21px}.club-detail-box p,.club-detail-box li{color:var(--mut)}
.club-detail-box li{margin:8px 0}
@media(max-width:700px){.club-detail-hero,.club-detail-grid{grid-template-columns:1fr}.club-detail-hero .ph{height:150px}}
</style>
<?php require 'includes/footer.php'; ?>
