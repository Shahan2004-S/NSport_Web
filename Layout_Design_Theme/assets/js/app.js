const root = document.documentElement, btn = document.getElementById('themeBtn');
function setTheme(t) {
  root.dataset.theme = t;
  if (btn) { btn.classList.toggle('on', t === 'light'); btn.querySelector('span').textContent = t === 'dark' ? 'Dark mode' : 'Light mode'; }
  try { localStorage.setItem('th', t); } catch (e) {}
}
let saved; try { saved = localStorage.getItem('th'); } catch (e) {}
setTheme(saved || (matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light'));
if (btn) btn.onclick = () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
document.querySelectorAll('form[data-confirm]').forEach(f => f.onsubmit = e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); });

const calm = matchMedia('(prefers-reduced-motion:reduce)').matches;
document.querySelectorAll('.grid').forEach(g => [...g.children].forEach((c, i) => c.style.setProperty('--i', i)));
document.querySelectorAll('.auth input[type=password]').forEach(i => {
  const w = document.createElement('span'); w.className = 'pw'; i.parentNode.insertBefore(w, i); w.appendChild(i);
  const b = document.createElement('button'); b.type = 'button'; b.className = 'pw-btn'; b.textContent = 'Show'; b.setAttribute('aria-label', 'Show password');
  b.onclick = () => { const s = i.type === 'password'; i.type = s ? 'text' : 'password'; b.textContent = s ? 'Hide' : 'Show'; b.setAttribute('aria-label', s ? 'Hide password' : 'Show password'); };
  w.appendChild(b);
});
if (!calm) document.querySelectorAll('.nums b').forEach(b => {
  const m = b.textContent.trim().match(/^(\d+)(.*)$/); if (!m || +m[1] < 2) return;
  const end = +m[1], suf = m[2], t0 = performance.now();
  (function step(t) { const p = Math.min(1, (t - t0) / 900); b.textContent = Math.round(end * (1 - Math.pow(1 - p, 3))) + suf; if (p < 1) requestAnimationFrame(step); })(t0);
});

(function () {
  const sl = document.getElementById('slider'); if (!sl) return;
  const slides = [...sl.querySelectorAll('.slide')], dots = [...sl.querySelectorAll('.sl-dots button')];
  if (slides.length < 2) return;
  let i = 0, timer;
  function show(n) {
    i = (n + slides.length) % slides.length;
    slides.forEach((s, k) => { s.classList.toggle('on', k === i); s.setAttribute('aria-hidden', k === i ? 'false' : 'true'); });
    dots.forEach((d, k) => d.classList.toggle('on', k === i));
  }
  function play() { clearInterval(timer); if (!calm) timer = setInterval(() => show(i + 1), 6000); }
  sl.querySelector('.prev').onclick = () => { show(i - 1); play(); };
  sl.querySelector('.next').onclick = () => { show(i + 1); play(); };
  dots.forEach((d, k) => d.onclick = () => { show(k); play(); });
  sl.addEventListener('mouseenter', () => clearInterval(timer)); sl.addEventListener('mouseleave', play);
  let x0 = null;
  sl.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
  sl.addEventListener('touchend', e => { if (x0 === null) return; const d = e.changedTouches[0].clientX - x0; if (Math.abs(d) > 40) { show(i + (d < 0 ? 1 : -1)); play(); } x0 = null; });
  play();
})();

(function () {
  const m = document.getElementById('loginModal'); if (!m) return;
  const txt = document.getElementById('lmText'); let last;
  function open(msg, from) { last = from; txt.textContent = (msg ? msg + ': ' : '') + 'please log in or create an account first.'; m.hidden = false; m.querySelector('.btn').focus(); }
  function close() { m.hidden = true; if (last) last.focus(); }
  document.querySelectorAll('[data-login-prompt]').forEach(b => b.addEventListener('click', () => open(b.dataset.loginPrompt, b)));
  m.addEventListener('click', e => { if (e.target === m || e.target.closest('[data-close]')) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !m.hidden) close(); });
})();

document.addEventListener('click', e => document.querySelectorAll('details.menu[open]').forEach(d => { if (!d.contains(e.target)) d.removeAttribute('open'); }));

/* v4: heartbeat (students) + live "students online" (admin) */
(function () {
  const hb = document.querySelector('meta[name=hb]');
  if (hb) {   // a logged-in student: tell the server "I am here" about once a minute while the page is visible
    const ping = () => { if (document.visibilityState === 'visible') fetch(hb.content, { method: 'POST', credentials: 'same-origin', cache: 'no-store' }).catch(() => {}); };
    setInterval(ping, 60000);
    document.addEventListener('visibilitychange', ping);
  }
  const live = document.querySelector('[data-live]');
  if (!live) return;   // the rest is for the admin pages
  const list = document.querySelector('[data-live-list]'), stamp = document.querySelector('[data-live-time]');
  const ago = s => (s = +s) < 15 ? 'just now' : s < 60 ? s + ' s ago' : Math.floor(s / 60) + ' min ago';
  function render(d) {
    document.querySelectorAll('[data-live-count]').forEach(el => {
      if (el.textContent !== String(d.count)) { el.textContent = d.count; el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); }
    });
    if (list) {
      const chips = d.students.map(s => {
        const c = document.createElement('span'), n = document.createElement('b'), m = document.createElement('small');
        c.className = 'chip-on'; n.textContent = s.name; m.textContent = (s.student_id ? s.student_id + ' · ' : '') + ago(s.ago); c.append(n, m); return c;
      });
      if (!chips.length) { const p = document.createElement('p'); p.className = 'empty-on'; p.textContent = 'Nobody is logged in right now.'; chips.push(p); }
      list.replaceChildren(...chips);
    }
    if (stamp) stamp.textContent = 'Updated ' + new Date().toLocaleTimeString();
  }
  let busy = false;
  async function poll() {
    if (busy || document.visibilityState !== 'visible') return;
    busy = true;
    try { const r = await fetch(live.dataset.url, { credentials: 'same-origin', cache: 'no-store' }); if (r.ok) render(await r.json()); } catch (e) { /* try again next time */ }
    busy = false;
  }
  setInterval(poll, 10000);
  document.addEventListener('visibilitychange', poll);
  poll();
})();
