@extends('layouts.app')
@section('title', 'Daftar Kunjungan untuk Pembayaran')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Kunjungan untuk Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Pasien</th>
                                            <th>Umur</th>
                                            <th>Alamat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kunjungans as $kunjungan)
                                            <tr>
                                                <td>{{ $kunjungan->tanggal->format('d-m-Y') }}</td>
                                                <td>{{ $kunjungan->pasien->nama }}</td>
                                                <td>{{ \Carbon\Carbon::parse($kunjungan->pasien->tanggal_lahir)->age }}
                                                    tahun</td>
                                                <td>{{ $kunjungan->pasien->alamat }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#detailModal{{ $kunjungan->id }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#paymentModal{{ $kunjungan->id }}">
                                                        Proses Pembayaran
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data kunjungan untuk
                                                    pembayaran</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($kunjungans as $kunjungan)
        <!-- Modal Detail Kunjungan -->
        <div class="modal fade" id="detailModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="detailModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel{{ $kunjungan->id }}">Detail Kunjungan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6>Keluhan:</h6>
                        <p>{{ $kunjungan->keluhan }}</p>
                        <h6>Diagnosa:</h6>
                        <p>{{ $kunjungan->diagnosa }}</p>
                        <h6>Tindakan:</h6>
                        <p>{{ $kunjungan->tindakan }}</p>
                        <h6>Obat yang Diberikan:</h6>
                        <ul>
                            @foreach ($kunjungan->detailKunjungans as $detail)
                                <li>{{ $detail->obat->nama }} - {{ $detail->jumlah_obat }} {{ $detail->obat->satuan }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pembayaran -->
        <div class="modal fade" id="paymentModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="paymentModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="paymentForm{{ $kunjungan->id }}" action="{{ route('pembayaran.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel{{ $kunjungan->id }}">Proses Pembayaran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="kunjungan_id" value="{{ $kunjungan->id }}">
                            <div class="form-group">
                                <label for="total_bayar{{ $kunjungan->id }}">Total Bayar</label>
                                <input type="text" class="form-control total-bayar" id="total_bayar{{ $kunjungan->id }}"
                                    name="total_bayar" required>
                            </div>
                            <div class="form-group">
                                <label for="metode_pembayaran{{ $kunjungan->id }}">Metode Pembayaran</label>
                                <select class="form-control" id="metode_pembayaran{{ $kunjungan->id }}"
                                    name="metode_pembayaran" required>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="Kartu Kredit">Kartu Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi untuk memformat angka
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Fungsi untuk menghapus format
        function unformatNumber(number) {
            return number.replace(/\./g, '');
        }

        // Event listener untuk input total_bayar
        document.querySelectorAll('.total-bayar').forEach(function(input) {
            input.addEventListener('input', function(e) {
                // Hapus semua karakter non-digit
                let value = this.value.replace(/\D/g, '');

                // Format angka
                if (value !== '') {
                    value = formatNumber(value);
                    this.value = value;
                }
            });
        });

        // Event listener untuk form submission
        document.querySelectorAll('form[id^="paymentForm"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let totalBayarInput = this.querySelector('input[name="total_bayar"]');
                totalBayarInput.value = unformatNumber(totalBayarInput.value);
                this.submit();
            });
        });
    </script>
@endsection
