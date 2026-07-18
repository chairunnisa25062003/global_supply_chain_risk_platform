@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Artikel Analisis</h2>
            <p class="text-muted mb-0">Publikasikan artikel analisis risiko rantai pasok.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card p-4 mb-4">
        <div class="card-header border-0 px-0 pt-0">Tulis Artikel Baru</div>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.articles.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small text-muted">Judul</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">Isi Artikel</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Publikasikan
            </button>
        </form>
    </div>

    <div class="row g-3">
        @forelse ($articles as $article)
            <div class="col-md-6">
                <div class="card p-4 h-100 d-flex flex-column">
                    <h5 class="fw-bold mb-1">{{ $article->title }}</h5>
                    <span class="text-muted small mb-2">
                        Oleh {{ $article->user->name }} &middot; {{ $article->created_at->format('d M Y') }}
                    </span>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($article->content, 150) }}</p>
                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                          onsubmit="return confirm('Hapus artikel ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-4 text-center text-muted">Belum ada artikel.</div>
            </div>
        @endforelse
    </div>

@endsection
