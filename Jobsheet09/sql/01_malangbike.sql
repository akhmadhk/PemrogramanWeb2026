-- Skema database Malang Bike (PostgreSQL)
--   createdb malangbike
--   psql -d malangbike -f sql/01_malangbike.sql

CREATE TABLE IF NOT EXISTS sepeda (
    id SERIAL PRIMARY KEY,
    kode_sepeda VARCHAR(20) NOT NULL UNIQUE,
    merek VARCHAR(100) NOT NULL,
    jenis VARCHAR(100) NOT NULL,
    harga_sewa INTEGER NOT NULL CHECK (harga_sewa > 0),
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS pelanggan (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    alamat VARCHAR(255),
    no_telepon VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);
