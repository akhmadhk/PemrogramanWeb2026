<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sudahLogin = !empty($_SESSION['user_id']);

// Hitung prefix path ke root proyek (mis. "../" untuk file di dalam sepeda/)
$__root = str_replace('\\', '/', dirname(__DIR__));
$__dir  = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$__rel  = trim(substr($__dir, strlen($__root)), '/');
$base   = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
$__cur  = ltrim(($__rel === '' ? '' : $__rel . '/') . basename($_SERVER['SCRIPT_FILENAME']), '/');

function nav_aktif($file) {
    global $__cur;
    return $__cur === $file ? ' class="aktif"' : '';
}

// Tampilkan pesan flash (sekali pakai)
function tampil_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<p class="flash flash-' . htmlspecialchars($f['type']) . '">' . htmlspecialchars($f['pesan']) . '</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Malang Bike<?php echo isset($page_title) ? ' | ' . htmlspecialchars($page_title) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
    <header>
        <div class="brand-group">
            <h1>🚲MALANG BIKE</h1>
            <p class="subtitle">Sistem Informasi Penyewaan Sepeda</p>
        </div>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?php echo $base; ?>index.php"<?php echo nav_aktif('index.php'); ?>>Beranda</a></li>
                <li><a href="<?php echo $base; ?>sepeda/list.php"<?php echo nav_aktif('sepeda/list.php'); ?>>Daftar Sepeda</a></li>
                <li><a href="<?php echo $base; ?>pelanggan/list.php"<?php echo nav_aktif('pelanggan/list.php'); ?>>Daftar Pelanggan</a></li>
                <?php if ($sudahLogin): ?>
                <li><a href="<?php echo $base; ?>sepeda/tambah.php"<?php echo nav_aktif('sepeda/tambah.php'); ?>>Tambah Sepeda</a></li>
                <li><a href="<?php echo $base; ?>pelanggan/tambah.php"<?php echo nav_aktif('pelanggan/tambah.php'); ?>>Tambah Pelanggan</a></li>
                <li><span>Halo, <strong><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Petugas'); ?></strong></span> |
                    <a href="<?php echo $base; ?>auth/logout.php">Logout</a></li>
                <?php else: ?>
                <li><a href="<?php echo $base; ?>auth/login.php"<?php echo nav_aktif('auth/login.php'); ?>>Login Petugas</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
