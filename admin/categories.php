<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
require_role('admin');

$pdo = db_connect();
$err = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    if ($name === '') $err = 'Nama kategori kosong.';
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:n)");
            $stmt->execute(['n'=>$name]);
            $success = 'Kategori ditambahkan.';
        } catch (PDOException $e) {
            $err = 'Gagal menambahkan. Mungkin sudah ada.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id = :id")->execute(['id'=>$id]);
    header("Location: " . BASE_URL . "/admin/categories.php");
    exit;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Kelola Kategori - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-sm btn-link">← Kembali</a>
  <h4>Kelola Kategori</h4>

  <?php if($err): ?><div class="alert alert-danger"><?= h($err) ?></div><?php endif; ?>
  <?php if($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

  <form method="post" class="mb-3">
    <div class="input-group">
      <input name="name" class="form-control" placeholder="Nama kategori">
      <button name="add" class="btn btn-primary">Tambah</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead><tr><th>#</th><th>Nama</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php foreach($cats as $i => $c): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= h($c['name']) ?></td>
          <td>
            <a class="btn btn-sm btn-danger" href="?delete=<?= $c['id'] ?>" onclick="return confirm('Hapus kategori?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
