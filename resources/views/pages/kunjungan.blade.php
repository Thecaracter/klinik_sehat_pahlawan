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
                            <button type="button" class="btn btn-success" data-toggle="modal"
                                data-target="#pasienIdInputModal">
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
                                            <th>ID Pasien</th>
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
                                                <td>{{ $kunjungan->pasien_id }}</td>
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
                                                    <form id="delete-form-{{ $kunjungan->id }}"
                                                        action="{{ route('kunjungan.destroy', $kunjungan->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('{{ $kunjungan->id }}')">Hapus</button>
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

    <!-- Pasien ID Input Modal -->
    <div class="modal fade" id="pasienIdInputModal" tabindex="-1" role="dialog" aria-labelledby="pasienIdInputModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="checkPasienIdForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="pasienIdInputModalLabel">Masukkan ID Pasien</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pasien_id">ID Pasien</label>
                            <input type="text" class="form-control" id="pasien_id" name="pasien_id" required>
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
                <form method="POST" action="{{ route('kunjungan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createKunjunganModalLabel">Tambah Kunjungan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pasien_nama">Nama Pasien</label>
                            <input type="text" class="form-control" id="pasien_nama" name="pasien_nama" readonly>
                        </div>
                        <div class="form-group">
                            <label for="ditangani_oleh">Ditangani Oleh</label>
                            <select class="form-control" id="ditangani_oleh" name="ditangani_oleh" required>
                                <option value="dokter">Dokter</option>
                                <option value="bidan">Bidan</option>
                                <option value="perawat">Perawat</option>
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
                    <form method="POST" action="{{ route('kunjungan.update', $kunjungan->id) }}"
                        enctype="multipart/form-data">
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
                                <label for="pasien_nama{{ $kunjungan->id }}">Nama Pasien</label>
                                <input type="text" class="form-control" id="pasien_nama{{ $kunjungan->id }}"
                                    value="{{ $kunjungan->pasien->nama }}" readonly>
                                <input type="hidden" id="pasien_id{{ $kunjungan->id }}" name="pasien_id"
                                    value="{{ $kunjungan->pasien_id }}">
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Fungsi untuk menutup semua modal
                function closeAllModals() {
                    $('.modal').modal('hide');
                }

                // Fungsi untuk memformat tanggal
                function formatDate(date) {
                    var d = new Date(date),
                        month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();

                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;

                    return [year, month, day].join('-');
                }

                // Set tanggal hari ini sebagai default untuk input tanggal
                $('input[type="date"]').val(formatDate(new Date()));

                // Handler untuk form check pasien ID
                $('#checkPasienIdForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '{{ route('kunjungan.checkPasienId') }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            console.log('Full response:', response); // Debugging
                            if (response.success) {
                                closeAllModals();
                                setTimeout(function() {
                                    $('#pasien_id').val(response.pasien_id);
                                    $('#pasien_nama').val(response.pasien_nama);
                                    console.log('Pasien ID set to:', $('#pasien_id')
                                        .val()); // Debugging
                                    console.log('Pasien Nama set to:', $('#pasien_nama')
                                        .val()); // Debugging
                                    $('#createKunjunganModal').modal('show');
                                }, 500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseJSON); // Debugging
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON && xhr.responseJSON.message ? xhr
                                    .responseJSON.message :
                                    'Terjadi kesalahan saat memeriksa ID pasien.'
                            });
                        }
                    });
                });
                // Handler untuk form submit kunjungan
                $('#createKunjunganModal form').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#createKunjunganModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Kunjungan berhasil ditambahkan.'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location
                                        .reload(); // Reload halaman untuk menampilkan data terbaru
                                }
                            });
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseJSON); // Debugging
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal menambahkan kunjungan. Silakan coba lagi.'
                            });
                        }
                    });
                });

                // Handler untuk tombol "Tambah Kunjungan"
                $('[data-target="#pasienIdInputModal"]').on('click', function(e) {
                    e.preventDefault();
                    closeAllModals();
                    setTimeout(function() {
                        $('#pasienIdInputModal').modal('show');
                    }, 500);
                });

                // Handler untuk tombol edit, tambah foto, dan lihat foto
                $('[data-toggle="modal"]').on('click', function(e) {
                    e.preventDefault();
                    closeAllModals();
                    var targetModal = $(this).data('target');
                    setTimeout(function() {
                        $(targetModal).modal('show');
                    }, 500);
                });

                // Validasi form sebelum submit
                $('form').on('submit', function(e) {
                    var requiredFields = $(this).find('[required]');
                    var isValid = true;

                    requiredFields.each(function() {
                        if ($(this).val() === '') {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Harap isi semua field yang diperlukan!'
                        });
                    }
                });

                // Reset form modal setelah ditutup
                $('.modal').on('hidden.bs.modal', function() {
                    $(this).find('form').trigger('reset');
                    $(this).find('img[id$="Preview"]').attr('src', '').hide();
                });

                // Tambahkan validasi real-time untuk input
                $('input, textarea, select').on('input change', function() {
                    if ($(this).attr('required') && $(this).val() === '') {
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Inisialisasi tooltip Bootstrap
                $('[data-toggle="tooltip"]').tooltip();

                // Fungsi untuk preview foto
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

                // Event listener untuk input file
                $('input[type="file"]').on('change', function() {
                    previewPhoto(this, $(this).attr('id') + 'Preview');
                });

                // Fungsi untuk konfirmasi penghapusan
                window.confirmDelete = function(id) {
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

                // Debugging: Log semua submit form
                $('form').on('submit', function() {
                    console.log('Form submitted:', $(this).attr('action'));
                    console.log('Form data:', $(this).serialize());
                });
            });
        </script>
    @endpush
@endsection
