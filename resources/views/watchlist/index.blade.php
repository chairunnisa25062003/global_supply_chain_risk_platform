@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">My Watchlist</h2>
        <p class="text-muted mb-0">Negara yang kamu pantau, tersimpan khusus untuk akun kamu.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="add-watchlist-form" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label small text-muted">Tambah Negara</label>
                <input type="text" id="add-country-input" class="form-control" placeholder="Contoh: Japan">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-star me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>

    <div id="watchlist-items" class="row g-3">
        {{-- Diisi JavaScript --}}
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('add-watchlist-form');
    const input = document.getElementById('add-country-input');
    const listBox = document.getElementById('watchlist-items');

    function riskBadgeClass(level) {
        return { low: 'risk-low', medium: 'risk-medium', high: 'risk-high' }[level] || 'risk-low';
    }

    function buildCard(item) {
        return `
            <div class="col-md-4" id="watchlist-item-${item.id}">
                <div class="card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="d-block">${item.country_name}</strong>
                            <span class="text-muted small risk-loading-${item.id}">Memuat risk score...</span>
                        </div>
                        <button class="btn btn-outline-primary btn-sm remove-btn" data-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    async function loadRiskFor(item) {
        try {
            const response = await fetch(`/api/risk?country=${encodeURIComponent(item.country_name)}`);
            const data = await response.json();
            const labelEl = document.querySelector(`.risk-loading-${item.id}`);
            if (labelEl) {
                labelEl.outerHTML = `<span class="risk-badge ${riskBadgeClass(data.level)}">${data.score} - ${data.level}</span>`;
            }
        } catch (error) {
            console.error(error);
        }
    }

    function attachRemoveHandlers() {
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const id = this.dataset.id;
                try {
                    await fetch(`/api/watchlist/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    });
                    document.getElementById(`watchlist-item-${id}`).remove();
                } catch (error) {
                    console.error(error);
                }
            });
        });
    }

    async function loadWatchlist() {
        listBox.innerHTML = `<div class="col-12 text-muted small">Memuat watchlist...</div>`;

        try {
            const response = await fetch('/api/watchlist');
            const items = await response.json();

            if (items.length === 0) {
                listBox.innerHTML = `<div class="col-12"><div class="card p-4 text-center text-muted">Belum ada negara di watchlist kamu.</div></div>`;
                return;
            }

            listBox.innerHTML = items.map(buildCard).join('');
            attachRemoveHandlers();

            items.forEach(loadRiskFor);

        } catch (error) {
            console.error(error);
            listBox.innerHTML = `<div class="col-12 text-muted small">Gagal memuat watchlist.</div>`;
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const countryName = input.value.trim();
        if (!countryName) return;

        try {
            await fetch('/api/watchlist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ country_name: countryName }),
            });

            input.value = '';
            loadWatchlist();
        } catch (error) {
            console.error(error);
        }
    });

    loadWatchlist();

});
</script>
@endpush
