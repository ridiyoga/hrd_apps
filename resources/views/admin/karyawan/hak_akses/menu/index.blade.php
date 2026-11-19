@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style type="text/css">
        .select2-container {
            z-index: 9999 !important;
        }

        /* ukuran teks di area pilihan (input select2) */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem !important;
            /* Bootstrap small (14px) */
            min-height: calc(1.5em + 0.75rem + 2px);
            /* biar tinggi konsisten */
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

        .swal2-container {
            z-index: 9999 !important;
        }

        .dataTables_wrapper {
            width: 100% !important;
        }

        .tree-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 20px;
        }

        .tree-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .tree-wrapper::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .group-box {
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fafafa;
            overflow-x: auto;
            overflow-y: hidden;
            max-width: 100%;
            position: relative;
        }

        /* RESET */
        #tree-container ul {
            padding-top: 20px;
            position: relative;
            padding-left: 0;
        }

        #tree-container ul ul {
            padding-top: 20px;
        }

        #tree-container ul ul li:only-child::before {
            display: none;
        }

        /* Garis horizontal antar anak */
        #tree-container ul li {
            float: left;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 5px 0 5px;
        }

        /* Vertical line from parent */
        #tree-container ul li::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            width: 0;
            height: 20px;
        }

        /* Horizontal line connecting to siblings */
        #tree-container ul li::after {
            content: '';
            position: absolute;
            top: 0px;
            left: 0;
            width: 100%;
            height: 2px;
            border-top: 2px solid #ccc;
        }

        #tree-container ul>li::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            height: 20px;
            /* Sama dengan padding-top ul */
            width: 0;
            display: block;
        }

        /* Hilangkan garis untuk single child */
        #tree-container ul li:only-child::after,
        #tree-container ul li:only-child::before {
            display: none;
        }

        /* Hapus garis kiri pada first-child */
        #tree-container ul li:first-child::after {
            border-left: 2px solid #ccc;
            border-radius: 0;
            left: 50%;
            width: 50%;
        }

        /* Hapus garis kanan pada last-child */
        #tree-container ul li:last-child::after {
            border-right: 2px solid #ccc;
            width: 50%;
        }

        /* Box node */
        .node {
            text-align: center;
            display: inline-block;
            min-width: 100px;
            max-width: 160px;
            padding: 10px 15px;
            border: 2px solid #1bb1df;
            border-radius: 12px;
            background: #fff;
            position: relative;
        }


        /* LEVEL COLORS */
        .l1 {
            border: 2px solid #005BEA;
        }

        .l2 {
            border: 2px solid #1BB1DF;
        }

        .l3 {
            border: 2px solid #673AB7;
        }

        .l4 {
            border: 2px solid #FFC107;
        }

        .l5 {
            border: 2px solid #FF005D;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA HAK AKSES MENU</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="modal fade" id="modal_hak_akses_karyawan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_add_access" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">

                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-4">
                                            <!-- Account -->
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <input type="text" name="role_id" id="role_id" value=""
                                                            hidden>
                                                        <label for=" group_id" class="form-label">DAFTAR KARYAWAN</label>
                                                        <div class="float-right">
                                                            <button type="button" class="btn btn-sm btn-primary"
                                                                id="btn_select_all">Select All</button>
                                                        </div>
                                                        <table id="table_akses_karyawan" class="table table-hover"
                                                            style="width: 100%; font-size: smaller;">
                                                            <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Username</th>
                                                                    <th>Nama&nbsp;Karyawan</th>
                                                                    <th>Departemen</th>
                                                                    <th>Jabatan</th>
                                                                    <th>Shift</th>
                                                                    <th>Pilih</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close"></i>&nbsp;Close
                                        </button>
                                        <button type="button" class="btn_save_add_access btn btn-sm btn-primary"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="master-table-toolbar mb-2">
                            {{-- <button type="button" class="btn btn-sm btn-primary" id="btn_add_group"><i
                                    class="mdi mdi-plus"></i>Tambah</button> --}}
                        </div>
                        <div class="master-table-area">
                            <table class="table" id="table_akses" style="width: 100%; font-size: small;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Akses</th>
                                        <th>Deskripsi&nbsp;Akses</th>
                                        <th>Kategori</th>
                                        <th>Total&nbsp;User</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy_sub2').val();
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

            $('#parent_approval_id').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Parent...",
                allowClear: true
            });

            let holding = "{{ $holding->holding_code }}";
            let holding_id = "{{ $holding->id }}";
            var table = $('#table_akses').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('users/hak_akses/menu-datatable') }}" + '/' + holding,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'role_description',
                        name: 'role_description'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'total_karyawan',
                        name: 'total_karyawan'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });

            $(document).on('click', '#btn_hak_akses_karyawan', function() {
                // console.log('ok');
                var id = $(this).data('id');
                $('#role_id').val(id);
                $('#table_akses_karyawan').DataTable().clear().destroy();
                var table = $('#table_akses_karyawan').DataTable({
                    pageLength: -1,
                    searching: false,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, 'All']
                    ],
                    "scrollY": true,
                    "scrollX": true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('users/hak_akses/karyawan-datatable') }}" + '/' + id + '/' +
                            holding,
                    },
                    columns: [{
                            data: "id",

                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'nama_karyawan',
                            name: 'nama_karyawan'
                        },
                        {
                            data: 'nama_departemen',
                            name: 'nama_departemen'
                        },
                        {
                            data: 'nama_jabatan',
                            name: 'nama_jabatan'
                        },
                        {
                            data: 'shift',
                            name: 'shift'
                        },
                        {
                            data: 'option',
                            name: 'option'
                        },
                    ],
                    order: [
                        [1, 'asc']
                    ]
                });
                $('#modal_hak_akses_karyawan').modal('show');
            });

            $(document).on('click', '.btn_save_add_access', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var role = $('#role_id').val();
                var menu = [];
                $('input[name="menu_id[]"]:checked').each(function() {
                    menu.push($(this).val());
                });
                var url = "{{ url('users/hak_akses/save_access') }}/" + get_holding;
                // console.log(menu);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': role,
                        'menu': menu,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mohon tunggu...',
                            text: 'Sedang memproses data',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        $('#form_add_access')[0].reset();
                        $('#modal_hak_akses_karyawan').modal('hide');
                        $('#table_akses').DataTable().ajax.reload();
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.error,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_add_access')[0].reset();
                        $('#modal_hak_akses_karyawan').modal('hide');
                        $('#table_akses').DataTable().ajax.reload();
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            })
            $(document).on('click', '#btn_select_all', function() {
                let isChecked = $(this).data('checked') || false;

                $('input[name="menu_id[]"]').prop('checked', !isChecked);

                $(this).data('checked', !isChecked);

                if (!isChecked) {
                    $(this)
                        .removeClass('btn-primary')
                        .addClass('btn-success')
                        .text('Unselect All');
                } else {
                    $(this)
                        .removeClass('btn-success')
                        .addClass('btn-primary')
                        .text('Select All');
                }
            });


        });
    </script>
@endsection
