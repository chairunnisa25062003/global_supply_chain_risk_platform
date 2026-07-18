<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GSCR Platform</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

@vite(['resources/css/app.css','resources/js/app.js'])

@stack('styles')

</head>

<body>

{{-- ============================================================
     NAVBAR — komponen resmi Bootstrap 5 (navbar-expand-lg)
     Otomatis jadi hamburger menu di layar kecil, tidak perlu
     JS/CSS custom buat itu, semua sudah disediakan Bootstrap.
============================================================ --}}
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container-fluid px-4">

        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <i class="bi bi-globe-americas fs-4"></i>
            <span class="fw-bold">GSCR <small class="fw-normal d-none d-sm-inline">Risk Platform</small></span>
        </a>

        {{-- Tombol hamburger, otomatis muncul di layar < 992px --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-globe me-1"></i> Countries
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('weather') }}">
                        <i class="bi bi-cloud-sun me-1"></i> Weather
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-graph-up-arrow me-1"></i> Economy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('currency') }}">
                        <i class="bi bi-currency-exchange me-1"></i> Currency
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-anchor me-1"></i> Ports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href=href="{{ route('news') }}">
                        <i class="bi bi-newspaper me-1"></i> News
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-exclamation-triangle me-1"></i> Risk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bar-chart-line me-1"></i> Analytics
                    </a>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('compare') }}">
                        <i class="bi bi-arrow-left-right me-1"></i> Compare
                    </a>
                </li>
                </li>
               <li class="nav-item">
                    <a class="nav-link" href="{{ route('watchlist') }}">
                        <i class="bi bi-star me-1"></i> Watchlist
                    </a>
                </li>

@guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                 <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                     <a class="nav-link" href="{{ route('register') }}">
                <i class="bi bi-person-plus me-1"></i> Daftar
                    </a>
                </li>
@else
                <li class="nav-item">
                    <span class="nav-link">
                <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
 @csrf
            <button type="submit" class="nav-link btn btn-link" style="border:none; background:none;">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </li>
@endguest

            </ul>
        </div>
    </div>
</nav>

<main class="content">
    <div class="container-fluid px-4 py-4">
        @yield('content')
    </div>
</main>

@stack('scripts')

</body>
</html>
