@extends('layouts.app')

@section('content')

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Currency Impact Dashboard</h2>
        <p class="text-muted mb-0">Pantau nilai tukar & tren perubahan kurs 30 hari terakhir.</p>
    </div>

    <div class="card p-3 mb-4">
        <form id="currency-form" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted">Dari (Base)</label>
                <select id="base-select" class="form-select">
                    <option value="USD" selected>USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - Pound Sterling</option>
                    <option value="JPY">JPY - Japanese Yen</option>
                    <option value="CNY">CNY - Chinese Yuan</option>
                    <option value="IDR">IDR - Indonesian Rupiah</option>
                    <option value="AUD">AUD - Australian Dollar</option>
                    <option value="CAD">CAD - Canadian Dollar</option>
                    <option value="CHF">CHF - Swiss Franc</option>
                    <option value="HKD">HKD - Hong Kong Dollar</option>
                    <option value="SGD">SGD - Singapore Dollar</option>
                    <option value="INR">INR - Indian Rupee</option>
                    <option value="KRW">KRW - South Korean Won</option>
                    <option value="MYR">MYR - Malaysian Ringgit</option>
                    <option value="THB">THB - Thai Baht</option>
                    <option value="PHP">PHP - Philippine Peso</option>
                    <option value="MXN">MXN - Mexican Peso</option>
                    <option value="BRL">BRL - Brazilian Real</option>
                    <option value="ZAR">ZAR - South African Rand</option>
                    <option value="NZD">NZD - New Zealand Dollar</option>
                    <option value="SEK">SEK - Swedish Krona</option>
                    <option value="NOK">NOK - Norwegian Krone</option>
                    <option value="DKK">DKK - Danish Krone</option>
                    <option value="PLN">PLN - Polish Zloty</option>
                    <option value="TRY">TRY - Turkish Lira</option>
                    <option value="ILS">ILS - Israeli Shekel</option>
                    <option value="CZK">CZK - Czech Koruna</option>
                    <option value="HUF">HUF - Hungarian Forint</option>
                    <option value="RON">RON - Romanian Leu</option>
                    <option value="ISK">ISK - Icelandic Krona</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Ke (Target)</label>
                <select id="target-select" class="form-select">
                    <option value="IDR" selected>IDR - Indonesian Rupiah</option>
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="GBP">GBP - Pound Sterling</option>
                    <option value="JPY">JPY - Japanese Yen</option>
                    <option value="CNY">CNY - Chinese Yuan</option>
                    <option value="AUD">AUD - Australian Dollar</option>
                    <option value="CAD">CAD - Canadian Dollar</option>
                    <option value="CHF">CHF - Swiss Franc</option>
                    <option value="HKD">HKD - Hong Kong Dollar</option>
                    <option value="SGD">SGD - Singapore Dollar</option>
                    <option value="INR">INR - Indian Rupee</option>
                    <option value="KRW">KRW - South Korean Won</option>
                    <option value="MYR">MYR - Malaysian Ringgit</option>
                    <option value="THB">THB - Thai Baht</option>
                    <option value="PHP">PHP - Philippine Peso</option>
                    <option value="MXN">MXN - Mexican Peso</option>
                    <option value="BRL">BRL - Brazilian Real</option>
                    <option value="ZAR">ZAR - South African Rand</option>
                    <option value="NZD">NZD - New Zealand Dollar</option>
                    <option value="SEK">SEK - Swedish Krona</option>
                    <option value="NOK">NOK - Norwegian Krone</option>
                    <option value="DKK">DKK - Danish Krone</option>
                    <option value="PLN">PLN - Polish Zloty</option>
                    <option value="TRY">TRY - Turkish Lira</option>
                    <option value="ILS">ILS - Israeli Shekel</option>
                    <option value="CZK">CZK - Czech Koruna</option>
                    <option value="HUF">HUF - Hungarian Forint</option>
                    <option value="RON">RON - Romanian Leu</option>
                    <option value="ISK">ISK - Icelandic Krona</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card p-4" id="rate-summary">
                <span class="text-muted small">Kurs Saat Ini</span>
                <h2 class="fw-bold my-1" id="rate-value">--</h2>
                <span class="text-muted small" id="rate-date">Memuat...</span>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-header border-0 px-0 pt-0">Tren 30 Hari Terakhir</div>
                <canvas id="rate-chart" height="120"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('currency-form');
    const baseSelect = document.getElementById('base-select');
    const targetSelect = document.getElementById('target-select');
    const rateValue = document.getElementById('rate-value');
    const rateDate = document.getElementById('rate-date');
    const canvas = document.getElementById('rate-chart');

    let chartInstance = null;

    function renderChart(labels, data, target) {
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `Kurs ke ${target}`,
                    data: data,
                    borderColor: '#4338CA',
                    backgroundColor: 'rgba(67, 56, 202, 0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: false },
                },
            },
        });
    }

    async function loadCurrency(base, target) {
        rateValue.textContent = '...';
        rateDate.textContent = 'Memuat...';

        try {
            const response = await fetch(`/api/currency?base=${base}&target=${target}`);
            const data = await response.json();

            if (!response.ok) {
                rateValue.textContent = '-';
                rateDate.textContent = data.message;
                return;
            }

            rateValue.textContent = `1 ${data.base} = ${data.rate.toLocaleString('en-US', { maximumFractionDigits: 4 })} ${data.target}`;
            rateDate.textContent = `Update: ${data.date}`;

            const labels = data.history.map(item => item.date);
            const values = data.history.map(item => item.rate);
            renderChart(labels, values, data.target);

        } catch (error) {
            console.error(error);
            rateValue.textContent = '-';
            rateDate.textContent = 'Gagal memuat data.';
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        loadCurrency(baseSelect.value, targetSelect.value);
    });

    loadCurrency(baseSelect.value, targetSelect.value);

});
</script>
@endpush
