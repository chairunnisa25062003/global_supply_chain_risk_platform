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

    <div class="card p-4">
        <div class="card-header border-0 px-0 pt-0">Perbandingan Risk Score</div>
        <canvas id="analytics-chart" height="120"></canvas>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('analytics-form');
    const input = document.getElementById('analytics-countries-input');
    let chart = null;

    // Warna bar disesuaikan level risiko tiap negara
    function colorForLevel(level) {
        return { low: '#2F9E5B', medium: '#D48A16', high: '#C6362B' }[level] || '#6B6B76';
    }

    async function fetchRisk(country) {
        const response = await fetch(`/api/risk?country=${encodeURIComponent(country.trim())}`);
        return response.json();
    }

    function renderChart(results) {
        if (chart) chart.destroy();

        chart = new Chart(document.getElementById('analytics-chart'), {
            type: 'bar',
            data: {
                labels: results.map(r => r.country),
                datasets: [{
                    label: 'Risk Score',
                    data: results.map(r => r.score),
                    backgroundColor: results.map(r => colorForLevel(r.level)),
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { max: 100 } },
            },
        });
    }

    async function loadAnalytics(countryListText) {
        const countries = countryListText.split(',').map(c => c.trim()).filter(Boolean);

       
        const results = await Promise.all(countries.map(fetchRisk));

        renderChart(results);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        loadAnalytics(input.value);
    });

    loadAnalytics(input.value);

});
</script>
@endpush
