<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Malang Bike | Tambah Pelanggan</title>
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
                <li><a href="../sepeda/tambah.html">Tambah Sepeda</a></li>
                <li><a href="../pelanggan/list.html">Daftar Pelanggan</a></li>
                <li><a href="../pelanggan/tambah.html" class="aktif">Tambah Pelanggan</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Tambah Pelanggan</h2>
            <form id="form-tambah">
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" required>
                </p>
                <p>
                    <label for="alamat">Alamat</label><br>
                    <input type="text" id="alamat" name="alamat" required>
                </p>
                <p>
                    <label for="telepon">No. Telepon</label><br>
                    <input type="text" id="telepon" name="telepon" required>
                </p>
                <p>
                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email" required>
                </p>
                <p>
                    <label for="jaminan">Jaminan</label><br>
                    <select id="jaminan" name="jaminan">
                        <option value="KTP">KTP</option>
                        <option value="SIM">SIM</option>
                        <option value="KTM">KTM</option>
                    </select>
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
