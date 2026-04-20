<?php
session_start();
require_once 'config/db.php';

// Fetch projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id ASC");
$projects = $stmt->fetchAll();

// Fetch services (education content)
$stmt = $pdo->query("SELECT s.*, p.title as project_title FROM services s JOIN projects p ON s.project_id = p.id ORDER BY s.id ASC");
$services = $stmt->fetchAll();

// Prepare projects data for JS
$projects_js = json_encode($projects);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sudut Layar — Creative Studio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- CURSOR -->
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAVIGATION -->
<nav>
  <a href="#" class="nav-logo" onclick="scrollToTop(event)">
    <svg class="logo-mark" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="2" width="40" height="40" stroke="#090f1a" stroke-width="1"/>
      <line x1="2" y1="2" x2="9" y2="2" stroke="#2d6fbb" stroke-width="1.5"/>
      <line x1="2" y1="2" x2="2" y2="9" stroke="#2d6fbb" stroke-width="1.5"/>
      <line x1="42" y1="35" x2="42" y2="42" stroke="#2d6fbb" stroke-width="1.5"/>
      <line x1="35" y1="42" x2="42" y2="42" stroke="#2d6fbb" stroke-width="1.5"/>
      <path d="M10 16 Q10 11 15 11 L22 11 Q27 11 27 16 Q27 21 22 22 L17 22 Q10 23 10 28 Q10 33 17 33 L28 33" stroke="#090f1a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
      <path d="M30 11 L30 33 L38 33" stroke="#2d6fbb" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <div class="logo-wordmark">
      <span class="logo-main">SUDUT LAYAR</span>
      <span class="logo-sub">Creative Studio</span>
    </div>
  </a>
  <ul class="nav-links">
    <li><a href="#work" onclick="navTo(event,'work')">Trailer</a></li>
    <li><a href="#services" onclick="navTo(event,'services')">Edukasi</a></li>
    <li><a href="#complaint" onclick="navTo(event,'complaint')">Komplain</a></li>
    <li><a href="#contact" onclick="navTo(event,'contact')">Kontak</a></li>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="dashboard.php">Dashboard</a></li>
    <?php endif; ?>
  </ul>
  <div class="nav-right">
    <div class="user-indicator <?php echo isset($_SESSION['user_id']) ? 'show' : ''; ?>" id="userIndicator">
      <span class="user-dot"></span>
      <span id="userIndicatorName"><?php echo isset($_SESSION['user_id']) ? strtoupper(htmlspecialchars($_SESSION['user_name'])) : ''; ?></span>
    </div>
    <button class="btn-login" id="navLoginBtn" onclick="handleAuthClick()"><?php echo isset($_SESSION['user_id']) ? 'Keluar' : 'Masuk'; ?></button>
  </div>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="hero-bg-text">SUDUT<br/>LAYAR</div>
  <div class="hero-content">
    <div class="hero-eyebrow">Sudut Layar ·</div>
    <h1 class="hero-headline">Film sebagai<br/>media <em>belajar</em><br/>yang <em>bermakna</em></h1>
  </div>
  <div class="hero-foot">
    <p class="hero-desc">The Cinema Edu adalah platform edukasi berbasis film yang menghadirkan trailer, sinopsis, dan nilai pembelajaran dalam satu ruang digital yang terkurasi dan mudah diakses oleh semua kalangan.</p>
    <a href="#work" class="hero-cta" onclick="navTo(event,'work')">Lihat trailer<span class="cta-arrow">↓</span></a>
  </div>
</section>

<!-- MARQUEE -->
<div class="marquee-band">
  <div class="marquee-track">
    <?php foreach ($projects as $p): ?>
      <span class="marquee-item"><?php echo htmlspecialchars($p['title']); ?><span class="marquee-dot"></span></span>
    <?php endforeach; ?>
    <!-- Repeat for infinite loop -->
    <?php foreach ($projects as $p): ?>
      <span class="marquee-item"><?php echo htmlspecialchars($p['title']); ?><span class="marquee-dot"></span></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- WORK -->
<section id="work">
  <div class="work-header reveal">
    <div>
      <div class="section-tag">Karya P5 SMKN 1 Cikarang Selatan <span>/ 2024–2025</span></div>
      <h2 class="work-headline">Proyek yang<br/><em>kami Tampilkan</em></h2>
    </div>
  </div>
  <div class="portfolio-grid reveal" id="portfolioGrid">
    <?php foreach ($projects as $p): ?>
    <div class="proj" onclick="openVideoModal('<?php echo $p['id']; ?>')">
      <img class="proj-img" src="<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" onerror="this.src='https://via.placeholder.com/800x1200?text=Poster'" />
      <div class="proj-overlay">
        <div class="proj-info">
          <div class="proj-cat"><?php echo htmlspecialchars($p['category']); ?></div>
          <div class="proj-title"><?php echo htmlspecialchars($p['title']); ?></div>
          <div class="proj-actions">
            <div class="proj-play-hint"><span class="play-icon">▶</span> Tonton</div>
            <button class="proj-review-btn" onclick="event.stopPropagation();openReviewModal('<?php echo $p['id']; ?>')">★ Ulasan</button>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SERVICES -->
<section id="services">
  <div class="services-header reveal">
    <div>
      <div class="section-tag">berikut ini edukasi</div>
      <h2 class="services-headline">Edukasi<br/>Dalam <em>Setiap</em><br/>Tayangan</h2>
    </div>
  </div>
  <div class="services-grid reveal" id="servicesGrid">
    <?php $i = 1; foreach ($services as $s): ?>
    <div class="service-item">
      <div class="service-num"><?php echo str_pad($i++, 2, '0', STR_PAD_LEFT); ?></div>
      <div class="service-name"><?php echo htmlspecialchars($s['project_title']); ?></div>
      <div class="service-desc"><?php echo nl2br(htmlspecialchars($s['content'])); ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- TESTIMONIAL -->
<div class="testimonial-section">
  <div class="reveal">
    <blockquote class="testimonial-quote">"Sudut Layar tidak hanya menampilkan film — mereka menghidupkan makna dibalik layar, memberikan perspektif edukasi yang mendalam bagi setiap penonton."</blockquote>
    <div class="testimonial-attr">Tim Kurasi Sudut Layar</div>
  </div>
</div>

<!-- CONTACT -->
<section id="contact">
  <div class="reveal">
    <h2 class="contact-headline">Mari kita<br/><em>bercerita</em></h2>
    <p class="contact-sub">Punya proyek film yang ingin diwujudkan? Kami selalu terbuka untuk kolaborasi yang bermakna.</p>
    <div class="contact-links">
      <a href="mailto:sudutlayar@smkn1cisel.sch.id" class="contact-email">sudutlayar@smkn1cisel.sch.id</a>
    </div>
  </div>
</section>

<!-- COMPLAINT -->
<section id="complaint">
  <div class="reveal">
    <div class="section-tag">Pusat Bantuan <span>/ Kirim Keluhan</span></div>
    <h2 class="complaint-headline">Ada yang ingin<br/><em>disampaikan?</em></h2>
    <p class="complaint-desc">Kami mendengar setiap masukan. Sampaikan keluhan atau saran Anda dan tim kami akan merespons melalui email.</p>
    <div class="complaint-form-wrap">
      <div class="error-box" id="complaintErr" style="display:none; color:#c33; margin-bottom:1rem; padding:1rem; background:#fff0f0; border-left:3px solid #c33;"></div>
      <!-- FORM -->
      <div id="complaintFormBody">
        <div class="complaint-grid">
          <div class="complaint-field">
            <label class="complaint-label">Nama Lengkap</label>
            <input class="complaint-input" type="text" id="cName" placeholder="Nama Anda" />
          </div>
          <div class="complaint-field">
            <label class="complaint-label">Email (@gmail.com)</label>
            <input class="complaint-input" type="email" id="cEmail" placeholder="nama@gmail.com" />
          </div>
          <div class="complaint-field">
            <label class="complaint-label">Terkait Film</label>
            <select class="complaint-select" id="cFilm">
              <option value="">— Pilih Film —</option>
              <?php foreach ($projects as $p): ?>
                <option><?php echo htmlspecialchars($p['title']); ?></option>
              <?php endforeach; ?>
              <option>Lainnya / Umum</option>
            </select>
          </div>
          <div class="complaint-field">
            <label class="complaint-label">Kategori</label>
            <select class="complaint-select" id="cCategory">
              <option value="">— Pilih Kategori —</option>
              <option>Video tidak dapat diputar</option>
              <option>Konten tidak sesuai</option>
              <option>Masalah teknis website</option>
              <option>Saran & Masukan</option>
              <option>Lainnya</option>
            </select>
          </div>
          <div class="complaint-field full">
            <label class="complaint-label">Isi Keluhan / Pesan</label>
            <textarea class="complaint-textarea" id="cMessage" placeholder="Ceritakan masalah atau masukan Anda secara detail..."></textarea>
          </div>
        </div>
        <div class="complaint-footer">
          <span class="complaint-note" style="font-family:'DM Mono',monospace; font-size:0.6rem; color:var(--muted)">Dikirim ke thecinemaedu@gmail.com</span>
          <button class="btn-complaint" onclick="submitComplaint()">Kirim Keluhan →</button>
        </div>
      </div>
      <!-- SUCCESS -->
      <div id="complaintSuccess" style="display:none; text-align:center; padding:2rem;">
        <div style="font-size:3rem; margin-bottom:1rem;">✦</div>
        <h3>Pesan Terkirim!</h3>
        <p>Terima kasih. Tim kami akan membalas melalui email segera.</p>
        <button class="btn-complaint" style="margin-top:2rem;" onclick="resetComplaintForm()">Kirim Lagi</button>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-copy">© 2025 Sudut Layar. Dibuat dengan penuh niat.</div>
  <ul class="footer-links">
    <li><a href="#" onclick="openLoginModal(event)">Masuk Portal</a></li>
  </ul>
</footer>

<!-- VIDEO MODAL -->
<div class="video-modal-overlay" id="videoModal">
  <div class="video-modal-inner">
    <div class="video-modal-header">
      <div>
        <div class="video-modal-cat" id="vCat" style="font-family:'DM Mono',monospace; font-size:0.6rem; letter-spacing:2px; text-transform:uppercase; color:var(--warm);"></div>
        <div class="video-modal-title" id="vTitle" style="font-size:2rem; font-weight:300;"></div>
      </div>
      <button class="video-modal-close" onclick="closeVideoModal()" style="background:none; border:1px solid rgba(255,255,255,0.2); color:#fff; width:40px; height:40px; cursor:none;">✕</button>
    </div>
    <div class="video-frame-wrap">
      <iframe id="videoFrame" src="" allowfullscreen allow="autoplay; encrypted-media"></iframe>
    </div>
    <!-- COMMENTS & RATING -->
    <div class="comments-section" id="commentsSection">
      <h3>Ulasan & Rating</h3>
      <div id="avgRatingArea" style="margin: 1.5rem 0; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05);"></div>
      <div id="commentFormArea"></div>
      <div id="commentsList" style="margin-top:2rem;"></div>
    </div>
  </div>
</div>

<!-- LOGIN MODAL -->
<?php include 'includes/login_modal.php'; ?>

<script>
  // Inject data for dynamic modals
  const PROJECTS_DATA = <?php echo $projects_js; ?>;
</script>
<script src="assets/js/script.js"></script>
</body>
</html>
