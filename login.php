<?php
require_once 'inc/functions.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute(['u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    } else {
        $err = 'Login gagal: username atau password salah.';
    }
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login Admin - Galeri Foto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">Admin Login</h4>
            <?php if($err): ?>
              <div class="alert alert-danger"><?= h($err) ?></div>
            <?php endif; ?>
            <form method="post" novalidate>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
              </div>
              <button class="btn btn-primary">Login</button>
            </form>
            <hr>
            <a href="<?= BASE_URL ?>/index.php">Kembali ke Galeri Publik</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
