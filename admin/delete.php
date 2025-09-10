<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$pdo = db_connect();

$stmt = $pdo->prepare("SELECT filename FROM photos WHERE id = :id");
$stmt->execute(['id'=>$id]);
$photo = $stmt->fetch();

if ($photo) {
    if ($photo['filename'] && file_exists(UPLOAD_DIR . $photo['filename'])) {
        @unlink(UPLOAD_DIR . $photo['filename']);
    }
    $pdo->prepare("DELETE FROM photos WHERE id = :id")->execute(['id'=>$id]);
}

header('Location: ' . BASE_URL . '/admin/dashboard.php');
exit;
