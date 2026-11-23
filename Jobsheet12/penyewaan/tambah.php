<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$daftarPelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Hanya sepeda yang TIDAK sedang disewa (tidak punya penyewaan berstatus 'disewa')
$daftarSepedaTersedia = $pdo->query("
    SELECT * FROM sepeda s
    WHERE NOT EXISTS (
        SELECT 1 FROM penyewaan p WHERE p.sepeda_id = s.id AND p.status = 'disewa'
    )
    ORDER BY kode_sepeda
")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Sewa Sepeda Baru";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Penyewaan Sepeda Baru</h2>
    <?php tampil_flash(); ?>

    <?php if (empty($daftarPelanggan)): ?>
        <p class="flash flash-danger">Belum ada data pelanggan. Tambahkan pelanggan terlebih dahulu.</p>
    <?php elseif (empty($daftarSepedaTersedia)): ?>
        <p class="flash flash-danger">Tidak ada sepeda yang tersedia saat ini (semua sedang disewa).</p>
    <?php else: ?>
    <form method="post" action="proses_tambah.php">
        <?php echo csrf_field(); ?>
        <p>
            <label for="pelanggan_id">Pelanggan</label>
            <select id="pelanggan_id" name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($daftarPelanggan as $pl): ?>
                <option value="<?php echo (int)$pl['id']; ?>">
                    <?php echo e($pl['nama']); ?> (<?php echo e($pl['no_telepon']); ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="sepeda_id">Sepeda (hanya yang tersedia)</label>
            <select id="sepeda_id" name="sepeda_id" required>
                <option value="">-- Pilih Sepeda --</option>
                <?php foreach ($daftarSepedaTersedia as $s): ?>
                <option value="<?php echo (int)$s['id']; ?>">
                    <?php echo e($s['kode_sepeda']); ?> - <?php echo e($s['merek']); ?> <?php echo e($s['jenis']); ?>
                    (Rp <?php echo number_format($s['harga_sewa'], 0, ',', '.'); ?>/hari)
                </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="form-actions">
            <button type="submit">Simpan Penyewaan</button>
            <a href="../index.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
