<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id    = (int)($_POST['id'] ?? 0);
$kode  = trim($_POST['kode_sepeda'] ?? '');
$merek = trim($_POST['merek'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');
$harga = (int)($_POST['harga'] ?? 0);

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$errors = [];
if ($kode === '')  $errors[] = "Kode sepeda wajib diisi.";
if ($merek === '') $errors[] = "Merek sepeda wajib diisi.";
if ($jenis === '') $errors[] = "Jenis sepeda wajib diisi.";
if ($harga <= 0)   $errors[] = "Harga sewa harus berupa angka lebih dari 0.";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => implode(' ', $errors)];
    header("Location: edit.php?id=$id");
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE sepeda
        SET kode_sepeda = :kode, merek = :merek, jenis = :jenis, harga_sewa = :harga
        WHERE id = :id
    ");
    $stmt->execute(['kode' => $kode, 'merek' => $merek, 'jenis' => $jenis, 'harga' => $harga, 'id' => $id]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data sepeda berhasil diperbarui!'];
    header('Location: list.php');
} catch (PDOException $e) {
    $pesan = $e->getCode() === '23505'
        ? "Kode sepeda '{$kode}' sudah dipakai sepeda lain."
        : 'Gagal memperbarui data: ' . $e->getMessage();
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
    header("Location: edit.php?id=$id");
}
exit;
