<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Login Petugas";
require __DIR__ . '/../includes/header.php';
?>

<section class="auth-card">
    <h2>Login Petugas</h2>
    <?php tampil_flash(); ?>

    <form method="post" action="proses_login.php">
        <p>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Masukkan username" autofocus>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Masukkan password">
        </p>
        <p class="form-actions">
            <button type="submit">Masuk</button>
            <a href="register.php" class="btn btn-secondary">Belum punya akun? Registrasi</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
