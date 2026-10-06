<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Registrasi Petugas";
require __DIR__ . '/../includes/header.php';
?>

<section class="auth-card">
    <h2>Registrasi Petugas</h2>
    <?php tampil_flash(); ?>

    <form action="proses_register.php" method="POST">
        <p>
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
        </p>
        <p>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Masukkan username">
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="6" required placeholder="Minimal 6 karakter">
        </p>
        <p class="form-actions">
            <button type="submit">Daftar</button>
            <a href="login.php" class="btn btn-secondary">Sudah punya akun? Login</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
