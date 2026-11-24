</main>
<footer><div><b>NSBM Green University</b><span>Mahenwaththa, Pitipana, Homagama, Sri Lanka</span><span>+94 11 544 5000 · inquiries@nsbm.ac.lk</span><span>Student project for the Faculty of Computing. Not the official NSBM website.</span></div></footer>
<?php if (!user()): ?>
<div class="modal" id="loginModal" hidden>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="lmTitle">
    <button class="modal-x" type="button" aria-label="Close" data-close>×</button>
    <div class="modal-ico" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div>
    <h3 id="lmTitle">Log in to continue</h3>
    <p id="lmText">You need an account to send requests.</p>
    <div class="modal-acts">
      <a class="btn" href="<?= BASE ?>/login.php">Student log in</a>
      <a class="btn alt" href="<?= BASE ?>/register.php">Create account</a>
    </div>
    <small><a href="<?= BASE ?>/admin/login.php">Admin log in</a> · <a href="<?= BASE ?>/admin/register.php">Admin sign up</a></small>
  </div>
</div>
<?php endif; ?>
</body></html>
