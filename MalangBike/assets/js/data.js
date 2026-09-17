const defaultBikes = [
  { id: 1, name: 'Malang City 01', type: 'City Bike', price: 15000, description: 'Nyaman untuk keliling pusat Kota Malang.' },
  { id: 2, name: 'Bromo MTB 01', type: 'Mountain Bike', price: 25000, description: 'Cocok untuk rute dengan medan menanjak.' },
  { id: 3, name: 'Electric Ride 01', type: 'Electric Bike', price: 35000, description: 'Sepeda listrik untuk perjalanan lebih santai.' }
];

const defaultLocations = [
  { id: 1, name: 'Alun-Alun Malang', area: 'Klojen', address: 'Jl. Merdeka Selatan, Kota Malang' },
  { id: 2, name: 'Stasiun Kota Malang', area: 'Klojen', address: 'Jl. Trunojoyo No. 10, Kota Malang' },
  { id: 3, name: 'Ijen Boulevard', area: 'Klojen', address: 'Jl. Ijen, Kota Malang' }
];

function getData(key, defaults) {
  const saved = localStorage.getItem(key);
  if (!saved) {
    localStorage.setItem(key, JSON.stringify(defaults));
    return defaults;
  }
  try { return JSON.parse(saved); } catch { return defaults; }
}

function saveData(key, data) {
  localStorage.setItem(key, JSON.stringify(data));
}

function rupiah(value) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
}

function renderBikes() {
  const target = document.getElementById('bikeList');
  if (!target) return;
  const bikes = getData('bikes', defaultBikes);
  target.innerHTML = bikes.length ? bikes.map((bike, index) => `
    <tr>
      <td>${index + 1}</td>
      <td class="fw-semibold">${escapeHtml(bike.name)}</td>
      <td>${escapeHtml(bike.type)}</td>
      <td>${rupiah(bike.price)}</td>
      <td>${escapeHtml(bike.description || '-')}</td>
      <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteBike(${bike.id})">Hapus</button></td>
    </tr>`).join('') : '<tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data sepeda.</td></tr>';
}

function renderLocations() {
  const target = document.getElementById('locationList');
  if (!target) return;
  const locations = getData('locations', defaultLocations);
  target.innerHTML = locations.length ? locations.map((location, index) => `
    <tr>
      <td>${index + 1}</td>
      <td class="fw-semibold">${escapeHtml(location.name)}</td>
      <td>${escapeHtml(location.area)}</td>
      <td>${escapeHtml(location.address)}</td>
      <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteLocation(${location.id})">Hapus</button></td>
    </tr>`).join('') : '<tr><td colspan="5" class="text-center text-secondary py-4">Belum ada data lokasi.</td></tr>';
}

function deleteBike(id) {
  if (!confirm('Hapus data sepeda ini?')) return;
  const bikes = getData('bikes', defaultBikes).filter(bike => bike.id !== id);
  saveData('bikes', bikes);
  renderBikes();
}

function deleteLocation(id) {
  if (!confirm('Hapus data lokasi ini?')) return;
  const locations = getData('locations', defaultLocations).filter(location => location.id !== id);
  saveData('locations', locations);
  renderLocations();
}

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
}

document.addEventListener('DOMContentLoaded', () => {
  const bikes = getData('bikes', defaultBikes);
  const locations = getData('locations', defaultLocations);
  const totalBikes = document.getElementById('totalBikes');
  const totalLocations = document.getElementById('totalLocations');
  if (totalBikes) totalBikes.textContent = bikes.length;
  if (totalLocations) totalLocations.textContent = locations.length;
  renderBikes();
  renderLocations();

  const bikeForm = document.getElementById('bikeForm');
  if (bikeForm) bikeForm.addEventListener('submit', event => {
    event.preventDefault();
    const data = getData('bikes', defaultBikes);
    data.push({ id: Date.now(), name: document.getElementById('name').value.trim(), type: document.getElementById('type').value, price: Number(document.getElementById('price').value), description: document.getElementById('description').value.trim() });
    saveData('bikes', data);
    window.location.href = 'list.html';
  });

  const locationForm = document.getElementById('locationForm');
  if (locationForm) locationForm.addEventListener('submit', event => {
    event.preventDefault();
    const data = getData('locations', defaultLocations);
    data.push({ id: Date.now(), name: document.getElementById('locationName').value.trim(), area: document.getElementById('area').value.trim(), address: document.getElementById('address').value.trim() });
    saveData('locations', data);
    window.location.href = 'list.html';
  });
});
