<?php
session_start();
include "database.php";

// validasi id
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT galeri.*, kategori.nama AS kategori 
                        FROM galeri 
                        LEFT JOIN kategori ON galeri.kategori_id = kategori.id
                        WHERE galeri.id = $id");

if ($result->num_rows == 0) {
    echo "Foto tidak ditemukan!";
    exit;
}

$foto = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($foto['judul']) ?> - Detail Foto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 30px;
    }
    .container {
      max-width: 1000px;
      margin: auto;
      display: flex;
      gap: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .container img {
      width: 500px;
      max-height: 500px;
      object-fit: cover;
      border-radius: 10px;
    }
    .info {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    h1 {
      margin-top: 0;
      color: #2c3e50;
    }
    p {
      font-size: 14px;
      line-height: 1.6;
      color: #555;
    }
    .meta {
      margin-top: 15px;
      font-size: 13px;
      color: #777;
    }
    .back {
      display: inline-block;
      margin-top: 20px;
      padding: 8px 14px;
      background: #3498db;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
    }
    .back:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="uploads/<?= htmlspecialchars($foto['file']) ?>" alt="<?= htmlspecialchars($foto['judul']) ?>">

    <div class="info">
      <div>
        <h1><?= htmlspecialchars($foto['judul']) ?></h1>
        <p><b>Kategori:</b> <?= $foto['kategori'] ?? 'Tanpa Kategori' ?></p>
        <p><b>Deskripsi:</b><br><?= !empty($foto['deskripsi']) ? nl2br(htmlspecialchars($foto['deskripsi'])) : '-' ?></p>
        <p class="meta"><b>Tanggal Upload:</b> <?= $foto['tanggal_upload'] ?></p>
      </div>
      <div>
        <a class="back" href="index.php">⬅ Kembali ke Galeri</a>
      </div>
    </div>
  </div>
</body>
</html>
