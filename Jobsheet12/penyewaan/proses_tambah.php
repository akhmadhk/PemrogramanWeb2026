<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

csrf_verify();

$pelangganId = (int)($_POST['pelanggan_id'] ?? 0);
$sepedaId    = (int)($_POST['sepeda_id'] ?? 0);

if ($pelangganId <= 0 || $sepedaId <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Pelanggan dan sepeda wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Kunci baris sepeda (FOR UPDATE) supaya dua petugas yang menyewakan sepeda
    // yang sama hampir bersamaan harus antre — mencegah satu sepeda disewa dua kali.
    $cek = $pdo->prepare("SELECT id FROM sepeda WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $sepedaId]);
    if (!$cek->fetch()) {
        throw new Exception('Sepeda tidak ditemukan.');
    }

    $cekPelanggan = $pdo->prepare("SELECT id FROM pelanggan WHERE id = :id");
    $cekPelanggan->execute(['id' => $pelangganId]);
    if (!$cekPelanggan->fetch()) {
        throw new Exception('Pelanggan tidak ditemukan.');
    }

    $aktif = $pdo->prepare("SELECT COUNT(*) FROM penyewaan WHERE sepeda_id = :id AND status = 'disewa'");
    $aktif->execute(['id' => $sepedaId]);
    if ($aktif->fetchColumn() > 0) {
        throw new Exception('Sepeda ini sedang disewa.');
    }

    $insert = $pdo->prepare(
        "INSERT INTO penyewaan (sepeda_id, pelanggan_id, tanggal_sewa, status)
         VALUES (:sepeda_id, :pelanggan_id, CURRENT_DATE, 'disewa')"
    );
    $insert->execute(['sepeda_id' => $sepedaId, 'pelanggan_id' => $pelangganId]);

    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Penyewaan berhasil dicatat.'];
    header('Location: ../index.php');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Penyewaan gagal: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Terjadi kesalahan database. Silakan coba lagi.'];
    header('Location: tambah.php');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal mencatat penyewaan: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
