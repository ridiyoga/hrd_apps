@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
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
                            <h5 class="card-title m-0 me-2">MASTER ROLE ACCESS</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="modal fade" id="modal_add_role" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" action="{{ url('/role/role_save_add/' . $holding->holding_code) }}"
                                    class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Role</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <label for="role_name">Nama Role</label>
                                                        <input type="text" name="role_name" id="role_name"
                                                            class="form-control" placeholder="Nama Role">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="role_description">Deskripsi Role</label>
                                                        <textarea name="role_description" id="role_description" class="form-control" placeholder="Deskripsi Role"></textarea>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="kategori">Kategori</label>
                                                        <select class="form-control" name="kategori" id="kategori">
                                                            <option value="">Pilih Kategori</option>
                                                            <option value="web">WEB</option>
                                                            <option value="mobile">MOBILE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <table class="table" id="table_menu" style=" font-size: small;" width="100%"
                                            hidden>
                                            <thead class="table-primary w-100">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nama&nbsp;Menu&nbsp;Access</th>
                                                    <th>kategori</th>
                                                    <th>Pilihan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_edit_role" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" action="{{ url('/role/role_save_update/' . $holding->holding_code) }}"
                                    class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Role</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <input type="text" value="" name="id_role" id="id_role"
                                                        hidden>
                                                    <div class="form-group mb-3">
                                                        <label for="role_name_update">Nama Role</label>
                                                        <input type="text" name="role_name_update" id="role_name_update"
                                                            class="form-control" placeholder="Nama Role">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="role_description_update">Deskripsi Role</label>
                                                        <textarea name="role_description_update" id="role_description_update" class="form-control"
                                                            placeholder="Deskripsi Role"></textarea>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label for="kategori_update">Kategori</label>
                                                        <select class="form-control" name="kategori_update"
                                                            id="kategori_update">
                                                            <option value="">Pilih Kategori</option>
                                                            <option value="web">WEB</option>
                                                            <option value="mobile">MOBILE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table" id="table_menu_update" style=" font-size: small;"
                                                width="100%" hidden>
                                                <thead class="table-primary w-100">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama&nbsp;Menu&nbsp;Access</th>
                                                        <th>kategori</th>
                                                        <th>Pilihan</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-flex master-table-toolbar">
                            <button id="btn_add_role" class="btn btn-sm btn-info mb-3 me-1">Tambah Role</button>
                            <table class="table" id="table_role2" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Role</th>
                                        <th>Deskripsi&nbsp;Role</th>
                                        <th>Kategori</th>
                                        <th>List&nbsp;Menu&nbsp;Access</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_role" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Role</th>
                                    <th>Deskripsi&nbsp;Role</th>
                                    <th>Kategori</th>
                                    <th>List&nbsp;Menu&nbsp;Access</th>
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
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            let holding = "{{ $holding->holding_code }}";
            // console.log(holding);
        var table = $('#table_role').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('role-datatable') }}" + '/' + holding,
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
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'list_menu',
                        name: 'list_menu'
                    }, {
                        data: 'action',
                        name: 'action'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
            $('#table_role2').hide();
            var table = $('#table_role2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('role-datatable') }}" + '/' + holding,
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: 'Excel',
                    titleAttr: 'Excel',
                    title: 'MASTER SHIFT',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
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
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'list_menu',
                        name: 'list_menu'
                    }, {
                        data: 'action',
                        name: 'action'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
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


            $(document).on('change', '.parent-checkbox', function() {

                let parentId = $(this).data('id');
                let isChecked = $(this).is(':checked');

                $('.child-checkbox[data-parent="' + parentId + '"]')
                    .prop('checked', isChecked);
            });
            $(document).on('change', '.parent_update-checkbox', function() {

                let parentId = $(this).data('id');
                let isChecked = $(this).is(':checked');

                $('.child_update-checkbox[data-parent="' + parentId + '"]')
                    .prop('checked', isChecked);
            });
            $(document).on('change', '#kategori', function() {
                var id = $(this).val();
                if (id == 'web') {
                    $('#table_menu').attr('hidden', false);
                    datatable_menu(holding, 'web');
                } else if (id == 'mobile') {
                    $('#table_menu').attr('hidden', false);
                    datatable_menu(holding, 'mobile');
                } else {
                    $('#table_menu').attr('hidden', true);
                }
            });

            function datatable_menu(holding, kategori) {
                // console.log(kategori);
                $('#table_menu').DataTable().destroy();
                $('#table_menu').DataTable({
                    pageLength: '1000',
                    "scrollY": true,
                    "scrollX": true,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ url('menu-datatable') }}" + '/' + kategori + '/' + holding,
                    },
                    columns: [{
                            data: "id",

                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'nama_menu',
                            name: 'nama_menu'
                        },
                        {
                            data: 'kategori',
                            name: 'kategori'
                        },

                        {
                            data: 'option',
                            name: 'option'
                        },
                    ]
                });
            }

            function datatable_menu_update(id, holding, kategori) {
                // console.log(kategori);
                $('#table_menu_update').DataTable().destroy();
                $('#table_menu_update').DataTable({
                    pageLength: '1000',
                    "scrollY": true,
                    "scrollX": true,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ url('menu-datatable-update') }}" + '/' + id + '/' + kategori + '/' +
                            holding,
                    },
                    columns: [{
                            data: "id",

                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'nama_menu',
                            name: 'nama_menu'
                        },
                        {
                            data: 'kategori',
                            name: 'kategori'
                        },

                        {
                            data: 'option',
                            name: 'option'
                        },
                    ]
                });
            }
            $(document).on("click", "#btn_add_role", function() {
                $('#modal_add_role').modal('show');

            });
            $('#foto_karyawan').change(function() {

                let reader = new FileReader();
                // console.log(reader);
                reader.onload = (e) => {

                    $('#template_foto_karyawan').attr('src', e.target.result);
                }

                reader.readAsDataURL(this.files[0]);

            });
            $(document).on("click", ".btn_edit_role", function() {

                let id = $(this).data('id');
                let role_name = $(this).data('role_name');
                let description = $(this).data('description');
                var kategori = $(this).data('kategori');
                // console.log(kategori);
                $('#id_role').val(id);
                $('#role_name_update').val(role_name);
                $('#role_description_update').val(description);
                $('#kategori_update').val(kategori);

                $(document).on('change', '#kategori_update', function() {
                    var kategori = $(this).val();
                    if (kategori == 'web') {
                        $('#table_menu_update').attr('hidden', false);
                        datatable_menu_update(id, holding, 'web');
                    } else if (kategori == 'mobile') {
                        $('#table_menu_update').attr('hidden', false);
                        datatable_menu_update(id, holding, 'mobile');
                    } else {
                        $('#table_menu_update').attr('hidden', true);
                    }
                });

                $('#table_menu_update').attr('hidden', false);
                datatable_menu_update(id, holding, kategori);
                // show modal dulu
                $('#modal_edit_role').modal('show');



            });
            $(document).on('click', '#btn_delete_karyawan', function() {
                // var id = $(this).data('id');
                // let holding = $(this).data("holding");
                // console.log(id);
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Kamu tidak dapat mengembalikan data ini",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ url('/karyawan/delete/') }}" + '/' + id + '/' +
                                holding,
                            type: "GET",
                            error: function() {
                                alert('Something is wrong');
                            },
                            success: function(data) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data anda berhasil di hapus.',
                                    icon: 'success',
                                    timer: 1500
                                })
                                $('#table_access_karyawan').DataTable().ajax.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Cancelled!',
                            text: 'Your data is safe :',
                            icon: 'error',
                            timer: 1500
                        })
                    }
                });

            });
        });
    </script>
@endsection
