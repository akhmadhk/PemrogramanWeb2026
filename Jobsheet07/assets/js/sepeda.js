// Mengambil & menampilkan Daftar Sepeda secara asinkron dari data/sepeda.json
async function muatDaftarSepeda() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");

    if (!tbody) return;


    if (loading) loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        // Simulasi delay jaringan agar loading indicator sempat terlihat
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/sepeda.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }

        const daftarSepeda = await res.json();

        daftarSepeda.forEach(function (sepeda) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + sepeda.kode + "</td>" +
                "<td>" + sepeda.merek + "</td>" +
                "<td>" + sepeda.jenis + "</td>" +
                "<td>Rp " + sepeda.harga.toLocaleString("id-ID") + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });

    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"5\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        if (loading) loading.style.display = "none";
    }
}

document.addEventListener("DOMContentLoaded", muatDaftarSepeda);