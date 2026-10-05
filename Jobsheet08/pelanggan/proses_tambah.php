<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$nama       = trim($_POST['nama'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');

if ($nama === '' || $alamat === '' || $no_telepon === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Semua kolom wajib diisi!'];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO pelanggan (nama, alamat, no_telepon) VALUES (:nama, :alamat, :no_telepon)");
    $stmt->execute(['nama' => $nama, 'alamat' => $alamat, 'no_telepon' => $no_telepon]);
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data pelanggan berhasil ditambahkan!'];
    header('Location: list.php');
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal menyimpan data: ' . $e->getMessage()];
    header('Location: tambah.php');
}
exit;
