<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Username dan password wajib diisi!'];
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Hanya password_verify (password selalu tersimpan sebagai hash)
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // cegah session fixation
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['nama']     = $user['nama'] ?: $user['username'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header('Location: ../index.php');
        exit;
    }

    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Username atau password salah!'];
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal membaca data user. Pastikan tabel "users" sudah dibuat di database.'];
}
header('Location: login.php');
exit;
