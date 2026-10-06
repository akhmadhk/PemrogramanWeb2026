<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$kode  = trim($_POST['kode_sepeda'] ?? '');
$merek = trim($_POST['merek'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');
$harga = (int)($_POST['harga'] ?? 0);

if ($kode === '' || $merek === '' || $jenis === '' || $harga <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Semua bidang form wajib diisi dengan benar!'];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO sepeda (kode_sepeda, merek, jenis, harga_sewa)
        VALUES (:kode, :merek, :jenis, :harga)
    ");
    $stmt->execute(['kode' => $kode, 'merek' => $merek, 'jenis' => $jenis, 'harga' => $harga]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data sepeda berhasil ditambahkan.'];
    header('Location: list.php');
} catch (PDOException $e) {
    // 23505 = pelanggaran UNIQUE di PostgreSQL
    $pesan = $e->getCode() === '23505'
        ? "Kode sepeda '{$kode}' sudah terdaftar! Gunakan kode lain."
        : 'Terjadi kesalahan database: ' . $e->getMessage();
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
    header('Location: tambah.php');
}
exit;
