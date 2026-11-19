@extends('admin.layouts.dashboard')
@section('css')
@include('css_baru')
<link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
<style>
    .kbw-signature {
        width: 100%;
        height: 160px;
        border: 3px solid #f3e8ff !important;
        background: #ffffff;
    }

    #sig canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* Efek saat area tanda tangan di-hover / aktif */
    #sig {
        transition: all 0.25s ease-in-out;
    }

    #sig:hover,
    #sig:focus-within {
        border-color: #2563eb !important;
        /* Biru terang */
        background-color: #ffffff !important;
        /* Berubah jadi putih bersih */
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.12) !important;
        /* Glow halus */
    }
</style>
@endsection
@section('isi')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-dark"><i class="mdi mdi-hospital-box-outline"></i>&nbsp;LEMBUR
                            KARYAWAN
                        </h5>
                        <input type="hidden" id="menu_id_add" value="">

                    </div>
                </div>
                <div class="card-body">

                    <hr class="my-3">
                    <input type="hidden" id="menu_id_add" value="">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">

                        <li class="nav-item active" role="presentation">
                            <button class="nav-link active" id="kuota-tab" data-bs-toggle="tab"
                                data-bs-target="#kuota-tab-pane" type="button" role="tab"
                                aria-controls="kuota-tab-pane" aria-selected="true">PENGAJUAN</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hari-tab" data-bs-toggle="tab" data-bs-target="#hari-tab-pane"
                                type="button" role="tab" aria-controls="hari-tab-pane"
                                aria-selected="false">Riwayat LEMBUR</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane active fade show" id="kuota-tab-pane" role="tabpanel"
                            aria-labelledby="kuota-tab" tabindex="0">
                            <div id="table_pengajuan_lembur_div">
                                <table class="table table-hover" id="table_pengajuan_lembur"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>NAMA KARYAWAN</th>
                                            <th>JABATAN KARYAWAN</th>
                                            <th>TANGGAL LEMBUR</th>
                                            <th>STATUS</th>
                                            <th>KETERANGAN</th>
                                            <th>NAMA ATASAN&nbsp;1</th>
                                            <th>JABATAN ATASAN&nbsp;1</th>
                                            <th>NAMA ATASAN&nbsp;2</th>
                                            <th>JABATAN ATASAN&nbsp;2</th>
                                            <th>PERSETUJUAN HRD</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0 small">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="hari-tab-pane" role="tabpanel" aria-labelledby="hari-tab"
                            tabindex="0">
                            <div id="collapseFilterWrapper" class="sticky-top bg-white" style="z-index: 1020;">
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
                            <div id="content_null">
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
                            <div id="table_riwayat_div">
                                <table class="table table-hover" id="table_riwayat"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>NAMA KARYAWAN</th>
                                            <th>JABATAN KARYAWAN</th>
                                            <th>TANGGAL LEMBUR</th>
                                            <th>STATUS</th>
                                            <th>KETERANGAN</th>
                                            <th>NAMA ATASAN&nbsp;1</th>
                                            <th>JABATAN ATASAN&nbsp;1</th>
                                            <th>NAMA ATASAN&nbsp;2</th>
                                            <th>JABATAN ATASAN&nbsp;2</th>
                                            <th>HRD</th>
                                            <th>DETAIL LEMBUR</th>
                                            <th>FORM LEMBUR</th>
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
</div>
<div class="modal fade" id="modal_approve_lembur" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="backDropModalTitle">Setujui Pengajuan Lembur</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form_lembur" method="post" enctype="multipart/form-data">
                    <table class="table table-hover mb-4" id="table_detail_lembur"
                        style="width: 100%; font-size: smaller;">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>NAMA KARYAWAN</th>
                                <th>TANGGAL</th>
                                <th>MULAI</th>
                                <th>SELESAI</th>
                                <th>TOTAL (JAM)</th>
                                <th>PRESENSI MASUK</th>
                                <th>PRESENSI KELUAR</th>
                                <th>STATUS LEMBUR</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 small">
                        </tbody>
                    </table>

                    @csrf
                    <div class="d-flex justify-content-end">

                        <input type="hidden" id="id_lembur" name="id_lembur">
                        <div class="form-floating form-floating-outline mb-2" style="width: 50%;">
                            <select class="form-control" id="setuju" name="setuju" autofocus>
                                <option value="1"> SETUJU</option>
                                <option value="2"> TOLAK</option>
                            </select>
                            <label for="setuju">PERSETUJUAN</label>
                        </div>
                    </div>
                    <!-- Canvas Container dengan Dashed Border & Accent Color -->
                    <div class="d-flex justify-content-end">

                        <div id="sig" class="kbw-signature rounded-4 position-relative overflow-hidden mb-2"
                            style="width: 50%; height: 210px; border: 2px dashed #93c5fd; background-color: #f8fafc;">

                            <!-- Watermark / Petunjuk dengan Warna Cerah -->
                            <div
                                class="position-absolute top-50 start-50 translate-middle text-center pe-none user-select-none opacity-75">
                                <div
                                    class="mb-2 d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm p-3 text-primary">
                                    <i class="fa-solid fa-pen-nib fs-4"></i>
                                </div>
                                <p class="small mb-0 text-secondary fw-medium">Tanda tangan di area ini</p>
                            </div>

                            <!-- Canvas -->
                            <canvas id="signature-pad"
                                style="width:50%; height:100%; touch-action: none !important; position: relative; z-index: 2; cursor: crosshair;"
                                hidden></canvas>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">

                        <div class="d-flex justify-content-between align-items-right px-1 mb-2 w-50">
                            <span class="text-muted small" style="font-size: 0.8rem;">
                                <i class="fa-regular fa-circle-check text-success me-1"></i>Gunakan mouse untuk tanda
                                tangan
                            </span>

                            <button type="button" class="btn btn-outline-danger btn-sm" id="clear">
                                <span class="mdi mdi-trash-can-outline"></span>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>

                    <textarea id="signature" name="signature" style="display: none"></textarea>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-3 px-4 border" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" id="btn_approve_lembur"
                    class="btn btn-primary rounded-3 px-4 fw-medium shadow-sm">
                    Simpan
                </button>
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
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

{{-- <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    let holding = '{{ $holding->holding_code }}';




    // Filter Table End

    $(document).ready(function() {
        var menu_id = $('#asoy').val();
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
    })


    var table2 = $('#table_pengajuan_lembur').DataTable({
        responsive: true,
        "scrollY": true,
        "scrollX": true,
        processing: true,
        serverSide: true,
        paginate: false,
        filter: true,
        info: false,
        dom: 'Bfrtip',
        ajax: {
            url: "{{ url('/dt_lembur') }}" + "/" + holding,
            // data: {
            //     start_date: start_date,
            //     end_date: end_date,
            // }
        },
        buttons: [{

            extend: 'excelHtml5',
            className: 'btn btn-sm btn-success',
            text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
            titleAttr: 'Excel',
            title: 'LAPORAN LEMBUR KARYAWAN',
            messageTop: 'LAPORAN LEMBUR KARYAWAN',
            exportOptions: {
                columns: ':not(:first-child)',
            },
            filename: function() {
                var d = new Date();
                var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                    .getDate();
                var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                    .getSeconds();
                return 'LAPORAN_LEMBUR_KARYAWAN' +
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
                data: 'jabatan_pengaju',
                name: 'jabatan_pengaju'
            },

            {
                data: 'tanggal_lembur',
                name: 'tanggal_lembur'
            },
            {
                data: 'status_lembur',
                name: 'status_lembur'
            },
            {
                data: 'keterangan_lembur',
                name: 'keterangan_lembur'
            },
            {
                data: 'nama_atasan',
                name: 'nama_atasan'
            },
            {
                data: 'jabatan_atasan1',
                name: 'jabatan_atasan1'
            },
            {
                data: 'nama_atasan2',
                name: 'nama_atasan2'
            },
            {
                data: 'jabatan_atasan2',
                name: 'jabatan_atasan2'
            },
            {
                data: 'approve',
                name: 'approve'
            },
        ],
        order: [
            [3, 'asc']
        ]
    });

    // console.log(table2.responsive);
    $('#kuota-tab').on('shown.bs.tab', function(e) {
        table3.columns.adjust().draw().responsive.recalc();
        // table.draw();
    });

    // TTD Versi Web
    const canvas = $('#signature-pad')[0];
    const signaturePad = new SignaturePad(canvas);

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;

        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);

        signaturePad.clear();
    }

    $(document).on('click', '#btn_setuju', function() {
        $('#signature-pad').removeAttr('hidden');
        let id = $(this).data('id');
        $('#id_lembur').val(id);
        if ($.fn.DataTable.isDataTable('#table_detail_lembur')) {

            $('#table_detail_lembur')
                .DataTable()
                .ajax.url("{{ url('/dt_detail_lembur') }}" + "/" + id)
                .load();

        } else {
            var table4 = $('#table_detail_lembur').DataTable({
                responsive: true,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: true,
                info: false,
                ajax: {
                    url: "{{ url('/dt_detail_lembur') }}" + "/" + id,
                    // data: {
                    //     start_date: start_date,
                    //     end_date: end_date,
                    // }
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
                        data: 'tgl_lembur',
                        name: 'tgl_lembur'
                    },
                    {
                        data: 'jam_mulai',
                        name: 'jam_mulai'
                    },
                    {
                        data: 'jam_selesai',
                        name: 'jam_selesai'
                    },
                    {
                        data: 'selisih_convert',
                        name: 'selisih_convert'
                    },
                    {
                        data: 'presensi_masuk',
                        name: 'presensi_masuk'
                    },
                    {
                        data: 'presensi_keluar',
                        name: 'presensi_keluar'
                    },
                    {
                        data: 'status_lembur',
                        name: 'status_lembur'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
        }

        $('#modal_approve_lembur').modal('show');
    });


    $('#modal_approve_lembur').on('shown.bs.modal', function() {
        resizeCanvas();

        console.log({
            width: canvas.width,
            height: canvas.height
        });
    });

    $(window).on('resize', function() {
        resizeCanvas();
    });

    $('#clear').on('click', function() {
        signaturePad.clear();
    });

    // TTD Versi Web End
    $('#btn_approve_lembur').on('click', function(e) {
        $('#modal_approve_lembur').modal('hide');
        let btn = $(this);
        let originalText = btn.html();

        let signature_value = '';
        if (!signaturePad.isEmpty()) {
            signature_value = signaturePad.toDataURL();
        }
        $('#signature').val(signature_value);
        // post
        $.ajax({
            url: "{{ url('lembur_approve_hrd') }}" + '/' + holding,
            type: "POST",
            data: $('#form_lembur').serialize(),
            beforeSend: function() {
                Swal.fire({
                    title: 'Memuat Data...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            error: function() {
                Swal.close();
                alert('Something is wrong!');
                // console.log(formData);
            },
            success: function(data) {
                Swal.close();
                if (data.code == 200) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: data.message,
                        icon: 'success',
                        timer: 5000
                    });
                    $('#table_pengajuan_lembur').DataTable().ajax.reload();
                } else if (data.code == 400) {

                    let errors = data.errors;
                    // console.log(errors);
                    let errorMessages = '';

                    Object.keys(errors).forEach(function(key) {
                        errors[key].forEach(function(message) {
                            errorMessages += `• ${message}\n`;
                        });
                    });
                    Swal.fire({
                        // title: data.message,
                        text: errorMessages,
                        icon: 'warning',
                        timer: 4500
                    })

                } else if (data.code == 510) {
                    Swal.fire({
                        text: data.message,
                        icon: 'warning',
                        timer: 4500
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: data.error,
                        icon: 'error',
                        timer: 4500
                    })

                }
            }

        })
    });
    $(document).ready(function() {
        var menu_id = $('#asoy').val();
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
    })

    // Filter table Per tanggal
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
    $('#table_riwayat_div').hide();
    $('#btn_filter').click(function(e) {

        $('#table_riwayat_div').show();
        $('#content_null').empty();
        table3.ajax.reload(null, true);


    });

    var start_date = $('#start_date').val() || '';
    var end_date = $('#end_date').val() || '';
    var table3 = $('#table_riwayat').DataTable({
        responsive: true,
        "scrollY": true,
        "scrollX": true,
        processing: true,
        serverSide: true,
        paginate: false,
        filter: true,
        info: false,
        // dom: 'Bfrtip',
        ajax: {
            url: "{{ url('/dt_lembur_riwayat') }}" + "/" + holding,
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        buttons: [{

            extend: 'excelHtml5',
            className: 'btn btn-sm btn-success',
            text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
            titleAttr: 'Excel',
            title: 'LAPORAN LEMBUR',
            messageTop: 'LAPORAN LEMBUR',
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
            }, {
                data: 'nama_karyawan',
                name: 'nama_karyawan'
            },

            {
                data: 'jabatan_pengaju',
                name: 'jabatan_pengaju'
            },

            {
                data: 'tanggal_lembur',
                name: 'tanggal_lembur'
            },
            {
                data: 'status_lembur',
                name: 'status_lembur'
            },
            {
                data: 'keterangan_lembur',
                name: 'keterangan_lembur'
            },
            {
                data: 'nama_atasan',
                name: 'nama_atasan'
            },
            {
                data: 'jabatan_atasan1',
                name: 'jabatan_atasan1'
            },
            {
                data: 'nama_atasan2',
                name: 'nama_atasan2'
            },
            {
                data: 'jabatan_atasan2',
                name: 'jabatan_atasan2'
            },
            {
                data: 'nama_hrd',
                name: 'nama_hrd'
            },
            {
                data: 'detail',
                name: 'detail'
            },
            {
                data: 'form_lembur',
                name: 'form_lembur'
            },
        ],
        order: [
            [1, 'desc']
        ]
    });
</script>
@endsection