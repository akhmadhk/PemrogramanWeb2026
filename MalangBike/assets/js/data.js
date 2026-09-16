const bikeDefaults = [
  {
    name: "City Bike",
    type: "City Bike",
    price: 15000,
    description:
      "Cocok untuk berkeliling pusat Kota Malang dan area Kayutangan.",
    icon: "🚲",
  },
  {
    name: "Mountain Bike",
    type: "Mountain Bike",
    price: 25000,
    description: "Pilihan untuk perjalanan yang lebih jauh dan menantang.",
    icon: "🚵",
  },
  {
    name: "Electric Bike",
    type: "Electric Bike",
    price: 35000,
    description: "Nyaman untuk perjalanan jauh tanpa cepat kelelahan.",
    icon: "⚡🚲",
  },
];
const locationDefaults = [
  {
    name: "Kayutangan Heritage",
    area: "Area pusat kota",
    address: "Kayutangan, Malang",
    icon: "📍",
  },
  {
    name: "Alun-Alun Malang",
    area: "Area alun-alun",
    address: "Alun-Alun Malang",
    icon: "🏛️",
  },
  {
    name: "Ijen Boulevard",
    area: "Area Jalan Ijen",
    address: "Jl. Ijen, Malang",
    icon: "🌳",
  },
  {
    name: "Area Kampus",
    area: "Malang Kota",
    address: "Kawasan kampus Malang",
    icon: "🎓",
  },
];
function data(k, d) {
  let x = localStorage.getItem(k);
  if (!x) {
    localStorage.setItem(k, JSON.stringify(d));
    return d;
  }
  return JSON.parse(x);
}
function rupiah(n) {
  return "Rp" + Number(n).toLocaleString("id-ID");
}
function esc(s) {
  return String(s).replace(
    /[&<>"']/g,
    (m) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
      })[m],
  );
}
const bl = document.getElementById("bikeList");
if (bl) {
  const bikes = data("bikes", bikeDefaults);
  bl.innerHTML = bikes
    .map(
      (b, i) =>
        `<div class="col-md-6 col-lg-4"><div class="bike-card"><div class="bike-image">${b.icon || "🚲"}</div><div class="bike-card-body"><span class="bike-tag">${esc(b.type)}</span><h3>${esc(b.name)}</h3><p>${esc(b.description)}</p><div class="price">${rupiah(b.price)} <small>/ jam</small></div><button class="btn btn-delete" onclick="hapusSepeda(${i})">Hapus</button></div></div></div>`,
    )
    .join("");
}
function hapusSepeda(i) {
  if (confirm("Hapus sepeda ini?")) {
    let a = data("bikes", bikeDefaults);
    a.splice(i, 1);
    localStorage.setItem("bikes", JSON.stringify(a));
    location.reload();
  }
}
const ll = document.getElementById("locationList");
if (ll) {
  const a = data("locations", locationDefaults);
  ll.innerHTML = a
    .map(
      (x, i) =>
        `<div class="location-item"><span>${x.icon || "📍"}</span><div class="flex-grow-1"><h4>${esc(x.name)}</h4><p>${esc(x.area)}${x.address ? " · " + esc(x.address) : ""}</p></div><button class="btn btn-delete" onclick="hapusLokasi(${i})">Hapus</button></div>`,
    )
    .join("");
}
function hapusLokasi(i) {
  if (confirm("Hapus lokasi ini?")) {
    let a = data("locations", locationDefaults);
    a.splice(i, 1);
    localStorage.setItem("locations", JSON.stringify(a));
    location.reload();
  }
}
const bf = document.getElementById("bikeForm");
if (bf)
  bf.onsubmit = (e) => {
    e.preventDefault();
    let a = data("bikes", bikeDefaults);
    a.push({
      name: name.value,
      type: type.value,
      price: price.value,
      description: description.value,
      icon:
        type.value === "Mountain Bike"
          ? "🚵"
          : type.value === "Electric Bike"
            ? "⚡🚲"
            : "🚲",
    });
    localStorage.setItem("bikes", JSON.stringify(a));
    location.href = "list.html";
  };
const lf = document.getElementById("locationForm");
if (lf)
  lf.onsubmit = (e) => {
    e.preventDefault();
    let a = data("locations", locationDefaults);
    a.push({
      name: name.value,
      area: area.value,
      address: address.value,
      icon: "📍",
    });
    localStorage.setItem("locations", JSON.stringify(a));
    location.href = "list.html";
  };
