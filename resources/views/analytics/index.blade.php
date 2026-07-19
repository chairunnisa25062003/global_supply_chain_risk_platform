@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Risk Analytics</h2>
        <p class="text-muted mb-0">Bandingkan risk score beberapa negara sekaligus dalam satu grafik.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="analytics-form" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label small text-muted">Daftar Negara (pisahkan dengan koma)</label>
                <input type="text" id="analytics-countries-input" class="form-control"
                       value="Germany, China, Indonesia, Brazil, Nigeria">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-bar-chart me-1"></i> Analisis
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0">Perbandingan Risk Score</div>
                <p class="text-muted small mb-3">Batang lebih tinggi = risiko lebih besar. Bandingkan tinggi antar negara.</p>
                <canvas id="analytics-chart" height="140"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0">Distribusi Level Risiko</div>
                <p class="text-muted small mb-3">Dari negara yang dibandingkan, berapa yang masuk kategori Low/Medium/High.</p>
                <canvas id="distribution-chart" height="200"></canvas>
                <div class="mt-3" id="distribution-legend"></div>
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

    const form = document.getElementById('analytics-form');
    const input = document.getElementById('analytics-countries-input');
    let barChart = null;
    let doughnutChart = null;

    function colorForLevel(level) {
        return { low: '#2F9E5B', medium: '#D48A16', high: '#C6362B' }[level] || '#6B6B76';
    }

    async function fetchRisk(country) {
        const response = await fetch(`/api/risk?country=${encodeURIComponent(country.trim())}`);
        return response.json();
    }

    function renderBarChart(results) {
        if (barChart) barChart.destroy();

        barChart = new Chart(document.getElementById('analytics-chart'), {
            type: 'bar',
            data: {
                labels: results.map(r => r.country),
                datasets: [{
                    label: 'Risk Score',
                    data: results.map(r => r.score),
                    backgroundColor: results.map(r => colorForLevel(r.level)),
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 56,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { max: 100, grid: { color: 'rgba(0,0,0,0.06)' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    function renderDistributionChart(results) {
        if (doughnutChart) doughnutChart.destroy();

        const counts = { low: 0, medium: 0, high: 0 };
        results.forEach(r => counts[r.level] = (counts[r.level] || 0) + 1);

        doughnutChart = new Chart(document.getElementById('distribution-chart'), {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                datasets: [{
                    data: [counts.low, counts.medium, counts.high],
                    backgroundColor: ['#2F9E5B', '#D48A16', '#C6362B'],
                    borderWidth: 3,
                    borderColor: '#FBFAF8',
                }],
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { display: false } },
            },
        });

        document.getElementById('distribution-legend').innerHTML = `
            <div class="d-flex justify-content-between small mb-1">
                <span><span class="risk-badge risk-low">●</span> Low</span><strong>${counts.low}</strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span><span class="risk-badge risk-medium">●</span> Medium</span><strong>${counts.medium}</strong>
            </div>
            <div class="d-flex justify-content-between small">
                <span><span class="risk-badge risk-high">●</span> High</span><strong>${counts.high}</strong>
            </div>
        `;
    }

    async function loadAnalytics(countryListText) {
        const countries = countryListText.split(',').map(c => c.trim()).filter(Boolean);
        const results = await Promise.all(countries.map(fetchRisk));

        renderBarChart(results);
        renderDistributionChart(results);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        loadAnalytics(input.value);
    });

    loadAnalytics(input.value);

});
</script>
@endpush
