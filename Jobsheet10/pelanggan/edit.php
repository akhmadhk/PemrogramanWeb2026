<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id = :id");
$stmt->execute(['id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Data pelanggan tidak ditemukan.'];
    header('Location: list.php');
    exit;
}

$page_title = "Edit Pelanggan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Edit Pelanggan</h2>
    <?php tampil_flash(); ?>

    <form id="form-tambah" action="proses_edit.php" method="POST" novalidate>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <p>
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars($p['nama']); ?>">
        </p>
        <p>
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" required value="<?php echo htmlspecialchars($p['alamat']); ?>">
        </p>
        <p>
            <label for="no_telepon">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon" required value="<?php echo htmlspecialchars($p['no_telepon']); ?>">
        </p>
        <p class="form-actions">
            <button type="submit">Simpan Perubahan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
