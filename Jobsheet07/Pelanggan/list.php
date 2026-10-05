<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Malang Bike | Daftar Pelanggan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="brand-group">
            <h1>🚲 MALANG BIKE</h1>
            <p class="subtitle">Sistem Informasi Penyewaan Sepeda</p>
        </div>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="../index.php">Beranda</a></li>
                <li><a href="../sepeda/list.php">Daftar Sepeda</a></li>
                <li><a href="../sepeda/tambah.php">Tambah Sepeda</a></li>
                <li><a href="../pelanggan/list.php">Daftar Pelanggan</a></li>
                <li><a href="../pelanggan/tambah.php">Tambah Pelanggan</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Daftar Pelanggan</h2>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Cari data...">
            </div>
            <p id="loading-indicator" style="display:none;">Memuat data...</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris diisi dinamis oleh assets/js/pelanggan.js -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Malang Bike &mdash; Pemrograman Web</p>
    </footer>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/pelanggan.js"></script>
</body>
</html>
