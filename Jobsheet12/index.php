<?php
session_start();
require __DIR__ . '/includes/koneksi.php';

$totalSepeda    = $pdo->query("SELECT COUNT(*) FROM sepeda")->fetchColumn();
$totalPelanggan = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$totalDisewa    = $pdo->query("SELECT COUNT(*) FROM penyewaan WHERE status = 'disewa'")->fetchColumn();

$page_title = "Beranda";
require __DIR__ . '/includes/header.php';
?>

<section>
    <h2>Selamat Datang di Malang Bike</h2>
    <?php tampil_flash(); ?>
    <p>Website pengelolaan data sepeda dan pelanggan Malang Bike.</p>
</section>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Sepeda</h3>
        <p><?php echo $totalSepeda; ?></p>
        <a href="sepeda/list.php">Lihat Daftar Sepeda &rarr;</a>
    </article>
    <article>
        <h3>Total Pelanggan</h3>
        <p><?php echo $totalPelanggan; ?></p>
        <a href="pelanggan/list.php">Lihat Daftar Pelanggan &rarr;</a>
    </article>
    <article>
        <h3>Sedang Disewa</h3>
        <p><?php echo $totalDisewa; ?></p>
        <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="penyewaan/kembali.php">Lihat Penyewaan Aktif &rarr;</a>
        <?php endif; ?>
    </article>
    <article>
        <h3>Status Sistem</h3>
        <p>Aktif</p>
        <span><?php echo !empty($_SESSION['user_id']) ? 'Login sebagai petugas' : 'Mode pengunjung'; ?></span>
    </article>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
