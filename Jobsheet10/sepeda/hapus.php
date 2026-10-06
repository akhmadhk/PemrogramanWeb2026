<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
try {
    $stmt = $pdo->prepare("DELETE FROM sepeda WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = $stmt->rowCount() > 0
        ? ['type' => 'success', 'pesan' => 'Data sepeda berhasil dihapus.']
        : ['type' => 'danger', 'pesan' => 'Data sepeda tidak ditemukan.'];
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal menghapus: ' . $e->getMessage()];
}
header('Location: list.php');
exit;
