<?php
require_once 'config/db.php';

try {
    // Add role column if not exists (fail-safe)
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'demo') DEFAULT 'client' AFTER password");

    // Clear users table
    $pdo->exec("TRUNCATE TABLE users");

    // Re-seed users with correct roles and encrypted passwords
    $users = [
        ['Admin Studio', 'admin@gmail.com', 'admin123', 'admin'],
        ['Portal Klien', 'klien@gmail.com', 'studio2025', 'client'],
        ['Demo User', 'demo@gmail.com', 'demo', 'demo']
    ];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

    foreach ($users as $u) {
        $hashed = password_hash($u[2], PASSWORD_DEFAULT);
        $stmt->execute([$u[0], $u[1], $hashed, $u[3]]);
        echo "User '{$u[1]}' [{$u[3]}] berhasil didaftarkan dengan password: <b>{$u[2]}</b><br>";
    }

    echo "<br><b>Selesai! Database Anda sudah siap dengan sistem ROLE.</b>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
