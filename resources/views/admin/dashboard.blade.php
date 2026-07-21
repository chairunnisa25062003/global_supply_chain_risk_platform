@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Admin Dashboard</h2>
        <p class="text-muted mb-0">Kelola user, dataset pelabuhan, dan artikel analisis.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Total User</span>
                <h3 class="fw-bold my-1">{{ $stats['total_users'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Total Pelabuhan</span>
                <h3 class="fw-bold my-1">{{ $stats['total_ports'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Total Artikel</span>
                <h3 class="fw-bold my-1">{{ $stats['total_articles'] }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card p-3">
                <span class="text-muted small">Total Watchlist</span>
                <h3 class="fw-bold my-1">{{ $stats['total_watchlists'] }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card p-4">
                    <i class="bi bi-people fs-3 mb-2"></i>
                    <h5 class="fw-bold mb-1">Kelola User</h5>
                    <span class="text-muted small">Ubah role, hapus akun</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ports') }}" class="text-decoration-none">
                <div class="card p-4">
                    <i class="bi bi-geo-alt fs-3 mb-2"></i>
                    <h5 class="fw-bold mb-1">Kelola Pelabuhan</h5>
                    <span class="text-muted small">Tambah / hapus data pelabuhan</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.articles') }}" class="text-decoration-none">
                <div class="card p-4">
                    <i class="bi bi-file-text fs-3 mb-2"></i>
                    <h5 class="fw-bold mb-1">Kelola Artikel</h5>
                    <span class="text-muted small">Publikasi artikel analisis</span>
                </div>
            </a>
        </div>
    </div>

    {{-- BARU: Activity Log, bukti tabel activity_logs beneran terpakai --}}
    <div class="card p-4">
        <div class="card-header border-0 px-0 pt-0">Aktivitas Admin Terbaru</div>
        @forelse ($recentActivity as $log)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <strong class="d-block small">{{ $log->description ?? $log->action }}</strong>
                    <span class="text-muted small">oleh {{ $log->user->name ?? 'Unknown' }}</span>
                </div>
                <span class="text-muted small">{{ $log->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <span class="text-muted small">Belum ada aktivitas tercatat.</span>
        @endforelse
    </div>

@endsection
