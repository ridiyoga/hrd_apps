@extends('admin.layouts.dashboard')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">

    <style>
        #table_penerbit {
            width: 100% !important;
        }

        #table_penerbit th,
        #table_penerbit td {
            vertical-align: middle;
        }

        #table_penerbit th:nth-child(7),
        #table_penerbit td:nth-child(7) {
            width: 170px !important;
            min-width: 170px !important;
            white-space: nowrap !important;
            text-align: center;
        }

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
                        <h5 class="card-title mb-0">MASTER PENERBIT DOKUMEN</h5>
                        <a href="{{ route('dokumen_legal.penerbit.create', $holding->holding_code) }}"
                            class="btn btn-primary">
                            + Tambah
                        </a>
                    </div>

                    {{-- BODY --}}
                    <div class="card-body">

                        <table id="table_penerbit" class="table table-bordered table-striped w-100">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Nama Penerbit</th>
                                    <th>Jenis</th>
                                    <th>Kategori Dokumen</th>
                                    <th>PIC</th>
                                    <th>No. PIC</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($penerbitDokumen as $row)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>

                                        <td>
                                            <strong>{{ $row->nama_penerbit }}</strong>
                                            @if ($row->alamat_penerbit)
                                                <div class="text-muted" style="font-size: 12px;">
                                                    {{ $row->alamat_penerbit }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @php
                                                $jenis = strtolower($row->jenis_penerbit);
                                                $color = match ($jenis) {
                                                    'internal' => 'info',
                                                    'eksternal', 'external' => 'warning',
                                                    'pemerintah', 'government' => 'success',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($row->jenis_penerbit) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $row->kategoriDokumen->nama_kategori ?? '-' }}
                                        </td>

                                        <td>{{ $row->nama_pic_penerbit ?? '-' }}</td>
                                        <td>{{ $row->nomor_pic_penerbit ?? '-' }}</td>

                                        {{-- AKSI --}}
                                        <td class="text-center">
                                            <div class="aksi-wrap">

                                                <a href="{{ route('dokumen_legal.penerbit.show', [$holding->holding_code, $row->id_penerbit]) }}"
                                                    class="btn btn-info btn-sm" title="Lihat">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>

                                                <a href="{{ route('dokumen_legal.penerbit.edit', [$holding->holding_code, $row->id_penerbit]) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </a>

                                                <form
                                                    action="{{ route('dokumen_legal.penerbit.destroy', [$holding->holding_code, $row->id_penerbit]) }}"
                                                    method="POST" onsubmit="return confirm('Yakin hapus penerbit ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="mdi mdi-delete-forever"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Data penerbit belum tersedia
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

    @if ($penerbitDokumen->count())
        <script>
            $(document).ready(function() {
                $('#table_penerbit').DataTable({
                    pageLength: 25,
                    autoWidth: false,
                    responsive: false,
                    scrollX: false,
                    language: {
                        emptyTable: "Data belum tersedia"
                    },
                    columnDefs: [{
                            targets: [0, 2, 5, 6],
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
