@extends('admin.layouts.dashboard')
@section('css')
@include('css_baru')
@endsection
@section('isi')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-dark"><i class="mdi mdi-beach me-2"></i>CUTI</h5>
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
                                aria-controls="kuota-tab-pane" aria-selected="true">Kuota Per-Karyawan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hari-tab" data-bs-toggle="tab" data-bs-target="#hari-tab-pane"
                                type="button" role="tab" aria-controls="hari-tab-pane" aria-selected="false">Cuti
                                Per-Hari</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane active fade show" id="kuota-tab-pane" role="tabpanel"
                            aria-labelledby="kuota-tab" tabindex="0">
                            <button class="btn btn-sm btn-info mb-4" id="btn_tambah_kontrak" type="button">
                                <i class="mdi mdi-plus me-1"></i> Tambah Kuota Cuti
                            </button>
                            <div id="table_kuota_div">
                                <table class="table table-hover" id="table_kuota"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>NO.&nbsp;IDENTITAS&nbsp;KARYAWAN</th>
                                            <th>NAMA&nbsp;KARYAWAN</th>
                                            <th>KUOTA</th>
                                            <th>TERPAKAI</th>
                                            <th>SISA</th>
                                            <th>AKSI</th>
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
                            <div id="table_hari_div">
                                <table class="table table-hover" id="table_hari"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No.</th>
                                            <th>TANGGAL&nbsp;CUTI</th>
                                            <th>NAMA&nbsp;CUTI</th>
                                            <th>NO.&nbsp;IDENTITAS&nbsp;KARYAWAN</th>
                                            <th>NAMA&nbsp;KARYAWAN</th>
                                            <th>STATUS</th>
                                            <th>PILIHAN</th>
                                            <th>TANGGAL&nbsp;ORIGINAL</th>
                                            <th>DIUBAH&nbsp;OLEH</th>
                                            <th>DIUBAH&nbsp;PADA</th>
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
<div class="modal fade" id="modal_edit_cuti" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <form class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="backDropModalTitle">Edit Kuota Terpakai</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="hidden" name="id" id="id_update" value="">
                            <input type="text" class="form-control" id="name_update" name="kuota" autofocus
                                readonly>
                            <label for="name_update" class="float-left">Nama</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="hidden" name="id_update" id="id_update" value="">
                            <input type="number" class="form-control" id="kuota_update" name="kuota" autofocus
                                readonly>
                            <label for="kuota_update" class="float-left">Kuota</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" id="terpakai_update" name="kuota"
                                autofocus>
                            <label for="terpakai_update" class="float-left">Terpakai</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control" id="sisa_update" name="kuota" autofocus
                                readonly>
                            <label for="sisa_update" class="float-left">Sisa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_update_cuti" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_edit_hari" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <form class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="backDropModalTitle">Edit Tanggal Cuti</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="hidden" name="id" id="id_hari" value="">
                            <input type="text" class="form-control" id="name_hari_update" name="name_hari"
                                autofocus readonly>
                            <label for="name_update" class="float-left">Nama</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="tgl_cuti_update" name="tgl_cuti"
                                autofocus readonly>
                            <label for="tgl_cuti_update" class="float-left">Tanggal Cuti Lama</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="tgl_baru_update" name="tgl_cuti"
                                autofocus>
                            <label for="tgl_baru_update" class="float-left">Tanggal Cuti Baru</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_update_hari" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_hapus_hari" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <form class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="backDropModalTitle">Edit Kuota Terpakai</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="hidden" name="id" id="id_hapus_hari" value="">
                            <input type="text" class="form-control" id="name_hari_hapus" name="name_hari"
                                autofocus readonly>
                            <label for="name_hapus" class="float-left">Nama</label>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="tgl_cuti_hapus" name="tgl_cuti" autofocus
                                readonly>
                            <label for="tgl_cuti_hapus" class="float-left">Tanggal Cuti</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_hapus_hari" class="btn btn-danger">Batalkan</button>
                </div>
            </div>
        </form>
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


    var table2 = $('#table_kuota').DataTable({
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
            url: "{{ url('/dt_karyawan_kuota_cuti') }}" + "/" + holding,
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
                data: 'terpakai',
                name: 'terpakai'
            },
            {
                data: 'sisa',
                name: 'sisa'
            },
            {
                data: 'edit',
                name: 'edit'
            },
        ],
        order: [
            [2, 'asc']
        ]
    });

    // console.log(table2.responsive);
    $('#kuota-tab').on('shown.bs.tab', function(e) {
        table3.columns.adjust().draw().responsive.recalc();
        // table.draw();
    });

    $(document).on("click", "#edit_cuti", function() {
        let id = $(this).data('id');
        let name = $(this).data("name");
        let kuota = $(this).data("kuota");
        let terpakai = $(this).data("terpakai");
        let sisa = $(this).data("sisa");
        // console.log(jamkeluar);
        $('#id_update').val(id);
        $('#name_update').val(name);
        $('#kuota_update').val(kuota);
        $('#terpakai_update').val(terpakai);
        $('#sisa_update').val(sisa);
        $('#modal_edit_cuti').modal('show');

    });
    $('#terpakai_update').on('input', function() {
        let kuota = parseInt($('#kuota_update').val()) || 0;
        let terpakai = parseInt($(this).val()) || 0;

        if (terpakai > kuota) {
            terpakai = kuota;
            $(this).val(kuota);
        }

        $('#sisa_update').val(kuota - terpakai);
    });
    $('#btn_update_cuti').on('click', function(e) {
        $('#modal_edit_cuti').modal('hide');
        e.preventDefault();
        var formData = new FormData();

        //ambil data dari form
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', $('#id_update').val());
        formData.append('terpakai', $('#terpakai_update').val());
        formData.append('sisa', $('#sisa_update').val());
        formData.append('name', $('#name_update').val());
        formData.append('menu_id', $('#menu_id_add').val());

        // post
        $.ajax({
            type: "POST",

            url: "{{ url('/cuti_update/') }}" + '/' + holding,
            data: formData,
            contentType: false,
            processData: false,
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
                    })
                    //mengosongkan modal dan menyembunyikannya
                    $('#table_kuota').DataTable().ajax.reload();
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

                } else if (data.code == 600) {
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
        });
    });
    $(document).on("click", "#edit_hari", function() {
        let id = $(this).data('id');
        let name = $(this).data("name");
        let tgl_cuti = $(this).data("tgl_cuti");
        // console.log(tgl_cuti);
        $('#id_hari').val(id);
        $('#name_hari_update').val(name);
        $('#tgl_cuti_update').val(tgl_cuti);
        $('#modal_edit_hari').modal('show');

    });
    // $('#tgl_baru_update').on('change', function() {
    //     let tglLama = new Date();
    //     let tglBaru = new Date($(this).val());

    //     // Selisih hari
    //     let selisihHari = (tglLama - tglBaru) / (1000 * 60 * 60 * 24);

    //     if (selisihHari > 3) {
    //         alert('Maksimal backdate 2 hari');
    //         $(this).val('');
    //     }
    // });


    $('#btn_update_hari').on('click', function(e) {
        $('#modal_edit_hari').modal('hide');
        e.preventDefault();
        var formData = new FormData();

        //ambil data dari form
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', $('#id_hari').val());
        formData.append('tgl_cuti', $('#tgl_cuti_update').val());
        formData.append('tgl_baru', $('#tgl_baru_update').val());
        formData.append('menu_id', $('#menu_id_add').val());


        // post
        $.ajax({
            type: "POST",

            url: "{{ url('/hari_update/') }}" + '/' + holding,
            data: formData,
            contentType: false,
            processData: false,
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
                    })
                    //mengosongkan modal dan menyembunyikannya
                    $('#table_hari').DataTable().ajax.reload();
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

                } else if (data.code == 600) {
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
        });
    });
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
    $('#table_hari_div').hide();
    $('#btn_filter').click(function(e) {

        $('#table_hari_div').show();
        $('#content_null').empty();
        table3.ajax.reload(null, true);


    });
    var start_date = $('#start_date').val() || '';
    var end_date = $('#end_date').val() || '';
    var table3 = $('#table_hari').DataTable({
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
            url: "{{ url('/dt_karyawan_hari_cuti') }}" + "/" + holding,
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
                data: 'tgl_cuti',
                name: 'tgl_cuti'
            },
            {
                data: 'nama_cuti',
                name: 'nama_cuti'
            },
            {
                data: 'nomor_identitas_karyawan',
                name: 'nomor_identitas_karyawan'
            },
            {
                data: 'nama_karyawan',
                name: 'nama_karyawan'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'edit',
                name: 'edit'
            },
            {
                data: 'original',
                name: 'original'
            },
            {
                data: 'updated_by_name',
                name: 'updated_by_name'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            },
        ],
        order: [
            [1, 'desc']
        ]
    });
    $(document).on("click", "#hapus_hari", function() {
        let id = $(this).data('id');
        let name = $(this).data("name");
        let tgl_cuti = $(this).data("tgl_cuti");
        // console.log(tgl_cuti);
        $('#id_hapus_hari').val(id);
        $('#name_hari_hapus').val(name);
        $('#tgl_cuti_hapus').val(tgl_cuti);
        $('#modal_hapus_hari').modal('show');
    });
    $('#btn_hapus_hari').on('click', function(e) {
        $('#modal_hapus_hari').modal('hide');
        e.preventDefault();
        var formData = new FormData();

        //ambil data dari form
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('id', $('#id_hapus_hari').val());
        formData.append('tgl_cuti', $('#tgl_cuti_hapus').val());
        formData.append('menu_id', $('#menu_id_add').val());


        // post
        $.ajax({
            type: "POST",

            url: "{{ url('/hapus_hari_update/') }}" + '/' + holding,
            data: formData,
            contentType: false,
            processData: false,
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
                    })
                    //mengosongkan modal dan menyembunyikannya
                    $('#table_hari').DataTable().ajax.reload();
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

                } else if (data.code == 600) {
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
        });
    });
</script>
@endsection