@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <!-- FixedColumns extension -->
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/content_filter.css') }}">
    <style type="text/css">
        /* ukuran teks di area pilihan (input select2) */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem !important;
            /* Bootstrap small (14px) */
            min-height: calc(1.5em + 0.75rem + 2px);
            /* biar tinggi konsisten */
        }

        .table .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        /* ===============================
                                                                                                                                           TABLE LOADING OVERLAY
                                                                                                                                            ================================ */
        .table-loading {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        /* Spinner modern */
        .spinner {
            width: 36px;
            height: 36px;
            border: 4px solid #e5e7eb;
            border-top-color: #7367f0;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ukuran teks di dropdown list */
        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 0.875rem !important;
        }

        /* Fokus warna primary */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25) !important;
        }

        /* Background dan teks saat option terpilih */
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        /* Hover option */
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
            color: var(--bs-primary) !important;
        }

        /* ukuran huruf untuk pilihan yang sudah dipilih (tag dalam box) */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            font-size: 0.75rem;
            /* kecilin text */
            padding: 2px 6px;
            /* biar nggak terlalu tinggi */
            line-height: 1.2;
        }

        /* icon "x" di tag */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            font-size: 0.7rem;
            margin-right: 2px;
        }

        /* tulisan placeholder / hasil render */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            font-size: 0.8rem;
        }

        /* ===============================
                                                                                                                                            SELECT LOADING
                                                                                                                                            ================================ */
        .select-loader {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            z-index: 5;
            pointer-events: none;
        }

        /* ===============================
                                                                                                                                            DOT LOADER (3 DOT)
                                                                                                                                            ================================ */
        .dot-loader {
            display: flex;
            gap: 4px;
        }

        .dot-loader span {
            width: 6px;
            height: 6px;
            background-color: #7367f0;
            border-radius: 50%;
            opacity: 0.3;
            animation: dotPulse 1.2s infinite ease-in-out;
        }

        .dot-loader span:nth-child(1) {
            animation-delay: 0s;
        }

        .dot-loader span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot-loader span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotPulse {
            0% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.3;
            }
        }

        .discipline-filter-panel {
            padding: 1.25rem 1.5rem;
        }

        .discipline-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 1.5rem 1.25rem;
        }

        .discipline-filter-grid+.discipline-filter-grid {
            margin-top: 1.5rem;
        }

        .discipline-filter-field,
        .discipline-filter-date,
        .discipline-filter-action {
            min-width: 0;
        }

        .discipline-filter-date {
            grid-column: span 2;
        }

        .discipline-filter-action {
            align-self: stretch;
            display: flex;
            align-items: stretch;
        }

        .discipline-filter-grid .select2-container {
            width: 100% !important;
        }

        .discipline-filter-grid .form-floating-outline,
        .discipline-filter-grid #reportrange,
        .discipline-filter-grid #reportrange button,
        .discipline-filter-grid #btn_filter {
            width: 100%;
        }

        .discipline-filter-grid #reportrange button {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1199.98px) {
            .discipline-filter-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .discipline-filter-panel {
                padding: 1rem;
            }

            .discipline-filter-grid {
                grid-template-columns: 1fr;
            }

            .discipline-filter-date {
                grid-column: auto;
            }
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card" style="height: 100vh;">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="card-title m-0 me-2">REPORT KEDISIPLINAN KARYAWAN</h6>
                        </div>
                    </div>

                    <div class="card-body card-body-table-report p-0"
                        style="height: calc(1000vh - 60px); overflow-y: auto; position: relative;">
                        <!-- <div class="card-body p-0" style="height: calc(1000vh - 60px); overflow-y: auto;"> -->
                        <!-- Filter selalu di atas -->
                        <div id="collapseFilterWrapper" class="sticky-top bg-white" style="z-index: 1020;">
                            <div class="discipline-filter-panel">

                                <div class="discipline-filter-grid">
                                    <div class="discipline-filter-field">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="departemen_filter[]"
                                                id="departemen_filter" multiple>
                                                <option disabled value="">-Pilih Departemen-</option>
                                                @foreach ($departemen as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="departemen_filter">Departemen</label>
                                        </div>
                                    </div>
                                    <div class="discipline-filter-field">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="divisi_filter[]"
                                                id="divisi_filter" multiple>
                                                <option selected disabled value="">-- Pilih Divisi --</option>
                                            </select>
                                            <label for="divisi_filter">Divisi</label>
                                            <!-- loader -->
                                            <div class="select-loader d-none">
                                                <div class="dot-loader">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="discipline-filter-field">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="bagian_filter[]"
                                                id="bagian_filter" multiple>
                                                <option selected disabled value="">-- Pilih Bagian --</option>
                                            </select>
                                            <label for="bagian_filter">Bagian</label>
                                            <!-- loader -->
                                            <div class="select-loader d-none">
                                                <div class="dot-loader">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="discipline-filter-field">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="jabatan_filter[]"
                                                id="jabatan_filter" multiple>
                                                <option selected disabled value="">-- Pilih Jabatan --</option>
                                            </select>
                                            <label for="jabatan_filter">Jabatan</label>
                                            <!-- loader -->
                                            <div class="select-loader d-none">
                                                <div class="dot-loader">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="discipline-filter-grid">
                                    <div class="discipline-filter-field">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="shift_filter[]"
                                                id="shift_filter" multiple>
                                                <option disabled value="">-Pilih Shift-</option>
                                                <option value="SHIFT">SHIFT</option>
                                                <option value="NON SHIFT">NON SHIFT</option>
                                            </select>
                                            <label for="shift_filter">Shift</label>
                                        </div>
                                    </div>
                                    <div class="discipline-filter-date">
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
                                    <div class="discipline-filter-action">
                                        <button type="button" class="btn btn-block w-100" id="btn_filter">
                                            <i class="mdi mdi-filter-outline text-primary"></i><span
                                                class="text-primary">&nbsp;Filter</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="content-scroll p-3">
                            <!-- Table rekap -->
                            <div class="mt-4">
                                <div id="content_null">
                                    <div class="alert alert-secondary" role="alert">
                                        <div class="alert-content text-center">
                                            <svg width="120" height="120" class="text-center" viewBox="0 0 120 120"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">

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
                            </div>
                            <div id="tableLoading" class="table-loading d-none">
                                <div class="loading-content">
                                    <div class="spinner"></div>
                                    <span>Memuat data...</span>
                                </div>
                            </div>
                            <table class="table table-bordered table-hover table-striped nowrap" id="table_rekapdata"
                                style=" width: 100%; font-size: small; ">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <!-- jQuery (kalau belum ada di layout) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Dependencies EXPORT (WAJIB SEBELUM buttons.html5) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Buttons (match DataTables 1.13.x on this page) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Plugin lain -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="{{ asset('assets/assets_users/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>


    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy_sub').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);

            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('menu_id', menu_id);
            // console.log(formData);

            $.ajax({
                type: "POST",

                url: "{{ url('/recruitment/update_menu') }}",
                data: formData,
                contentType: false,
                processData: false,
                error: function() {
                    alert('Something is wrong!');
                    // console.log(formData);
                },
            });

            var holding = '{{ $holding->holding_code }}';
            var holding_id = '{{ $holding->id }}';
            var chart_absensi_masuk;
            var data_column;
            let table_rekapdata;

            var start = moment().startOf('month');
            var end = moment().endOf('month');
            var lstart, lend;
            console.log(start, end);

            function cb(start, end) {
                $('#reportrange .date_daterange').html(start.format('D MMMM YYYY') + ' - ' + end.format(
                    'D MMMM YYYY'));
                lstart = start.format('YYYY-MM-DD');
                lend = end.format('YYYY-MM-DD');
                $('#start_date').val(lstart);
                $('#end_date').val(lend);
                // console.log(start, end);

            }

            function showTableLoading() {
                $('#tableLoading').removeClass('d-none');
            }

            function hideTableLoading() {
                $('#tableLoading').addClass('d-none');
            }

            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .addClass('d-none');
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

            $('#btn_filter').click(function(e) {
                // ambil langsung dari form
                var departemen_filter = $('#departemen_filter').val() || [];
                var departemen_filter = $('#departemen_filter').val() || [];
                var divisi_filter = $('#divisi_filter').val() || [];
                var bagian_filter = $('#bagian_filter').val() || [];
                var jabatan_filter = $('#jabatan_filter').val() || [];
                var shift_filter = $('#shift_filter').val() || [];
                var start_date = $('#start_date').val() || '';
                var end_date = $('#end_date').val() || '';
                // console.log(start_date, end_date);
                //console.log(lstart, lend, departemen_filter, divisi_filter, bagian_filter, jabatan_filter, start, end);
                $.ajax({
                    url: "{{ url('report/absensi/kedisiplinan/get_columns') }}" + '/' + holding,
                    method: "get",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        departemen_filter: departemen_filter,
                        divisi_filter: divisi_filter,
                        bagian_filter: bagian_filter,
                        jabatan_filter: jabatan_filter,
                        shift_filter: shift_filter,
                    },
                    success: function(data) {
                        // console.log(data);
                        datacolumn = [{
                                data: 'id',
                                render: function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                data: 'btn_detail',
                                name: 'btn_detail'
                            },
                            {
                                data: 'nama_karyawan',
                                name: 'nama_karyawan'
                            },
                            {
                                data: 'nomor_identitas_karyawan',
                                name: 'nomor_identitas_karyawan'
                            },
                            {
                                data: 'departemen',
                                name: 'departemen'
                            },
                            {
                                data: 'divisi',
                                name: 'divisi'
                            },
                            {
                                data: 'jabatan',
                                name: 'jabatan'
                            },
                            {
                                data: 'shift',
                                name: 'shift'
                            },
                            {
                                data: 'total_datang_lebih_awal',
                                name: 'total_datang_lebih_awal'
                            },
                            {
                                data: 'total_hadir_tepat_waktu',
                                name: 'total_hadir_tepat_waktu'
                            },
                            {
                                data: 'total_hadir_telat_hadir',
                                name: 'total_hadir_telat_hadir'
                            },
                            {
                                data: 'total_hadir_telat_hadir1',
                                name: 'total_hadir_telat_hadir1'
                            },
                            {
                                data: 'tidak_presensi_masuk',
                                name: 'tidak_presensi_masuk'
                            },
                            {
                                data: 'total_pulang_cepat',
                                name: 'total_pulang_cepat'
                            },
                            {
                                data: 'total_overtime_pulang',
                                name: 'total_overtime_pulang'
                            },
                            {
                                data: 'tidak_presensi_pulang',
                                name: 'tidak_presensi_pulang'
                            },
                            {
                                data: 'total_hadir',
                                name: 'total_hadir'
                            },
                            {
                                data: 'total_izin_true',
                                name: 'total_izin_true'
                            },
                            {
                                data: 'total_cuti_true',
                                name: 'total_cuti_true'
                            },
                            {
                                data: 'total_cuti_non',
                                name: 'total_cuti_non'
                            },
                            {
                                data: 'total_tidak_masuk',
                                name: 'total_tidak_masuk'
                            },
                            {
                                data: 'total_dinas_true',
                                name: 'total_dinas_true'
                            },
                            {
                                data: 'tukar_libur',
                                name: 'tukar_libur'
                            },
                            {
                                data: 'total_libur',
                                name: 'total_libur'
                            },
                            {
                                data: 'tidak_hadir_kerja',
                                name: 'tidak_hadir_kerja'
                            },
                            {
                                data: 'total_tidak_hadir',
                                name: 'total_tidak_hadir'
                            },
                            {
                                data: 'total_semua',
                                name: 'total_semua'
                            },
                        ]
                        const data_column = datacolumn.concat(data.datacolumn);
                        // console.log(data_column);
                        // 1. Destroy DataTable dulu kalau sudah ada
                        if ($.fn.DataTable.isDataTable('#table_rekapdata')) {
                            $('#table_rekapdata').DataTable().clear().destroy();
                        }

                        $('#content_null').empty();
                        $('#table_rekapdata').empty();
                        $('#table_rekapdata').append(
                            '<thead class="text-center  align-middle" style="font-weight: bold; white-space: nowrap;"><tr><th rowspan="3" class="text-center">No.</th><th rowspan="3" class="text-center">Action</th><th rowspan="3" class="text-center">Nama&nbsp;Karyawan</th><th rowspan="3" class="text-center">ID&nbsp;Karyawan</th><th rowspan="3" clas="text-center">Departemen</th><th rowspan="3" clas="text-center">Divisi</th><th rowspan="3" clas="text-center">Jabatan</th><th rowspan="3" class="text-center">Shift</th><th colspan="19" class="text-center">&nbsp;PRESENSI&nbsp;</th><th class="text-center th_count_date1">Detail&nbsp;Per&nbsp;Tanggal</th></tr><tr><th colspan="5" class="text-center">Masuk</th><th colspan="3" class="text-center">Pulang</th><th rowspan="2">Total&nbsp;Hadir</th><th colspan="5" class="text-center">Keterangan</th><th rowspan="2">Tukar&nbsp;Libur</th><th rowspan="2">Libur</th><th rowspan="2">ALFA</th><th rowspan="2">Total&nbsp;Tidak&nbsp;Hadir</th><th rowspan="2">Total&nbsp;Hari</th><th class="text-center th_count_date2">Tanggal</th></tr><tr id="date_absensi"></tr></thead><tbody class="text-center align-middle"></tbody>'
                        );
                        $('#date_absensi').empty();
                        // kolom absensi summary (misalnya 8 kolom)
                        $('#date_absensi')
                            .append('<th>Datang&nbsp;Lebih&nbsp;Cepat</th>')
                            .append('<th>Tepat&nbsp;Waktu</th>')
                            .append('<th>Telat&nbsp;(-15&nbsp;Menit)</th>')
                            .append('<th>Telat&nbsp;(+15&nbsp;Menit)</th>')
                            .append('<th>Tidak&nbsp;Presensi&nbsp;Masuk</th>')
                            .append('<th>Pulang&nbsp;Cepat</th>')
                            .append('<th>Overtime&nbsp;Pulang</th>')
                            .append('<th>Tidak&nbsp;Presensi&nbsp;Pulang</th>')
                            .append('<th>&nbsp;Sakit&nbsp;</th>')
                            .append('<th>&nbsp;Cuti&nbsp;Tahunan</th>')
                            .append('<th>&nbsp;Cuti&nbsp;Non&nbsp;Tahunan</th>')
                            .append('<th>&nbsp;Izin&nbsp;Tidak&nbsp;Masuk&nbsp;</th>')
                            .append('<th>&nbsp;Dinas&nbsp;</th>');

                        // kolom tanggal dinamis dari backend
                        // console.log(data.data_columns_header);
                        $('.th_count_date2').attr('colspan', data.count_period);
                        $('.th_count_date1').attr('colspan', data.count_period);
                        $.each(data.data_columns_header, function(i, col) {
                            $('#date_absensi').append('<th class="text-center">' + col
                                .header + '</th>');
                        });
                        // console.log('Header baris kedua sekarang:');
                        // console.log(data.count_period);


                        // console.log(departemen_filter, divisi_filter, bagian_filter, jabatan_filter, shift_filter, start_date, end_date, data_column);
                        load_data(departemen_filter, divisi_filter, bagian_filter,
                            jabatan_filter, shift_filter, start_date, end_date, data_column);
                    },
                    error: function(data) {
                        var errors = data.errors();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errors,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                        });
                    }
                });



            })
            $('#departemen_filter').change(function(e) {
                departemen_filter_dept = $(this).val() || '';
                // console.log(departemen_filter_dept);
                var url = "{{ url('report/absensi/kedisiplinan/get_divisi') }}" + '/' + holding_id;
                // console.log(departemen_filter_dept, divisi_filter_dept, bagian_filter_dept, jabatan_filter_dept);
                showSelectLoading('#divisi_filter');
                showSelectLoading('#bagian_filter');
                showSelectLoading('#jabatan_filter');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        holding: holding_id,
                        departemen_filter: departemen_filter_dept,
                    },
                    cache: false,

                    success: function(data_dept) {

                        // console.log(departemen_filter_dept, divisi_filter_dept, bagian_filter_dept, jabatan_filter_dept);
                        $('#divisi_filter').html(data_dept.select);
                        $('#bagian_filter').html('<option value="">Pilih Bagian</option>');
                        $('#jabatan_filter').html('<option value="">Pilih Jabatan</option>');
                        // refresh select2 biar dropdown kebaca data baru
                        // destroy & init ulang
                        let isOpen = $('#divisi_filter').data('select2') && $('#divisi_filter')
                            .data('select2').isOpen();

                        $('#divisi_filter').html(data_dept.select).trigger('change.select2');
                        // $('#bagian_filter').html(data_dept.select).trigger('change.select2');
                        // $('#jabatan_filter').html(data_dept.select).trigger('change.select2');
                        hideSelectLoading('#divisi_filter');
                        // hideSelectLoading('#bagian_filter');
                        // hideSelectLoading('#jabatan_filter');
                        // langsung pilih opsi pertama kalau ada
                        let firstOpt = $('#divisi_filter option:eq(0)').val();
                        if (firstOpt) {
                            $('#divisi_filter').val(firstOpt).trigger('change');
                        }
                        if (isOpen) {
                            $('#divisi_filter').select2('open');
                        }

                    },

                    error: function(data) {
                        Swal.close();
                        hideSelectLoading('#divisi_filter');
                        hideSelectLoading('#bagian_filter');
                        hideSelectLoading('#jabatan_filter');
                        // console.log('error:', data)
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.responseJSON.message,
                            timer: 4200,
                            showConfirmButton: false,
                        })
                    },

                })
            })
            $('#divisi_filter').change(function() {
                divisi_filter = $(this).val() || '';
                showSelectLoading('#bagian_filter');
                showSelectLoading('#jabatan_filter');
                // $('#table_rekapdata').DataTable().destroy();
                $.ajax({
                    type: 'GET',
                    url: "{{ url('report/absensi/kedisiplinan/get_bagian') }}" + '/' + holding_id,
                    data: {
                        holding: holding_id,
                        divisi_filter: divisi_filter,
                    },
                    cache: false,

                    success: function(data_divisi) {
                        // console.log(data_divisi);
                        $('#bagian_filter').html(data_divisi.select);
                        $('#jabatan_filter').html('<option value="">Pilih Jabatan..</option>');
                        let isOpen = $('#bagian_filter').data('select2') && $('#bagian_filter')
                            .data('select2').isOpen();

                        $('#bagian_filter').html(data_divisi.select).trigger('change.select2');
                        // $('#jabatan_filter').html(data_divisi.select).trigger('change.select2');

                        hideSelectLoading('#bagian_filter');
                        // hideSelectLoading('#jabatan_filter');
                        // langsung pilih opsi pertama kalau ada
                        let firstOpt = $('#bagian_filter option:eq(0)').val();
                        if (firstOpt) {
                            $('#bagian_filter').val(firstOpt).trigger('change');
                        }
                        if (isOpen) {
                            $('#bagian_filter').select2('open');
                        }

                    },
                    error: function(data) {
                        Swal.close();
                        hideSelectLoading('#bagian_filter');
                        hideSelectLoading('#jabatan_filter');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.responseJSON.message,
                            timer: 4200,
                            showConfirmButton: false,
                        })
                    },

                })
            })
            $('#bagian_filter').change(function() {
                bagian_filter = $(this).val() || '';
                showSelectLoading('#jabatan_filter');
                // $('#table_rekapdata').DataTable().destroy();
                $.ajax({
                    type: 'GET',
                    url: "{{ url('report/absensi/kedisiplinan/get_jabatan') }}" + '/' + holding_id,
                    data: {
                        holding: holding_id,
                        bagian_filter: bagian_filter
                    },
                    cache: false,

                    success: function(data_jabatan) {

                        $('#jabatan_filter').html(data_jabatan.select);
                        let isOpen = $('#jabatan_filter').data('select2') && $(
                            '#jabatan_filter').data('select2').isOpen();

                        $('#jabatan_filter').html(data_jabatan.select).trigger(
                            'change.select2');
                        hideSelectLoading('#jabatan_filter');
                        // langsung pilih opsi pertama kalau ada
                        let firstOpt = $('#jabatan_filter option:eq(0)').val();
                        if (firstOpt) {
                            $('#jabatan_filter').val(firstOpt).trigger('change');
                        }
                        if (isOpen) {
                            $('#jabatan_filter').select2('open');
                        }
                    },
                    error: function(data) {
                        Swal.close();
                        hideSelectLoading('#jabatan_filter');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.responseJSON.message,
                            timer: 4200,
                            showConfirmButton: false,
                        })
                    },

                })
            })

            $('#departemen_filter').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Departemen",
                width: '100%',
                allowClear: true
            });
            $('#divisi_filter').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Divisi",
                width: '100%',
                allowClear: true
            });
            $('#bagian_filter').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Bagian",
                width: '100%',
                allowClear: true
            });
            $('#jabatan_filter').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Jabatan",
                width: '100%',
                allowClear: true
            });
            $('#shift_filter').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Shift",
                width: '100%',
                allowClear: true
            });

            function load_data(departemen_filter = '', divisi_filter = '', bagian_filter = '', jabatan_filter = '',
                shift_filter = '', start_date = '', end_date = '', data_column = '') {
                // console.log("Jumlah TH terakhir:", $('#table_rekapdata thead tr:last th').length);
                // console.log("Jumlah column DataTable:", data_column.length);

                // $('#table_rekapdata thead tr:last th').each(function(i, th) {
                // console.log(i, $(th).text().trim());
                // });
                var table_rekapdata = $('#table_rekapdata').DataTable({
                    "scrollY": true,
                    "scrollX": true,
                    processing: true,
                    autoWidth: false,
                    serverSide: true,
                    deferRender: true,
                    pageLength: -1,
                    scrollCollapse: true,
                    scrollX: true,
                    fixedColumns: {
                        leftColumns: 3
                    },
                    fixedHeader: {
                        header: true,
                        headerContainer: '.card-body-table-report' // opsional, bantu sinkronisasi
                    },
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    destroy: true,
                    dom: "<'row mb-2'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [{
                            extend: 'excelHtml5',
                            className: 'btn btn-sm btn-outline-light',
                            text: '<i class="mdi mdi-file-excel text-success"></i><span class="text-success">&nbsp;Excel</span>',
                            titleAttr: 'Export ke Excel',
                            title: 'DATA ABSENSI & KEDISIPLINAN KARYAWAN',
                            messageTop: function() {
                                return 'Periode: ' + start_date + ' s/d ' + end_date;
                            },
                            exportOptions: {
                                columns: ':not(:first-child)'
                            },
                            filename: function() {
                                var d = new Date();
                                var date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                                    .getDate();
                                var time = d.getHours() + '-' + d.getMinutes() + '-' + d
                                    .getSeconds();
                                return 'LAPORAN_ABSENSI_&_KEDISIPLINAN_KARYAWAN_{{ $holding->holding_name }}_' +
                                    date + '_' + time;
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn btn-outline-light btn-sm',
                            text: '<i class="mdi mdi-file-pdf-box text-danger"></i><span class="text-danger">&nbsp;PDF</span>',
                            titleAttr: 'Export ke PDF',
                            title: 'DATA ABSENSI & KEDISIPLINAN KARYAWAN',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: ':not(:first-child)'
                            },
                            filename: function() {
                                var d = new Date();
                                var date = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                                    .getDate();
                                var time = d.getHours() + '-' + d.getMinutes() + '-' + d
                                    .getSeconds();
                                return 'LAPORAN_ABSENSI_&_KEDISIPLINAN_KARYAWAN_{{ $holding->holding_name }}_' +
                                    date + '_' + time;
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-outline-light btn-sm',
                            text: '<i class="mdi mdi-printer text-info"></i><span class="text-info">&nbsp;Print</span>',
                            titleAttr: 'Cetak Data',
                            title: 'DATA ABSENSI & KEDISIPLINAN KARYAWAN'
                        },
                        {
                            extend: 'copyHtml5',
                            className: 'btn btn-outline-light btn-sm',
                            text: '<i class="mdi mdi-content-copy text-secondary"></i><span class="text-secondary">&nbsp;Copy</span>',
                            titleAttr: 'Salin ke Clipboard',
                            title: 'DATA ABSENSI & KEDISIPLINAN KARYAWAN'
                        }
                    ],

                    ajax: {
                        url: "{{ url('report/absensi/kedisiplinan-datatable') }}" + '/' + holding,
                        data: {
                            start_date: start_date,
                            end_date: end_date,
                            departemen_filter: departemen_filter,
                            divisi_filter: divisi_filter,
                            bagian_filter: bagian_filter,
                            jabatan_filter: jabatan_filter,
                            shift_filter: shift_filter,
                        }
                    },
                    columns: data_column,
                    columnDefs: [{
                        targets: [4, 5, 6], // kolom name & email
                        className: 'text-start' // Bootstrap 5
                    }, ],
                    order: [
                        [2, 'asc']
                    ]

                });
            }
            $('#table_rekapdata').on('preXhr.dt', function(e, settings, data) {
                if (data.search && data.search.value) {
                    // kalau ada search value, biarkan loading default datatable
                    return;
                }
                showTableLoading();
            });

            // tutup Swal setelah data datatable selesai di-load
            // setelah datatable selesai load
            $('#table_rekapdata').on('xhr.dt', function(e, settings, json, xhr) {
                // tutup swal hanya kalau sebelumnya kita buka (bukan saat search)
                if (!settings.oPreviousSearch.sSearch) {
                    hideTableLoading();
                }
            });
            var table1 = $('#table_rekapdata1').DataTable({
                "scrollY": true,
                "scrollX": true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                deferRender: true,
                ajax: {
                    url: "{{ url('rekapdata-datatable_harian') }}" + '/' + holding,
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
                        data: 'nomor_identitas_karyawan',
                        name: 'nomor_identitas_karyawan'
                    },
                    {
                        data: 'total_hadir_tepat_waktu',
                        name: 'total_hadir_tepat_waktu'
                    },
                    {
                        data: 'total_hadir_telat_hadir',
                        name: 'total_hadir_telat_hadir'
                    },
                    {
                        data: 'total_izin_true',
                        name: 'total_izin_true'
                    },
                    {
                        data: 'total_cuti_true',
                        name: 'total_cuti_true'
                    },
                    {
                        data: 'total_cuti_non',
                        name: 'total_cuti_non'
                    },
                    {
                        data: 'total_dinas_true',
                        name: 'total_dinas_true'
                    },
                    {
                        data: 'tidak_hadir_kerja',
                        name: 'tidak_hadir_kerja'
                    },
                    {
                        data: 'total_semua',
                        name: 'total_semua'
                    },

                ],
                order: [
                    [2, 'asc']
                ]
            });
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                table1.columns.adjust().draw().responsive.recalc();
                // table.draw();
            })


            function get_grafik_absensi(departemen_filter = '', divisi_filter = '', bagian_filter = '',
                jabatan_filter = '', start_date = '', end_date = '') {
                // console.log(filter_month);
                $.ajax({
                    url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/report/absensi/kedisiplinan/get_grafik_absensi') }}@else{{ url('report/absensi/kedisiplinan/get_grafik_absensi') }}@endif" +
                        '/' + holding,
                    data: {
                        holding: holding,
                        start_date: start_date,
                        end_date: end_date,
                        departemen_filter: departemen_filter,
                        divisi_filter: divisi_filter,
                        bagian_filter: bagian_filter,
                        jabatan_filter: jabatan_filter,
                        shift_filter: shift_filter,
                    },
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        // console.log(data);
                        var label_absensi5 = data.label_absensi;
                        var data_absensi_masuk_tepatwaktu = data.data_absensi_masuk_tepatwaktu;
                        var data_absensi_masuk_tidak_hadir = data.data_absensi_masuk_tidak_hadir;
                        var data_absensi_masuk_telat = data.data_absensi_masuk_telat;
                        var data_absensi_masuk_cuti = data.data_absensi_masuk_cuti;
                        var options = {
                            series: [{
                                name: 'Total Tepat Waktu',
                                data: data_absensi_masuk_tepatwaktu
                            }, {
                                name: 'Total Terlambat ',
                                data: data_absensi_masuk_telat
                            }, {
                                name: 'Total Tidak Hadir ',
                                data: data_absensi_masuk_tidak_hadir,
                            }, {
                                name: 'Total Cuti ',
                                data: data_absensi_masuk_cuti
                            }],
                            annotations: {
                                points: [{
                                    x: 'Bananas',
                                    seriesIndex: 0,
                                    label: {
                                        borderColor: '#775DD0',
                                        offsetY: 0,
                                        style: {
                                            color: '#fff',
                                            background: '#775DD0',
                                        },
                                        text: 'Bananas are good',
                                    }
                                }]
                            },
                            chart: {
                                height: 400,
                                type: 'line',
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '50%',
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: [3, 3, 2]
                            },
                            colors: ["#008000", "#F8C300", "#8B0000", "#1E90FF"],
                            grid: {
                                row: {
                                    colors: ['#fff', '#f2f2f2']
                                }
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                },
                                categories: label_absensi5,
                                tickAmount: 31
                            },
                            yaxis: {
                                title: {
                                    text: 'Jumlah Karyawan Absensi',
                                },
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'light',
                                    type: "horizontal",
                                    shadeIntensity: 0.25,
                                    gradientToColors: undefined,
                                    inverseColors: true,
                                    opacityFrom: 0.85,
                                    opacityTo: 0.85,
                                    stops: [50, 0, 100]
                                },
                            }
                        };
                        if (chart_absensi_masuk) {
                            chart_absensi_masuk.destroy();
                        }
                        var chart_absensi_masuk = new ApexCharts(document.querySelector(
                            "#grafik_absensi"), options);

                        chart_absensi_masuk.render();
                        chart_absensi_masuk.updateSeries([{
                            name: 'Total Tepat Waktu',
                            data: data_absensi_masuk_tepatwaktu
                        }, {
                            name: 'Total Terlambat ',
                            data: data_absensi_masuk_telat
                        }, {
                            name: 'Total Tidak Hadir ',
                            data: data_absensi_masuk_tidak_hadir
                        }, {
                            name: 'Total Cuti ',
                            data: data_absensi_masuk_cuti
                        }])
                    }
                });

            }
        });
        $(document).ready(function() {
            $('#collapseGrafik').on('shown.bs.collapse', function() {
                $('#toggleBtn').text('Tutup');
            });

            $('#collapseGrafik').on('hidden.bs.collapse', function() {
                $('#toggleBtn').text('Buka');
            });
            $('#collapseFilter').on('shown.bs.collapse', function() {
                $('#toggleBtn').text('Tutup');
            });

            $('#collapseFilter').on('hidden.bs.collapse', function() {
                $('#toggleBtn').text('Buka');
            });
        });
    </script>
@endsection
