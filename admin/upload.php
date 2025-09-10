<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
require_role('admin');

$pdo = db_connect();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $category_id = $_POST['category_id'] ?: null;

    if (empty($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        $err = 'Pilih file foto terlebih dahulu.';
    } else {
        list($ok, $res) = upload_image($_FILES['photo']);
        if (!$ok) {
            $err = $res;
        } else {
            $filename = $res;
            $stmt = $pdo->prepare("INSERT INTO photos (filename, title, category_id) VALUES (:f, :t, :c)");
            $stmt->execute(['f'=>$filename, 't'=>$title, 'c'=>$category_id]);
            $success = 'Foto berhasil diupload.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Foto - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-sm btn-link">← Kembali</a>
  <h4>Upload Foto</h4>

  <?php if($err): ?><div class="alert alert-danger"><?= h($err) ?></div><?php endif; ?>
  <?php if($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Foto</label>
      <input type="file" name="photo" accept="image/*" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Kategori</label>
      <select name="category_id" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Upload</button>
  </form>
</div>
</body>
</html>
