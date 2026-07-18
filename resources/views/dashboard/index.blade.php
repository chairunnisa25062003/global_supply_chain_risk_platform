@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1">Welcome Back</h2>
            <p class="text-muted mb-0">Monitor global supply chain risk from one dashboard.</p>
        </div>
        <a href="{{ route('watchlist') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Country to Watchlist
        </a>
    </div>

    {{-- ================================================================
         BARIS STATISTIK RINGKAS (baru) — biar dashboard tidak sepi
         dan lebih terasa "Business Intelligence" sesuai spesifikasi.
         Semua angka di sini dihitung dari data ASLI (bukan hardcode).
    ================================================================ --}}
    <div class="row g-3 mb-4" id="stat-row">
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Avg Risk Score</span>
                <h4 class="fw-bold mb-0" id="stat-avg-risk">--</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Negara High Risk</span>
                <h4 class="fw-bold mb-0" id="stat-high-risk">--</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Berita Dianalisis</span>
                <h4 class="fw-bold mb-0" id="stat-news-count">--</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Pelabuhan Terdaftar</span>
                <h4 class="fw-bold mb-0" id="stat-port-count">--</h4>
            </div>
        </div>
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

    <div class="row g-3">
        <div class="col-lg-8">
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
                    <tbody id="risk-table-body">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Widget baru: mini feed berita terbaru --}}
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <div class="card-header border-0 px-0 pt-0">Latest News</div>
                <div id="news-feed">
                    <span class="text-muted small">Memuat berita...</span>
                </div>
                <a href="{{ route('news') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                    Lihat semua berita
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const watchlist = ['Germany', 'China', 'Indonesia'];

    const cardsContainer = document.getElementById('risk-cards');
    const tableBody = document.getElementById('risk-table-body');
    const newsFeed = document.getElementById('news-feed');

    function levelToClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }
    function levelToBorderClass(level) {
        return { low: 'risk-low-border', medium: 'risk-medium-border', high: 'risk-high-border' }[level] || 'risk-low-border';
    }
    function levelToLabel(level) {
        return { low: 'Low Risk', medium: 'Medium Risk', high: 'High Risk' }[level] || level;
    }

    async function fetchRisk(country) {
        const response = await fetch(`/api/risk?country=${encodeURIComponent(country)}`);
        if (!response.ok) throw new Error(`Gagal ambil data untuk ${country}`);
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

    // --- Muat risk score watchlist + hitung statistik ringkas ---
    Promise.all(watchlist.map(country => fetchRisk(country)))
        .then(results => {
            cardsContainer.innerHTML = results.map(buildCard).join('');
            tableBody.innerHTML = results.map(buildTableRow).join('');

            // Statistik dihitung LANGSUNG dari hasil di atas, bukan angka baru
            const avgScore = Math.round(results.reduce((sum, r) => sum + r.score, 0) / results.length);
            const highRiskCount = results.filter(r => r.level === 'high').length;

            document.getElementById('stat-avg-risk').textContent = avgScore;
            document.getElementById('stat-high-risk').textContent = highRiskCount;
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
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Gagal memuat data.</td></tr>`;
        });

    // --- Statistik: jumlah pelabuhan terdaftar ---
    fetch('/api/ports')
        .then(res => res.json())
        .then(ports => {
            document.getElementById('stat-port-count').textContent = ports.length;
        })
        .catch(() => {
            document.getElementById('stat-port-count').textContent = '-';
        });

    // --- Widget: mini feed berita + statistik jumlah berita ---
    fetch('/api/news?keyword=supply chain logistics')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-news-count').textContent = data.total;

            const topArticles = data.articles.slice(0, 4);

            if (topArticles.length === 0) {
                newsFeed.innerHTML = `<span class="text-muted small">Belum ada berita (cek GNEWS_API_KEY).</span>`;
                return;
            }

            newsFeed.innerHTML = topArticles.map(article => `
                <a href="${article.url}" target="_blank" rel="noopener" class="d-block text-decoration-none mb-3 pb-3 border-bottom">
                    <strong class="d-block small text-dark" style="line-height:1.3;">${article.title}</strong>
                    <span class="text-muted" style="font-size:.75rem;">${article.source}</span>
                </a>
            `).join('');
        })
        .catch(() => {
            document.getElementById('stat-news-count').textContent = '-';
            newsFeed.innerHTML = `<span class="text-muted small">Gagal memuat berita.</span>`;
        });

});
</script>
@endpush
