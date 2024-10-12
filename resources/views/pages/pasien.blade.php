@extends('layouts.app')
@section('title', 'Daftar Pasien')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Pasien</h4>
                            <button type="button" class="btn btn-success" data-toggle="modal"
                                data-target="#createPasienModal">
                                <i class="fa fa-plus"></i> Tambah Pasien
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pasiens.index') }}" method="GET" class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search"
                                        placeholder="Cari berdasarkan NIK, Nama, atau Alamat"
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
                                            <th class="px-4 py-2">No Pasien</th>
                                            <th class="px-4 py-2">NIK</th>
                                            <th class="px-4 py-2">Nama</th>
                                            <th class="px-4 py-2">Umur</th>
                                            <th class="px-4 py-2">Alamat</th>
                                            <th class="px-4 py-2">No HP</th>
                                            <th class="px-4 py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pasiens as $pasien)
                                            <tr>
                                                <td>PSN{{ $pasien->id }}</td>
                                                <td>{{ $pasien->nik ?? 'N/A' }}</td>
                                                <td>{{ $pasien->nama }}</td>
                                                <td>{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} tahun</td>
                                                <td>{{ $pasien->alamat }}</td>
                                                <td>{{ $pasien->no_hp }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-warning"
                                                            data-toggle="modal"
                                                            data-target="#editPasienModal{{ $pasien->id }}">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete('{{ $pasien->id }}')">Hapus</button>
                                                    </div>
                                                    <form id="delete-form-{{ $pasien->id }}"
                                                        action="{{ route('pasiens.destroy', $pasien->id) }}" method="POST"
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data pasien untuk
                                                    ditampilkan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($pasiens->hasPages())
                                <nav>
                                    <ul class="pagination">
                                        @if ($pasiens->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">&laquo;</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $pasiens->previousPageUrl() }}"
                                                    rel="prev">&laquo;</a>
                                            </li>
                                        @endif

                                        @foreach ($pasiens->getUrlRange(1, $pasiens->lastPage()) as $page => $url)
                                            @if ($page == $pasiens->currentPage())
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

                                        @if ($pasiens->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $pasiens->nextPageUrl() }}"
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

    <!-- Create Pasien Modal -->
    <div class="modal fade" id="createPasienModal" tabindex="-1" role="dialog" aria-labelledby="createPasienModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('pasiens.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPasienModalLabel">Tambah Pasien Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nik">NIK (Opsional)</label>
                            <input type="text" class="form-control" id="nik" name="nik" maxlength="16">
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
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

    @foreach ($pasiens as $pasien)
        <!-- Edit Pasien Modal -->
        <div class="modal fade" id="editPasienModal{{ $pasien->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editPasienModalLabel{{ $pasien->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('pasiens.update', $pasien->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPasienModalLabel{{ $pasien->id }}">Edit Data Pasien</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nik{{ $pasien->id }}">NIK (Opsional)</label>
                                <input type="text" class="form-control" id="nik{{ $pasien->id }}" name="nik"
                                    maxlength="16" value="{{ $pasien->nik }}">
                            </div>
                            <div class="form-group">
                                <label for="nama{{ $pasien->id }}">Nama</label>
                                <input type="text" class="form-control" id="nama{{ $pasien->id }}" name="nama"
                                    value="{{ $pasien->nama }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir{{ $pasien->id }}">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir{{ $pasien->id }}"
                                    name="tanggal_lahir"
                                    value="{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat{{ $pasien->id }}">Alamat</label>
                                <textarea class="form-control" id="alamat{{ $pasien->id }}" name="alamat" required>{{ $pasien->alamat }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="no_hp{{ $pasien->id }}">No HP</label>
                                <input type="text" class="form-control" id="no_hp{{ $pasien->id }}" name="no_hp"
                                    value="{{ $pasien->no_hp }}" required>
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

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pasien ini akan dihapus permanen!",
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
