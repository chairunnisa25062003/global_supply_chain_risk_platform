@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola User</h2>
            <p class="text-muted mb-0">Ubah role atau hapus akun.</p>
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

    <div class="card p-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="risk-badge {{ $user->role === 'admin' ? 'risk-high' : 'risk-low' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        Jadikan {{ $user->role === 'admin' ? 'User' : 'Admin' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus akun {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">Akun kamu</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
