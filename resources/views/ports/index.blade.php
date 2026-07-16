@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #port-map {
        height: 480px;
        width: 100%;
        border-radius: 14px;
    }
</style>
@endpush

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Port Location Dashboard</h2>
        <p class="text-muted mb-0">Lokasi pelabuhan utama dunia. Data tersimpan lokal (bukan API real-time).</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="port-search-form" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted">Cari Nama Pelabuhan</label>
                <input type="text" id="port-search-input" class="form-control" placeholder="Contoh: Singapore, Rotterdam">
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted">Cari Negara</label>
                <input type="text" id="port-country-input" class="form-control" placeholder="Contoh: China, Germany">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-2">
                <div id="port-map"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-3" style="max-height: 500px; overflow-y: auto;">
                <div class="card-header border-0 px-0 pt-0" id="port-list-header">Daftar Pelabuhan</div>
                <div id="port-list"></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('port-search-form');
    const searchInput = document.getElementById('port-search-input');
    const countryInput = document.getElementById('port-country-input');
    const listBox = document.getElementById('port-list');
    const listHeader = document.getElementById('port-list-header');

    const map = L.map('port-map').setView([20, 20], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    let markers = []; 

    function clearMarkers() {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];
    }

    function buildListItem(port) {
        return `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom port-item"
                 data-lat="${port.latitude}" data-lon="${port.longitude}" style="cursor:pointer;">
                <div>
                    <strong class="d-block">${port.name}</strong>
                    <span class="text-muted small">${port.country} ${port.unlocode ? '· ' + port.unlocode : ''}</span>
                </div>
                <span class="risk-badge risk-low">${port.harbor_size ?? '-'}</span>
            </div>
        `;
    }

    async function loadPorts(search = '', country = '') {
        listBox.innerHTML = `<span class="text-muted small">Memuat data pelabuhan...</span>`;

        try {
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (country) params.append('country', country);

            const response = await fetch(`/api/ports?${params.toString()}`);
            const ports = await response.json();

            clearMarkers();

            if (ports.length === 0) {
                listBox.innerHTML = `<span class="text-muted small">Tidak ada pelabuhan yang cocok.</span>`;
                listHeader.textContent = 'Daftar Pelabuhan (0)';
                return;
            }

            listHeader.textContent = `Daftar Pelabuhan (${ports.length})`;

        
            ports.forEach(port => {
                const marker = L.circleMarker([port.latitude, port.longitude], {
                    radius: 7,
                    fillColor: '#4338CA',
                    color: '#1A1A2E',
                    weight: 1.5,
                    fillOpacity: 0.85,
                }).addTo(map);

                marker.bindPopup(`<strong>${port.name}</strong><br>${port.country}`);
                markers.push(marker);
            });

            listBox.innerHTML = ports.map(buildListItem).join('');

            document.querySelectorAll('.port-item').forEach(item => {
                item.addEventListener('click', function () {
                    const lat = parseFloat(this.dataset.lat);
                    const lon = parseFloat(this.dataset.lon);
                    map.setView([lat, lon], 9);
                });
            });

            if (ports.length === 1) {
                map.setView([ports[0].latitude, ports[0].longitude], 9);
            } else {
                map.setView([20, 20], 2);
            }

        } catch (error) {
            console.error(error);
            listBox.innerHTML = `<span class="text-muted small">Gagal memuat data pelabuhan.</span>`;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        loadPorts(searchInput.value.trim(), countryInput.value.trim());
    });


    loadPorts();

});
</script>
@endpush
