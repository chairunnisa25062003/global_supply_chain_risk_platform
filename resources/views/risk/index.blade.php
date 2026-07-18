@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Risk Scoring Engine</h2>
        <p class="text-muted mb-0">Cari skor risiko 1 negara, lengkap dengan rincian tiap faktor penyusunnya.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="risk-search-form" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label small text-muted">Nama Negara</label>
                <input type="text" id="risk-country-input" class="form-control" value="Germany">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Cek Risk Score
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card p-4 text-center" id="risk-summary">
                <span class="text-muted small">Memuat...</span>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0">Rincian Faktor Risiko</div>
                <canvas id="risk-breakdown-chart" height="180"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('risk-search-form');
    const input = document.getElementById('risk-country-input');
    const summaryBox = document.getElementById('risk-summary');

    let breakdownChart = null;

    function riskBadgeClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }

    function renderBreakdownChart(breakdown) {
        if (breakdownChart) breakdownChart.destroy();

        breakdownChart = new Chart(document.getElementById('risk-breakdown-chart'), {
            type: 'bar',
            data: {
                labels: ['Weather', 'Inflation', 'News Sentiment', 'Currency'],
                datasets: [{
                    label: 'Skor Sub-faktor (0-100)',
                    data: [breakdown.weather, breakdown.inflation, breakdown.news, breakdown.currency],
                    backgroundColor: ['#6BB1AD', '#D48A16', '#C6362B', '#4338CA'],
                    borderRadius: 6,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { max: 100 } },
            },
        });
    }

    async function loadRisk(countryName) {
        summaryBox.innerHTML = `<span class="text-muted small">Menghitung risk score "${countryName}"...</span>`;

        try {
            const response = await fetch(`/api/risk?country=${encodeURIComponent(countryName)}`);
            const data = await response.json();

            summaryBox.innerHTML = `
                <span class="text-muted small d-block mb-1">${countryName}</span>
                <h1 class="fw-bold mb-2">${data.score}</h1>
                <span class="risk-badge ${riskBadgeClass(data.level)}">${data.level}</span>
            `;

            renderBreakdownChart(data.breakdown);

        } catch (error) {
            console.error(error);
            summaryBox.innerHTML = `<span class="text-muted small">Gagal memuat data.</span>`;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const countryName = input.value.trim();
        if (countryName) loadRisk(countryName);
    });

    loadRisk(input.value);

});
</script>
@endpush
