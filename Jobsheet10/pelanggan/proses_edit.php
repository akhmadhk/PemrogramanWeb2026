<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$nama       = trim($_POST['nama'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$errors = [];
if ($nama === '')       $errors[] = "Nama pelanggan wajib diisi.";
if ($alamat === '')     $errors[] = "Alamat wajib diisi.";
if ($no_telepon === '') $errors[] = "Nomor telepon wajib diisi.";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => implode(' ', $errors)];
    header("Location: edit.php?id=$id");
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE pelanggan SET nama = :nama, alamat = :alamat, no_telepon = :no_telepon WHERE id = :id
    ");
    $stmt->execute(['nama' => $nama, 'alamat' => $alamat, 'no_telepon' => $no_telepon, 'id' => $id]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data pelanggan berhasil diperbarui!'];
    header('Location: list.php');
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal memperbarui data: ' . $e->getMessage()];
    header("Location: edit.php?id=$id");
}
exit;
