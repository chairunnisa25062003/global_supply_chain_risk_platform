@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Dataset Pelabuhan</h2>
            <p class="text-muted mb-0">Tambah, hapus, atau ubah status kemacetan pelabuhan.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- BARU: Import CSV, buat masukin dataset pelabuhan dalam jumlah
         banyak sekaligus, bukan tambah satu-satu manual --}}
    <div class="card p-4 mb-4">
        <div class="card-header border-0 px-0 pt-0">Import Pelabuhan dari CSV</div>
        <p class="text-muted small">
            Download dataset dari <a href="https://opendata.upply.com/seaports" target="_blank" rel="noopener">Upply Global Seaport Open Data</a>,
            pastikan file punya kolom <code>name</code>, <code>country</code>, <code>latitude</code>, <code>longitude</code>
            (kolom lain seperti <code>unlocode</code>, <code>harbor_size</code>, <code>status</code> opsional).
        </p>
        <form method="POST" action="{{ route('admin.ports.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-9">
                <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upload me-1"></i> Import
                </button>
            </div>
        </form>
    </div>

    <div class="card p-4 mb-4">
        <div class="card-header border-0 px-0 pt-0">Tambah Pelabuhan Baru</div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.ports.store') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label small text-muted">Nama Pelabuhan</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Negara</label>
                <input type="text" name="country" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">UN/LOCODE</label>
                <input type="text" name="unlocode" class="form-control" maxlength="10">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Latitude</label>
                <input type="number" step="any" name="latitude" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Longitude</label>
                <input type="number" step="any" name="longitude" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Ukuran</label>
                <select name="harbor_size" class="form-select">
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large" selected>Large</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="Normal" selected>Normal</option>
                    <option value="Busy">Busy</option>
                    <option value="Congested">Congested</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Pelabuhan
                </button>
            </div>
        </form>
    </div>

    <div class="card p-4" style="max-height: 500px; overflow-y: auto;">
        <div class="card-header border-0 px-0 pt-0">Daftar Pelabuhan ({{ $ports->count() }})</div>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Negara</th>
                    <th>UN/LOCODE</th>
                    <th>Ukuran</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ports as $port)
                    <tr>
                        <td>{{ $port->name }}</td>
                        <td>{{ $port->country }}</td>
                        <td>{{ $port->unlocode ?? '-' }}</td>
                        <td>{{ $port->harbor_size ?? '-' }}</td>
                        <td>
                            {{-- BARU: dropdown status, langsung submit tiap kali diganti --}}
                            <form method="POST" action="{{ route('admin.ports.update-status', $port) }}"
                                  onchange="this.submit()" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm"
                                        style="width:auto; display:inline-block;">
                                    <option value="Normal" {{ $port->status === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Busy" {{ $port->status === 'Busy' ? 'selected' : '' }}>Busy</option>
                                    <option value="Congested" {{ $port->status === 'Congested' ? 'selected' : '' }}>Congested</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.ports.destroy', $port) }}"
                                  onsubmit="return confirm('Hapus pelabuhan {{ $port->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
