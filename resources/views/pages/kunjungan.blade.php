@extends('layouts.app')
@section('title', 'Daftar Kunjungan')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Kunjungan</h4>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#nikInputModal">
                                <i class="fa fa-plus"></i> Tambah Kunjungan
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
                            <div class="table-responsive">
                                <table class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>NIK Pasien</th>
                                            <th>Nama Pasien</th>
                                            <th>Ditangani Oleh</th>
                                            <th>Keluhan</th>
                                            <th>Status</th>
                                            <th>Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kunjungans as $kunjungan)
                                            <tr>
                                                <td>{{ $kunjungan->pasien_nik }}</td>
                                                <td>{{ $kunjungan->pasien->nama }}</td>
                                                <td>{{ $kunjungan->ditangani_oleh }}</td>

                                                <td>{{ $kunjungan->keluhan }}</td>
                                                <td>{{ $kunjungan->status }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#showPhotosModal{{ $kunjungan->id }}">
                                                        Lihat Foto ({{ $kunjungan->fotoKunjungan->count() }})
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-toggle="modal"
                                                        data-target="#editKunjunganModal{{ $kunjungan->id }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('{{ $kunjungan->id }}')">Hapus</button>
                                                    <br>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-toggle="modal"
                                                        data-target="#addPhotoModal{{ $kunjungan->id }}">
                                                        Tambah Foto
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data kunjungan</td>
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

    <!-- NIK Input Modal -->
    <div class="modal fade" id="nikInputModal" tabindex="-1" role="dialog" aria-labelledby="nikInputModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('kunjungan.checkNik') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="nikInputModalLabel">Masukkan NIK Pasien</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nik">NIK Pasien</label>
                            <input type="text" class="form-control" id="nik" name="nik" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Kunjungan Modal -->
    <div class="modal fade" id="createKunjunganModal" tabindex="-1" role="dialog"
        aria-labelledby="createKunjunganModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('kunjungan.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createKunjunganModalLabel">Tambah Kunjungan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="pasien_nik" name="pasien_nik" value="">
                        <div class="form-group">
                            <label for="ditangani_oleh">Ditangani Oleh</label>
                            <select class="form-control" id="ditangani_oleh" name="ditangani_oleh" required>
                                <option value="dokter">Dokter</option>
                                <option value="bidan">Bidan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="form-group">
                            <label for="keluhan">Keluhan</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" required></textarea>
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

    @foreach ($kunjungans as $kunjungan)
        <!-- Edit Kunjungan Modal -->
        <div class="modal fade" id="editKunjunganModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editKunjunganModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('kunjungan.update', $kunjungan->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editKunjunganModalLabel{{ $kunjungan->id }}">Edit Data Kunjungan
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="pasien_nik{{ $kunjungan->id }}">NIK Pasien</label>
                                <input type="text" class="form-control" id="pasien_nik{{ $kunjungan->id }}"
                                    name="pasien_nik" value="{{ $kunjungan->pasien_nik }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="ditangani_oleh{{ $kunjungan->id }}">Ditangani Oleh</label>
                                <select class="form-control" id="ditangani_oleh{{ $kunjungan->id }}"
                                    name="ditangani_oleh" required>
                                    <option value="dokter" {{ $kunjungan->ditangani_oleh == 'dokter' ? 'selected' : '' }}>
                                        Dokter</option>
                                    <option value="bidan" {{ $kunjungan->ditangani_oleh == 'bidan' ? 'selected' : '' }}>
                                        Bidan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal{{ $kunjungan->id }}">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal{{ $kunjungan->id }}"
                                    name="tanggal" value="{{ $kunjungan->tanggal->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="keluhan{{ $kunjungan->id }}">Keluhan</label>
                                <textarea class="form-control" id="keluhan{{ $kunjungan->id }}" name="keluhan" required>{{ $kunjungan->keluhan }}</textarea>
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

        <!-- Add Photo Modal -->
        <div class="modal fade" id="addPhotoModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="addPhotoModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('kunjungan.addPhoto', $kunjungan->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPhotoModalLabel{{ $kunjungan->id }}">Tambah Foto Kunjungan
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama_foto{{ $kunjungan->id }}">Nama Foto</label>
                                <input type="text" class="form-control" id="nama_foto{{ $kunjungan->id }}"
                                    name="nama_foto" required>
                            </div>
                            <div class="form-group">
                                <label for="foto{{ $kunjungan->id }}">Pilih Foto</label>
                                <input type="file" class="form-control-file" id="foto{{ $kunjungan->id }}"
                                    name="foto" required
                                    onchange="previewPhoto(this, 'fotoPreview{{ $kunjungan->id }}')">
                            </div>
                            <img id="fotoPreview{{ $kunjungan->id }}" src="#" alt="Preview"
                                style="max-width:100%; max-height:200px; display:none;" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Unggah Foto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Show Photos Modal -->
        <div class="modal fade" id="showPhotosModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="showPhotosModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showPhotosModalLabel{{ $kunjungan->id }}">Foto Kunjungan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @foreach ($kunjungan->fotoKunjungan as $foto)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ asset($foto->foto) }}" alt="{{ $foto->nama }}" class="img-fluid">
                                    <p>{{ $foto->nama }}</p>
                                    <form action="{{ route('kunjungan.deleteFoto', $foto->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data kunjungan ini akan dihapus permanen!",
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

        @if (session('showCreateModal'))
            $(document).ready(function() {
                $('#createKunjunganModal').modal('show');
                $('#pasien_nik').val('{{ session('nik') }}');
            });
        @endif

        function previewPhoto(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result);
                    $('#' + previewId).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Inisialisasi tooltip Bootstrap jika digunakan
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        // Fungsi untuk mereset form modal setelah ditutup
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
            $(this).find('img[id$="Preview"]').attr('src', '').hide();
        });
    </script>

    @foreach ($kunjungans as $kunjungan)
        <form id="delete-form-{{ $kunjungan->id }}" action="{{ route('kunjungan.destroy', $kunjungan->id) }}"
            method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
