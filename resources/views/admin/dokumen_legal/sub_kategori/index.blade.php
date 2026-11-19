@extends('admin.layouts.dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">

<style>
    /* Paksa tabel full mengikuti card */
    #table_sub_kategori {
        width: 100% !important;
    }

    div.dataTables_wrapper {
        width: 100%;
    }

    /* Biar kolom tidak mengecil */
    #table_sub_kategori th,
    #table_sub_kategori td {
        white-space: nowrap;
    }

    /* Lebar kolom (biar mirip dokumen legal) */
    #table_sub_kategori th:nth-child(1),
    #table_sub_kategori td:nth-child(1) {
        width: 180px;
    }

    #table_sub_kategori th:nth-child(3),
    #table_sub_kategori td:nth-child(3) {
        width: 170px;
        text-align: center;
    }

    /* Aksi button biar rapi */
    .btn-aksi {
        width: 38px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>
@endsection

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">SUB KATEGORI DOKUMEN LEGAL</h5>

            <a href="{{ route('dokumen_legal.kategori.kategori_sub.create', $holding->holding_code) }}" class="btn btn-primary">
                + TAMBAH
            </a>
        </div>

        <div class="card-body">
            <table id="table_sub_kategori" class="table table-bordered table-striped w-100">
                <thead class="table-primary">
                    <tr>
                        <th>ID SUB KATEGORI</th>
                        <th>NAMA SUB KATEGORI</th>
                        <th>KATEGORI</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($subKategoriDokumen as $doc)
                    <tr>
                        <td>{{ $doc->id_sub }}</td>
                        <td>{{ $doc->sub_name }}</td>
                        <td>{{ $doc->kategori->nama_kategori ?? '-' }}</td>

                        <td class="text-center">
                            {{-- VIEW --}}
                            <a href="{{ route('dokumen_legal.kategori.kategori_sub.show', [$holding->holding_code, $doc->id_sub]) }}"
                                class="btn btn-info btn-sm me-1" title="Lihat">
                                <i class="mdi mdi-eye"></i>
                            </a>

                            {{-- EDIT --}}
                            <a href="{{ route('dokumen_legal.kategori.kategori_sub.edit', [$holding->holding_code, $doc->id_sub]) }}"
                                class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="mdi mdi-pencil-outline"></i>
                            </a>

                            {{-- DELETE --}}
                            <form
                                action="{{ route('dokumen_legal.kategori.kategori_sub.destroy', [$holding->holding_code, $doc->id_sub]) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin hapus sub kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="mdi mdi-delete-forever"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>

<script>
    $(function() {
        $('#table_sub_kategori').DataTable({
            pageLength: 25,

            // ini yang bikin tabel tidak "ngecil"
            autoWidth: false,
            responsive: false,
            scrollX: false,

            columnDefs: [{
                    targets: 0,
                    width: 180
                }, // ID
                {
                    targets: 2,
                    width: 170,
                    orderable: false,
                    className: 'text-center'
                } // AKSI
            ],

            language: {
                emptyTable: "Data belum tersedia"
            }
        });
    });
</script>
@endsection