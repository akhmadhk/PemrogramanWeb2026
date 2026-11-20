<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Sengaja hanya menerima POST (bukan GET) agar penghapusan tidak bisa
// dipicu tanpa sengaja lewat link/preview crawler.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM sepeda WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = $stmt->rowCount() > 0
            ? ['type' => 'success', 'pesan' => 'Data sepeda berhasil dihapus.']
            : ['type' => 'danger', 'pesan' => 'Data sepeda tidak ditemukan.'];
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal menghapus: ' . $e->getMessage()];
    }
}

header('Location: list.php');
exit;
