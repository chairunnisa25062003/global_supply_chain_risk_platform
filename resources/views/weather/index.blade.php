@extends('layouts.app')

@push('styles')
{{-- Leaflet.js diambil dari CDN, tidak perlu npm install --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #weather-map {
        height: 480px;
        width: 100%;
        border-radius: 14px;
    }
</style>
@endpush

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Global Weather Monitoring</h2>
        <p class="text-muted mb-0">Pantau cuaca real-time di lokasi manapun lewat peta interaktif.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="weather-search-form" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label for="location-input" class="form-label small text-muted">Nama Kota / Negara</label>
                <input type="text" id="location-input" class="form-control"
                       placeholder="Contoh: Jakarta, Rotterdam, Shanghai" value="Jakarta">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-cloud-sun me-1"></i> Cek Cuaca
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-2">
                <div id="weather-map"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 h-100" id="weather-detail">
                <span class="text-muted small">Cari lokasi untuk melihat detail cuaca.</span>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
{{-- Leaflet JS, juga dari CDN --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('weather-search-form');
    const input = document.getElementById('location-input');
    const detailBox = document.getElementById('weather-detail');

    const map = L.map('weather-map').setView([20, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    let currentMarker = null; 


    function markerColorFor(condition, isStorm) {
        if (isStorm) return '#C6362B';               
        if (condition === 'Rain' || condition === 'Rain Showers' || condition === 'Thunderstorm') return '#D48A16'; // kuning - waspada
        return '#2F9E5B';                            
    }

    function buildDetailHTML(data) {
        const stormBadge = data.is_storm
            ? `<span class="risk-badge risk-high mt-2 d-inline-block">Storm Warning</span>`
            : '';

        return `
            <h4 class="fw-bold mb-1">${data.location_name}</h4>
            <span class="text-muted small d-block mb-3">${data.country}</span>

            <div class="mb-2">
                <span class="text-muted small d-block">Suhu</span>
                <strong style="font-size:1.4rem;">${data.temperature ?? '-'}&deg;C</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kondisi</span>
                <strong>${data.condition}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Curah Hujan</span>
                <strong>${data.precipitation ?? 0} mm</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kecepatan Angin</span>
                <strong>${data.wind_speed ?? '-'} km/h</strong>
            </div>
            ${stormBadge}
        `;
    }

    async function searchWeather(location) {
        detailBox.innerHTML = `<span class="text-muted small">Mencari cuaca untuk "${location}"...</span>`;

        try {
            const response = await fetch(`/api/weather?location=${encodeURIComponent(location)}`);
            const data = await response.json();

            if (!response.ok) {
                detailBox.innerHTML = `<span class="text-muted small">${data.message}</span>`;
                return;
            }

    
            map.setView([data.latitude, data.longitude], 8);

        
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            const color = markerColorFor(data.condition, data.is_storm);

            currentMarker = L.circleMarker([data.latitude, data.longitude], {
                radius: 12,
                fillColor: color,
                color: '#1A1A2E',
                weight: 2,
                fillOpacity: 0.9,
            }).addTo(map);

            currentMarker.bindPopup(`<strong>${data.location_name}</strong><br>${data.condition}, ${data.temperature}&deg;C`).openPopup();

            detailBox.innerHTML = buildDetailHTML(data);

        } catch (error) {
            console.error(error);
            detailBox.innerHTML = `<span class="text-muted small">Gagal memuat data cuaca. Coba lagi.</span>`;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const location = input.value.trim();
        if (location) {
            searchWeather(location);
        }
    });

    searchWeather(input.value);

});
</script>
@endpush
