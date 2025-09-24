## Gallery Foto Komunitas
📌 Deskripsi Aplikasi:

Gallery Foto Komunitas adalah aplikasi berbasis web yang digunakan untuk mengelola, menampilkan, dan berbagi foto dalam sebuah komunitas. Sistem ini memudahkan anggota maupun pengunjung untuk melihat koleksi foto dengan tampilan yang rapi, modern, dan interaktif.

🎯 Tujuan:
   Menjadi wadah dokumentasi kegiatan komunitas.
   Memudahkan publik mengakses, mencari, dan mengunduh foto.
   Memberikan panel admin untuk mengelola foto, kategori, dan pengguna.

🔑 Fitur Utama:
   Galeri Publik: Tampilan grid foto dengan pencarian berdasarkan judul dan filter kategori.
   Lightbox/Modal Viewer: Foto bisa dibuka dengan tampilan besar tanpa berpindah halaman.
   Pagination: Menampilkan 12 foto per halaman agar lebih ringan.
   Download: Pengunjung bisa mengunduh foto langsung.
   Manajemen Admin: Admin dapat login, mengunggah, menghapus, dan mengatur foto.
   User Roles: Mendukung peran pengguna (misalnya admin, user).

Petunjuk singkat:
1. Copy folder galeri-foto ke C:/laragon/www/
2. Import database.sql ke MySQL (phpMyAdmin).
3. Jalankan setup_admin.php sekali via browser: http://localhost/galeri-foto/setup_admin.php
   - Ini akan membuat user 'admin' dengan password 'admin123'.
   - Setelah itu hapus file setup_admin.php demi keamanan.
4. Pastikan folder assets/uploads writable.
5. Akses:
   - Publik: http://localhost/galeri-foto/index.php
   - Admin: http://localhost/galeri-foto/login.php
6. Fitur tambahan:
   - Pencarian (by title) dan filter kategori di halaman publik.
   - Pagination (12 item per halaman).
   - Lightbox menggunakan Bootstrap Modal.
   - Simple user roles (role di tabel users). Default admin dibuat lewat setup_admin.php.
   - Di halaman publik ada fitur download.

screenshot 
---
![Gallery](/docs/gallery.PNG)
![login](/docs/image.png)
![dashboard admin](/docs/image-1.png)
![kelola kategori](/docs/image-2.png)
---

## 📊 Flowchart

Lihat flowchart di sini: [flowchart.md](./flowchart.md)
