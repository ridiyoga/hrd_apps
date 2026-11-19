@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header master-report-toolbar">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA MASTER BAGIAN</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-3">
                        <div class="d-flex master-table-toolbar">
                            <table class="table" id="table_bagian2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Departemen</th>
                                        <th>Divisi</th>
                                        <th>Nama Bagian</th>
                                        <th>Jumlah&nbsp;Jabatan</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_bagian" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Departemen</th>
                                    <th>Divisi</th>
                                    <th>Nama Bagian</th>
                                    <th>Jumlah&nbsp;Jabatan</th>
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

    <!-- modal lihat divisi -->
    <div class="modal fade" id="modal_lihat_jabatan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class=" modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle"> Daftar Jabatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12">
                        <table class="table" id="table_lihat_jabatan" style="width: 100%; font-size: smaller;">
                            <thead class="table-primary">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Jabatan</th>
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
        var table = $('#table_bagian').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/bagian-datatable') }}@else{{ url('bagian-datatable') }}@endif" +
                    '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_departemen',
                    name: 'nama_departemen'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_bagian',
                    name: 'nama_bagian'
                },
                {
                    data: 'jumlah_jabatan',
                    name: 'jumlah_jabatan'
                },
            ],
            order: [
                [4, 'asc']
            ]
        });
        $('#table_bagian2').hide();
        var table = $('#table_bagian2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/bagian-datatable') }}@else{{ url('bagian-datatable') }}@endif" +
                    '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'BAGIAN',
                messageTop: '{{ $holding->holding_name }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'SELURUH_BAGIAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_departemen',
                    name: 'nama_departemen'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_bagian',
                    name: 'nama_bagian'
                },
                {
                    data: 'jumlah_jabatan_excel',
                    name: 'jumlah_jabatan_excel'
                },
            ],
            order: [
                [4, 'asc']
            ]
        });
    </script>
    <script>
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
        $('#nama_departemen_add').on('change', function() {
            let id_dept = $(this).val();
            var holding = '{{ $holding->id }}';

            let url = "{{ url('/bagian/get_divisi') }}" + "/" + id_dept + "/" + holding
            // console.log(holding);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_dept: id_dept
                // },
                success: function(response) {
                    // console.log(response);
                    $('#nama_divisi_add').html(response);
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $('#nama_departemen_update').on('change', function() {
            let id_dept = $(this).val();
            var holding = '{{ $holding->id }}';
            let url = "{{ url('/bagian/get_divisi') }}" + "/" + id_dept + "/" + holding;
            // console.log(id_dept);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_dept: id_dept
                // },
                success: function(response) {
                    // console.log(response);
                    $('#nama_divisi_update').html(response);
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $(document).on("click", "#btn_edit_bagian", function() {
            let id = $(this).data('id');
            let dept = $(this).data("dept");
            let divisi = $(this).data("divisi");
            let bagian = $(this).data("bagian");
            let holding = $(this).data("holding");
            // console.log(dept);
            $('#id_bagian_update').val(id);
            $('#nama_departemen_update option').filter(function() {
                // console.log($(this).val().trim());
                return $(this).val().trim() == dept
            }).prop('selected', true)
            $('#nama_divisi_update option').filter(function() {
                // console.log($(this).val().trim());
                return $(this).val().trim() == divisi
            }).prop('selected', true)
            $('#nama_bagian_update').val(bagian);
            $('#modal_edit_bagian').modal('show');

        });
        $('#btn_bagian_update').on('click', function(e) {
            $('#modal_edit_bagian').modal('hide');
            e.preventDefault();
            var formData = new FormData();
            // console.log(formData);
            // console.log($('#nama_bagian_update').val());
            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_bagian', $('#id_bagian_update').val());
            formData.append('nama_departemen', $('#nama_departemen_update').val());
            formData.append('nama_divisi', $('#nama_divisi_update').val());
            formData.append('nama_bagian', $('#nama_bagian_update').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/bagian/update/') }}" + '/' + holding,
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
                        $('#table_bagian').DataTable().ajax.reload();
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
        $('#btn_save_bagian').on('click', function(e) {
            $('#modal_tambah_bagian').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_departemen', $('#nama_departemen_add').val());
            formData.append('nama_divisi', $('#nama_divisi_add').val());
            formData.append('nama_bagian', $('#nama_bagian_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/bagian/insert/') }}" + '/' + holding,
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
                        $('#bagian_add_form')[0].reset();
                        $('#table_bagian').DataTable().ajax.reload();
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
        $(document).on('click', '#btn_delete_bagian', function() {
            var id = $(this).data('id');
            let divisi = $(this).data("divisi");
            let holding = $(this).data("holding");
            var menu_id = $('#asoy').val();
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
                        url: "@if (Auth::user()->is_admin == 'hrd'){{ url('/hrd/bagian/delete/') }}@else{{ url('/bagian/delete/') }}@endif" +
                            '/' + id + '/' + holding + '/' + menu_id,
                        type: "GET",
                        data: {
                            id: id,
                            divisi: divisi,
                            holding: holding,
                        },
                        error: function() {
                            alert('Something is wrong');
                        },
                        success: function(data) {
                            // console.log(data.status);
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data anda berhasil di hapus.',
                                    icon: 'success',
                                    timer: 1500
                                })
                                $('#table_bagian').DataTable().ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Bagian Masih Terhubung Karyawan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_bagian').DataTable().ajax.reload();
                            } else if (data.status == 0) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Bagian Masih Terhubung Jabatan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_bagian').DataTable().ajax.reload();
                            }
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
        $(document).on('click', '#btn_lihat_jabatan', function() {

            let id = $(this).data('id');
            let holding = $(this).data("holding");
            let url = "{{ url('jabatan/jabatan-datatable') }}" +
                '/' + id + '/' + holding;
            // console.log(url);
            var table1 = $('#table_lihat_jabatan').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                retrieve: true,
                ajax: {
                    url: url,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_jabatan',
                        name: 'nama_jabatan'
                    },
                ],
                order: [
                    [1, 'asc'],
                    [2, 'asc'],
                ],
            });
            $('#modal_lihat_jabatan').modal('show');
            $('#modal_lihat_jabatan').on('hidden.bs.modal', function(e) {
                table1.destroy();
                $('#table_bagian').DataTable().ajax.reload();
            })
        });
    </script>
@endsection
