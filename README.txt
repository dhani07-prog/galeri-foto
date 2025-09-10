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
