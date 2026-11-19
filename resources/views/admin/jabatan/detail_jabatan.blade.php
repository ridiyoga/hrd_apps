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
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2"><a
                                    href="@if (Auth::user()->is_admin == 'hrd') {{ url('hrd/jabatan/' . $holding->holding_code) }}@else{{ url('jabatan/' . $holding->holding_code) }} @endif"><i
                                        class="mdi mdi-arrow-left-bold"></i></a>&nbsp;DAFTAR JABATAN</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-1" style="max-width: 500px; font-size: 0.85rem;">

                            <!-- Baris Pembuat -->
                            <div class="col-4 text-secondary py-0.5">DEPARTEMEN</div>
                            <div class="col-8 text-dark py-0.5">: {{ $divisi->Departemen->nama_departemen ?? '-' }}</div>

                            <!-- Baris No Legal -->
                            <div class="col-4 text-secondary py-0.5">DIVISI</div>
                            <div class="col-8 text-dark py-0.5">: {{ $divisi->nama_divisi ?? '-' }}</div>

                        </div>
                        <hr class="my-3">


                        <div class="modal fade" id="modal_karyawan_jabatan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class=" modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle"> Daftar Karyawan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-lg-12">
                                            <table class="table" id="table_karyawan_jabatan"
                                                style="width: 100%; font-size: small;">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama&nbsp;Karyawan</th>
                                                        <th>Jabatan</th>
                                                        <th>Bagian</th>
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
                        <div class="modal fade" id="modal_tambah_jabatan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form id="form_add_jabatan" method="post" class=" modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Jabatan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <input type="hidden" id="nama_departemen_add" name="nama_departemen"
                                                    value="{{ $divisi->Departemen->id }}">
                                                <input type="hidden" id="nama_divisi_add" name="nama_divisi"
                                                    value="{{ $divisi->id }}">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control"
                                                        value="{{ $divisi->nama_divisi }}" readonly>
                                                    <label for="nama_divisi" class="float-left">Nama Divisi</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select class="form-control" id="nama_bagian_add" name="nama_bagian"
                                                        autofocus>
                                                        <option value=""> Pilih Bagian</option>
                                                        @foreach ($data_bagian as $bagian)
                                                            <option value="{{ $bagian->id }}">
                                                                {{ $bagian->nama_bagian }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="nama_bagian" class="float-left">Nama Bagian</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline_add">
                                                    <select class="form-control" id="nama_jabatan_add" name="nama_jabatan">
                                                        <option value=""> Pilih Jabatan</option>
                                                        @foreach ($jabatan_master as $master)
                                                            <option value="{{ $master->id }}">
                                                                {{ $master->jabatan_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="nama_bagian" class="float-left">Nama Jabatan</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="btn_save_jabatan"
                                            class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- modal edit -->
                        <div class="modal fade" id="modal_edit_jabatan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form method="" class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Jabatan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <input type="hidden" id="id_update">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input class="form-control" readonly id="nama_divisi_update"
                                                        name="nama_divisi" value="">
                                                    <label for="nama_divisi_update" class="float-left">Nama
                                                        Divisi</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select class="form-control" id="nama_bagian_update"
                                                        name="nama_bagian" autofocus>
                                                        <option value=""> Pilih Bagian</option>
                                                        @foreach ($data_bagian as $bagian)
                                                            <option value="{{ $bagian->id }}">
                                                                {{ $bagian->nama_bagian }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="nama_bagian" class="float-left">Nama Bagian</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input class="form-control" readonly id="nama_jabatan_update"
                                                        name="nama_jabatan" value="">
                                                    <label for="nama_jabatan_update" class="float-left">Nama
                                                        Divisi</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="btn_update_jabatan"
                                            class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex master-table-toolbar">
                            <button type="button" id="btn_add_jabatan"
                                class="btn btn-sm btn-info waves-effect waves-light me-1" data-bs-toggle="modal"
                                data-bs-target="#modal_tambah_jabatan"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_jabatan2" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Jabatan</th>
                                        <th>Bagian</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                            <table class="table" id="table_jabatan" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Jabatan</th>
                                        <th>Bagian</th>
                                        {{-- <th>Jumlah&nbsp;Karyawan</th> --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
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
        let holding = '{{ $holding->holding_code }}';
        var id = '{{ $divisi->id }}';
        // console.log(id);
        var table = $('#table_jabatan').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('jabatan-datatable') }}" + '/' + id + '/' + holding,
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
                {
                    data: 'nama_bagian',
                    name: 'nama_bagian'
                },
                // {
                //     data: 'jumlah_karyawan',
                //     name: 'jumlah_karyawan'
                // },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [1, 'asc'],
                [2, 'asc'],
            ],
        });
        $('#table_jabatan2').hide();

        var table2 = $('#table_jabatan2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('jabatan-datatable') }}" + '/' + id + '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'JABATAN DEPARTEMEN {{ $divisi->Departemen->nama_departemen ?? '-' }}, DIVISI {{ $divisi->nama_divisi ?? '-' }}',
                messageTop: '{{ $holding->holding_name }}',
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
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'nama_bagian',
                    name: 'nama_bagian'
                },
                // {
                //     data: 'jumlah_karyawan',
                //     name: 'jumlah_karyawan'
                // },
                // {
                //     data: 'option',
                //     name: 'option'
                // },
            ],
            order: [
                [1, 'asc'],
                [2, 'asc'],
            ],
        });
    </script>
    <script>
        $(document).on("click", "#btn_edit_jabatan", function() {
            let id = $(this).data('id');
            let bagian = $(this).data("bagian");
            let jabatan = $(this).data("jabatan");
            let divisi = $(this).data("divisi");
            // console.log(bagian);
            $('#id_update').val(id);
            $('#nama_jabatan_update').val(jabatan);
            $('#nama_bagian_update').val(bagian);
            $('#nama_divisi_update').val(divisi);
            $('#modal_edit_jabatan').modal('show');

        });
        $(document).on('click', '#btn_add_jabatan', function() {
            $('#form_add_jabatan').trigger('reset');
            $('#modal_tambah_jabatan').modal('show');
        });
        $(document).on('click', '#btn_lihat_bawahan', function() {

            let id = $(this).data('id');
            let holding = $(this).data("holding");
            let url =
                "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/bawahanjabatan-datatable') }}@else{{ url('bawahanjabatan-datatable') }}@endif" +
                '/' + id + '/' + holding;
            // console.log(url);
            var table1 = $('#table_bawahan_jabatan').DataTable({
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
                    {
                        data: 'nama_bagian',
                        name: 'nama_bagian'
                    },
                    {
                        data: 'jabatan_atasan',
                        name: 'jabatan_atasan'
                    },
                    {
                        data: 'jumlah_karyawan',
                        name: 'jumlah_karyawan'
                    },
                ],
                order: [
                    [1, 'asc'],
                    [2, 'asc'],
                    [4, 'asc'],
                ],
            });
            $('#modal_bawahan_jabatan').modal('show');
            $('#modal_bawahan_jabatan').on('hidden.bs.modal', function(e) {
                table1.destroy();
                $('#table_jabatan').DataTable().ajax.reload();
            })
        });
        $(document).on('click', '#btn_lihat_karyawan', function() {

            let id = $(this).data('id');
            let holding = $(this).data("holding");
            let url =
                "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/karyawanjabatan-datatable') }}@else{{ url('karyawanjabatan-datatable') }}@endif" +
                '/' + id + '/' + holding;
            // console.log(id);
            var table1 = $('#table_karyawan_jabatan').DataTable({
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'nama_jabatan',
                        name: 'nama_jabatan'
                    },
                    {
                        data: 'nama_bagian',
                        name: 'nama_bagian'
                    },
                ],
                order: [
                    [1, 'asc'],
                    [2, 'asc'],
                    [3, 'asc'],
                ],
            });
            $('#modal_karyawan_jabatan').modal('show');
            $('#modal_karyawan_jabatan').on('hidden.bs.modal', function(e) {
                table1.destroy();
                $('#table_jabatan').DataTable().ajax.reload();
            })
        });
        $(document).on('click', '#btn_delete_jabatan', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id = $(this).data('id');
            var menu_id = $('#asoy').val();
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id,
                            menu_id: menu_id,
                        },
                        url: "{{ url('/jabatan/delete/') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memuat Data...',
                                html: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal
                                        .showLoading();
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#table_jabatan').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#table_jabatan').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'error',
                                    text: data.error,
                                    icon: 'error',
                                    timer: 6500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        $('#btn_save_jabatan').on('click', function(e) {
            $('#modal_tambah_jabatan').modal('hide');

            e.preventDefault();
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_jabatan', $('#nama_jabatan_add').val());
            formData.append('nama_departemen', $('#nama_departemen_add').val());
            formData.append('nama_divisi', $('#nama_divisi_add').val());
            formData.append('nama_bagian', $('#nama_bagian_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // formData.append('main', $('#main_update').val());
            // console.log(formData);
            $.ajax({
                type: "POST",

                url: "{{ url('/jabatan/insert/') }}" + '/' + holding,
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
                    alert('Something is wrong');
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
                        $('#table_jabatan').DataTable().ajax.reload();
                        $('#form_add_jabatan').trigger('reset')
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
                            timer: 10000
                        })

                    }
                }

            });
        });
        $('#btn_update_jabatan').on('click', function(e) {
            $('#modal_edit_jabatan').modal('hide');

            e.preventDefault();
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_update').val());
            formData.append('nama_bagian', $('#nama_bagian_update').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // formData.append('main', $('#main_update').val());
            // console.log(formData);
            $.ajax({
                type: "POST",

                url: "{{ url('/jabatan/update/') }}",
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
                    alert('Something is wrong');
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
                        $('#table_jabatan').DataTable().ajax.reload();
                        $('#form_add_jabatan').trigger('reset')
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
                            timer: 10000
                        })

                    }
                }

            });
        });
    </script>
@endsection
