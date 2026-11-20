<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $like = '%' . $keyword . '%';

    $hitung = $pdo->prepare("SELECT COUNT(*) FROM pelanggan WHERE nama ILIKE :q1 OR alamat ILIKE :q2 OR no_telepon ILIKE :q3");
    $hitung->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT * FROM pelanggan
        WHERE nama ILIKE :q1 OR alamat ILIKE :q2 OR no_telepon ILIKE :q3
        ORDER BY id DESC LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue('q1', $like);
    $stmt->bindValue('q2', $like);
    $stmt->bindValue('q3', $like);
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM pelanggan ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftar     = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$page_title = "Daftar Pelanggan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Daftar Pelanggan</h2>
    <?php tampil_flash(); ?>

    <div class="toolbar">
        <a href="tambah.php" class="btn btn-primary">Tambah Pelanggan Baru</a>
        <form method="get" action="list.php">
            <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama, alamat, no. telp...">
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
                        <a href="edit.php?id=<?php echo (int)$p['id']; ?>" class="btn-edit">Edit</a>
                        <form class="form-hapus" method="post" action="hapus.php">
                            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <nav class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
           class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </nav>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
