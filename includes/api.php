<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Helper for security
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // ADMIN ONLY ACTIONS
    if (in_array($action, ['add_project', 'edit_project', 'delete_project'])) {
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
            exit;
        }

        if ($action === 'add_project') {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO projects (title, category, year, image, google_drive_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$data['title'], $data['category'], $data['year'], $data['image'], $data['drive_id']]);
                $projId = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO services (project_id, title, content) VALUES (?, ?, ?)");
                $stmt->execute([$projId, 'Edukasi Film', $data['edu_content']]);
                
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'edit_project') {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("UPDATE projects SET title = ?, category = ?, year = ?, image = ?, google_drive_id = ? WHERE id = ?");
                $stmt->execute([$data['title'], $data['category'], $data['year'], $data['image'], $data['drive_id'], $data['id']]);
                
                $stmt = $pdo->prepare("UPDATE services SET content = ? WHERE project_id = ?");
                $stmt->execute([$data['edu_content'], $data['id']]);
                
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    if ($action === 'delete_project') {
        if (!isAdmin()) exit;
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }

    // PUBLIC/CLIENT ACTIONS
    if ($action === 'add_comment') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Login required.']);
            exit;
        }
        $projId = $data['project_id'] ?? 0;
        $commentText = $data['comment'] ?? '';
        $rating = $data['rating'] ?? 5;
        $parentId = $data['parent_id'] ?? null;

        if (empty($commentText)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO comments (project_id, user_id, comment, rating, parent_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$projId, $_SESSION['user_id'], $commentText, $rating, $parentId]);
        echo json_encode(['success' => true]);
        exit;
    }
}

if ($action === 'get_comments') {
    $projId = $_GET['id'] ?? 0;
    
    // Fetch top level comments
    $stmt = $pdo->prepare("SELECT c.*, u.name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.project_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC");
    $stmt->execute([$projId]);
    $comments = $stmt->fetchAll();

    foreach ($comments as &$c) {
        // Fetch replies
        $stmt = $pdo->prepare("SELECT c.*, u.name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? ORDER BY c.created_at ASC");
        $stmt->execute([$c['id']]);
        $c['replies'] = $stmt->fetchAll();
    }

    // Fetch avg rating and total count
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM comments WHERE project_id = ? AND parent_id IS NULL");
    $stmt->execute([$projId]);
    $stats = $stmt->fetch();

    echo json_encode([
        'comments' => $comments,
        'avg_rating' => (float)($stats['avg_rating'] ?: 0),
        'total_comments' => (int)$stats['total']
    ]);
    exit;
}

if ($action === 'get_project_edu') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT content FROM services WHERE project_id = ?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch() ?: ['content' => '']);
    exit;
}
?>
