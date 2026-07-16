@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Country Comparison Engine</h2>
        <p class="text-muted mb-0">Bandingkan GDP, inflasi, cuaca, kurs, dan risk score dua negara berdampingan.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="compare-form" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small text-muted">Negara 1</label>
                <input type="text" id="country1-input" class="form-control" value="Germany">
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted">Negara 2</label>
                <input type="text" id="country2-input" class="form-control" value="Australia">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-left-right me-1"></i> Bandingkan
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3" id="compare-result">
        <div class="col-12">
            <div class="card p-4 text-center text-muted">Masukkan 2 negara lalu klik "Bandingkan".</div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('compare-form');
    const input1 = document.getElementById('country1-input');
    const input2 = document.getElementById('country2-input');
    const resultBox = document.getElementById('compare-result');

    function riskBadgeClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }

    function formatNumber(value) {
        if (value === null || value === undefined) return '-';
        return new Intl.NumberFormat('en-US').format(value);
    }

    function formatGDP(value) {
        if (value === null || value === undefined) return '-';
        if (value >= 1e12) return '$' + (value / 1e12).toFixed(2) + 'T';
        if (value >= 1e9) return '$' + (value / 1e9).toFixed(2) + 'B';
        return '$' + formatNumber(value);
    }
    
    function comparisonIcon(valueA, valueB, higherIsBetter = true) {
        if (valueA === null || valueB === null || valueA === valueB) return '';
        const aIsBetter = higherIsBetter ? valueA > valueB : valueA < valueB;
        return aIsBetter
            ? '<i class="bi bi-arrow-up text-success"></i>'
            : '<i class="bi bi-arrow-down text-danger"></i>';
    }

    function buildCountryColumn(data, other, higherGdpBetter) {
        return `
            <div class="card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    ${data.flag ? `<img src="${data.flag}" style="width:48px;border-radius:4px;">` : ''}
                    <div>
                        <h4 class="fw-bold mb-0">${data.name}</h4>
                        <span class="text-muted small">${data.capital}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted small">Populasi</span>
                    <strong>${formatNumber(data.population)}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted small">GDP ${data.gdp_year ? '(' + data.gdp_year + ')' : ''}</span>
                    <strong>${formatGDP(data.gdp)} ${comparisonIcon(data.gdp, other.gdp, true)}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted small">Inflasi</span>
                    <strong>${data.inflation !== null ? data.inflation.toFixed(2) + '%' : '-'} ${comparisonIcon(data.inflation, other.inflation, false)}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted small">Cuaca</span>
                    <strong>${data.weather.condition}, ${data.weather.temperature ?? '-'}&deg;C</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted small">Kurs (${data.currency_code}, 30 hari)</span>
                    <strong>${data.currency_change_pct}%</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <span class="text-muted small">Risk Score</span>
                    <span class="risk-badge ${riskBadgeClass(data.risk_level)}">${data.risk_score} — ${data.risk_level}</span>
                </div>
            </div>
        `;
    }

    async function compareCountries(country1, country2) {
        resultBox.innerHTML = `<div class="col-12"><div class="card p-4 text-center text-muted">Membandingkan ${country1} vs ${country2}...</div></div>`;

        try {
            const response = await fetch(`/api/compare?country1=${encodeURIComponent(country1)}&country2=${encodeURIComponent(country2)}`);
            const data = await response.json();

            if (!response.ok) {
                resultBox.innerHTML = `<div class="col-12"><div class="card p-4 text-center text-muted">${data.message}</div></div>`;
                return;
            }

            resultBox.innerHTML = `
                <div class="col-md-6">${buildCountryColumn(data.country1, data.country2)}</div>
                <div class="col-md-6">${buildCountryColumn(data.country2, data.country1)}</div>
            `;

        } catch (error) {
            console.error(error);
            resultBox.innerHTML = `<div class="col-12"><div class="card p-4 text-center text-muted">Gagal memuat data perbandingan.</div></div>`;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const c1 = input1.value.trim();
        const c2 = input2.value.trim();
        if (c1 && c2) compareCountries(c1, c2);
    });

    compareCountries(input1.value, input2.value);

});
</script>
@endpush
