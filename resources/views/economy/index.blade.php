@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Economy Trend Dashboard</h2>
        <p class="text-muted mb-0">Tren GDP dan Inflasi 15 tahun terakhir, data dari World Bank.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="economy-form" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label small text-muted">Nama Negara</label>
                <input type="text" id="economy-country-input" class="form-control" value="Germany">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-graph-up me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0" id="gdp-chart-title">GDP Trend</div>
                <p class="text-muted small mb-3">Ukuran ekonomi negara per tahun. Garis naik = ekonomi tumbuh.</p>
                <canvas id="gdp-chart" height="220"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0" id="inflation-chart-title">Inflation Trend</div>
                <p class="text-muted small mb-3">Kenaikan harga barang secara umum tiap tahun (persen).</p>
                <canvas id="inflation-chart" height="220"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6B6B76';
Chart.defaults.plugins.tooltip.backgroundColor = '#1A1A2E';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { family: "'Sora', sans-serif", weight: '700', size: 13 };

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('economy-form');
    const input = document.getElementById('economy-country-input');

    let gdpChart = null;
    let inflationChart = null;

    function createGradient(ctx, colorStart) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, colorStart + '55');
        gradient.addColorStop(1, colorStart + '00');
        return gradient;
    }

    function formatGDPShort(value) {
        if (value >= 1e12) return (value / 1e12).toFixed(2) + 'T';
        if (value >= 1e9) return (value / 1e9).toFixed(2) + 'B';
        return value;
    }

    function renderGdpChart(history) {
        if (gdpChart) gdpChart.destroy();
        const ctx = document.getElementById('gdp-chart').getContext('2d');

        gdpChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: history.map(item => item.year),
                datasets: [{
                    label: 'GDP (USD)',
                    data: history.map(item => item.value),
                    borderColor: '#4338CA',
                    backgroundColor: createGradient(ctx, '#4338CA'),
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4338CA',
                    borderWidth: 2.5,
                }],
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: { callback: (value) => formatGDPShort(value) },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    function renderInflationChart(history) {
        if (inflationChart) inflationChart.destroy();
        const ctx = document.getElementById('inflation-chart').getContext('2d');

        inflationChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: history.map(item => item.year),
                datasets: [{
                    label: 'Inflasi (%)',
                    data: history.map(item => item.value),
                    borderColor: '#C6362B',
                    backgroundColor: createGradient(ctx, '#C6362B'),
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#C6362B',
                    borderWidth: 2.5,
                }],
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(0,0,0,0.06)' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    async function loadEconomy(countryName) {
        try {
            const response = await fetch(`/api/economy?country=${encodeURIComponent(countryName)}`);
            const data = await response.json();

            if (!response.ok) {
                alert(data.message);
                return;
            }

            document.getElementById('gdp-chart-title').textContent = `GDP Trend — ${data.name}`;
            document.getElementById('inflation-chart-title').textContent = `Inflation Trend — ${data.name}`;

            renderGdpChart(data.gdp_history);
            renderInflationChart(data.inflation_history);

        } catch (error) {
            console.error(error);
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const countryName = input.value.trim();
        if (countryName) loadEconomy(countryName);
    });

    loadEconomy(input.value);

});
</script>
@endpush
