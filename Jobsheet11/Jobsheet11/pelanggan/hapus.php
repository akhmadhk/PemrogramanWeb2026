<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

// Sengaja hanya menerima POST (bukan GET) agar penghapusan tidak bisa
// dipicu tanpa sengaja lewat link/gambar/preview crawler.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();

$id = (int)($_POST['id'] ?? 0);
try {
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = $stmt->rowCount() > 0
        ? ['type' => 'success', 'pesan' => 'Data pelanggan berhasil dihapus.']
        : ['type' => 'danger', 'pesan' => 'Data pelanggan tidak ditemukan.'];
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal menghapus: ' . $e->getMessage()];
}
header('Location: list.php');
exit;
