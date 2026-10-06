<?php
session_start();
require __DIR__ . '/../includes/auth.php';
$page_title = "Tambah Pelanggan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Tambah Pelanggan</h2>
    <?php tampil_flash(); ?>

    <form id="form-tambah" action="proses_tambah.php" method="POST" novalidate>
        <p>
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>
        </p>
        <p>
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" required>
        </p>
        <p>
            <label for="no_telepon">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" required>
        </p>
        <p class="form-actions">
            <button type="submit">Simpan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
