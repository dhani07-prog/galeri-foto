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
    $params[':q'] = '%' . $q . '%';
}
if ($category) {
    $where[] = "p.category_id = :cat";
    $params[':cat'] = $category;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM photos p $whereSql");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

// ambil foto
$sql = "SELECT p.*, c.name as category 
        FROM photos p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $whereSql 
        ORDER BY p.uploaded_at DESC 
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$extraQuery = http_build_query(array_filter(['q' => $q, 'category' => $category]));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Galeri Foto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Navbar abu gelap */
    .navbar {
      background-color: #343a40 !important;
    }
    .navbar .navbar-brand, 
    .navbar .btn, 
    .navbar span {
      color: #fff !important;
      font-weight: bold;
    }

    /* Header Galeri Publik */
    .gallery-header {
      background-color: #6c757d;
      color: white;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
      padding: 25px 0;
      border-radius: 12px;
      margin: 20px auto 30px;
      width: 80%;
    }

    /* Full photo gallery */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 15px;
    }
    .gallery-item {
      position: relative;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      animation: fadeIn 1s ease-in-out;
    }
    .gallery-item img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s;
    }
    .gallery-item:hover img {
      transform: scale(1.05);
    }
    .gallery-info {
      padding: 8px;
    }

    /* Animasi muncul */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Footer abu gelap */
    footer {
      background-color: #343a40;
      color: white;
      padding: 35px 20px;
      margin-top: 40px;
      text-align: center;
      border-radius: 8px 8px 0 0;
      font-size: 16px;
    }
    footer p {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    footer i {
      margin-right: 5px;
    }

    /* Pagination */
    .custom-pagination {
      display: flex;
      justify-content: center;
      margin-top: 25px;
    }
    .custom-pagination .page-link {
      background-color: #6c757d;
      color: white;
      border: none;
      margin: 0 3px;
      border-radius: 6px;
      padding: 8px 14px;
      font-weight: bold;
    }
    .custom-pagination .page-link:hover {
      background-color: #5a6268;
      color: #fff;
    }
    .custom-pagination .active .page-link {
      background-color: #495057;
    }

    /* Lightbox image pas */
    #lightboxImage {
      max-height: 80vh;
      width: auto;
      margin: 0 auto;
      display: block;
      object-fit: contain;
    }

    /* Tombol Next / Prev di dalam modal */
    .lightbox-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 2rem;
      color: white;
      background: rgba(0,0,0,0.6);
      border: none;
      padding: 10px 15px;
      border-radius: 50%;
      cursor: pointer;
      z-index: 1056;
    }
    .lightbox-prev { left: 15px; }
    .lightbox-next { right: 15px; }
    .lightbox-nav:hover {
      background: rgba(0,0,0,0.8);
    }

    /* Search input dengan icon */
    .search-box {
      position: relative;
    }
    .search-box input {
      padding-left: 30px;
      background: #f1f1f1;
    }
    .search-box i {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #555;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom">
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
        <a class="btn btn-sm btn-light" href="<?= BASE_URL ?>/login.php">Login Admin</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="gallery-header">
    <h3>GALERI PUBLIK</h3>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <form method="get" class="d-flex justify-content-center">
        <div class="search-box me-2 w-25">
          <i class="bi bi-search"></i>
          <input type="text" name="q" value="<?= h($q) ?>" class="form-control form-control-sm" placeholder="Cari judul...">
        </div>
        <select name="category" class="form-select form-select-sm me-2 w-25">
          <option value="">Semua Kategori</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($category==$cat['id'])?'selected':'' ?>>
              <?= h($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-sm btn-secondary">Cari</button>
      </form>
    </div>
  </div>

  <div class="gallery-grid">
    <?php if (count($photos) === 0): ?>
      <div class="alert alert-info">Belum ada foto.</div>
    <?php endif; ?>

    <?php foreach($photos as $p): ?>
      <div class="gallery-item">
        <a href="#"
           class="open-lightbox"
           data-file="<?= BASE_URL ?>/assets/uploads/<?= h($p['filename']) ?>"
           data-title="<?= h($p['title']) ?>"
           data-category="<?= h($p['category'] ?? '—') ?>">
          <img src="<?= BASE_URL ?>/assets/uploads/<?= h($p['filename']) ?>" alt="<?= h($p['title']) ?>">
        </a>
        <div class="gallery-info">
          <h6 class="mb-1"><?= h($p['title'] ?: 'Untitled') ?></h6>
          <small class="text-muted"><?= h($p['category'] ?? '—') ?> • <?= date('d M Y', strtotime($p['uploaded_at'])) ?></small>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <div class="custom-pagination">
    <?php
    echo paginate($total, $perPage, $page, BASE_URL . '/index.php', trim('q=' . urlencode($q) . '&category=' . $category, '&'));
    ?>
  </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body p-0 text-center position-relative">
        <img id="lightboxImage" src="">
        <button class="lightbox-nav lightbox-prev"><i class="bi bi-chevron-left"></i></button>
        <button class="lightbox-nav lightbox-next"><i class="bi bi-chevron-right"></i></button>
      </div>
      <div class="modal-footer">
        <h6 id="lightboxTitle" class="me-auto text-white"></h6>
        <a id="downloadLink" href="#" class="btn btn-success btn-sm mt-2">Download</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<footer>
  <p class="footer-links">
    <span>© <?= date("Y") ?> Semua Hak Cipta Dilindungi</span>
    <span>
      <i class="bi bi-envelope"></i>
      <a href="mailto:ainurrama07@gmail.com" class="email-link">
        ainurrama07@gmail.com
      </a>
    </span>
    <span>
      <i class="bi bi-instagram"></i>
      <a href="https://instagram.com/ramm_fdk03" target="_blank" class="ig-link">
        @ramm_fdk03
      </a>
    </span>
  </p>
</footer>

<style>
  footer {
    background-color: #343a40; /* abu gelap */
    color: white;
    padding: 35px 20px;
    margin-top: 40px;
    text-align: center;
    border-radius: 8px 8px 0 0;
    font-size: 16px;
  }

  .footer-links span {
    margin: 0 15px;
    display: inline-flex;
    align-items: center; /* sejajarin ikon + teks */
    gap: 6px;
  }

  .footer-links a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  /* Hover effect email */
  .footer-links a.email-link:hover,
  .footer-links a.email-link:hover + i,
  .footer-links i.bi-envelope:hover {
    color: #0d6efd; /* biru */
  }

  /* Hover effect Instagram */
  .footer-links a.ig-link:hover,
  .footer-links a.ig-link:hover + i,
  .footer-links i.bi-instagram:hover {
    color: #e4405f; /* pink Instagram */
  }
</style>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const images = document.querySelectorAll('.open-lightbox');
  const modalEl = document.getElementById('lightboxModal');
  const modal = new bootstrap.Modal(modalEl);
  const lightboxImage = document.getElementById('lightboxImage');
  const lightboxTitle = document.getElementById('lightboxTitle');
  const downloadLink = document.getElementById('downloadLink');
  let currentIndex = 0;

  function showImage(index) {
    if (!images.length) return;
    if (index < 0) index = images.length - 1;
    if (index >= images.length) index = 0;
    const img = images[index];
    lightboxImage.src = img.dataset.file;
    lightboxTitle.textContent = img.dataset.title + " - " + img.dataset.category;
    downloadLink.href = img.dataset.file;
    currentIndex = index;
    modal.show();
  }

  images.forEach((img, i) => {
    img.addEventListener('click', e => {
      e.preventDefault();
      showImage(i);
    });
  });

  document.querySelector('.lightbox-prev').addEventListener('click', () => {
    showImage(currentIndex - 1);
  });
  document.querySelector('.lightbox-next').addEventListener('click', () => {
    showImage(currentIndex + 1);
  });
</script>
</body>
</html>
