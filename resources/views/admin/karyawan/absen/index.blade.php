@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
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
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-hospital-box-outline"></i>&nbsp;KELOLA ABSENSI
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body">

                        <hr class="my-3">




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
                        <div id="table_absen_div">
                            <table class="table table-hover" id="table_absen" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>NIK</th>
                                        <th>Nama&nbsp;Karyawan</th>
                                        <th>Holding</th>
                                        <th>Nama&nbsp;Shift</th>
                                        <th>Jam&nbsp;Kerja</th>
                                        <th>Tanggal&nbsp;Masuk</th>
                                        <th>Keterangan&nbsp;Presensi</th>
                                        <th>Aksi</th>
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
    <div class="modal fade" id="modal_cuti" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="form_potong_cuti" method="post" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Setujui Pengajuan Izin Tidak Masuk</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="id_karyawan" name="id_karyawan">
                    <input type="hidden" id="id_kuota_cuti" name="id_kuota_cuti">
                    <input type="hidden" id="tanggal" name="tanggal">
                    <input type="hidden" id="menu_absen" name="menu_id" value="">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="nama_karyawan_add" name="nama_karyawan"
                                    placeholder="Nama Karyawan" readonly />
                                <label for="nama_karyawan_add" class="float-left">Nama Karyawan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="sisa_cuti_add" name="sisa_cuti"
                                    placeholder="Sisa Cuti" readonly />
                                <label for="sisa_cuti_add" class="float-left">Sisa Cuti</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating form-floating-outline mb-3">
                        <select class="form-control" id="pengurangan_add" name="pengurangan" autofocus>
                            <option value="" selected> Pilih Berapa Hari</option>
                            <option value="1"> 1 HARI</option>
                            <option value="2"> 2 HARI</option>
                        </select>
                        <label for="setuju">Pemotongan Cuti</label>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="terpotong_add" name="terpotong"
                                    placeholder="Sisa Cuti Setelah Terpotong" readonly />
                                <label for="terpotong_add" class="float-left">Sisa Cuti Setelah Terpotong</label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-4 border" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" id="btn_save_potong"
                        class="btn btn-primary rounded-3 px-4 fw-medium shadow-sm">
                        Simpan
                    </button>
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
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        let holding = '{{ $holding->holding_code }}';

        // Filter Table End

        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);
            $('#menu_absen').val(menu_id);

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
        $('#table_absen_div').hide();
        $('#btn_filter').click(function(e) {

            $('#table_absen_div').show();
            $('#content_null').empty();
            table3.ajax.reload(null, true);


        });
        var start_date = $('#start_date').val() || '';
        var end_date = $('#end_date').val() || '';
        var table3 = $('#table_absen').DataTable({
            responsive: true,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            // paginate: false,
            paging: false,
            filter: true,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/kelola_absen_dt') }}" + "/" + holding,
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
                title: 'LAPORAN ABSENSI',
                messageTop: 'LAPORAN ABSENSI',
                exportOptions: {
                    columns: ':visible',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                        .getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                        .getSeconds();
                    return 'LAPORAN_PERJALANAN DINAS_KARYAWAN ' +
                        start_date + ' - ' + end_date;
                },
            }],
            columns: [{
                    data: "no",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
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
                    data: 'holding',
                    name: 'holding'
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'jam_kerja',
                    name: 'jam_kerja'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'keterangan_presensi',
                    name: 'keterangan_presensi'
                },
                {
                    data: 'aksi',
                    name: 'aksi'
                },
            ],
            order: [
                [0, 'ASC']
            ]
        });
        $(document).on('click', '#btn_cuti', function() {
            let id = $(this).data('id');
            let nama_karyawan = $(this).data('nama_karyawan');
            let id_kuota_cuti = $(this).data('id_kuota_cuti');
            let sisa = $(this).data('sisa');
            let tanggal = $(this).data('tanggal');

            $('#pengurangan_add').val('');
            $('#terpotong_add').val('');
            $('#id_karyawan').val(id);
            $('#nama_karyawan_add').val(nama_karyawan);
            $('#id_kuota_cuti').val(id_kuota_cuti);
            $('#tanggal').val(tanggal);
            $('#sisa_cuti_add').val(sisa);
            $('#modal_cuti').modal('show');
        });
        $(document).on('change', '#pengurangan_add', function() {
            let sisaCuti = $('#sisa_cuti_add').val();
            let pengurangan = $('#pengurangan_add').val();

            sisaCuti = sisaCuti === '' ? 0 : parseFloat(sisaCuti);
            pengurangan = pengurangan === '' ? 0 : parseFloat(pengurangan);

            let hasil = sisaCuti - pengurangan;
            if (hasil < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Pengurangan cuti melebihi sisa cuti yang tersedia.',
                    confirmButtonText: 'OK'
                });

                $('#pengurangan_add').val('');
                $('#terpotong_add').val('');
                return;
            }

            $('#terpotong_add').val(hasil);
        });
        $('#btn_save_potong').on('click', function(e) {
            $('#modal_cuti').modal('hide');
            e.preventDefault();
            var formData = new FormData(document.getElementById('form_potong_cuti'));

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/kelola_absen/potong_cuti/') }}" + '/' + holding,
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
                        $('#form_potong_cuti')[0].reset();
                        $('#table_absen').DataTable().ajax.reload();
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
