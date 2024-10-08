@extends('layouts.app')
@section('title', 'Daftar Pemeriksaan')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Pemeriksaan</h4>
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
                                            <th>Tanggal</th>
                                            <th>Keluhan</th>
                                            <th>Umur</th>
                                            <th>Foto</th>
                                            <th>Riwayat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kunjungans as $kunjungan)
                                            <tr>
                                                <td>{{ $kunjungan->pasien_nik }}</td>
                                                <td>{{ $kunjungan->pasien->nama }}</td>
                                                <td>{{ $kunjungan->tanggal->format('d-m-Y') }}</td>
                                                <td>{{ $kunjungan->keluhan }}</td>
                                                <td>{{ \Carbon\Carbon::parse($kunjungan->pasien->tanggal_lahir)->age }}
                                                    tahun</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                        data-target="#showPhotosModal{{ $kunjungan->id }}">
                                                        Lihat Foto ({{ $kunjungan->fotoKunjungan->count() }})
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-toggle="modal" data-target="#riwayatModal{{ $kunjungan->id }}">
                                                        Lihat Riwayat
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-toggle="modal"
                                                        data-target="#pemeriksaanModal{{ $kunjungan->id }}">
                                                        Periksa
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-toggle="modal"
                                                        data-target="#addPhotoModal{{ $kunjungan->id }}">
                                                        Tambah Foto
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data pemeriksaan</td>
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
        <!-- Pemeriksaan Modal -->
        <div class="modal fade" id="pemeriksaanModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="pemeriksaanModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('pemeriksaan.update', $kunjungan->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="pemeriksaanModalLabel{{ $kunjungan->id }}">Pemeriksaan
                                {{ $kunjungan->pasien->nama }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="diagnosa{{ $kunjungan->id }}">Diagnosa</label>
                                <textarea class="form-control" id="diagnosa{{ $kunjungan->id }}" name="diagnosa" required>{{ old('diagnosa', $kunjungan->diagnosa) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="tindakan{{ $kunjungan->id }}">Tindakan</label>
                                <textarea class="form-control" id="tindakan{{ $kunjungan->id }}" name="tindakan" required>{{ old('tindakan', $kunjungan->tindakan) }}</textarea>
                            </div>
                            <div id="obatContainer{{ $kunjungan->id }}">
                                <h5>Obat yang Diberikan</h5>
                                @foreach ($kunjungan->detailKunjungans as $index => $detail)
                                    <div class="form-row mt-3 obat-row">
                                        <div class="col-md-3">
                                            <label>Nama Obat</label>
                                            <select name="obat_id[]" class="form-control" required>
                                                @foreach ($obats as $obat)
                                                    <option value="{{ $obat->id }}"
                                                        {{ $detail->id_obat == $obat->id ? 'selected' : '' }}
                                                        data-satuan="{{ $obat->satuan }}">
                                                        {{ $obat->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Jumlah</label>
                                            <input type="number" name="jumlah_obat[]" class="form-control jumlah-obat"
                                                placeholder="Jumlah" required step="0.01" min="0"
                                                value="{{ $detail->jumlah_obat }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Satuan</label>
                                            <input type="text" class="form-control satuan-obat" readonly
                                                value="{{ $detail->obat->satuan }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Instruksi</label>
                                            <input type="text" name="instruksi[]" class="form-control"
                                                placeholder="Instruksi" required value="{{ $detail->instruksi }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm delete-obat">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-secondary mt-2"
                                onclick="addObatInput({{ $kunjungan->id }})">Tambah Obat</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Pemeriksaan</button>
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
                    <form method="POST" action="{{ route('pemeriksaan.uploadFoto', $kunjungan->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPhotoModalLabel{{ $kunjungan->id }}">Tambah Foto Pemeriksaan
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="foto{{ $kunjungan->id }}">Pilih Foto (bisa pilih lebih dari satu)</label>
                                <input type="file" class="form-control-file" id="foto{{ $kunjungan->id }}"
                                    name="foto[]" multiple required onchange="handleFileSelect(this)">
                            </div>
                            <div id="fotoNameContainer{{ $kunjungan->id }}">
                                <!-- Nama foto akan ditambahkan di sini secara dinamis -->
                            </div>
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
                        <h5 class="modal-title" id="showPhotosModalLabel{{ $kunjungan->id }}">Foto Pemeriksaan</h5>
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
                                    <form action="{{ route('pemeriksaan.deleteFoto', $foto->id) }}" method="POST">
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

        <!-- Riwayat Modal -->
        <div class="modal fade" id="riwayatModal{{ $kunjungan->id }}" tabindex="-1" role="dialog"
            aria-labelledby="riwayatModalLabel{{ $kunjungan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="riwayatModalLabel{{ $kunjungan->id }}">Riwayat Kunjungan
                            {{ $kunjungan->pasien->nama }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($kunjungan->riwayatKunjungan->isNotEmpty())
                            @foreach ($kunjungan->riwayatKunjungan as $riwayat)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        Tanggal: {{ $riwayat->tanggal->format('d-m-Y') }}
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">Keluhan: {{ $riwayat->keluhan }}</h6>
                                        <p class="card-text"><strong>Diagnosa:</strong> {{ $riwayat->diagnosa }}</p>
                                        <p class="card-text"><strong>Tindakan:</strong> {{ $riwayat->tindakan }}</p>

                                        @if ($riwayat->detailKunjungans->isNotEmpty())
                                            <h6>Obat yang diberikan:</h6>
                                            <ul>
                                                @foreach ($riwayat->detailKunjungans as $detail)
                                                    <li>{{ $detail->obat->nama }} - {{ $detail->jumlah_obat }}
                                                        {{ $detail->obat->satuan }} ({{ $detail->instruksi }})</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($riwayat->fotoKunjungan->isNotEmpty())
                                            <h6>Foto:</h6>
                                            <div class="row">
                                                @foreach ($riwayat->fotoKunjungan as $foto)
                                                    <div class="col-md-4 mb-2">
                                                        <a href="{{ asset($foto->foto) }}"
                                                            data-fancybox="gallery-{{ $riwayat->id }}"
                                                            data-caption="{{ $foto->nama }}">
                                                            <img src="{{ asset($foto->foto) }}"
                                                                alt="{{ $foto->nama }}" class="img-fluid">
                                                        </a>
                                                        <p class="text-center">{{ $foto->nama }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Tidak ada riwayat kunjungan sebelumnya.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to existing obat rows
            const obatRows = document.querySelectorAll('.obat-row');
            obatRows.forEach(addEventListenersToObatRow);

            // Initialize tooltips if used
            $('[data-toggle="tooltip"]').tooltip();

            // Reset modal forms on close
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form').trigger('reset');
                $(this).find('[id^=fotoNameContainer]').html('');
            });
        });

        function addObatInput(kunjunganId) {
            const container = document.getElementById('obatContainer' + kunjunganId);
            const obatInput = `
        <div class="row mb-3 obat-row align-items-start">
            <div class="col-md-5">
                <div class="form-group mb-2">
                    <label for="obat_id" class="form-label">Nama Obat</label>
                    <select name="obat_id[]" class="form-control" required>
                        <option value="">Pilih Obat</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}" data-satuan="{{ $obat->satuan }}">{{ $obat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="jumlah_obat" class="form-label">Jumlah</label>
                    <div class="d-flex">
                        <div class="input-group flex-grow-1 mr-2">
                            <input type="number" name="jumlah_obat[]" class="form-control jumlah-obat" placeholder="Jumlah" required step="0.01" min="0">
                            <div class="input-group-append">
                                <span class="input-group-text satuan-obat" style="min-width: 60px; height: 40px;">-</span>
                            </div>
                        </div>
                       
                        
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm delete-obat" style="align-self: flex-end; margin-left: 10px;">Hapus</button>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label for="instruksi" class="form-label">Instruksi Penggunaan</label>
                    <textarea name="instruksi[]" class="form-control" 
                              style="resize: none; height: 100px;" 
                              placeholder="Tuliskan instruksi penggunaan obat secara detail di sini" required></textarea>
                </div>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', obatInput);

            // Add event listeners to the new row
            addEventListenersToObatRow(container.lastElementChild);
        }

        function addEventListenersToObatRow(row) {
            const selectObat = row.querySelector('select[name="obat_id[]"]');
            const inputJumlah = row.querySelector('.jumlah-obat');
            const spanSatuan = row.querySelector('.satuan-obat');
            const deleteButton = row.querySelector('.delete-obat');

            selectObat.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const satuan = selectedOption.getAttribute('data-satuan');
                spanSatuan.textContent = satuan || '-';
            });

            inputJumlah.addEventListener('input', function() {
                validateJumlahObat(this);
            });

            deleteButton.addEventListener('click', function() {
                row.remove();
            });
        }

        function validateJumlahObat(input) {
            const value = parseFloat(input.value);
            if (isNaN(value) || value < 0) {
                input.setCustomValidity('Jumlah harus 0 atau lebih');
            } else {
                input.setCustomValidity('');
            }
        }

        function handleFileSelect(input) {
            const container = document.getElementById('fotoNameContainer' + input.id.replace('foto', ''));
            container.innerHTML = ''; // Clear previous inputs

            if (input.files && input.files.length > 0) {
                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    const div = document.createElement('div');
                    div.className = 'form-group mt-2';
                    div.innerHTML = `
                    <label for="nama_foto_${i}">Nama untuk foto "${file.name}":</label>
                    <input type="text" class="form-control" name="nama_foto[]" id="nama_foto_${i}" required>
                `;
                    container.appendChild(div);
                }
            }
        }

        function confirmDelete(event, formId) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
    <style>
        .fancybox__container {
            z-index: 1060 !important;
            /* Harus lebih tinggi dari z-index modal Bootstrap */
        }
    </style>
@endsection
