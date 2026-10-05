<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Malang Bike | Tambah Sepeda</title>
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
                <li><a href="../sepeda/list.html">Daftar Sepeda</a></li>
                <li><a href="../sepeda/tambah.html" class="aktif">Tambah Sepeda</a></li>
                <li><a href="../pelanggan/list.html">Daftar Pelanggan</a></li>
                <li><a href="../pelanggan/tambah.html">Tambah Pelanggan</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Tambah Sepeda</h2>
            <form id="form-tambah">
                <p>
                    <label for="kode">Kode Sepeda</label><br>
                    <input type="text" id="kode" name="kode" required>
                </p>
                <p>
                    <label for="merek">Merek</label><br>
                    <input type="text" id="merek" name="merek" required>
                </p>
                <p>
                    <label for="jenis">Jenis</label><br>
                    <input type="text" id="jenis" name="jenis" required>
                </p>
                <p>
                    <label for="harga">Harga Sewa per Hari (Rp)</label><br>
                    <input type="text" id="harga" name="harga" required>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Malang Bike &mdash; Pemrograman Web</p>
    </footer>
    <script src="../assets/js/app.js"></script>
</body>
</html>
