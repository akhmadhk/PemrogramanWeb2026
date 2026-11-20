<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $like = '%' . $keyword . '%';

    $hitung = $pdo->prepare("SELECT COUNT(*) FROM sepeda WHERE merek ILIKE :q1 OR jenis ILIKE :q2 OR kode_sepeda ILIKE :q3");
    $hitung->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT * FROM sepeda
        WHERE merek ILIKE :q1 OR jenis ILIKE :q2 OR kode_sepeda ILIKE :q3
        ORDER BY id DESC LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue('q1', $like);
    $stmt->bindValue('q2', $like);
    $stmt->bindValue('q3', $like);
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM sepeda")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM sepeda ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftar     = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$page_title = "Daftar Sepeda";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Daftar Sepeda</h2>
    <?php tampil_flash(); ?>

    <div class="toolbar">
        <a href="tambah.php" class="btn btn-primary">Tambah Sepeda Baru</a>
        <form method="get" action="list.php">
            <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari kode, merek, atau jenis...">
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
                        <a href="edit.php?id=<?php echo (int)$s['id']; ?>" class="btn-edit">Edit</a>
                        <form class="form-hapus" method="post" action="hapus.php">
                            <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
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
