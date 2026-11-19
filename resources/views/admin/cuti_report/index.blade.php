@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <style>
        .cuti-report-tabs {
            display: flex;
            flex: 1 1 0;
            flex-direction: column;
            height: 0;
            min-height: 0;
        }

        .cuti-report-tabs>.tab-pane.active {
            display: flex;
            flex: 1 1 0;
            flex-direction: column;
            height: 100%;
            min-height: 0;
        }

        .cuti-report-tabs>.tab-pane.active>.master-table-area {
            flex: 1 1 0;
            height: 0;
            min-height: 0;
        }

        .cuti-report-tabs .master-table-area .dt-container>.dt-scroll {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .cuti-report-tabs .master-table-area .dt-scroll-head,
        .cuti-report-tabs .master-table-area .dt-scroll-foot {
            flex: 0 0 auto;
        }

        .cuti-report-tabs .master-table-area .dt-scroll-body {
            flex: 1 1 auto;
            min-height: 0;
            height: auto !important;
            max-height: none !important;
        }

        .cuti-report-filter {
            flex: 0 0 auto;
        }

        .cuti-report-empty {
            flex: 1 1 0;
            min-height: 0;
        }

        .cuti-report-empty:empty {
            display: none;
        }

        .cuti-report-empty>.alert {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            margin-bottom: 0;
        }

        .cuti-report-filter .card-body {
            padding: 0.75rem 0;
        }

        .cuti-report-filter .row {
            --bs-gutter-y: 0.5rem;
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
                    <div class="card-header master-report-toolbar">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-beach me-2"></i>LAPORAN CUTI</h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body master-table-card-content">

                        {{-- <hr class="my-3"> --}}
                        <input type="hidden" id="menu_id_add" value="">

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cuti-tab" data-bs-toggle="tab"
                                    data-bs-target="#cuti-tab-pane" type="button" role="tab"
                                    aria-controls="cuti-tab-pane" aria-selected="false">Form
                                    Cuti</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kuota-tab" data-bs-toggle="tab"
                                    data-bs-target="#kuota-tab-pane" type="button" role="tab"
                                    aria-controls="kuota-tab-pane" aria-selected="false">Filter Kuota</button>
                            </li>
                        </ul>
                        <div class="tab-content cuti-report-tabs" id="myTabContent">
                            <div class="tab-pane fade show active" id="cuti-tab-pane" role="tabpanel"
                                aria-labelledby="cuti-tab" tabindex="0">
                                <div id="collapseFilterWrapper" class="sticky-top bg-white cuti-report-filter"
                                    style="z-index: 1020;">
                                    <div class="card-body">
                                        <div class="row gy-4 align-items-end">
                                            <div class="col-lg-6 col-md-6col-sm-12">
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

                                            <div class="col-lg-2 col-md-12 col-sm-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-info w-100" id="btn_filter">
                                                    <i class="mdi mdi-filter-outline"></i>&nbsp;Filter
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div id="content_null" class="cuti-report-empty">
                                    <div class="alert alert-secondary" role="alert">
                                        <div class="alert-content text-center">
                                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">

                                                <rect x="25" y="25" width="70" height="70" rx="8"
                                                    ry="8" stroke="#CED4DA" stroke-width="2" />

                                                <path d="M35 35H85" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                                <path d="M35 45H75" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                                <path d="M35 55H85" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />

                                                <path d="M40 70L80 70" stroke="#7E8A9A" stroke-width="2"
                                                    stroke-dasharray="4 4" stroke-linecap="round" />
                                                <path d="M40 80L70 80" stroke="#7E8A9A" stroke-width="2"
                                                    stroke-dasharray="4 4" stroke-linecap="round" />

                                            </svg>
                                            <h4 class="alert-heading text-center">Tidak Ada Data</h4>
                                            <p class="text-center">Filter Data Terlebih Dahulu</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="table_cuti_div" class="master-table-area">
                                    <table class="table table-hover" id="table_cuti"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>PENGAJU</th>
                                                <th>NO.&nbsp;CUTI</th>
                                                <th>NAMA&nbsp;CUTI</th>
                                                <th>JABATAN&nbsp;PENGAJU</th>
                                                <th>DIBUAT&nbsp;PADA</th>
                                                <th>PERIODE&nbsp;CUTI</th>
                                                <th>DISETUJUI</th>
                                                <th>MENYETUJUI&nbsp;1</th>
                                                <th>WAKTU&nbsp;DISETUJUI&nbsp;1</th>
                                                <th>MENYETUJUI&nbsp;2</th>
                                                <th>WAKTU&nbsp;DISETUJUI&nbsp;2</th>
                                                <th>LIHAT</th>
                                                <th>KUOTA</th>
                                                <th>TERPAKAI</th>
                                                <th>SISA</th>
                                                {{-- <th>DETAIL</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 small">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- // filter --}}
                            <div class="tab-pane fade show" id="kuota-tab-pane" role="tabpanel"
                                aria-labelledby="kuota-tab" tabindex="0">
                                <div id="collapseFilterWrapper" class="sticky-top bg-white cuti-report-filter"
                                    style="z-index: 1020;">
                                    <div class="card-body">
                                        <div class="row gy-4 align-items-end">
                                            <div class="col-lg-6 col-md-6col-sm-12">
                                                <div class="form-floating form-floating-outline">
                                                    <div id="reportrange2" style="white-space: nowrap;">
                                                        <button class="btn btn-outline-secondary w-100 ">
                                                            <span class="fw-bold">FILTER&nbsp;DATE&nbsp;:&nbsp;</span>
                                                            <span class="date_daterange"></span>
                                                            <input type="date" id="start_date2" name="start_date2"
                                                                hidden value="">
                                                            <input type="date" id="end_date2" name="end_date2" hidden
                                                                value="">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-12 col-sm-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-info w-100" id="btn_filter2">
                                                    <i class="mdi mdi-filter-outline"></i>&nbsp;Filter
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div id="content_null2" class="cuti-report-empty">
                                    <div class="alert alert-secondary" role="alert">
                                        <div class="alert-content text-center">
                                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">

                                                <rect x="25" y="25" width="70" height="70" rx="8"
                                                    ry="8" stroke="#CED4DA" stroke-width="2" />

                                                <path d="M35 35H85" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                                <path d="M35 45H75" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                                <path d="M35 55H85" stroke="#E6E8EB" stroke-width="1.5"
                                                    stroke-linecap="round" />

                                                <path d="M40 70L80 70" stroke="#7E8A9A" stroke-width="2"
                                                    stroke-dasharray="4 4" stroke-linecap="round" />
                                                <path d="M40 80L70 80" stroke="#7E8A9A" stroke-width="2"
                                                    stroke-dasharray="4 4" stroke-linecap="round" />

                                            </svg>
                                            <h4 class="alert-heading text-center">Tidak Ada Data</h4>
                                            <p class="text-center">Filter Data Terlebih Dahulu</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="table_kuota_div" class="master-table-area">
                                    <table class="table table-hover" id="table_kuota"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>NO.&nbsp;IDENTITAS&nbsp;KARYAWAN</th>
                                                <th>NAMA&nbsp;KARYAWAN</th>
                                                <th>KUOTA</th>
                                                <th>TOTAL TERPAKAI</th>
                                                <th>TERPAKAI BY FILTER</th>
                                                <th>SISA</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0 small">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- // Filter End --}}
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
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = '{{ $holding->holding_code }}';
        var start = moment().startOf('month');
        var end = moment().endOf('month');
        // var start = moment('2025-07-21');
        // var end = moment('2025-08-21');
        var lstart, lend;

        function clearCutiReportToolbar(paneId) {
            document.querySelectorAll('[data-master-report-pane="' + paneId + '"]').forEach(function(control) {
                control.remove();
            });
        }


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
        $('#table_cuti_div').hide();



        $('#btn_filter').click(function(e) {

            $('#table_cuti_div').show();
            var start_date = $('#start_date').val() || '';
            var end_date = $('#end_date').val() || '';

            if ($.fn.DataTable.isDataTable('#table_cuti')) {
                clearCutiReportToolbar('cuti-tab-pane');
                $('#table_cuti').DataTable().destroy();
                $('#table_cuti tbody').empty();
            }

            console.log(start_date, end_date);
            $('#content_null').empty();
            var table = $('#table_cuti').DataTable({
                pageLength: 10,
                "scrollY": "200px",
                "scrollX": true,
                scrollCollapse: true,
                processing: true,
                serverSide: true,
                paginate: false,
                dom: 'Blfrtip',
                ajax: {
                    url: "{{ url('/report/dt_report_cuti') }}" + "/" + holding,
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
                    title: 'LAPORAN CUTI',
                    messageTop: 'Periode : ' + start_date + ' s/d ' + end_date,
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                            .getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                            .getSeconds();
                        return 'LAPORAN_CUTI_{{ $holding->holding_name }}_' + l + ' ' + n;
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
                        data: 'no_form_cuti',
                        name: 'no_form_cuti'
                    },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'jabatan_pengaju',
                        name: 'jabatan_pengaju'
                    },
                    // {
                    //     data: 'penempatan_kerja',
                    //     name: 'penempatan_kerja'
                    // },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'periode',
                        name: 'periode'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'nama_atasan',
                        name: 'nama_atasan'
                    },
                    {
                        data: 'waktu_approve',
                        name: 'waktu_approve'
                    },
                    {
                        data: 'nama_atasan2',
                        name: 'nama_atasan2'
                    },
                    {
                        data: 'waktu_approve2',
                        name: 'waktu_approve2'
                    },
                    {
                        data: 'lihat',
                        name: 'lihat'
                    },
                    {
                        data: 'kuota',
                        name: 'kuota'
                    },
                    {
                        data: 'terpakai',
                        name: 'terpakai'
                    },
                    {
                        data: 'sisa',
                        name: 'sisa'
                    },
                    // {
                    //     data: 'approved_by_name',
                    //     name: 'approved_by_name'
                    // },
                    // {
                    //     data: 'jabatan_penyetuju',
                    //     name: 'jabatan_penyetuju'
                    // },
                    // {
                    //     data: 'approved_at',
                    //     name: 'approved_at'
                    // },
                    // {
                    //     data: 'detail',
                    //     name: 'detail'
                    // },
                    // {
                    //     data: 'riwayat_pengubahan',
                    //     name: 'riwayat_pengubahan'
                    // },
                ],
                order: [
                    [5, 'desc']
                ]
            });
        });

        var start2 = moment().startOf('month');
        var end2 = moment().endOf('month');
        // var start = moment('2025-07-21');
        // var end = moment('2025-08-21');
        var lstart2, lend2;


        function cb(start2, end2) {
            lstart2 = start2.format('YYYY-MM-DD');
            lend2 = end2.format('YYYY-MM-DD');
            $('#start_date').val(lstart2);
            $('#end_date').val(lend2);
            $('#reportrange2 .date_daterange').html(start2.format('D MMMM YYYY') + ' - ' + end2.format('D MMMM YYYY'));
            // console.log(start, end);

        }

        $('#reportrange2').daterangepicker({
            startDate: start2,
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

        cb(start2, end2);
        $('#table_kuota_div').hide();

        $('#btn_filter2').click(function(e) {
            $('#table_kuota_div').show();
            var start_date2 = $('#start_date').val() || '';
            var end_date2 = $('#end_date').val() || '';
            if ($.fn.DataTable.isDataTable('#table_kuota')) {
                clearCutiReportToolbar('kuota-tab-pane');
                $('#table_kuota').DataTable().destroy();
                $('#table_kuota tbody').empty();
            }
            $('#content_null2').empty();

            var table2 = $('#table_kuota').DataTable({
                responsive: true,
                pageLength: 10,
                "scrollY": "200px",
                "scrollX": true,
                scrollCollapse: true,
                processing: true,
                serverSide: true,
                filter: true,
                paginate: false,
                dom: 'Blfrtip',
                ajax: {
                    url: "{{ url('/report/dt_laporan_kuota_cuti') }}" + "/" + holding,
                    data: {
                        start_date: start_date2,
                        end_date: end_date2,
                    }
                },
                buttons: [{

                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LAPORAN MAPPING SHIFT',
                    messageTop: 'KUOTA CUTI KARYAWAN',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                            .getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                            .getSeconds();
                        return 'KUOTA_CUTI_KARYAWAN' +
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
                        data: 'nomor_identitas_karyawan',
                        name: 'nomor_identitas_karyawan'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'kuota',
                        name: 'kuota'
                    },
                    {
                        data: 'total_terpakai',
                        name: 'total_terpakai'
                    },
                    {
                        data: 'terpakai',
                        name: 'terpakai'
                    },
                    {
                        data: 'sisa',
                        name: 'sisa'
                    },
                ],
                order: [
                    [2, 'asc']
                ]
            });
        });
    </script>
@endsection
