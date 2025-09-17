<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
require_role('admin');

$pdo = db_connect();
$stats = [
    'photos' => $pdo->query("SELECT COUNT(*) FROM photos")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];

$photos = $pdo->query("SELECT p.*, c.name as category FROM photos p LEFT JOIN categories c ON p.category_id = c.id ORDER BY uploaded_at DESC LIMIT 50")->fetchAll();

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/admin/dashboard.php">Admin - Galeri</a>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?= BASE_URL ?>/">Lihat Publik</a>
      <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>/logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Dashboard</h4>
    <div>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/upload.php">Upload Foto</a>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/admin/categories.php">Kelola Kategori</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card p-3">
         <h6>Foto</h6>
        <strong><?= $stats['photos'] ?></strong>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Kategori</h6>
        <strong><?= $stats['categories'] ?></strong>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Users</h6>
        <strong><?= $stats['users'] ?></strong>
      </div>
    </div>
  </div>

  <h5>Daftar Foto Terbaru</h5>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Preview</th>
          <th>Title</th>
          <th>Kategori</th>
          <th>Uploaded</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($photos as $i => $p): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><img src="<?= BASE_URL ?>/assets/uploads/<?= h($p['filename']) ?>" style="height:50px;object-fit:cover"></td>
          <td><?= h($p['title']) ?></td>
          <td><?= h($p['category'] ?? '-') ?></td>
          <td><?= h($p['uploaded_at']) ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="<?= BASE_URL ?>/admin/edit_photo.php?id=<?= $p['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>/admin/delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus foto ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
