<?php
// setup_admin.php - run once to create admin user with password 'admin123'
require_once 'inc/functions.php';
$pdo = db_connect();
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u");
$stmt->execute(['u'=>$username]);
if ($stmt->fetch()) {
    echo "User 'admin' sudah ada.\n";
} else {
    $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:u, :p, :r)")->execute([
        'u'=>$username, 'p'=>$password, 'r'=>$role
    ]);
    echo "User 'admin' dibuat dengan password 'admin123'. Silakan hapus file setup_admin.php setelah selesai.\n";
}
