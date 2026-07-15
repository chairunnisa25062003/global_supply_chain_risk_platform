@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Country Dashboard</h2>
        <p class="text-muted mb-0">Cari data ekonomi & identitas negara secara real-time.</p>
    </div>

    {{-- Form pencarian --}}
    <div class="card p-3 mb-4">
        <form id="country-search-form" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label for="country-input" class="form-label small text-muted">Nama Negara</label>
                <input type="text" id="country-input" class="form-control"
                       placeholder="Contoh: Germany, Indonesia, Japan" value="Germany">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Hasil pencarian akan disuntikkan JavaScript ke sini --}}
    <div id="country-result">
        <div class="card p-4 text-center text-muted">
            Masukkan nama negara lalu klik "Cari" untuk melihat datanya.
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('country-search-form');
    const input = document.getElementById('country-input');
    const resultBox = document.getElementById('country-result');

    // Format angka besar (population, GDP) supaya gampang dibaca,
    // misal 83000000 jadi "83,000,000"
    function formatNumber(value) {
        if (value === null || value === undefined) return '-';
        return new Intl.NumberFormat('en-US').format(value);
    }

    // GDP biasanya angkanya sangat besar (triliun), kita singkat jadi "$4.2T"
    function formatGDP(value) {
        if (value === null || value === undefined) return '-';
        if (value >= 1e12) return '$' + (value / 1e12).toFixed(2) + 'T';
        if (value >= 1e9) return '$' + (value / 1e9).toFixed(2) + 'B';
        if (value >= 1e6) return '$' + (value / 1e6).toFixed(2) + 'M';
        return '$' + formatNumber(value);
    }

    function buildProfileCard(data) {
        return `
            <div class="card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    ${data.flag ? `<img src="${data.flag}" alt="${data.name}" style="width:56px; height:auto; border-radius:4px;">` : ''}
                    <div>
                        <h3 class="fw-bold mb-0">${data.name}</h3>
                        <span class="text-muted small">${data.official_name}</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Ibu Kota</span>
                        <strong>${data.capital}</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Wilayah</span>
                        <strong>${data.region} — ${data.subregion}</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Populasi</span>
                        <strong>${formatNumber(data.population)}</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Mata Uang</span>
                        <strong>${data.currency_name} (${data.currency_code})</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">GDP ${data.gdp_year ? '(' + data.gdp_year + ')' : ''}</span>
                        <strong>${formatGDP(data.gdp)}</strong>
                    </div>
                    <div class="col-md-3 col-6">
                        <span class="text-muted small d-block">Inflasi ${data.inflation_year ? '(' + data.inflation_year + ')' : ''}</span>
                        <strong>${data.inflation !== null ? data.inflation.toFixed(2) + '%' : '-'}</strong>
                    </div>
                    <div class="col-md-6 col-12">
                        <span class="text-muted small d-block">Bahasa</span>
                        <strong>${data.languages}</strong>
                    </div>
                </div>
            </div>
        `;
    }

    async function searchCountry(countryName) {
        resultBox.innerHTML = `<div class="card p-4 text-center text-muted">Mencari data "${countryName}"...</div>`;

        try {
            const response = await fetch(`/api/countries?country=${encodeURIComponent(countryName)}`);
            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<div class="card p-4 text-center text-muted">${data.message}</div>`;
                return;
            }

            resultBox.innerHTML = buildProfileCard(data);

        } catch (error) {
            console.error(error);
            resultBox.innerHTML = `<div class="card p-4 text-center text-muted">Gagal memuat data. Coba lagi.</div>`;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const countryName = input.value.trim();
        if (countryName) {
            searchCountry(countryName);
        }
    });

    // Langsung tampilkan Germany begitu halaman dibuka, biar tidak kosong
    searchCountry(input.value);

});
</script>
@endpush
