<div class="modal-overlay" id="loginModal">
  <div class="login-modal" id="loginModalBox">
    <button style="position:absolute; top:1.5rem; right:1.5rem; background:none; border:none; color:var(--muted); cursor:none; font-size:1.2rem;" onclick="closeLoginModal()">✕</button>
    <div id="loginForm">
      <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:2.5rem;">
        <svg width="28" height="28" viewBox="0 0 44 44" fill="none">
          <rect x="2" y="2" width="40" height="40" stroke="#090f1a" stroke-width="1"/>
          <line x1="2" y1="2" x2="9" y2="2" stroke="#2d6fbb" stroke-width="1.5"/>
          <line x1="2" y1="2" x2="2" y2="9" stroke="#2d6fbb" stroke-width="1.5"/>
          <line x1="42" y1="35" x2="42" y2="42" stroke="#2d6fbb" stroke-width="1.5"/>
          <line x1="35" y1="42" x2="42" y2="42" stroke="#2d6fbb" stroke-width="1.5"/>
          <path d="M10 16 Q10 11 15 11 L22 11 Q27 11 27 16 Q27 21 22 22 L17 22 Q10 23 10 28 Q10 33 17 33 L28 33" stroke="#090f1a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
          <path d="M30 11 L30 33 L38 33" stroke="#2d6fbb" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span style="font-family:'Bebas Neue',sans-serif; font-size:1.1rem; letter-spacing:3px; color:var(--ink);">SUDUT LAYAR</span>
      </div>
      
      <div id="loginModeHeadline">
        <h2 style="font-size:2.4rem; font-weight:300; letter-spacing:-0.5px; margin-bottom:0.5rem; line-height:1.1;">Selamat<br/>Datang Kembali</h2>
        <p style="font-size:0.9rem; color:var(--muted); font-weight:300; margin-bottom:2.5rem; line-height:1.6;">Masuk ke portal studio untuk mengakses proyek dan aset Anda.</p>
      </div>
      
      <div id="registerModeHeadline" style="display:none">
        <h2 style="font-size:2.4rem; font-weight:300; letter-spacing:-0.5px; margin-bottom:0.5rem; line-height:1.1;">Buat<br/>Akun Baru</h2>
        <p style="font-size:0.9rem; color:var(--muted); font-weight:300; margin-bottom:2.5rem; line-height:1.6;">Daftar untuk mengakses fitur kolaborasi dan aset eksklusif.</p>
      </div>

      <div class="error-box" id="loginError" style="background:#eff5ff; border-left:3px solid var(--copper); padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.85rem; color:#1a3f72; display:none;"></div>
      
      <div class="form-field" id="fieldFullName" style="display:none; margin-bottom:1.25rem;">
        <label style="display:block; font-family:'DM Mono',monospace; font-size:0.62rem; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Nama Lengkap</label>
        <input class="complaint-input" type="text" id="inputFullName" placeholder="Nama Anda" style="width:100%; border:none; border-bottom:1px solid rgba(0,0,0,0.15); padding:0.6rem 0; font-family:'Cormorant Garamond',serif; font-size:1.1rem; outline:none; background:none;">
      </div>
      
      <div class="form-field" style="margin-bottom:1.25rem;">
        <label style="display:block; font-family:'DM Mono',monospace; font-size:0.62rem; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Alamat Email</label>
        <input class="complaint-input" type="email" id="inputEmail" placeholder="nama@gmail.com" style="width:100%; border:none; border-bottom:1px solid rgba(0,0,0,0.15); padding:0.6rem 0; font-family:'Cormorant Garamond',serif; font-size:1.1rem; outline:none; background:none;">
      </div>
      
      <div class="form-field" style="margin-bottom:1.25rem;">
        <label style="display:block; font-family:'DM Mono',monospace; font-size:0.62rem; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Kata Sandi</label>
        <input class="complaint-input" type="password" id="inputPassword" placeholder="••••••••" style="width:100%; border:none; border-bottom:1px solid rgba(0,0,0,0.15); padding:0.6rem 0; font-family:'Cormorant Garamond',serif; font-size:1.1rem; outline:none; background:none;">
      </div>

      <div class="form-field" id="fieldConfirmPass" style="display:none; margin-bottom:1.25rem;">
        <label style="display:block; font-family:'DM Mono',monospace; font-size:0.62rem; letter-spacing:2px; text-transform:uppercase; color:var(--muted); margin-bottom:0.5rem;">Konfirmasi Kata Sandi</label>
        <input class="complaint-input" type="password" id="inputConfirmPass" placeholder="••••••••" style="width:100%; border:none; border-bottom:1px solid rgba(0,0,0,0.15); padding:0.6rem 0; font-family:'Cormorant Garamond',serif; font-size:1.1rem; outline:none; background:none;">
      </div>

      <button class="btn-complaint" id="btnSubmitAuth" onclick="handleSubmitAuth()" style="width:100%; margin-top:1.5rem; padding:1.1rem; font-size:0.72rem; letter-spacing:3px;">Masuk ke Studio</button>
      
      <p style="text-align:center; margin-top:2rem; font-size:0.85rem; color:var(--muted); font-weight:300;">
        <span id="authSwitchText">Belum punya akun?</span> 
        <a href="javascript:void(0)" onclick="toggleAuthMode()" style="color:var(--copper); text-decoration:none; font-style:italic;" id="authSwitchLink">Daftar sebagai klien</a>
      </p>
    </div>
    
    <div id="loginSuccess" style="display:none; text-align:center; padding:2rem 0;">
      <div style="font-size:3rem; margin-bottom:1rem;">✦</div>
      <h2 id="successName" style="font-size:2rem; font-weight:300; margin-bottom:0.5rem;">Halo!</h2>
      <p id="successMsg" style="font-size:0.9rem; color:var(--muted); line-height:1.6;">Anda berhasil masuk ke portal Sudut Layar.</p>
      <button class="btn-complaint" style="margin-top:2rem;" onclick="closeLoginModal()">Lanjutkan</button>
    </div>
  </div>
</div>
