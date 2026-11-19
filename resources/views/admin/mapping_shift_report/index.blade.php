@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <style>
        .mapping-report-body,
        #table_shift_div,
        #content_null {
            min-height: 0;
        }

        .mapping-report-body {
            display: flex;
            flex-direction: column;
        }

        .mapping-report-filter,
        .mapping-report-divider,
        .mapping-shift-export {
            flex: 0 0 auto;
        }

        .mapping-shift-export {
            display: none;
        }

        #mapping-shift-export-control,
        #mapping-shift-export-control .dt-buttons,
        #mapping-shift-export-control .dt-button {
            width: 100%;
            margin: 0;
        }

        #mapping-shift-export-control .dt-button {
            min-height: 40px;
        }

        .mapping-report-filter > .card-body {
            padding: 0.75rem 1rem;
        }

        .mapping-report-controls {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            width: 100%;
        }

        .mapping-filter-date {
            flex: 1 1 360px;
            max-width: 500px;
        }

        .mapping-filter-action {
            flex: 0 0 110px;
        }

        #mapping-shift-export-column {
            flex: 0 0 140px;
        }

        #mapping-shift-table-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        #mapping-shift-table-controls .dt-length,
        #mapping-shift-table-controls .dt-search {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin: 0;
            white-space: nowrap;
        }

        #mapping-shift-table-controls .dt-search input {
            width: 170px;
            margin-left: 0 !important;
        }

        .mapping-report-body .master-table-area .dt-container > .dt-layout-row:first-child,
        .mapping-report-body .master-table-area .dt-container > .row:first-child {
            display: none !important;
        }

        @media (max-width: 1199.98px) {
            .mapping-report-controls {
                flex-wrap: wrap;
            }

            #mapping-shift-table-controls {
                width: 100%;
                margin-left: 0;
            }
        }

        #table_shift_div {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        #content_null {
            flex: 1 1 auto;
            overflow: auto;
        }

        #content_null:empty {
            display: none;
        }
    </style>
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-camera-switch me-2"></i>LAPORAN MAPPING SHIFT</h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body mapping-report-body">
                        {{-- <hr class="my-3 mapping-report-divider"> --}}
                        <div id="collapseFilterWrapper" class="sticky-top bg-white mapping-report-filter" style="z-index: 1020;">
                            <div class="card-body">
                                <div class="mapping-report-controls">
                                    <div class="mapping-filter-date">
                                        <div class="form-floating form-floating-outline">
                                            <div id="reportrange" style="white-space: nowrap;">
                                                <button class="btn btn-outline-secondary w-100 ">
                                                    <span class="fw-bold">FILTER&nbsp;DATE&nbsp;:&nbsp;</span>
                                                    <span class="date_daterange"></span>
                                                    <input type="date" id="start_date" name="start_date" hidden
                                                        value="">
                                                    <input type="date" id="end_date" name="end_date" hidden
                                                        value="">
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mapping-filter-action">
                                        <button type="button" class="btn btn-info w-100" id="btn_filter">
                                            <i class="mdi mdi-filter-outline"></i>&nbsp;Filter
                                        </button>
                                    </div>
                                    <div class="d-none"
                                        id="mapping-shift-export-column">
                                        <div id="mapping-shift-export-control"></div>
                                    </div>
                                    <div class="d-none" id="mapping-shift-table-controls">
                                        <div id="mapping-shift-length-control"></div>
                                        <div id="mapping-shift-search-control"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{-- <hr class="my-3 mapping-report-divider"> --}}
                        <div id="content_null">
                            <div class="alert alert-secondary" role="alert">
                                <div class="alert-content text-center">
                                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">

                                        <rect x="25" y="25" width="70" height="70" rx="8" ry="8"
                                            stroke="#CED4DA" stroke-width="2" />

                                        <path d="M35 35H85" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M35 45H75" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M35 55H85" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />

                                        <path d="M40 70L80 70" stroke="#7E8A9A" stroke-width="2" stroke-dasharray="4 4"
                                            stroke-linecap="round" />
                                        <path d="M40 80L70 80" stroke="#7E8A9A" stroke-width="2" stroke-dasharray="4 4"
                                            stroke-linecap="round" />

                                    </svg>
                                    <h4 class="alert-heading text-center">Tidak Ada Data</h4>
                                    <p class="text-center">Filter Data Terlebih Dahulu</p>
                                </div>
                            </div>
                        </div>
                        <div id="table_shift_div">
                            <div class="mapping-shift-export master-table-toolbar">
                            <table class="table table-hover" id="table_shift2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>LEGAL&nbsp;NUMBER</th>
                                        <th>JUDUL&nbsp;SHIFT</th>
                                        <th>PENGAJU</th>
                                        <th>JABATAN</th>
                                        <th>DIVSI</th>
                                        <th>BAGIAN</th>
                                        <th>DEPARTEMEN</th>
                                        <th>PENEMPATAN&nbsp;KERJA</th>
                                        <th>DIBUAT&nbsp;PADA</th>
                                        <th>TANGGAL&nbsp;MULAI</th>
                                        <th>TANGGAL&nbsp;BERAKHIR</th>
                                        <th>STATUS</th>
                                        <th>DISETUJUI&nbsp;OLEH</th>
                                        <th>JABATAN&nbsp;MENYETUJUI</th>
                                        <th>DISETUJUI&nbsp;PADA</th>
                                        <th>DETAIL</th>
                                        <th>PENGUBAHAN</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0 small">
                                </tbody>
                            </table>
                            </div>
                            <div class="master-table-area">
                            <table class="table table-hover" id="table_shift" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>LEGAL&nbsp;NUMBER</th>
                                        <th>JUDUL&nbsp;SHIFT</th>
                                        <th>PENGAJU</th>
                                        <th>JABATAN&nbsp;PENGAJU</th>
                                        <th>PENEMPATAN&nbsp;KERJA</th>
                                        <th>DIBUAT&nbsp;PADA</th>
                                        <th>TANGGAL&nbsp;MULAI</th>
                                        <th>TANGGAL&nbsp;BERAKHIR</th>
                                        <th>STATUS</th>
                                        <th>DISETUJUI&nbsp;OLEH</th>
                                        <th>JABATAN&nbsp;MENYETUJUI</th>
                                        <th>DISETUJUI&nbsp;PADA</th>
                                        <th>DETAIL</th>
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

    </div>
    <div class="modal fade" id="modal_daftar_shift" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Riwayat Edit Shift</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover" id="table_tukar" style="width: 100%; font-size: smaller;">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>ALASAN</th>
                                <th>LEGAL&nbsp;NUMBER</th>
                                <th>TANGGAL</th>
                                <th>DETAIL</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 small">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    {{-- <button type="submit" class="btn btn-primary">Save</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        var start = moment().startOf('month');
        var end = moment().endOf('month');
        // var start = moment('2025-07-21');
        // var end = moment('2025-08-21');
        var lstart, lend;


        function cb(start, end) {
            lstart = start.format('YYYY-MM-DD');
            lend = end.format('YYYY-MM-DD');
            $('#start_date').val(lstart);
            $('#end_date').val(lend);
            $('#reportrange .date_daterange').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            // console.log(start, end);

        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
        $('#table_shift_div').hide();
        $('#btn_filter').click(function(e) {
            $('#table_shift_div').show();
            var start_date = $('#start_date').val() || '';
            var end_date = $('#end_date').val() || '';

            console.log(start_date, end_date);

            $('#content_null').empty();
            // $('#table_shift').empty();
            let holding = '{{ $holding->holding_code }}';
            if ($.fn.DataTable.isDataTable('#table_shift')) {
                $('#table_shift').DataTable().destroy();
            }
            var table = $('#table_shift').DataTable({
                pageLength: 10,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/report/dt_laporan_shift') }}" + "/" + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                buttons: [{

                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LAPORAN MAPPING SHIFT',
                    messageTop: 'Bulan : ' + start_date + ' s/d ' + end_date,
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                            .getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                            .getSeconds();
                        return 'LAPORAN_REKRUTMEN_{{ $holding->holding_name }}_' +
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
                        data: 'legal_number_mapping',
                        name: 'legal_number_mapping'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'input_by_name',
                        name: 'input_by_name'
                    },
                    {
                        data: 'jabatan_pengaju',
                        name: 'jabatan_pengaju'
                    },
                    {
                        data: 'penempatan_kerja',
                        name: 'penempatan_kerja'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'approved',
                        name: 'approved'
                    },
                    {
                        data: 'approved_by_name',
                        name: 'approved_by_name'
                    },
                    {
                        data: 'jabatan_penyetuju',
                        name: 'jabatan_penyetuju'
                    },
                    {
                        data: 'approved_at',
                        name: 'approved_at'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    },
                    {
                        data: 'riwayat_pengubahan',
                        name: 'riwayat_pengubahan'
                    },
                ],
                order: [
                    [6, 'desc']
                ]
            });

            const lengthControl = $('#table_shift_wrapper .dt-length').detach();
            const searchControl = $('#table_shift_wrapper .dt-search').detach();
            $('#mapping-shift-length-control').empty().append(lengthControl);
            $('#mapping-shift-search-control').empty().append(searchControl);
            $('#mapping-shift-table-controls').removeClass('d-none');

            $('#table_shift2').hide();
            if ($.fn.DataTable.isDataTable('#table_shift2')) {
                $('#table_shift2').DataTable().destroy();
            }
            var table = $('#table_shift2').DataTable({


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
                    url: "{{ url('/report/dt_laporan_shift') }}" + "/" + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                buttons: [{

                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LAPORAN MAPPING SHIFT',
                    messageTop: '',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                            .getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                            .getSeconds();
                        return 'LAPORAN_REKRUTMEN_{{ $holding->holding_name }}_' +
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
                        data: 'legal_number_mapping',
                        name: 'legal_number_mapping'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'input_by_name',
                        name: 'input_by_name'
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'approved',
                        name: 'approved'
                    },
                    {
                        data: 'approved_by_name',
                        name: 'approved_by_name'
                    },
                    {
                        data: 'jabatan_penyetuju',
                        name: 'jabatan_penyetuju'
                    },
                    {
                        data: 'approved_at',
                        name: 'approved_at'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    },
                    {
                        data: 'riwayat_pengubahan',
                        name: 'riwayat_pengubahan'
                    },
                ],
                order: [
                    [6, 'desc']
                ]
            });

            const exportButtons = $('#table_shift2_wrapper .dt-buttons').detach();
            $('#mapping-shift-export-control').empty().append(exportButtons);
            $('#mapping-shift-export-column').removeClass('d-none');
        })
        $(document).on('click', '#btn_daftar_tukar', function() {
            var id = $(this).data('id');
            let holding = '{{ $holding->holding_code }}';

            // console.log(id);
            if ($.fn.DataTable.isDataTable('#table_tukar')) {
                $('#table_tukar').DataTable().destroy();
                $('#table_tukar tbody').empty(); // optional
            }


            var table3 = $('#table_tukar').DataTable({
                pageLength: 10,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/report/dt_laporan_tukar') }}" + "/" + holding + "/" + id,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'alasan',
                        name: 'alasan'
                    },
                    {
                        data: 'no_form',
                        name: 'no_form'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                    {
                        data: 'detail',
                        name: 'detail'
                    },
                ],
                order: [
                    [3, 'asc']
                ]
            });
            $('#modal_daftar_shift').modal('show');
        });
    </script>
@endsection
