@extends('layouts.app')
@section('title', 'Daftar Obat')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Obat</h4>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createObatModal">
                                <i class="fa fa-plus"></i> Tambah Obat
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('obat.index') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search"
                                        placeholder="Cari berdasarkan Kode Obat, Nama, atau Merk"
                                        value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>

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
                            <div id="results" class="table-responsive">
                                <table class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2">Kode Obat</th>
                                            <th class="px-4 py-2">Nama</th>
                                            <th class="px-4 py-2">Merk</th>
                                            <th class="px-4 py-2">Jenis</th>
                                            <th class="px-4 py-2">Stok</th>
                                            <th class="px-4 py-2">Harga</th>
                                            <th class="px-4 py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($obat->isEmpty())
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data obat untuk
                                                    ditampilkan</td>
                                            </tr>
                                        @else
                                            @foreach ($obat as $item)
                                                <tr>
                                                    <td>{{ $item->kode_obat }}</td>
                                                    <td>{{ $item->nama }}</td>
                                                    <td>{{ $item->merk }}</td>
                                                    <td>{{ $item->jenis }}</td>
                                                    <td>{{ $item->formatStok() }}</td>
                                                    <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-warning"
                                                                data-toggle="modal"
                                                                data-target="#editObatModal{{ $item->id }}">
                                                                Edit
                                                            </button>
                                                            <button class="btn btn-sm btn-danger"
                                                                onclick="confirmDelete('{{ $item->id }}')">Hapus</button>
                                                        </div>
                                                        <form id="delete-form-{{ $item->id }}"
                                                            action="{{ route('obat.destroy', $item->id) }}" method="POST"
                                                            style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if ($obat->hasPages())
                                <nav>
                                    <ul class="pagination">
                                        {{-- Previous Page Link --}}
                                        @if ($obat->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">&laquo;</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $obat->previousPageUrl() }}"
                                                    rel="prev">&laquo;</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($obat->getUrlRange(1, $obat->lastPage()) as $page => $url)
                                            @if ($page == $obat->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if ($obat->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $obat->nextPageUrl() }}"
                                                    rel="next">&raquo;</a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">&raquo;</span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Obat Modal -->
    <div class="modal fade" id="createObatModal" tabindex="-1" role="dialog" aria-labelledby="createObatModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('obat.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createObatModalLabel">Tambah Obat Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kode_obat">Kode Obat</label>
                            <input type="text" class="form-control" id="kode_obat" name="kode_obat"
                                value="{{ $nextKodeObat }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="merk">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="jenis">Jenis</label>
                            <input type="text" class="form-control" id="jenis" name="jenis" required>
                        </div>
                        <div class="form-group">
                            <label for="kegunaan">Kegunaan</label>
                            <textarea class="form-control" id="kegunaan" name="kegunaan" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                @foreach ($satuanOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
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

    @foreach ($obat as $item)
        <!-- Edit Obat Modal -->
        <div class="modal fade" id="editObatModal{{ $item->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editObatModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('obat.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editObatModalLabel{{ $item->id }}">Edit Data Obat</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="kode_obat{{ $item->id }}">Kode Obat</label>
                                <input type="text" class="form-control" id="kode_obat{{ $item->id }}"
                                    name="kode_obat" value="{{ $item->kode_obat }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="merk{{ $item->id }}">Merk</label>
                                <input type="text" class="form-control" id="merk{{ $item->id }}" name="merk"
                                    value="{{ $item->merk }}" required>
                            </div>
                            <div class="form-group">
                                <label for="nama{{ $item->id }}">Nama</label>
                                <input type="text" class="form-control" id="nama{{ $item->id }}" name="nama"
                                    value="{{ $item->nama }}" required>
                            </div>
                            <div class="form-group">
                                <label for="jenis{{ $item->id }}">Jenis</label>
                                <input type="text" class="form-control" id="jenis{{ $item->id }}" name="jenis"
                                    value="{{ $item->jenis }}" required>
                            </div>
                            <div class="form-group">
                                <label for="kegunaan{{ $item->id }}">Kegunaan</label>
                                <textarea class="form-control" id="kegunaan{{ $item->id }}" name="kegunaan" required>{{ $item->kegunaan }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="harga{{ $item->id }}">Harga</label>
                                <input type="number" class="form-control" id="harga{{ $item->id }}" name="harga"
                                    value="{{ $item->harga }}" required>
                            </div>
                            <div class="form-group">
                                <label for="satuan{{ $item->id }}">Satuan</label>
                                <select class="form-control" id="satuan{{ $item->id }}" name="satuan" required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach ($satuanOptions as $option)
                                        <option value="{{ $option }}"
                                            {{ $item->satuan == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
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
                text: "Data obat ini akan dihapus permanen!",
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
    </script>
@endsection
