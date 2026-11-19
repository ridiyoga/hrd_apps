@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <style>
        .mapping-detail-info,
        .mapping-detail-divider,
        .mapping-detail-controls,
        .mapping-detail-export {
            flex: 0 0 auto;
        }

        .mapping-detail-info {
            display: flex;
            align-items: center;
            gap: 0.75rem 1.5rem;
            width: 100%;
            font-size: 0.8rem;
        }

        .mapping-detail-info-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            min-width: 0;
        }

        .mapping-detail-info-label {
            color: #8a8d93;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .mapping-detail-info-value {
            color: #566a7f;
            white-space: nowrap;
        }

        .mapping-detail-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        #mapping-detail-export-control .dt-buttons,
        #mapping-detail-export-control .dt-button {
            margin: 0;
        }

        #mapping-detail-length-control .dt-length,
        #mapping-detail-search-control .dt-search {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin: 0;
            white-space: nowrap;
        }

        #mapping-detail-search-control {
            margin-left: auto;
        }

        #mapping-detail-search-control input {
            width: 190px;
            margin-left: 0 !important;
        }

        .mapping-detail-export {
            display: none;
        }

        .master-table-area .dt-container>.dt-layout-row:first-child,
        .master-table-area .dt-container>.row:first-child {
            display: none !important;
        }

        @media (max-width: 991.98px) {

            .mapping-detail-info,
            .mapping-detail-controls {
                flex-wrap: wrap;
            }

            #mapping-detail-search-control {
                margin-left: 0;
            }
        }
    </style>
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-camera-switch me-2"></i>DETAIL LAPORAN MAPPING SHIFT
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">
                        </div>

                    </div>
                    <div class="card-body">
                        <hr class="my-2 mapping-detail-divider">
                        <div class="mapping-detail-info">
                            <div class="mapping-detail-info-item">
                                <span class="mapping-detail-info-label">PEMBUAT</span>
                                <span class="mapping-detail-info-value">: {{ $head->input_by_name }}</span>
                            </div>
                            <div class="mapping-detail-info-item">
                                <span class="mapping-detail-info-label">NO. LEGAL</span>
                                <span class="mapping-detail-info-value">: {{ $head->legal_number_mapping }}</span>
                            </div>
                            <div class="mapping-detail-info-item">
                                <span class="mapping-detail-info-label">TANGGAL MULAI</span>
                                <span class="mapping-detail-info-value">: <i
                                        class="mdi mdi-calendar-clock me-1 text-muted"></i>{{ $head->start_date }}</span>
                            </div>
                            <div class="mapping-detail-info-item">
                                <span class="mapping-detail-info-label">TANGGAL AKHIR</span>
                                <span class="mapping-detail-info-value">: <i
                                        class="mdi mdi-calendar-check me-1 text-muted"></i>{{ $head->end_date }}</span>
                            </div>
                        </div>
                        <hr class="my-2 mapping-detail-divider">
                        <div class="mapping-detail-controls">
                            <div id="mapping-detail-export-control"></div>
                            <div id="mapping-detail-length-control"></div>
                            <div id="mapping-detail-search-control"></div>
                        </div>
                        <div class="mapping-detail-export master-table-toolbar">
                            <table class="table table-hover" id="table_detail_shift2"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>NAMA&nbsp;KARYAWAN</th>
                                        <th>HARI</th>
                                        <th>TANGGAL&nbsp;MASUK</th>
                                        <th>SHIFT</th>
                                        <th>JAM&nbsp;MASUK</th>
                                        <th>JAM&nbsp;PULANG</th>
                                        <th>JABATAN</th>
                                        <th>DIVISI</th>
                                        <th>BAGIAN</th>
                                        <th>DEPARTEMEN</th>
                                        <th>PENEMPATAN&nbsp;KERJA</th>
                                        <th>PENGUBAHAN</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0 small">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                            <table class="table table-hover" id="table_detail_shift"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>NAMA&nbsp;KARYAWAN</th>
                                        <th>HARI</th>
                                        <th>TANGGAL&nbsp;MASUK</th>
                                        <th>SHIFT</th>
                                        <th>JAM&nbsp;MASUK</th>
                                        <th>JAM&nbsp;PULANG</th>
                                        <th>JABATAN&nbsp;KARYAWAN</th>
                                        <th>PENEMPATAN&nbsp;KERJA</th>
                                        <th>PENGUBAHAN</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0 small">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = '{{ $holding->holding_code }}';
        let id = '{{ $id }}';
        var table = $('#table_detail_shift').DataTable({
            pageLength: 25,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            // dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/report/dt_laporan_detail_shift') }}" + "/" + holding + "/" + id,

            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
                {
                    data: 'hari_masuk',
                    name: 'hari_masuk'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'jam_masuk',
                    name: 'jam_masuk'
                },
                {
                    data: 'jam_keluar',
                    name: 'jam_keluar'
                },
                {
                    data: 'jabatan_karyawan',
                    name: 'jabatan_karyawan'
                },
                {
                    data: 'penempatan_kerja',
                    name: 'penempatan_kerja'
                },
                {
                    data: 'pengubahan',
                    name: 'pengubahan'
                },
            ],
            order: [
                [3, 'ASC']
            ]
        });

        const detailLengthControl = $('#table_detail_shift_wrapper .dt-length').detach();
        const detailSearchControl = $('#table_detail_shift_wrapper .dt-search').detach();
        $('#mapping-detail-length-control').append(detailLengthControl);
        $('#mapping-detail-search-control').append(detailSearchControl);

        $('#table_detail_shift2').hide();
        var table2 = $('#table_detail_shift2').DataTable({
            // pageLength: 25,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/report/dt_laporan_detail_shift') }}" + "/" + holding + "/" + id,

            },
            buttons: [{

                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'LAPORAN MAPPING SHIFT',
                messageTop: '{{ $head->input_by_name }}, NOMOR LEGAL : {{ $head->legal_number_mapping }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                        .getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                        .getSeconds();
                    return 'LAPORAN_MAPPING_SHIFT_{{ $holding->holding_name }}_' +
                        l + ' ' + n;
                },
            }],


            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
                {
                    data: 'hari_masuk',
                    name: 'hari_masuk'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'jam_masuk',
                    name: 'jam_masuk'
                },
                {
                    data: 'jam_keluar',
                    name: 'jam_keluar'
                },
                {
                    data: 'jabatan',
                    name: 'jabatan'
                },
                {
                    data: 'divisi',
                    name: 'divisi'
                },
                {
                    data: 'bagian',
                    name: 'bagian'
                },
                {
                    data: 'departemen',
                    name: 'departemen'
                },
                {
                    data: 'penempatan_kerja',
                    name: 'penempatan_kerja'
                },
                {
                    data: 'pengubahan',
                    name: 'pengubahan'
                },
            ],
            order: [
                [3, 'ASC']
            ]
        });

        const detailExportControl = $('#table_detail_shift2_wrapper .dt-buttons').detach();
        $('#mapping-detail-export-control').append(detailExportControl);
    </script>
@endsection
