// ===== 1. Hamburger Menu =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== 2. Konfirmasi Hapus =====
// Link ".btn-hapus": batal jika user menolak; jika setuju, hapus.php yang
// menghapus data. Untuk <button> (versi JSON), baris langsung dibuang.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        if (!confirm("Yakin ingin menghapus \"" + nama + "\"?")) {
            e.preventDefault();
            return;
        }
        if (btn.tagName !== "A" && row) row.remove();
    });
}

// ===== 3. Filter/Pencarian Tabel Real-Time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teks = row.textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

// ===== 4. Helper Validasi Form =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

// ===== 5. Validasi Form Utama =====
function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Semua field teks/email wajib diisi
        form.querySelectorAll("input[required]").forEach(function (input) {
            if (input.value.trim() === "") {
                tampilkanError(input, "Field ini wajib diisi.");
                valid = false;
            } else {
                hapusError(input);
            }
        });

        // Validasi Harga Sewa per hari
        const harga = form.querySelector("[name='harga']");
        if (harga && harga.value.trim() !== "") {
            const nilai = parseInt(harga.value, 10);
            if (isNaN(nilai) || nilai <= 0) {
                tampilkanError(harga, "Harga harus berupa angka lebih dari 0.");
                valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });
}

// Inisialisasi Seluruh Fungsi saat DOM siap
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});