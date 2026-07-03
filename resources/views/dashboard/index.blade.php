@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1">Welcome Back 👋</h2>
            <p class="text-muted mb-0">Monitor global supply chain risk from one dashboard.</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Country to Watchlist
        </button>
    </div>

    {{-- Ringkasan cepat, tiap kartu diberi warna sesuai level risiko --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card risk-low-border p-3 h-100">
                <span class="text-muted small">Germany</span>
                <h3 class="fw-bold my-1">22</h3>
                <span class="risk-badge risk-low">Low Risk</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card risk-medium-border p-3 h-100">
                <span class="text-muted small">China</span>
                <h3 class="fw-bold my-1">47</h3>
                <span class="risk-badge risk-medium">Medium Risk</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card risk-high-border p-3 h-100">
                <span class="text-muted small">Example Country</span>
                <h3 class="fw-bold my-1">78</h3>
                <span class="risk-badge risk-high">High Risk</span>
            </div>
        </div>

    </div>

    <div class="card p-4">
        <div class="card-header border-0 px-0 pt-0">Watchlist</div>
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Currency</th>
                    <th>Weather</th>
                    <th>Risk Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Germany</td>
                    <td>EUR</td>
                    <td>Clear</td>
                    <td>22</td>
                    <td><span class="risk-badge risk-low">Low</span></td>
                </tr>
                <tr>
                    <td>China</td>
                    <td>CNY</td>
                    <td>Storm Warning</td>
                    <td>47</td>
                    <td><span class="risk-badge risk-medium">Medium</span></td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
