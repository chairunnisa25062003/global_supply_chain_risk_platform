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
                {{-- Keterangan simpel, muncul SELALU, biar tidak perlu nebak cara bacanya --}}
                <p class="text-muted small mb-3">
                    Grafik ini menunjukkan skor 4 faktor (0–100) yang membentuk Risk Score.
                    Makin jauh titik dari tengah, makin tinggi skor faktor itu.
                </p>
                <canvas id="risk-breakdown-chart" height="200"></canvas>

                {{-- Daftar angka pendamping — supaya tidak perlu menebak dari bentuk radar-nya --}}
                <div class="row g-2 mt-3 pt-3 border-top" id="breakdown-list"></div>
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

    const form = document.getElementById('risk-search-form');
    const input = document.getElementById('risk-country-input');
    const summaryBox = document.getElementById('risk-summary');
    const breakdownList = document.getElementById('breakdown-list');

    let breakdownChart = null;

    const FACTOR_LABELS = {
        weather: 'Weather',
        inflation: 'Inflation',
        news: 'News Sentiment',
        currency: 'Currency',
    };
    const FACTOR_COLORS = {
        weather: '#6BB1AD',
        inflation: '#D48A16',
        news: '#C6362B',
        currency: '#4338CA',
    };

    function riskBadgeClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }

    function renderBreakdownChart(breakdown) {
        if (breakdownChart) breakdownChart.destroy();

        breakdownChart = new Chart(document.getElementById('risk-breakdown-chart'), {
            type: 'radar',
            data: {
                labels: ['Weather', 'Inflation', 'News Sentiment', 'Currency'],
                datasets: [{
                    label: 'Skor Sub-faktor (0-100)',
                    data: [breakdown.weather, breakdown.inflation, breakdown.news, breakdown.currency],
                    backgroundColor: 'rgba(67, 56, 202, 0.15)',
                    borderColor: '#4338CA',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#4338CA',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        min: 0,
                        max: 100,
                        ticks: { stepSize: 25, backdropColor: 'transparent' },
                        grid: { color: 'rgba(0,0,0,0.08)' },
                        angleLines: { color: 'rgba(0,0,0,0.08)' },
                        pointLabels: { font: { size: 13, weight: '600' } },
                    },
                },
            },
        });
    }


    function renderBreakdownList(breakdown) {
        breakdownList.innerHTML = Object.keys(FACTOR_LABELS).map(key => `
            <div class="col-6 col-md-3">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px; height:10px; border-radius:50%; background:${FACTOR_COLORS[key]}; display:inline-block;"></span>
                    <span class="text-muted small">${FACTOR_LABELS[key]}</span>
                </div>
                <strong style="font-size:1.1rem;">${breakdown[key]}</strong>
                <span class="text-muted small">/100</span>
            </div>
        `).join('');
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
                <p class="text-muted small mt-3 mb-0">
                    Skor 0–100. Semakin tinggi, semakin berisiko bagi rantai pasok.
                </p>
            `;

            renderBreakdownChart(data.breakdown);
            renderBreakdownList(data.breakdown);

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
