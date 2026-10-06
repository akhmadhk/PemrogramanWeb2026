<?php
session_start();
require __DIR__ . '/../includes/auth.php';
$page_title = "Tambah Sepeda";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Tambah Sepeda</h2>
    <?php tampil_flash(); ?>

    <form id="form-tambah" method="post" action="proses_tambah.php" novalidate>
        <p>
            <label for="kode_sepeda">Kode Sepeda</label>
            <input type="text" id="kode_sepeda" name="kode_sepeda" required placeholder="Contoh: MB-001">
        </p>
        <p>
            <label for="merek">Merek</label>
            <input type="text" id="merek" name="merek" required placeholder="Contoh: Polygon">
        </p>
        <p>
            <label for="jenis">Jenis</label>
            <input type="text" id="jenis" name="jenis" required placeholder="Contoh: Sepeda Gunung">
        </p>
        <p>
            <label for="harga">Harga Sewa per Hari (Rp)</label>
            <input type="number" id="harga" name="harga" min="1" required placeholder="50000">
        </p>
        <p class="form-actions">
            <button type="submit">Simpan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
