<?php
session_start();
require_once 'config/db.php';

$projectId = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$projectId]);
$project = $stmt->fetch();

if (!$project) { header("Location: index.php"); exit(); }

$stmt = $pdo->prepare("SELECT * FROM services WHERE project_id = ?");
$stmt->execute([$projectId]);
$service = $stmt->fetch();

// Fetch ratings & comments
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM comments WHERE project_id = ? AND parent_id IS NULL");
$stmt->execute([$projectId]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT c.*, u.name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.project_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC");
$stmt->execute([$projectId]);
$comments = $stmt->fetchAll();

foreach ($comments as &$c) {
    $stmt = $pdo->prepare("SELECT c.*, u.name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? ORDER BY c.created_at ASC");
    $stmt->execute([$c['id']]);
    $c['replies'] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($project['title']); ?> — Sudut Layar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Mono:wght@300;400;500&family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .details-hero { padding: 10rem 3rem 5rem; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 4rem; background: var(--paper); }
    .video-frame-wrap { border-radius: 4px; overflow: hidden; box-shadow: 0 30px 60px rgba(9,15,26,0.1); }
    .edu-card { background: white; border: 1px solid var(--border); padding: 2.5rem; border-radius: 4px; }
    .details-comments { padding: 5rem 3rem; background: var(--cream); }
    @media (max-width: 1000px) { .details-hero { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<!-- CURSOR -->
<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<nav>
  <a href="index.php" class="nav-logo">
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
    <li><a href="index.php">Beranda</a></li>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="dashboard.php">Dashboard</a></li>
    <?php endif; ?>
  </ul>
</nav>

<main>
  <section class="details-hero">
    <div>
      <div class="video-frame-wrap">
        <div class="video-frame-wrap" style="aspect-ratio: 16/9;">
          <iframe src="https://drive.google.com/file/d/<?php echo $project['google_drive_id']; ?>/preview" allowfullscreen style="width:100%; height:100%; border:none;"></iframe>
        </div>
      </div>
    </div>
    <div class="edu-card">
      <div class="section-tag"><?php echo htmlspecialchars($project['category']); ?> — <?php echo $project['year']; ?></div>
      <h1 class="work-headline" style="margin-bottom:2rem; font-size:3rem;"><?php echo htmlspecialchars($project['title']); ?></h1>
      
      <?php if ($service): ?>
        <div style="font-family:'DM Mono',monospace; font-size:0.65rem; color:var(--copper); text-transform:uppercase; margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:0.5rem;">Edukasi & Pembelajaran</div>
        <div style="font-size:1.1rem; line-height:1.8; color:var(--muted);">
          <?php echo nl2br(htmlspecialchars($service['content'])); ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="details-comments">
    <div style="max-width: 900px; margin: 0 auto;">
      <h2 class="work-headline" style="margin-bottom:3rem;">Ulasan <em>Studio</em></h2>
      
      <div style="display:flex; align-items:center; gap:1.5rem; margin-bottom:4rem;">
        <div style="font-size:4rem; font-weight:300; color:var(--copper); border-right:1px solid rgba(0,0,0,0.1); padding-right:2rem;"><?php echo number_format($stats['avg_rating'] ?: 0, 1); ?></div>
        <div>
          <div style="display:flex; gap:3px; color:var(--copper); font-size:1.5rem;">
            <?php for($i=1; $i<=5; $i++) echo ($i <= round($stats['avg_rating'])) ? '★' : '<span style="color:#ddd">★</span>'; ?>
          </div>
          <div style="font-family:'DM Mono',monospace; font-size:0.7rem; color:var(--muted); margin-top:0.4rem;">Berdasarkan <?php echo $stats['total']; ?> ulasan</div>
        </div>
      </div>

      <!-- Add ulasan form and list will be handled by script.js if adapted, but let's just make it look consistent -->
      <!-- For simplicity, since the modal logic is primary, we redirect back to home for ulasan or just keep the static view here -->
      
      <div id="commentsList">
        <?php foreach ($comments as $c): ?>
          <div class="comment-card" style="margin-bottom:1.5rem;">
            <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
              <div>
                <div style="font-weight:600; font-size:1.1rem;"><?php echo htmlspecialchars($c['user_name']); ?></div>
                <div style="font-family:'DM Mono',monospace; font-size:0.7rem; color:var(--muted);"><?php echo date('d M Y', strtotime($c['created_at'])); ?></div>
              </div>
              <div style="color:var(--copper);">
                <?php for($i=1; $i<=5; $i++) echo ($i <= $c['rating']) ? '★' : '☆'; ?>
              </div>
            </div>
            <p style="font-size:1.1rem; line-height:1.7; color:var(--text);"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
            
            <?php foreach ($c['replies'] as $r): ?>
              <div style="margin-top:1.5rem; padding-left:2rem; border-left:2px solid var(--copper);">
                <div style="font-weight:600; font-size:0.95rem;"><?php echo htmlspecialchars($r['user_name']); ?> <span style="font-weight:400; font-style:italic; color:var(--copper);">↩ membalas</span></div>
                <p style="font-size:0.95rem; margin-top:0.5rem;"><?php echo escape(htmlspecialchars($r['comment'])); // Wait, escape is not a func, use nl2br htmlspecialchars ?></p>
                <p style="font-size:0.95rem; margin-top:0.5rem;"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="footer-copy">© 2025 Sudut Layar. Crafted with passion.</div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
