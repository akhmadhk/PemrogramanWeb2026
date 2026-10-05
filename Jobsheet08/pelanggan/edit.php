<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id = :id");
$stmt->execute(['id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Data pelanggan tidak ditemukan.'];
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');

    if ($nama === '' || $alamat === '' || $no_telepon === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Semua kolom wajib diisi!'];
        header("Location: edit.php?id=$id");
        exit;
    }
    try {
        $u = $pdo->prepare("UPDATE pelanggan SET nama=:nama, alamat=:alamat, no_telepon=:no_telepon WHERE id=:id");
        $u->execute(['nama' => $nama, 'alamat' => $alamat, 'no_telepon' => $no_telepon, 'id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data pelanggan berhasil diperbarui.'];
        header('Location: list.php');
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Gagal memperbarui data: ' . $e->getMessage()];
        header("Location: edit.php?id=$id");
    }
    exit;
}

$page_title = "Edit Pelanggan";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Edit Pelanggan</h2>
    <?php tampil_flash(); ?>

    <form id="form-tambah" action="edit.php" method="POST" novalidate>
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
