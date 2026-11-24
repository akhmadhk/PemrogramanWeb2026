<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kembali.php');
    exit;
}

csrf_verify();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: kembali.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Kunci baris penyewaan agar klik ganda / dua petugas tidak memproses
    // pengembalian yang sama dua kali.
    $stmt = $pdo->prepare("SELECT status FROM penyewaan WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $trx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trx || $trx['status'] !== 'disewa') {
        throw new Exception('Transaksi tidak ditemukan atau sudah dikembalikan.');
    }

    $update = $pdo->prepare(
        "UPDATE penyewaan SET status = 'dikembalikan', tanggal_kembali = CURRENT_DATE WHERE id = :id"
    );
    $update->execute(['id' => $id]);

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Sepeda berhasil dikembalikan.'];
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Pengembalian gagal: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Terjadi kesalahan database. Silakan coba lagi.'];
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal memproses pengembalian: ' . $e->getMessage()];
}

header('Location: kembali.php');
exit;
