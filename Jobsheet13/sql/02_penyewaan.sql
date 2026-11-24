-- Jobsheet 12: tabel penyewaan, menghubungkan sepeda dan pelanggan
-- Jalankan: psql -d 01_malangbike -f sql/02_penyewaan.sql
-- (nama database harus sama dengan yang ada di includes/koneksi.php)

CREATE TABLE IF NOT EXISTS penyewaan (
    id SERIAL PRIMARY KEY,
    sepeda_id INTEGER NOT NULL REFERENCES sepeda(id),
    pelanggan_id INTEGER NOT NULL REFERENCES pelanggan(id),
    tanggal_sewa DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_kembali DATE,
    status VARCHAR(20) NOT NULL DEFAULT 'disewa'
        CHECK (status IN ('disewa', 'dikembalikan'))
);

-- Pengaman terakhir di level database: satu sepeda hanya boleh punya
-- SATU penyewaan aktif ('disewa') pada satu waktu.
CREATE UNIQUE INDEX IF NOT EXISTS uq_penyewaan_sepeda_aktif
    ON penyewaan (sepeda_id) WHERE status = 'disewa';
