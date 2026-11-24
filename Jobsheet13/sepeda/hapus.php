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
    $stmt = $pdo->prepare("DELETE FROM sepeda WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = $stmt->rowCount() > 0
        ? ['type' => 'success', 'pesan' => 'Data sepeda berhasil dihapus.']
        : ['type' => 'danger', 'pesan' => 'Data sepeda tidak ditemukan.'];
} catch (PDOException $e) {
    // 23503 = pelanggaran FOREIGN KEY: data ini masih dirujuk tabel penyewaan
    $pesan = $e->getCode() === '23503'
        ? 'Data sepeda tidak bisa dihapus karena sudah memiliki riwayat penyewaan.'
        : 'Gagal menghapus: ' . $e->getMessage();
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
}
header('Location: list.php');
exit;
