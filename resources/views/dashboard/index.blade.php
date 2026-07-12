@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1">Welcome Back</h2>
            <p class="text-muted mb-0">Monitor global supply chain risk from one dashboard.</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Country to Watchlist
        </button>
    </div>

    <div class="row g-3 mb-4" id="risk-cards">
        <div class="col-md-4" data-placeholder>
            <div class="card p-3 h-100">
                <span class="text-muted small">Loading...</span>
                <h3 class="fw-bold my-1">--</h3>
            </div>
        </div>
        <div class="col-md-4" data-placeholder>
            <div class="card p-3 h-100">
                <span class="text-muted small">Loading...</span>
                <h3 class="fw-bold my-1">--</h3>
            </div>
        </div>
        <div class="col-md-4" data-placeholder>
            <div class="card p-3 h-100">
                <span class="text-muted small">Loading...</span>
                <h3 class="fw-bold my-1">--</h3>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="card-header border-0 px-0 pt-0">Watchlist</div>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Risk Score</th>
                    <th>Status</th>
                    <th>News Sentiment</th>
                </tr>
            </thead>
            {{-- id ini yang jadi target JavaScript buat nyuntik baris data --}}
            <tbody id="risk-table-body">
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Loading data...</td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {


    const watchlist = ['Germany', 'China', 'Indonesia'];

    const cardsContainer = document.getElementById('risk-cards');
    const tableBody = document.getElementById('risk-table-body');

    function levelToClass(level) {
        return {
            low: 'risk-low',
            medium: 'risk-medium',
            high: 'risk-high',
        }[level] || 'risk-low';
    }

    function levelToBorderClass(level) {
        return {
            low: 'risk-low-border',
            medium: 'risk-medium-border',
            high: 'risk-high-border',
        }[level] || 'risk-low-border';
    }

    function levelToLabel(level) {
        return {
            low: 'Low Risk',
            medium: 'Medium Risk',
            high: 'High Risk',
        }[level] || level;
    }

    async function fetchRisk(country) {
        const response = await fetch(`/api/risk?country=${encodeURIComponent(country)}`);

        if (!response.ok) {
            throw new Error(`Gagal ambil data untuk ${country}`);
        }

        return response.json();
    }

    function buildCard(data) {
        return `
            <div class="col-md-4">
                <div class="card ${levelToBorderClass(data.level)} p-3 h-100">
                    <span class="text-muted small">${data.country}</span>
                    <h3 class="fw-bold my-1">${data.score}</h3>
                    <span class="risk-badge ${levelToClass(data.level)}">${levelToLabel(data.level)}</span>
                </div>
            </div>
        `;
    }

    function buildTableRow(data) {
        return `
            <tr>
                <td>${data.country}</td>
                <td>${data.score}</td>
                <td><span class="risk-badge ${levelToClass(data.level)}">${data.level}</span></td>
                <td>${data.sentiment.negative_pct}% negative (${data.sentiment.total_articles} artikel)</td>
            </tr>
        `;
    }


    Promise.all(watchlist.map(country => fetchRisk(country)))
        .then(results => {

            cardsContainer.innerHTML = results.map(buildCard).join('');
            tableBody.innerHTML = results.map(buildTableRow).join('');
        })
        .catch(error => {
            console.error(error);
            cardsContainer.innerHTML = `
                <div class="col-12">
                    <div class="card p-3 text-center text-muted">
                        Gagal memuat data risiko. Pastikan server Laravel & route /api/risk aktif.
                    </div>
                </div>
            `;
            tableBody.innerHTML = `
                <tr><td colspan="4" class="text-center text-muted py-3">Gagal memuat data.</td></tr>
            `;
        });

});
</script>
@endpush
