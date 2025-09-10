<?php
// config.php
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'galeri_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Laragon default: blank

define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('BASE_URL', '/galeri-foto'); // adjust if necessary

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
