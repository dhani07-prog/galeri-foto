<?php
// inc/functions.php
require_once __DIR__ . '/../config.php';

function db_connect() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'] ?? 'admin'
    ];
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function require_role($role) {
    $user = current_user();
    if (!$user || $user['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo '403 Forbidden - akses ditolak.';
        exit;
    }
}

function upload_image($file) {
    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    if ($file['error'] !== UPLOAD_ERR_OK) return [false, 'Upload error'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed)) return [false, 'Tipe file tidak diperbolehkan'];
    if ($file['size'] > 5 * 1024 * 1024) return [false, 'Maks 5MB'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)) . '.' . $ext;
    $target = UPLOAD_DIR . $basename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return [false, 'Gagal menyimpan file'];
    }
    return [true, $basename];
}

// pagination helper
function paginate($total, $perPage, $currentPage, $urlBase, $extraQuery = '') {
    $pages = ceil($total / $perPage);
    $html = '<nav><ul class="pagination">';
    for ($i=1;$i<=$pages;$i++) {
        $active = $i == $currentPage ? ' active' : '';
        $q = $extraQuery ? '&' . $extraQuery : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $urlBase . '?page=' . $i . $q . '">' . $i . '</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
