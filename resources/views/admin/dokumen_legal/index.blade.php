@extends('admin.layouts.dashboard')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /*table dokumen legal*/
        #table_dokumen_legal {
            width: 100% !important;
        }

        #table_dokumen_legal th,
        #table_dokumen_legal td {
            vertical-align: middle;
        }

        #table_dokumen_legal th:nth-child(6),
        #table_dokumen_legal td:nth-child(6) {
            width: 170px !important;
            min-width: 170px !important;
            white-space: nowrap !important;
            text-align: center;
        }

        /*aksi button*/
        .aksi-wrap {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .aksi-wrap .btn {
            width: 38px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /*form floating search*/
        .form-floating>label {
            font-size: 13px;
        }

        .sticky-top .card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
        }

        /*select2*/
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 44px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 6px 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }

        .dokumen-status-tabs {
            display: flex;
            gap: 8px;
            align-items: center;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .dokumen-status-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid #e0d7ff;
            border-radius: 8px;
            background: #fff;
            color: #5d596c;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .dokumen-status-tab.active {
            border-color: #8c57ff;
            background: #8c57ff;
            color: #fff;
        }

        .dokumen-status-tab .count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: #f1eefc;
            color: #5d596c;
            font-size: 12px;
        }

        .dokumen-status-tab.active .count {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }
    </style>
@endsection


@section('isi')
    @include('sweetalert::alert')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="card">

                    {{-- HEADER --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">DOKUMEN LEGAL</h5>
                        <a href="{{ route('dokumen_legal.create', $holding->holding_code) }}" class="btn btn-primary">
                            + Tambah
                        </a>
                    </div>

                    {{-- BODY --}}
                    <div class="card-body">
                        <div class="dokumen-status-tabs mb-3">
                            @foreach ($statusTabs as $key => $label)
                                @php
                                    $tabQuery = array_merge(request()->except('page'), ['status_filter' => $key]);
                                @endphp
                                <a href="{{ route('dokumen_legal.index', $holding->holding_code) . '?' . http_build_query($tabQuery) }}"
                                    class="dokumen-status-tab {{ $statusFilter === $key ? 'active' : '' }}">
                                    <span>{{ $label }}</span>
                                    <span class="count">{{ $statusCounts[$key] ?? 0 }}</span>
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" action="{{ route('dokumen_legal.index', $holding->holding_code) }}">
                            <input type="hidden" name="status_filter" value="{{ $statusFilter }}">
                            <div class="card mb-3">
                                <div class="card-body" id="filter-wrapper">
                                    <div class="row g-3 align-items-end">

                                        {{-- KATEGORI --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label class="form-label fw-semibold mb-1">Kategori Dokumen</label>
                                            <select class="form-select js-kategori" name="kategori_filter">
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($kategoriList as $kat)
                                                    <option value="{{ $kat->id_kategori }}"
                                                        @selected((string) $kategoriFilter === (string) $kat->id_kategori)>
                                                        {{ $kat->nama_kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- PENERBIT --}}
                                        <div class="col-lg-4 col-md-6">
                                            <label class="form-label fw-semibold mb-1">Penerbit Dokumen</label>
                                            <select class="form-select js-penerbit" name="penerbit_filter">
                                                <option value="">Pilih Penerbit</option>
                                                @foreach ($penerbitList as $penerbit)
                                                    <option value="{{ $penerbit->id_penerbit }}"
                                                        @selected((string) $penerbitFilter === (string) $penerbit->id_penerbit)>
                                                        {{ $penerbit->nama_penerbit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- ACTION --}}
                                        <div class="col-lg-4 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill">
                                                <i class="mdi mdi-filter-outline"></i> Filter
                                            </button>

                                            <a href="{{ route('dokumen_legal.index', $holding->holding_code) }}?status_filter={{ $statusFilter }}"
                                                class="btn btn-outline-secondary flex-fill">
                                                Reset
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>

                        <table id="table_dokumen_legal" class="table table-bordered table-striped w-100">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Nomor</th>
                                    <th>Nama Dokumen</th>
                                    <th>Site</th>
                                    <th style="width:120px;">Status</th>
                                    <th style="width:160px;">Berlaku Sampai</th>
                                    <th style="width:160px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($documents as $doc)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $doc->nomer_dukumen }}</td>
                                        <td>{{ $doc->nama }}</td>
                                        <td>{{ $doc->site?->site_name ?? '-' }}</td>

                                        {{-- STATUS --}}
                                        <td class="text-center">
                                            @php
                                                $status = strtolower($doc->status);
                                                $isOverdueActive =
                                                    $status === 'aktif' &&
                                                    $doc->tanggal_akhir &&
                                                    \Carbon\Carbon::parse($doc->tanggal_akhir)->lt(\Carbon\Carbon::today());

                                                $statusColor = $isOverdueActive ? 'danger' : match ($status) {
                                                    'aktif' => 'success',
                                                    'digantikan' => 'warning',
                                                    'expired' => 'danger',
                                                    'tidak_diperpanjang' => 'dark',
                                                    default => 'secondary',
                                                };

                                                $statusLabel = $isOverdueActive ? 'Lewat Tanggal' : match ($status) {
                                                    'aktif' => 'Aktif',
                                                    'digantikan' => 'Digantikan',
                                                    'expired' => 'Expired',
                                                    'tidak_diperpanjang' => 'Tidak Diperpanjang',
                                                    default => ucwords(str_replace('_', ' ', $status)),
                                                };
                                            @endphp

                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        {{-- BERLAKU SAMPAI --}}
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($doc->tanggal_akhir)->format('d/m/Y') }}
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="text-center">
                                            <div class="aksi-wrap">

                                                {{-- VIEW (SELALU ADA) --}}
                                                <a href="{{ route('dokumen_legal.show', [$holding->holding_code, $doc->id]) }}"
                                                    class="btn btn-info btn-sm" title="Lihat">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>

                                                {{-- EDIT & DELETE HANYA UNTUK AKTIF --}}
                                                @if ($status === 'aktif')
                                                    <a href="{{ route('dokumen_legal.edit', [$holding->holding_code, $doc->id]) }}"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('dokumen_legal.destroy', [$holding->holding_code, $doc->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin hapus dokumen ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="mdi mdi-delete-forever"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Data belum tersedia
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            const $wrapper = $('#filter-wrapper');
            const $kategori = $('.js-kategori');
            const $penerbit = $('.js-penerbit');

            function initKategori() {
                $kategori.select2({
                    placeholder: 'Pilih Kategori',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $wrapper
                });
            }

            function initPenerbit() {
                $penerbit.select2({
                    placeholder: 'Pilih Penerbit',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $wrapper
                });
            }

            initKategori();
            initPenerbit();

            // ===============================
            // KATEGORI → PENERBIT
            // ===============================
            $kategori.on('change', function() {
                const kategoriId = $(this).val();

                $penerbit.select2('destroy');
                $penerbit
                    .empty()
                    .append('<option value="">Pilih Penerbit</option>');

                if (!kategoriId) {
                    initPenerbit();
                    return;
                }

                $.get(
                    "{{ url('/dokumen_legal/ajax/penerbit_by_kategori') }}/" + kategoriId,
                    function(data) {
                        data.forEach(item => {
                            $penerbit.append(
                                new Option(item.text, item.id, false, false)
                            );
                        });
                        initPenerbit();
                    }
                );
            });

        });
    </script>

    @if ($documents->count())
        <script>
            $(document).ready(function() {
                $('#table_dokumen_legal').DataTable({
                    pageLength: 25,
                    autoWidth: false,
                    responsive: false,
                    scrollX: false,
                    language: {
                        emptyTable: "Data belum tersedia"
                    },
                    columnDefs: [{
                            targets: [0, 3, 4, 5],
                            className: 'text-center'
                        },
                        {
                            targets: 6,
                            orderable: false
                        }
                    ]
                });
            });
        </script>
    @endif
@endsection
