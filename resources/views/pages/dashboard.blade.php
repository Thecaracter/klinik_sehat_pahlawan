@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                    <h6 class="op-7 mb-2">Penjualan Sarang Walet</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Tenaga Ahli</p>
                                        <h4 class="card-title">{{ $users }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Obat</p>
                                        <h4 class="card-title">{{ $obatCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-luggage-cart"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Pasien Terdaftar</p>
                                        <h4 class="card-title">{{ $pasienCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                        <i class="far fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Kunjungan Selesai</p>
                                        <h4 class="card-title">{{ $kunjunganFinishCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mx-auto">
                    <div class="card card-sm">
                        <div class="card-body py-2">
                            <form id="periodForm" class="form-inline justify-content-center">
                                <div class="form-group mr-2">
                                    <select name="period" id="period" class="form-control form-control-sm">
                                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="year" id="year" class="form-control form-control-sm">
                                        @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group mr-2" id="monthSelectGroup"
                                    style="{{ $period == 'yearly' ? 'display:none;' : '' }}">
                                    <select name="month" id="month" class="form-control form-control-sm">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Grafik Kunjungan Selesai -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header py-2">
                            <h5 class="card-title m-0">Kunjungan Selesai</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="visitsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-round">
                        <div class="card-body">
                            <div class="card-head-row card-tools-still-right">
                                <h5 class="card-title">User Earnings</h5>
                            </div>
                            <div class="card-list">
                                @foreach ($earningsData as $user)
                                    <div class="item-list">
                                        <div class="avatar">
                                            <span
                                                class="avatar-title rounded-circle border border-white bg-info">{{ substr($user['name'], 0, 1) }}</span>
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">{{ $user['name'] }}</div>
                                            <div class="status">
                                                @foreach ($user['earnings'] as $earning)
                                                    <div>{{ $earning['date'] }}: Rp
                                                        {{ number_format($earning['total'], 0, ',', '.') }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                        {{-- <button class="btn btn-icon btn-link btn-sm">
                                            <i class="far fa-eye"></i>
                                        </button> --}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('alert'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const alert = @json(session('alert'));
                    if (alert) {
                        Swal.fire({
                            icon: alert.type,
                            title: alert.type.charAt(0).toUpperCase() + alert.type.slice(1),
                            text: alert.message,
                            confirmButtonText: 'Okay'
                        });
                    }
                });
            </script>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('visitsChart').getContext('2d');
            var chartData = @json($chartData);

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(data => data.date),
                    datasets: [{
                        label: 'Kunjungan Selesai',
                        data: chartData.map(data => data.total),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Kunjungan'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: '{{ $period == 'yearly' ? 'Bulan' : 'Tanggal' }}'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                }
            });

            // Handle form changes
            document.querySelector('select[name="period"]').addEventListener('change', function() {
                var monthSelectGroup = document.getElementById('monthSelectGroup');
                if (this.value === 'yearly') {
                    monthSelectGroup.style.display = 'none';
                } else {
                    monthSelectGroup.style.display = 'inline-block';
                }
            });

            // Handle form submission
            document.getElementById('periodForm').addEventListener('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var url = '{{ route('dashboard') }}?' + new URLSearchParams(formData).toString();
                window.location.href = url;
            });
        });
    </script>
@endpush
