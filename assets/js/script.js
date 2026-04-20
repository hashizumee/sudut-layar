// ═══════════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════════
let authMode = 'login';
let currentProjectId = null;

// ═══════════════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  initCursor();
  initReveal();

  // ESC key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      closeVideoModal();
      closeLoginModal();
    }
  });
});

// ═══════════════════════════════════════════════════
//  CURSOR
// ═══════════════════════════════════════════════════
function initCursor() {
  const c = document.getElementById('cursor');
  const r = document.getElementById('cursorRing');
  if (!c || !r) return;
  document.addEventListener('mousemove', e => {
    c.style.left = e.clientX + 'px';
    c.style.top = e.clientY + 'px';
    r.style.left = e.clientX + 'px';
    r.style.top = e.clientY + 'px';
  });
}

// ═══════════════════════════════════════════════════
//  SCROLL REVEAL
// ═══════════════════════════════════════════════════
function initReveal() {
  const els = document.querySelectorAll('.reveal');
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
  }, { threshold: 0.1 });
  els.forEach(el => obs.observe(el));
}

// ═══════════════════════════════════════════════════
//  NAV HELPERS
// ═══════════════════════════════════════════════════
function navTo(e, id) { e.preventDefault(); document.getElementById(id)?.scrollIntoView({ behavior:'smooth' }); }
function scrollToTop(e) { e.preventDefault(); window.scrollTo({ top:0, behavior:'smooth' }); }

// ═══════════════════════════════════════════════════
//  VIDEO MODAL
// ═══════════════════════════════════════════════════
function openVideoModal(id) {
  const p = PROJECTS_DATA.find(x => x.id == id);
  if (!p) return;

  currentProjectId = id;
  document.getElementById('vCat').textContent = p.category;
  document.getElementById('vTitle').textContent = p.title;

  let src = `https://drive.google.com/file/d/${p.google_drive_id}/preview`;
  if (p.is_drive_folder == 1) {
    src = `https://drive.google.com/file/d/${p.google_drive_id}/preview`; 
  }
  document.getElementById('videoFrame').src = src;

  loadComments(id);

  const overlay = document.getElementById('videoModal');
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function openReviewModal(id) {
  openVideoModal(id);
  setTimeout(() => {
    document.getElementById('commentsSection')?.scrollIntoView({ behavior:'smooth' });
  }, 400);
}

function closeVideoModal() {
  document.getElementById('videoModal').classList.remove('open');
  document.getElementById('videoFrame').src = '';
  document.body.style.overflow = '';
}

document.getElementById('videoModal').addEventListener('click', e => {
  if (e.target === document.getElementById('videoModal')) closeVideoModal();
});

// ═══════════════════════════════════════════════════
//  COMMENTS & RATINGS
// ═══════════════════════════════════════════════════
async function loadComments(id) {
  try {
    const res = await fetch(`includes/api.php?action=get_comments&id=${id}`);
    const data = await res.json();
    renderComments(data);
  } catch (e) { console.error("Failed to load comments", e); }
}

function renderComments(data) {
  const avgArea = document.getElementById('avgRatingArea');
  const listEl = document.getElementById('commentsList');
  const formArea = document.getElementById('commentFormArea');

  // Avg Rating
  if (data.total_comments > 0) {
    avgArea.innerHTML = `
      <div style="display:flex; align-items:center; gap:1.5rem;">
        <div style="font-size:3rem; font-weight:300; color:var(--copper);">${data.avg_rating.toFixed(1)}<small style="font-size:1rem; color:var(--muted);"> / 5.0</small></div>
        <div>
          <div class="stars">${starsHtml(Math.round(data.avg_rating))}</div>
          <div style="font-family:'DM Mono',monospace; font-size:0.65rem; color:var(--muted); margin-top:0.3rem;">Berdasarkan ${data.total_comments} ulasan</div>
        </div>
      </div>
    `;
  } else {
    avgArea.innerHTML = `<p style="font-style:italic; color:var(--muted);">Belum ada ulasan untuk film ini.</p>`;
  }

  // Form
  // (Assuming global variable IS_LOGGED_IN is set or similar, but for now we'll check if userIndicator is visible)
  const isLoggedIn = document.getElementById('userIndicator').classList.contains('show');
  
  if (isLoggedIn) {
    formArea.innerHTML = `
      <div class="comment-form" style="background:#fff; padding:1.5rem; border:1px solid rgba(0,0,0,0.05); border-radius:4px;">
        <h4 style="margin-bottom:1rem; font-weight:400;">Berikan Rating & Ulasan</h4>
        <div id="commentMsg" style="display:none; padding:0.5rem; margin-bottom:1rem; font-size:0.85rem;"></div>
        <div style="margin-bottom:1rem;">
          <label style="display:block; font-family:'DM Mono',monospace; font-size:0.6rem; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Rating</label>
          <div class="stars interactive" id="inputRating" data-value="5">
            ${interactiveStarsHtml(5)}
          </div>
        </div>
        <div style="margin-bottom:1.5rem;">
          <label style="display:block; font-family:'DM Mono',monospace; font-size:0.6rem; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Pesan Ulasan</label>
          <textarea id="commentTextInput" style="width:100%; min-height:80px; padding:0.75rem; border:1px solid #ddd; border-radius:4px; font-family:inherit; outline:none;" placeholder="Tulis pendapat Anda..."></textarea>
        </div>
        <button class="btn-complaint" onclick="submitComment()" style="padding:0.75rem 1.5rem; font-size:0.65rem;">Kirim Ulasan</button>
      </div>
    `;
  } else {
    formArea.innerHTML = `
      <div style="background:#f5f5f5; padding:1.5rem; border-radius:4px; text-align:center; color:var(--muted);">
        Silakan <a href="#" onclick="closeVideoModal();openLoginModal(event);return false" style="color:var(--copper); font-style:italic;">Masuk</a> untuk memberikan ulasan.
      </div>
    `;
  }

  // List
  if (data.comments.length > 0) {
    listEl.innerHTML = `<h4>Komentar (${data.total_comments})</h4>` + 
    data.comments.map(c => {
      const replies = (c.replies || []).map(r => `
        <div style="margin-top:0.75rem; padding:0.75rem 1rem; background:rgba(0,0,0,0.02); border-left:2px solid var(--copper); border-radius:4px;">
          <div style="font-weight:600; font-size:0.85rem;">${esc(r.user_name)} <span style="color:var(--copper); font-style:italic; font-weight:400;">↩ membalas</span></div>
          <div style="font-size:0.85rem; line-height:1.6; margin-top:0.4rem;">${esc(r.comment)}</div>
        </div>
      `).join('');

      return `
        <div class="comment-card">
          <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
            <div>
              <div style="font-weight:600;">${esc(c.user_name)}</div>
              <div style="font-family:'DM Mono',monospace; font-size:0.65rem; color:var(--muted);">${new Date(c.created_at).toLocaleDateString('id-ID')}</div>
            </div>
            <div class="stars">${starsHtml(c.rating)}</div>
          </div>
          <div style="font-size:0.95rem; line-height:1.6;">${esc(c.comment)}</div>
          <div style="margin-top:0.75rem;">
            ${isLoggedIn ? `<button onclick="toggleReplyForm('${c.id}')" style="background:none; border:none; color:var(--copper); font-family:'DM Mono',monospace; font-size:0.6rem; cursor:none; text-transform:uppercase;">↩ Balas</button>` : ''}
          </div>
          <div id="replies-${c.id}">${replies}</div>
          <div id="replyForm-${c.id}" style="display:none; margin-top:1rem; padding:1rem; background:#f9f9f9; border-radius:4px;">
            <textarea id="replyInput-${c.id}" style="width:100%; min-height:60px; padding:0.5rem; border:1px solid #ddd; outline:none; font-family:inherit; margin-bottom:0.5rem;" placeholder="Balas ulasan ini..."></textarea>
            <button class="btn-complaint" onclick="submitReply('${c.id}')" style="padding:0.4rem 1rem; font-size:0.55rem;">Kirim Balasan</button>
          </div>
        </div>
      `;
    }).join('');
  } else {
    listEl.innerHTML = '';
  }
}

function starsHtml(val) {
  let h = '';
  for (let i=1; i<=5; i++) h += `<span class="star ${i<=val?'filled':''}">★</span>`;
  return h;
}

function interactiveStarsHtml(val) {
  let h = '';
  for (let i=1; i<=5; i++) {
    h += `<span class="star ${i<=val?'filled':''}" style="cursor:none; font-size:1.5rem;" onclick="setRating(${i})" onmouseenter="highlightStars(${i})" onmouseleave="resetStars()">★</span>`;
  }
  return h;
}

function setRating(val) {
  document.getElementById('inputRating').dataset.value = val;
  resetStars();
}

function highlightStars(val) {
  const stars = document.getElementById('inputRating').querySelectorAll('.star');
  stars.forEach((s, i) => s.classList.toggle('filled', i < val));
}

function resetStars() {
  const val = parseInt(document.getElementById('inputRating').dataset.value);
  highlightStars(val);
}

async function submitComment() {
  const text = document.getElementById('commentTextInput').value.trim();
  const rating = document.getElementById('inputRating').dataset.value;
  const msg = document.getElementById('commentMsg');

  if (!text) {
    msg.textContent = "Ulasan tidak boleh kosong.";
    msg.style.display = 'block';
    msg.style.color = '#c33';
    return;
  }

  try {
    const res = await fetch('includes/api.php?action=add_comment', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ project_id: currentProjectId, comment: text, rating: rating })
    });
    const result = await res.json();
    if (result.success) {
      loadComments(currentProjectId);
    } else {
      msg.textContent = result.message;
      msg.style.display = 'block';
    }
  } catch (e) {
    msg.textContent = "Gagal mengirim ulasan.";
    msg.style.display = 'block';
  }
}

function toggleReplyForm(id) {
  const f = document.getElementById('replyForm-'+id);
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

async function submitReply(commentId) {
  const input = document.getElementById('replyInput-' + commentId);
  const text = input.value.trim();
  if (!text) return;

  try {
    const res = await fetch('includes/api.php?action=add_comment', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ project_id: currentProjectId, comment: text, parent_id: commentId })
    });
    const result = await res.json();
    if (result.success) {
      loadComments(currentProjectId);
    }
  } catch (e) { console.error(e); }
}

function esc(str) {
  if (!str) return '';
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ═══════════════════════════════════════════════════
//  AUTH LOGIC
// ═══════════════════════════════════════════════════
function handleAuthClick() {
  const isLogged = document.getElementById('userIndicator').classList.contains('show');
  if (isLogged) logoutUser();
  else openLoginModal();
}

function openLoginModal(e) {
  if (e) e.preventDefault();
  document.getElementById('loginModal').classList.add('open');
}

function closeLoginModal() {
  document.getElementById('loginModal').classList.remove('open');
  resetLoginForm();
}

function toggleAuthMode() {
  authMode = authMode === 'login' ? 'register' : 'login';
  const isReg = authMode === 'register';
  document.getElementById('loginModeHeadline').style.display = isReg ? 'none' : 'block';
  document.getElementById('registerModeHeadline').style.display = isReg ? 'block' : 'none';
  document.getElementById('fieldFullName').style.display = isReg ? 'block' : 'none';
  document.getElementById('fieldConfirmPass').style.display = isReg ? 'block' : 'none';
  document.getElementById('btnSubmitAuth').textContent = isReg ? 'Daftar Akun' : 'Masuk ke Studio';
  document.getElementById('authSwitchText').textContent = isReg ? 'Sudah punya akun?' : 'Belum punya akun?';
  document.getElementById('authSwitchLink').textContent = isReg ? 'Masuk di sini' : 'Daftar di sini';
}

function resetLoginForm() {
  ['inputFullName','inputEmail','inputPassword','inputConfirmPass'].forEach(id => {
    const el = document.getElementById(id); if(el) el.value = '';
  });
  const err = document.getElementById('loginError');
  err.textContent = ''; err.style.display = 'none';
  document.getElementById('loginSuccess').style.display = 'none';
  document.getElementById('loginForm').style.display = 'block';
}

async function handleSubmitAuth() {
  const email = document.getElementById('inputEmail').value.trim();
  const pass = document.getElementById('inputPassword').value;
  const name = document.getElementById('inputFullName')?.value.trim();
  const confirm = document.getElementById('inputConfirmPass')?.value;
  const errorEl = document.getElementById('loginError');

  if (!email || !pass) return showLoginError('Mohon isi email dan kata sandi.');
  if (authMode === 'register' && (!name || pass !== confirm)) return showLoginError('Harap lengkapi semua dan pastikan sandi cocok.');

  const url = authMode === 'register' ? 'includes/auth.php?action=register' : 'includes/auth.php?action=login';
  const body = authMode === 'register' ? { name, email, password: pass } : { email, password: pass };

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });
    const result = await res.json();
    if (result.success) {
      document.getElementById('loginForm').style.display = 'none';
      document.getElementById('successName').textContent = `Halo, ${result.user.name}!`;
      document.getElementById('loginSuccess').style.display = 'block';
      setTimeout(() => location.reload(), 2000);
    } else {
      showLoginError(result.message);
    }
  } catch (e) { showLoginError('Terjadi kesalahan koneksi.'); }
}

function showLoginError(m) {
  const err = document.getElementById('loginError');
  err.textContent = m;
  err.style.display = 'block';
}

async function logoutUser() {
  await fetch('includes/auth.php?action=logout');
  location.reload();
}

// ═══════════════════════════════════════════════════
//  COMPLAINT FORM
// ═══════════════════════════════════════════════════
function submitComplaint() {
  const name = document.getElementById('cName').value.trim();
  const email = document.getElementById('cEmail').value.trim();
  const film = document.getElementById('cFilm').value;
  const cat = document.getElementById('cCategory').value;
  const msgText = document.getElementById('cMessage').value.trim();
  const errEl = document.getElementById('complaintErr');

  if (!name || !email || !film || !cat || !msgText) {
    errEl.textContent = "Mohon lengkapi semua field.";
    errEl.style.display = 'block';
    return;
  }
  if (!email.includes('@gmail.com')) {
    errEl.textContent = "Gunakan alamat @gmail.com.";
    errEl.style.display = 'block';
    return;
  }
  errEl.style.display = 'none';

  const subject = encodeURIComponent(`[Keluhan] ${cat} - ${film}`);
  const body = encodeURIComponent(`Nama: ${name}\nEmail: ${email}\nFilm: ${film}\nKategori: ${cat}\n\nPesan:\n${msgText}`);
  window.location.href = `mailto:thecinemaedu@gmail.com?subject=${subject}&body=${body}`;

  setTimeout(() => {
    document.getElementById('complaintFormBody').style.display = 'none';
    document.getElementById('complaintSuccess').style.display = 'block';
  }, 1000);
}

function resetComplaintForm() {
  ['cName','cEmail','cMessage'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('cFilm').value = '';
  document.getElementById('cCategory').value = '';
  document.getElementById('complaintFormBody').style.display = 'block';
  document.getElementById('complaintSuccess').style.display = 'none';
}
