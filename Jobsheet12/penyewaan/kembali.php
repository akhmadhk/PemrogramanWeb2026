<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$keyword = trim($_GET['q'] ?? '');

$sqlDasar = "SELECT p.id, s.kode_sepeda, s.merek, pl.nama, p.tanggal_sewa
             FROM penyewaan p
             JOIN sepeda s ON s.id = p.sepeda_id
             JOIN pelanggan pl ON pl.id = p.pelanggan_id
             WHERE p.status = 'disewa'";

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        $sqlDasar . " AND (s.kode_sepeda ILIKE :q1 OR s.merek ILIKE :q2 OR pl.nama ILIKE :q3)
                      ORDER BY p.tanggal_sewa, p.id"
    );
    $like = '%' . $keyword . '%';
    $stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
} else {
    $stmt = $pdo->query($sqlDasar . " ORDER BY p.tanggal_sewa, p.id");
}
$daftarAktif = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Pengembalian Sepeda";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Pengembalian Sepeda</h2>
    <?php tampil_flash(); ?>

    <div class="toolbar">
        <span></span>
        <form method="get" action="kembali.php">
            <input type="text" id="search-input" name="q" value="<?php echo e($keyword); ?>" placeholder="Nama pelanggan, kode, atau merek...">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="kembali.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Pelanggan</th><th>Sepeda</th><th>Tgl Sewa</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($daftarAktif)): ?>
                <tr><td colspan="4" style="text-align:center;">
                    <?php echo $keyword !== '' ? 'Penyewaan aktif tidak ditemukan.' : 'Tidak ada penyewaan aktif.'; ?>
                </td></tr>
            <?php else: foreach ($daftarAktif as $trx): ?>
                <tr>
                    <td><?php echo e($trx['nama']); ?></td>
                    <td><?php echo e($trx['kode_sepeda']); ?> - <?php echo e($trx['merek']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($trx['tanggal_sewa'])); ?></td>
                    <td>
                        <form class="form-kembali" method="post" action="proses_kembali.php">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$trx['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-kembali">Kembalikan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
