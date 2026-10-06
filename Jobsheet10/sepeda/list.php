<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM sepeda
        WHERE merek ILIKE :q1 OR jenis ILIKE :q2 OR kode_sepeda ILIKE :q3
        ORDER BY id DESC
    ");
    $like = "%$keyword%";
    $stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $daftar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftar = $pdo->query("SELECT * FROM sepeda ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = "Daftar Sepeda";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Daftar Sepeda</h2>
    <?php tampil_flash(); ?>

    <div class="toolbar">
        <?php if ($sudahLogin): ?>
        <a href="tambah.php" class="btn btn-primary">Tambah Sepeda Baru</a>
        <?php else: ?><span></span><?php endif; ?>
        <form method="GET" action="list.php">
            <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari kode, merek, atau jenis...">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Kode Sepeda</th><th>Merek</th><th>Jenis</th><th>Harga/Hari</th><th>Tanggal Input</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="6" style="text-align:center;">
                    <?php echo $keyword !== '' ? 'Data sepeda tidak ditemukan.' : 'Belum ada data sepeda.'; ?>
                </td></tr>
            <?php else: foreach ($daftar as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['kode_sepeda']); ?></td>
                    <td><?php echo htmlspecialchars($s['merek']); ?></td>
                    <td><?php echo htmlspecialchars($s['jenis']); ?></td>
                    <td>Rp <?php echo number_format($s['harga_sewa'], 0, ',', '.'); ?></td>
                    <td><?php echo !empty($s['created_at']) ? date('d-m-Y H:i', strtotime($s['created_at'])) : '-'; ?></td>
                    <td>
                        <?php if ($sudahLogin): ?>
                        <a href="edit.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="hapus.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-danger btn-hapus">Hapus</a>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
