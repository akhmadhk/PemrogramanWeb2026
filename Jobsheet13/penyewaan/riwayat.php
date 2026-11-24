<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$pelangganId = (int)($_GET['pelanggan_id'] ?? 0);
$daftarPelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

$riwayat = [];
$pelangganTerpilih = null;

if ($pelangganId > 0) {
    $stmtP = $pdo->prepare("SELECT * FROM pelanggan WHERE id = :id");
    $stmtP->execute(['id' => $pelangganId]);
    $pelangganTerpilih = $stmtP->fetch(PDO::FETCH_ASSOC);

    if ($pelangganTerpilih) {
        $stmt = $pdo->prepare(
            "SELECT s.kode_sepeda, s.merek, s.jenis, p.tanggal_sewa, p.tanggal_kembali, p.status
             FROM penyewaan p
             JOIN sepeda s ON s.id = p.sepeda_id
             WHERE p.pelanggan_id = :id
             ORDER BY p.tanggal_sewa DESC, p.id DESC"
        );
        $stmt->execute(['id' => $pelangganId]);
        $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$page_title = "Riwayat Penyewaan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Riwayat Penyewaan</h2>

    <form method="get" action="riwayat.php">
        <p>
            <label for="pelanggan_id">Pilih Pelanggan</label>
            <select id="pelanggan_id" name="pelanggan_id">
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($daftarPelanggan as $pl): ?>
                <option value="<?php echo (int)$pl['id']; ?>" <?php echo $pelangganId === (int)$pl['id'] ? 'selected' : ''; ?>>
                    <?php echo e($pl['nama']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="form-actions">
            <button type="submit">Tampilkan</button>
        </p>
    </form>

    <?php if ($pelangganTerpilih): ?>
    <h3>Riwayat &mdash; <?php echo e($pelangganTerpilih['nama']); ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Sepeda</th><th>Sewa</th><th>Kembali</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="4" style="text-align:center;">Belum ada riwayat penyewaan.</td></tr>
            <?php else: foreach ($riwayat as $r): ?>
                <tr>
                    <td><?php echo e($r['kode_sepeda']); ?> - <?php echo e($r['merek']); ?> <?php echo e($r['jenis']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($r['tanggal_sewa'])); ?></td>
                    <td><?php echo !empty($r['tanggal_kembali']) ? date('d-m-Y', strtotime($r['tanggal_kembali'])) : '-'; ?></td>
                    <td><?php echo $r['status'] === 'disewa' ? 'Disewa' : 'Selesai'; ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
