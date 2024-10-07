@extends('layouts.app')
@section('title', 'Daftar Obat Masuk')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Obat Masuk</h4>
                            <button type="button" class="btn btn-success" data-toggle="modal"
                                data-target="#createObatMasukModal">
                                <i class="fa fa-plus"></i> Tambah Obat Masuk
                            </button>
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
                            <!-- Search Form -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <button id="toggleSearch" class="btn btn-primary mb-3">
                                        <i class="fas fa-search"></i> Toggle Pencarian
                                    </button>
                                    <form id="searchForm" action="{{ route('obatmasuk.index') }}" method="GET"
                                        style="display: none;">
                                        <div class="row align-items-end">
                                            <div class="col-md-4 mb-3">
                                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date"
                                                    value="{{ request('start_date') }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <button type="submit" class="btn btn-primary w-100"
                                                    style="height: 38px;">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode Obat</th>
                                            <th>Nama Obat</th>
                                            <th>Nomor Batch</th>
                                            <th>Jumlah</th>
                                            <th>Harga Beli</th>
                                            <th>Tanggal Kadaluarsa</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($obatmasuk as $item)
                                            <tr>
                                                <td>{{ $item->obat->kode_obat }}</td>
                                                <td>{{ $item->obat->nama }}</td>
                                                <td>{{ $item->nomor_batch }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                                <td>{{ $item->tanggal_kadaluarsa->format('d-m-Y') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-toggle="modal"
                                                        data-target="#editObatMasukModal{{ $item->id }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('{{ $item->id }}')">Hapus</button>
                                                    <form id="delete-form-{{ $item->id }}"
                                                        action="{{ route('obatmasuk.destroy', $item->id) }}" method="POST"
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $obatmasuk->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Obat Masuk Modal -->
    <div class="modal fade" id="createObatMasukModal" tabindex="-1" role="dialog"
        aria-labelledby="createObatMasukModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('obatmasuk.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createObatMasukModalLabel">Tambah Obat Masuk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="obat_id">Obat</label>
                            <select class="form-control" id="obat_id" name="obat_id" required>
                                <option value="">Pilih Obat</option>
                                @foreach ($obat as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->kode_obat }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nomor_batch">Nomor Batch</label>
                            <input type="text" class="form-control" id="nomor_batch" name="nomor_batch" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required min="0"
                                step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="harga_beli">Harga Beli</label>
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" required
                                min="0">
                        </div>
                        <div class="form-group">
                            <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($obatmasuk as $item)
        <!-- Edit Obat Masuk Modal -->
        <div class="modal fade" id="editObatMasukModal{{ $item->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editObatMasukModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('obatmasuk.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editObatMasukModalLabel{{ $item->id }}">Edit Obat Masuk</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="obat_id{{ $item->id }}">Obat</label>
                                <select class="form-control" id="obat_id{{ $item->id }}" name="obat_id" required>
                                    <option value="">Pilih Obat</option>
                                    @foreach ($obat as $obatItem)
                                        <option value="{{ $obatItem->id }}"
                                            {{ $item->obat_id == $obatItem->id ? 'selected' : '' }}>
                                            {{ $obatItem->nama }} ({{ $obatItem->kode_obat }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nomor_batch{{ $item->id }}">Nomor Batch</label>
                                <input type="text" class="form-control" id="nomor_batch{{ $item->id }}"
                                    name="nomor_batch" value="{{ $item->nomor_batch }}" required>
                            </div>
                            <div class="form-group">
                                <label for="jumlah{{ $item->id }}">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah{{ $item->id }}"
                                    name="jumlah" value="{{ $item->jumlah }}" required min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label for="harga_beli{{ $item->id }}">Harga Beli</label>
                                <input type="number" class="form-control" id="harga_beli{{ $item->id }}"
                                    name="harga_beli" value="{{ $item->harga_beli }}" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_kadaluarsa{{ $item->id }}">Tanggal Kadaluarsa</label>
                                <input type="date" class="form-control" id="tanggal_kadaluarsa{{ $item->id }}"
                                    name="tanggal_kadaluarsa" value="{{ $item->tanggal_kadaluarsa->format('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data obat masuk ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });

        // Set min date for start_date based on end_date
        document.getElementById('end_date').addEventListener('change', function() {
            document.getElementById('start_date').max = this.value;
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleSearch');
            const searchForm = document.getElementById('searchForm');

            toggleButton.addEventListener('click', function() {
                if (searchForm.style.display === 'none') {
                    searchForm.style.display = 'block';
                    toggleButton.innerHTML = '<i class="fas fa-times"></i> Tutup Pencarian';
                } else {
                    searchForm.style.display = 'none';
                    toggleButton.innerHTML = '<i class="fas fa-search"></i> Pencarian';
                }
            });

            // Jika ada parameter pencarian, tampilkan form
            if ({{ request()->has('start_date') || request()->has('end_date') ? 'true' : 'false' }}) {
                searchForm.style.display = 'block';
                toggleButton.innerHTML = '<i class="fas fa-times"></i> Tutup Pencarian';
            }
        });
    </script>
@endsection
