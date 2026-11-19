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
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-hospital-box-outline"></i>&nbsp;DETAIL LEMBUR
                                KARYAWAN
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body">

                        <hr class="my-3">
                        <table class="table table-hover mb-4" id="table_detail_lembur"
                            style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>NAMA KARYAWAN</th>
                                    <th>STATUS</th>
                                    <th>SPV</th>
                                    <th>ATASAN&nbsp;1</th>
                                    <th>ATASAN&nbsp;2</th>
                                    <th>HRD</th>
                                    <th>MULAI</th>
                                    <th>SELESAI</th>
                                    <th>TOTAL (JAM)</th>
                                    <th>PRESENSI MASUK</th>
                                    <th>PRESENSI KELUAR</th>
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
        let id = '{{ $id_lembur }}';
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

        var table4 = $('#table_detail_lembur').DataTable({
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
                url: "{{ url('/dt_detail_lembur_report') }}" + "/" + id,
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
                title: 'DETAIL LEMBUR KARYAWAN',
                messageTop: 'DETAIL LEMBUR KARYAWAN',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                        .getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                        .getSeconds();
                    return 'DETAIL_LEMBUR_KARYAWAN' +
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
                    data: 'status_lembur',
                    name: 'status_lembur'
                },
                {
                    data: 'spv',
                    name: 'spv'
                },
                {
                    data: 'atasan1',
                    name: 'atasan1'
                },
                {
                    data: 'atasan2',
                    name: 'atasan2'
                },
                {
                    data: 'hrd',
                    name: 'hrd'
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
                    data: 'presensi_pulang',
                    name: 'presensi_pulang'
                },
            ],
            order: [
                [1, 'asc']
            ]
        });


        // TTD Versi Web End
    </script>
@endsection
