<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM pelanggan
        WHERE nama ILIKE :q1 OR alamat ILIKE :q2 OR no_telepon ILIKE :q3
        ORDER BY id DESC
    ");
    $like = "%$keyword%";
    $stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $daftar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftar = $pdo->query("SELECT * FROM pelanggan ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = "Daftar Pelanggan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Daftar Pelanggan</h2>
    <?php tampil_flash(); ?>

    <div class="toolbar">
        <a href="tambah.php" class="btn btn-primary">Tambah Pelanggan Baru</a>
        <form method="GET" action="list.php">
            <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama, alamat, no. telp...">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Nama</th><th>Alamat</th><th>No. Telepon</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="4" style="text-align:center;">
                    <?php echo $keyword !== '' ? 'Data pelanggan tidak ditemukan.' : 'Belum ada data pelanggan.'; ?>
                </td></tr>
            <?php else: foreach ($daftar as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nama']); ?></td>
                    <td><?php echo htmlspecialchars($p['alamat']); ?></td>
                    <td><?php echo htmlspecialchars($p['no_telepon']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="hapus.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-danger btn-hapus">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
