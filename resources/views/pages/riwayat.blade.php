@extends('layouts.app')
@section('title', 'Riwayat Kunjungan')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Riwayat Kunjungan</h4>
                        </div>
                        <div class="card-body">
                            <form id="searchForm" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" id="search_type" name="search_type">
                                            <option value="all">Semua</option>
                                            <option value="tanggal">Tanggal</option>
                                            <option value="nik">NIK</option>
                                            <option value="nama">Nama Pasien</option>
                                            <option value="alamat">Alamat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="search_text" name="search"
                                            placeholder="Masukkan kata kunci pencarian">
                                        <input type="date" class="form-control" id="search_date" name="search"
                                            style="display: none;">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-block">Cari</button>
                                    </div>
                                </div>
                            </form>

                            <div id="kunjunganTable">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Pasien</th>
                                            <th>NIK</th>
                                            <th>Diagnosa</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kunjunganTableBody">
                                        @foreach ($kunjungans as $kunjungan)
                                            <tr class="kunjungan-row"
                                                data-tanggal="{{ $kunjungan->tanggal->format('Y-m-d') }}"
                                                data-nama="{{ $kunjungan->pasien->nama }}"
                                                data-nik="{{ $kunjungan->pasien->nik }}"
                                                data-alamat="{{ $kunjungan->pasien->alamat }}">
                                                <td>{{ $kunjungan->tanggal->format('d-m-Y') }}</td>
                                                <td>{{ $kunjungan->pasien->nama }}</td>
                                                <td>{{ $kunjungan->pasien->nik }}</td>
                                                <td>{{ $kunjungan->diagnosa }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        data-toggle="modal" data-target="#detailModal{{ $kunjungan->id }}">
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
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
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informasi Pasien:</h6>
                                <p><strong>Nama:</strong> {{ $kunjungan->pasien->nama }}</p>
                                <p><strong>NIK:</strong> {{ $kunjungan->pasien->nik }}</p>
                                <p><strong>Tanggal Lahir:</strong> {{ $kunjungan->pasien->tanggal_lahir->format('d-m-Y') }}
                                </p>
                                <p><strong>Alamat:</strong> {{ $kunjungan->pasien->alamat }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Informasi Kunjungan:</h6>
                                <p><strong>Tanggal:</strong> {{ $kunjungan->tanggal->format('d-m-Y') }}</p>
                                <p><strong>Keluhan:</strong> {{ $kunjungan->keluhan }}</p>
                                <p><strong>Diagnosa:</strong> {{ $kunjungan->diagnosa }}</p>
                                <p><strong>Tindakan:</strong> {{ $kunjungan->tindakan }}</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Obat yang Diberikan:</h6>
                        <ul>
                            @foreach ($kunjungan->detailKunjungans as $detail)
                                <li>{{ $detail->obat->nama }} - {{ $detail->jumlah_obat }} {{ $detail->obat->satuan }}
                                </li>
                            @endforeach
                        </ul>
                        <hr>
                        <h6>Foto Kunjungan:</h6>
                        <div class="row">
                            @foreach ($kunjungan->fotoKunjungan as $foto)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ asset($foto->foto) }}" alt="{{ $foto->nama }}" class="img-thumbnail">
                                    <p class="text-center mt-2">{{ $foto->nama }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function toggleSearchInput() {
                var searchType = $('#search_type').val();
                if (searchType === 'tanggal') {
                    $('#search_text').hide();
                    $('#search_date').show();
                } else {
                    $('#search_text').show();
                    $('#search_date').hide();
                }
            }

            $('#search_type').change(toggleSearchInput);

            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchType = $('#search_type').val();
                var searchValue = searchType === 'tanggal' ? $('#search_date').val() : $('#search_text')
                    .val().toLowerCase();

                $('.kunjungan-row').each(function() {
                    var row = $(this);
                    var showRow = false;

                    if (searchType === 'all' || searchValue === '') {
                        showRow = true;
                    } else {
                        var rowData = row.data(searchType);
                        if (searchType === 'tanggal') {
                            showRow = rowData === searchValue;
                        } else {
                            showRow = rowData.toLowerCase().includes(searchValue);
                        }
                    }

                    row.toggle(showRow);
                });

                updatePagination();
            });

            function updatePagination() {
                var visibleRows = $('.kunjungan-row:visible').length;
                var totalPages = Math.ceil(visibleRows / 10);
                var paginationHtml = '';

                if (totalPages > 1) {
                    paginationHtml += '<ul class="pagination">';
                    for (var i = 1; i <= totalPages; i++) {
                        paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + i +
                            '">' + i + '</a></li>';
                    }
                    paginationHtml += '</ul>';
                }

                $('#pagination').html(paginationHtml);
            }

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                var start = (page - 1) * 10;
                var end = start + 10;

                $('.kunjungan-row:visible').hide();
                $('.kunjungan-row:visible').slice(start, end).show();

                $('.pagination li').removeClass('active');
                $(this).parent().addClass('active');
            });

            // Initialize pagination
            updatePagination();
            $('.pagination a:first').click();
        });
    </script>
@endpush
