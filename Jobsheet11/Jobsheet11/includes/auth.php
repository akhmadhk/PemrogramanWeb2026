<?php
// Sertakan di halaman yang hanya boleh diakses petugas yang sudah login
// (tambah, edit, hapus, dan semua proses_*). Path relatif ke auth/ dari subfolder.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Silakan login terlebih dahulu.'];
    header('Location: ../auth/login.php');
    exit;
}
