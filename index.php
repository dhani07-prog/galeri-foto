<?php
require_once 'inc/functions.php';

// cek user login
$user = current_user();

$pdo = db_connect();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$perPage = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($q !== '') {
    $where[] = "(p.title LIKE :q)";
    $params['q'] = '%' . $q . '%';
}
if ($category) {
    $where[] = "p.category_id = :cat";
    $params['cat'] = $category;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM photos p $whereSql");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

$sql = "SELECT p.*, c.name as category 
        FROM photos p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereSql 
        ORDER BY p.uploaded_at DESC 
        LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue(':' . $k, $v);
$stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$photos = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$extraQuery = http_build_query(array_filter(['q' => $q, 'category' => $category]));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Galeri Foto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">Galeri Foto</a>
    <div>
      <?php if ($user): ?>
  <span class="me-2">👋 <?= h($user['username']) ?></span>
  <?php if ($user['role'] === 'admin'): ?>
    <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
  <?php endif; ?>
  <a class="btn btn-sm btn-danger" href="<?= BASE_URL ?>/logout.php">Logout</a>
<?php else: ?>
  <a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>/login.php">Login Admin</a>
<?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="row mb-3">
    <div class="col-md-8">
      <h3>Galeri Publik</h3>
    </div>
    <div class="col-md-4">
      <form method="get" class="d-flex">
        <input type="text" name="q" value="<?= h($q) ?>" class="form-control form-control-sm me-2" placeholder="Cari judul...">
        <select name="category" class="form-select form-select-sm me-2">
          <option value="">Semua Kategori</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($category==$cat['id'])?'selected':'' ?>>
              <?= h($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-outline-secondary">Cari</button>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <?php if (count($photos) === 0): ?>
      <div class="col-12">
        <div class="alert alert-info">Belum ada foto.</div>
      </div>
    <?php endif; ?>

    <?php foreach($photos as $p): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card shadow-sm">
          <a href="#"
             data-bs-toggle="modal"
             data-bs-target="#lightboxModal"
             data-src="<?= BASE_URL ?>/assets/uploads/<?= h($p['filename']) ?>"
             data-title="<?= h($p['title']) ?>"
             data-file="<?= urlencode($p['filename']) ?>">
            <img src="<?= BASE_URL ?>/assets/uploads/<?= h($p['filename']) ?>"
                 class="card-img-top"
                 style="height:200px;object-fit:cover">
          </a>
          <div class="card-body p-2">
            <h6 class="card-title mb-1"><?= h($p['title'] ?: 'Untitled') ?></h6>
            <small class="text-muted">
              <?= h($p['category'] ?? '—') ?> • <?= date('d M Y', strtotime($p['uploaded_at'])) ?>
            </small>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-4">
    <?php
    echo paginate($total, $perPage, $page, BASE_URL . '/index.php', trim('q=' . urlencode($q) . '&category=' . $category, '&'));
    ?>
  </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="lightboxImage" src="" style="width:100%;height:auto;display:block">
      </div>
      <div class="modal-footer">
        <h6 id="lightboxTitle" class="me-auto"></h6>
        <a id="downloadLink" href="#" class="btn btn-success btn-sm mt-2">Download</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  var modal = document.getElementById('lightboxModal');
  modal.addEventListener('show.bs.modal', function (event) {
    var trigger = event.relatedTarget;
    var src = trigger.getAttribute('data-src');
    var title = trigger.getAttribute('data-title');
    var file = trigger.getAttribute('data-file');

    document.getElementById('lightboxImage').src = src;
    document.getElementById('lightboxTitle').textContent = title || '';
    document.getElementById('downloadLink').href = "download.php?file=" + file;
  });
</script>
</body>
</html>
