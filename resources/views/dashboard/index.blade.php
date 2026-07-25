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
                <span class="text-muted small">Pelabuhan di Negara Watchlist</span>
                <h4 class="fw-bold mb-0" id="stat-port-count">--</h4>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3" id="weather-widget">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small d-block">Cuaca — <span id="weather-location">...</span></span>
                        <h4 class="fw-bold mb-0" id="weather-temp">--</h4>
                        <span class="text-muted small" id="weather-condition">Memuat...</span>
                    </div>
                    <i class="bi bi-cloud-sun fs-2 text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3" id="currency-widget">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small d-block">Kurs USD → <span id="currency-target-label">...</span></span>
                        <h4 class="fw-bold mb-0" id="currency-rate">--</h4>
                        <span class="text-muted small" id="currency-date">Memuat...</span>
                    </div>
                    <i class="bi bi-currency-exchange fs-2 text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{-- BARU: dipecah per negara, tampilkan nama pelabuhannya
                 langsung, bukan cuma angka gabungan --}}
            <div class="card p-3" id="port-status-widget" style="max-height: 220px; overflow-y: auto;">
                <span class="text-muted small d-block mb-2">Pelabuhan per Negara Watchlist</span>
                <div id="port-breakdown-list">
                    <span class="text-muted small">Memuat...</span>
                </div>
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

    const cardsContainer = document.getElementById('risk-cards');
    const tableBody = document.getElementById('risk-table-body');
    const newsFeed = document.getElementById('news-feed');
    const breakdownBox = document.getElementById('port-breakdown-list');

    function levelToClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }
    function levelToBorderClass(level) {
        return { low: 'risk-low-border', medium: 'risk-medium-border', high: 'risk-high-border' }[level] || 'risk-low-border';
    }
    function levelToLabel(level) {
        return { low: 'Low Risk', medium: 'Medium Risk', high: 'High Risk' }[level] || level;
    }
    function statusBadgeClass(status) {
        return { Normal: 'risk-low', Busy: 'risk-medium', Congested: 'risk-high' }[status] || 'risk-low';
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

    // Tampilan kosong, dipakai kalau user belum login ATAU watchlist-nya
    // masih kosong -- BUKAN diam-diam diganti negara acak/hardcode.
    function showEmptyWatchlistState(message) {
        cardsContainer.innerHTML = `
            <div class="col-12">
                <div class="card p-4 text-center text-muted">
                    ${message} Buka <a href="{{ route('watchlist') }}">halaman Watchlist</a> untuk menambahkan.
                </div>
            </div>
        `;
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Watchlist masih kosong.</td></tr>`;
        breakdownBox.innerHTML = `<span class="text-muted small">Watchlist masih kosong.</span>`;
        document.getElementById('stat-avg-risk').textContent = '-';
        document.getElementById('stat-high-risk').textContent = '-';
        document.getElementById('stat-port-count').textContent = '-';
        document.getElementById('weather-location').textContent = '-';
        document.getElementById('weather-condition').textContent = '-';
        document.getElementById('currency-target-label').textContent = '-';
        document.getElementById('currency-date').textContent = '-';
    }

    function loadRiskAndCards(watchlist) {
        Promise.all(watchlist.map(country => fetchRisk(country)))
            .then(results => {
                cardsContainer.innerHTML = results.map(buildCard).join('');
                tableBody.innerHTML = results.map(buildTableRow).join('');

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
    }

    function loadPortBreakdown(watchlist) {
        Promise.all(watchlist.map(country =>
            fetch(`/api/ports?country=${encodeURIComponent(country)}`).then(res => res.json())
                .then(ports => ({ country, ports }))
        ))
            .then(results => {
                const totalPorts = results.reduce((sum, r) => sum + r.ports.length, 0);
                document.getElementById('stat-port-count').textContent = totalPorts;

                breakdownBox.innerHTML = results.map(r => `
                    <div class="mb-2">
                        <strong class="small d-block">${r.country} (${r.ports.length})</strong>
                        ${r.ports.length === 0
                            ? '<span class="text-muted small">Belum ada data pelabuhan.</span>'
                            : r.ports.map(p => `
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="small">${p.name}</span>
                                    <span class="risk-badge ${statusBadgeClass(p.status)}" style="font-size:.65rem;">${p.status}</span>
                                </div>
                            `).join('')
                        }
                    </div>
                `).join('');
            })
            .catch(() => {
                document.getElementById('stat-port-count').textContent = '-';
                breakdownBox.innerHTML = '<span class="text-muted small">Gagal memuat data pelabuhan.</span>';
            });
    }

    function loadWeatherAndCurrency(primaryCountry) {
        fetch(`/api/countries?country=${encodeURIComponent(primaryCountry)}`)
            .then(res => res.json())
            .then(country => {
                const capital = country.capital && country.capital !== '-' ? country.capital : primaryCountry;
                const currencyCode = country.currency_code && country.currency_code !== '-' ? country.currency_code : 'EUR';

                fetch(`/api/weather?location=${encodeURIComponent(capital)}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('weather-location').textContent = data.location_name ?? capital;
                        document.getElementById('weather-temp').textContent = `${data.temperature ?? '-'}°C`;
                        document.getElementById('weather-condition').textContent = data.condition ?? '-';
                    })
                    .catch(() => {
                        document.getElementById('weather-condition').textContent = 'Gagal memuat.';
                    });

                document.getElementById('currency-target-label').textContent = currencyCode;
                fetch(`/api/currency?base=USD&target=${currencyCode}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('currency-rate').textContent =
                            `${data.rate.toLocaleString('en-US', { maximumFractionDigits: 4 })} ${currencyCode}`;
                        document.getElementById('currency-date').textContent = `Update: ${data.date}`;
                    })
                    .catch(() => {
                        document.getElementById('currency-date').textContent = 'Gagal memuat.';
                    });
            })
            .catch(() => {
                document.getElementById('weather-condition').textContent = 'Gagal memuat.';
                document.getElementById('currency-date').textContent = 'Gagal memuat.';
            });
    }

    function loadNews() {
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
    }

    // ================================================================
    // TITIK MASUK UTAMA: ambil Watchlist ASLI milik user yang sedang
    // login (dari tabel watchlists via /api/watchlist), BUKAN daftar
    // negara hardcode. Kalau belum login / watchlist kosong, tampilan
    // kosong yang jujur -- bukan diam-diam pakai Germany/China/Indonesia.
    // ================================================================
    fetch('/api/watchlist')
        .then(res => {
            if (!res.ok) throw new Error('unauthorized-or-failed');
            return res.json();
        })
        .then(items => {
            if (!items || items.length === 0) {
                showEmptyWatchlistState('Kamu belum menambahkan negara ke watchlist.');
                loadNews(); // berita tetap bisa tampil walau watchlist kosong
                return;
            }

            const watchlist = items.map(item => item.country_name);

            loadRiskAndCards(watchlist);
            loadPortBreakdown(watchlist);
            loadWeatherAndCurrency(watchlist[0]);
            loadNews();
        })
        .catch(() => {
            showEmptyWatchlistState('Login dan tambahkan negara ke watchlist untuk melihat ringkasan di sini.');
            loadNews();
        });

});
</script>
@endpush
