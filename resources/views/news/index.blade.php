@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">News Intelligence</h2>
        <p class="text-muted mb-0">Berita logistik, perdagangan, dan ekonomi global, dianalisis otomatis pakai Sentiment Analyzer.</p>
    </div>

    {{-- Tombol kategori cepat, sesuai spesifikasi: Logistics, Trade, Shipping, Economy --}}
    <div class="d-flex flex-wrap gap-2 mb-4" id="category-buttons">
        <button class="btn btn-outline-primary btn-sm category-btn" data-keyword="logistics supply chain">
            <i class="bi bi-truck me-1"></i> Logistics
        </button>
        <button class="btn btn-outline-primary btn-sm category-btn" data-keyword="global trade">
            <i class="bi bi-globe me-1"></i> Trade
        </button>
        <button class="btn btn-outline-primary btn-sm category-btn" data-keyword="shipping container">
            <i class="bi bi-truck me-1"></i> Shipping
        </button>
        <button class="btn btn-outline-primary btn-sm category-btn" data-keyword="global economy">
            <i class="bi bi-graph-up me-1"></i> Economy
        </button>
    </div>

    <div class="card p-3 mb-4">
        <form id="news-search-form" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label small text-muted">Kata Kunci Pencarian</label>
                <input type="text" id="news-search-input" class="form-control" placeholder="Contoh: port congestion, inflation">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="text-muted small" id="news-summary">Memuat berita...</span>
    </div>

    <div class="row g-3" id="news-list">
        {{-- Diisi JavaScript --}}
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchForm = document.getElementById('news-search-form');
    const searchInput = document.getElementById('news-search-input');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const newsList = document.getElementById('news-list');
    const newsSummary = document.getElementById('news-summary');

    function sentimentBadgeClass(sentiment) {
        return {
            Positive: 'risk-low',
            Negative: 'risk-high',
            Neutral: 'sentiment-neutral',
        }[sentiment] || 'sentiment-neutral';
    }

    function formatDate(isoDate) {
        if (!isoDate) return '-';
        return new Date(isoDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function buildNewsCard(article) {
        return `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 p-3 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-muted small">${article.source}</span>
                        <span class="risk-badge ${sentimentBadgeClass(article.sentiment)}">${article.sentiment}</span>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-size:1rem;">${article.title}</h5>
                    <p class="text-muted small flex-grow-1">${article.description ?? ''}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-muted small">${formatDate(article.published_at)}</span>
                        <a href="${article.url}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                            Baca <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    async function loadNews(keyword) {
        newsSummary.textContent = `Mencari berita "${keyword}"...`;
        newsList.innerHTML = '';

        try {
            const response = await fetch(`/api/news?keyword=${encodeURIComponent(keyword)}`);
            const data = await response.json();

            if (data.articles.length === 0) {
                newsSummary.textContent = 'Tidak ada berita ditemukan. Pastikan GNEWS_API_KEY sudah diisi di .env.';
                return;
            }

            newsSummary.textContent = `Menampilkan ${data.total} berita untuk "${data.keyword}"`;
            newsList.innerHTML = data.articles.map(buildNewsCard).join('');

        } catch (error) {
            console.error(error);
            newsSummary.textContent = 'Gagal memuat berita.';
        }
    }

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const keyword = searchInput.value.trim();
        if (keyword) loadNews(keyword);
    });

    categoryButtons.forEach(button => {
        button.addEventListener('click', function () {
            loadNews(this.dataset.keyword);
        });
    });

    loadNews('supply chain logistics');

});
</script>
@endpush
