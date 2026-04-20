<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'client';

// Fetch user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as comment_count FROM comments WHERE user_id = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

// Fetch all projects
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id ASC");
$allProjects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Sudut Layar</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .dash-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 4rem; }
    .stat-card { background: white; border: 1px solid var(--border); padding: 2.5rem; text-align: center; }
    .stat-num { font-size: 3rem; font-weight: 300; display: block; color: var(--copper); }
    .stat-label { font-family: 'DM Mono', monospace; font-size: 0.65rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); }
    
    /* ADMIN PANEL STYLES */
    .admin-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; margin-top: 2rem; }
    .admin-table-wrap { overflow-x: auto; background: white; border: 1px solid var(--border); border-radius: 4px; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .admin-table th, .admin-table td { padding: 1.2rem; text-align: left; border-bottom: 1px solid var(--border); }
    .admin-table th { font-family: 'DM Mono', monospace; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); background: #fafafa; }
    .btn-action { padding: 0.4rem 0.8rem; border: 1px solid var(--border); background: white; font-size: 0.7rem; cursor: none; margin-right: 0.5rem; transition: 0.3s; font-family:'DM Mono', monospace; }
    .btn-edit:hover { background: var(--copper); color: white; border-color: var(--copper); }
    .btn-delete:hover { background: #e11d48; color: white; border-color: #e11d48; }
    .btn-add { background: var(--ink); color: white; border: none; padding: 0.9rem 1.8rem; font-family: 'DM Mono', monospace; font-size: 0.7rem; letter-spacing: 2px; cursor: none; transition: background 0.25s; }
    .btn-add:hover { background: var(--copper); }
  </style>
</head>
<body>

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
    <li><a href="dashboard.php" class="active">Dashboard</a></li>
  </ul>
  <div class="nav-right">
    <div class="user-indicator">
      <span class="user-dot"></span>
      <span><?php echo strtoupper(htmlspecialchars($_SESSION['user_name'])); ?> (<?php echo strtoupper($userRole); ?>)</span>
    </div>
    <button class="btn-login" onclick="logoutUser()">KELUAR</button>
  </div>
  <div class="menu-toggle" id="mobileMenuBtn" onclick="toggleMobileMenu()">
    <span></span><span></span><span></span>
  </div>
</nav>

<main class="dash-container">
  <div class="work-header reveal">
    <div>
      <div class="section-tag">Portal <?php echo ucfirst($userRole); ?></div>
      <h2 class="work-headline">Selamat Datang,<br/><em><?php echo htmlspecialchars($_SESSION['user_name']); ?></em></h2>
    </div>
  </div>

  <?php if ($userRole === 'admin'): ?>
    <!-- ADMIN VIEW -->
    <div class="admin-actions reveal">
      <h3 style="font-weight: 300;">Manajemen Proyek</h3>
      <button class="btn-add" onclick="openProjectModal()">+ TAMBAH PROYEK</button>
    </div>

    <div class="admin-table-wrap reveal">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Tahun</th>
            <th>Drive ID</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allProjects as $p): ?>
          <tr>
            <td><?php echo $p['id']; ?></td>
            <td style="font-weight: 600;"><?php echo htmlspecialchars($p['title']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td><?php echo $p['year']; ?></td>
            <td style="font-family: 'DM Mono', monospace; font-size: 0.7rem;"><?php echo $p['google_drive_id']; ?></td>
            <td>
              <button class="btn-action btn-edit" onclick='editProject(<?php echo json_encode($p); ?>)'>Edit</button>
              <button class="btn-action btn-delete" onclick="deleteProject(<?php echo $p['id']; ?>)">Hapus</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php else: ?>
    <!-- CLIENT/DEMO VIEW -->
    <div class="dash-stats reveal">
      <div class="stat-card">
        <span class="stat-num"><?php echo count($allProjects); ?></span>
        <span class="stat-label">Total Karya</span>
      </div>
      <div class="stat-card">
        <span class="stat-num"><?php echo $stats['comment_count']; ?></span>
        <span class="stat-label">Review Anda</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">∞</span>
        <span class="stat-label">Akses Aset</span>
      </div>
    </div>

    <div class="section-tag reveal">Katalog Karya</div>
    <div class="portfolio-grid reveal">
      <?php foreach ($allProjects as $p): ?>
      <div class="proj" onclick="window.location.href='project-details.php?id=<?php echo $p['id']; ?>'">
        <img class="proj-img" src="<?php echo $p['image']; ?>" alt="<?php echo $p['title']; ?>">
        <div class="proj-overlay">
          <div class="proj-info">
            <div class="proj-cat"><?php echo $p['category']; ?></div>
            <div class="proj-title"><?php echo $p['title']; ?></div>
            <div class="proj-actions">
              <button class="proj-review-btn">LIHAT DETAIL</button>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<!-- PROJECT MODAL (Admin Only) -->
<?php if ($userRole === 'admin'): ?>
  <div class="modal-overlay" id="projectModal">
  <div class="login-modal" style="max-width: 600px;">
    <button style="position:absolute; top:1.5rem; right:1.5rem; background:none; border:none; color:var(--muted); cursor:none; font-size:1.2rem;" onclick="closeProjectModal()">✕</button>
    <form id="projectForm" onsubmit="handleProjectSubmit(event)">
      <input type="hidden" id="projId">
      <h2 id="modalTitle" style="font-size:2rem; font-weight:300; margin-bottom:2.5rem; letter-spacing:-0.5px;">Tambah Proyek</h2>
      
      <div class="form-field" style="margin-bottom:1.25rem;">
        <label class="complaint-label">Judul Film</label>
        <input type="text" id="projTitle" class="complaint-input" required>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom:1.25rem;">
        <div class="form-field">
          <label class="complaint-label">Kategori</label>
          <input type="text" id="projCat" class="complaint-input" required>
        </div>
        <div class="form-field">
          <label class="complaint-label">Tahun</label>
          <input type="text" id="projYear" class="complaint-input" required>
        </div>
      </div>
      <div class="form-field" style="margin-bottom:1.25rem;">
        <label class="complaint-label">URL Gambar (Poster)</label>
        <input type="text" id="projImg" class="complaint-input" required>
      </div>
      <div class="form-field" style="margin-bottom:1.25rem;">
        <label class="complaint-label">Google Drive Video ID</label>
        <input type="text" id="projDriveId" class="complaint-input" required>
      </div>
      <div class="form-field" style="margin-bottom:1.5rem;">
        <label class="complaint-label">Konten Edukasi</label>
        <textarea id="projEdu" class="complaint-textarea" rows="4" required></textarea>
      </div>

      <button type="submit" class="btn-complaint" style="width:100%; padding:1rem; font-size:0.72rem; letter-spacing:3px;" id="btnProjSubmit">SIMPAN PROYEK</button>
    </form>
  </div>
</div>
<?php endif; ?>

<footer>
  <div class="footer-copy">© 2025 Sudut Layar. Premium Client Experience.</div>
</footer>

<script src="assets/js/script.js"></script>
<?php if ($userRole === 'admin'): ?>
<script>
function openProjectModal(edit = false) {
    if (!edit) {
        document.getElementById('projectForm').reset();
        document.getElementById('projId').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Proyek';
    }
    document.getElementById('projectModal').classList.add('open');
}

function closeProjectModal() {
    document.getElementById('projectModal').classList.remove('open');
}

function editProject(p) {
    document.getElementById('projId').value = p.id;
    document.getElementById('projTitle').value = p.title;
    document.getElementById('projCat').value = p.category;
    document.getElementById('projYear').value = p.year;
    document.getElementById('projImg').value = p.image;
    document.getElementById('projDriveId').value = p.google_drive_id;
    document.getElementById('modalTitle').textContent = 'Edit Proyek';
    
    // Fetch education content for this project
    fetch(`includes/api.php?action=get_project_edu&id=${p.id}`)
      .then(res => res.json())
      .then(data => {
          document.getElementById('projEdu').value = data.content || '';
          openProjectModal(true);
      });
}

async function deleteProject(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus proyek ini?')) return;
    const res = await fetch(`includes/api.php?action=delete_project&id=${id}`, { method: 'POST' });
    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.message);
}

async function handleProjectSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('projId').value;
    const body = {
        id: id,
        title: document.getElementById('projTitle').value,
        category: document.getElementById('projCat').value,
        year: document.getElementById('projYear').value,
        image: document.getElementById('projImg').value,
        drive_id: document.getElementById('projDriveId').value,
        edu_content: document.getElementById('projEdu').value
    };

    const action = id ? 'edit_project' : 'add_project';
    const res = await fetch(`includes/api.php?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });
    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.message);
}
</script>
<?php endif; ?>
</body>
</html>
