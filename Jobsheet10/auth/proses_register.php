<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '')            $errors[] = "Nama wajib diisi.";
if ($username === '')        $errors[] = "Username wajib diisi.";
if (strlen($password) < 6)   $errors[] = "Password minimal 6 karakter.";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => implode(' ', $errors)];
    header('Location: register.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (nama, username, password, role)
        VALUES (:nama, :username, :password, 'petugas')
    ");
    $stmt->execute([
        'nama'     => $nama,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil! Silakan login.'];
    header('Location: login.php');
} catch (PDOException $e) {
    $pesan = $e->getCode() === '23505'
        ? 'Username sudah digunakan.'
        : 'Gagal mendaftar, terjadi kesalahan sistem.';
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
    header('Location: register.php');
}
exit;
