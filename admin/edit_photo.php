<?php
require_once __DIR__ . '/../inc/functions.php';
require_login();
require_role('admin');

$pdo = db_connect();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM photos WHERE id = :id");
$stmt->execute(['id'=>$id]);
$photo = $stmt->fetch();
if (!$photo) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$err = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $category_id = $_POST['category_id'] ?: null;

    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        list($ok, $res) = upload_image($_FILES['photo']);
        if (!$ok) {
            $err = $res;
        } else {
            if ($photo['filename'] && file_exists(UPLOAD_DIR . $photo['filename'])) {
                @unlink(UPLOAD_DIR . $photo['filename']);
            }
            $filename = $res;
            $stmt = $pdo->prepare("UPDATE photos SET filename = :f WHERE id = :id");
            $stmt->execute(['f'=>$filename, 'id'=>$id]);
        }
    }

    if (!$err) {
        $stmt = $pdo->prepare("UPDATE photos SET title = :t, category_id = :c WHERE id = :id");
        $stmt->execute(['t'=>$title, 'c'=>$category_id, 'id'=>$id]);
        $success = 'Data foto diperbarui.';
        $stmt = $pdo->prepare("SELECT * FROM photos WHERE id = :id");
        $stmt->execute(['id'=>$id]);
        $photo = $stmt->fetch();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Foto - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-sm btn-link">← Kembali</a>
  <h4>Edit Foto</h4>

  <?php if($err): ?><div class="alert alert-danger"><?= h($err) ?></div><?php endif; ?>
  <?php if($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

  <div class="mb-3">
    <img src="<?= BASE_URL ?>/assets/uploads/<?= h($photo['filename']) ?>" style="max-width:300px;object-fit:cover">
  </div>

  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Ganti Foto (opsional)</label>
      <input type="file" name="photo" accept="image/*" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input name="title" class="form-control" value="<?= h($photo['title']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Kategori</label>
      <select name="category_id" class="form-select">
        <option value="">-- Pilih --</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $photo['category_id']==$c['id']?'selected':'' ?>><?= h($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
</body>
</html>
