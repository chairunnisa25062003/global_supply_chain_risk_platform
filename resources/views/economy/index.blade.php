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
                <canvas id="gdp-chart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0" id="inflation-chart-title">Inflation Trend</div>
                <canvas id="inflation-chart" height="200"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('economy-form');
    const input = document.getElementById('economy-country-input');

    let gdpChart = null;
    let inflationChart = null;

    function formatGDPShort(value) {
        if (value >= 1e12) return (value / 1e12).toFixed(2) + 'T';
        if (value >= 1e9) return (value / 1e9).toFixed(2) + 'B';
        return value;
    }

    function renderGdpChart(history) {
        if (gdpChart) gdpChart.destroy();

        gdpChart = new Chart(document.getElementById('gdp-chart'), {
            type: 'line',
            data: {
                labels: history.map(item => item.year),
                datasets: [{
                    label: 'GDP (USD)',
                    data: history.map(item => item.value),
                    borderColor: '#4338CA',
                    backgroundColor: 'rgba(67, 56, 202, 0.08)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => formatGDPShort(value),
                        },
                    },
                },
            },
        });
    }

    function renderInflationChart(history) {
        if (inflationChart) inflationChart.destroy();

        inflationChart = new Chart(document.getElementById('inflation-chart'), {
            type: 'line',
            data: {
                labels: history.map(item => item.year),
                datasets: [{
                    label: 'Inflasi (%)',
                    data: history.map(item => item.value),
                    borderColor: '#C6362B',
                    backgroundColor: 'rgba(198, 54, 43, 0.08)',
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
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
