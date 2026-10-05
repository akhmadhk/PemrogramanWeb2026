<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Malang Bike | Daftar Sepeda</title>
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
                <li><a href="../index.html">Beranda</a></li>
                <li><a href="../sepeda/list.html" class="aktif">Daftar Sepeda</a></li>
                <li><a href="../sepeda/tambah.html">Tambah Sepeda</a></li>
                <li><a href="../pelanggan/list.html">Daftar Pelanggan</a></li>
                <li><a href="../pelanggan/tambah.html">Tambah Pelanggan</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Daftar Sepeda</h2>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Cari data...">
            </div>
            <p id="loading-indicator" style="display:none;">Memuat data...</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                        <th>Kode Sepeda</th>
                        <th>Merek</th>
                        <th>Jenis</th>
                        <th>Harga/Hari</th>
                        <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris diisi dinamis oleh assets/js/sepeda.js -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Malang Bike &mdash; Pemrograman Web</p>
    </footer>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/sepeda.js"></script>
</body>
</html>
