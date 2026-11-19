@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
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
                            <h5 class="card-title m-0 me-2">DATA MASTER SHIFT</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="modal fade" id="modal_tambah_shift" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form method="post" id="form_tambah_shift" class="modal-content"
                                    enctype="multipart/form-data">

                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Shift</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="nama_shift" name="nama_shift"
                                                        class="form-control @error('nama_shift') is-invalid @enderror"
                                                        placeholder="Masukkan Nama Shift" value="{{ old('nama_shift') }}" />
                                                    <label for="nama_shift">Nama Shift</label>
                                                </div>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_masuk" name="jam_masuk"
                                                        class="form-control @error('jam_masuk') is-invalid @enderror"
                                                        placeholder="Masukkan jam_masuk" value="{{ old('jam_masuk') }}" />
                                                    <label for="jam_masuk">Jam Masuk</label>
                                                </div>
                                                @error('jam_masuk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_min_masuk" name="jam_min_masuk"
                                                        class="form-control @error('jam_min_masuk') is-invalid @enderror"
                                                        placeholder="Masukkan jam_min_masuk"
                                                        value="{{ old('jam_min_masuk') }}" />
                                                    <label for="jam_min_masuk">Jam Minimal Masuk</label>
                                                </div>
                                                @error('jam_min_masuk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_terlambat" name="jam_terlambat"
                                                        class="form-control @error('jam_terlambat') is-invalid @enderror"
                                                        placeholder="Masukkan Jam Terlambat"
                                                        value="{{ old('jam_terlambat') }}" />
                                                    <label for="jam_terlambat">Jam Terlambat</label>
                                                </div>
                                                @error('jam_terlambat')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_pulang_cepat" name="jam_pulang_cepat"
                                                        class="form-control @error('jam_pulang_cepat') is-invalid @enderror"
                                                        placeholder="Masukkan Jam Pulang Cepat"
                                                        value="{{ old('jam_pulang_cepat') }}" />
                                                    <label for="jam_pulang_cepat">Jam Pulang Cepat</label>
                                                </div>
                                                @error('jam_pulang_cepat')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_keluar" name="jam_keluar"
                                                        class="form-control @error('jam_keluar') is-invalid @enderror"
                                                        placeholder="Masukkan jam_keluar"
                                                        value="{{ old('jam_keluar') }}" />
                                                    <label for="jam_keluar">Jam Pulang</label>
                                                </div>
                                                @error('jam_keluar')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Tutup
                                        </button>
                                        <button type="button" id="btn_simpan_shift"
                                            class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- modal edit -->
                        <div class="modal fade" id="modal_edit_shift" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form method="post"
                                    action="@if (Auth::user()->is_admin == 'hrd') {{ url('hrd/shift/update/' . $holding->id) }}@else {{ url('/shift/update/' . $holding->id) }} @endif"
                                    class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Shift</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <input type="hidden" name="id_shift" id="id_shift" value="">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="nama_shift_update" name="nama_shift_update"
                                                        class="form-control @error('nama_shift_update') is-invalid @enderror"
                                                        placeholder="Masukkan Nama Shift" value="" />
                                                    <label for="nama_shift_update">Nama Shift</label>
                                                </div>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_masuk_update" name="jam_masuk_update"
                                                        class="form-control @error('jam_masuk_update') is-invalid @enderror"
                                                        placeholder="Masukkan Jam Masuk" value="" />
                                                    <label for="jam_masuk_update">Jam Masuk</label>
                                                </div>
                                                @error('jam_masuk_update')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="time" id="jam_keluar_update" name="jam_keluar_update"
                                                        class="form-control @error('jam_keluar_update') is-invalid @enderror"
                                                        placeholder="Masukkan jam_keluar_update" value="" />
                                                    <label for="jam_keluar_update">Jam Keluar</label>
                                                </div>
                                                @error('jam_keluar_update')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
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
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_shift"><i
                                    class="menu-icon tf-icons mdi mdi-plus "></i>Tambah</button>
                            <table class="table" id="table_shift2" style="width: 100%; font-size: small;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Shift</th>
                                        <th>Jam&nbsp;Min&nbsp;Masuk</th>
                                        <th>Jam&nbsp;Masuk</th>
                                        <th>Jam&nbsp;Batas&nbsp;Terlambat</th>
                                        <th>Jam&nbsp;Keluar</th>
                                        <th>Jam&nbsp;Batas&nbsp;Pulang</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_shift" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Shift</th>
                                    <th>Jam&nbsp;Min&nbsp;Masuk</th>
                                    <th>Jam&nbsp;Masuk</th>
                                    <th>Jam&nbsp;Batas&nbsp;Terlambat</th>
                                    <th>Jam&nbsp;Keluar</th>
                                    <th>Jam&nbsp;Batas&nbsp;Pulang</th>
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
        let holding = window.location.pathname.split("/").pop();
        var table = $('#table_shift').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/shift-datatable') }}@else {{ url('shift-datatable') }}@endif" +
                    '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'jam_min_masuk',
                    name: 'jam_min_masuk'
                },
                {
                    data: 'jam_masuk',
                    name: 'jam_masuk'
                },
                {
                    data: 'jam_terlambat',
                    name: 'jam_terlambat'
                },
                {
                    data: 'jam_keluar',
                    name: 'jam_keluar'
                },
                {
                    data: 'jam_pulang_cepat',
                    name: 'jam_pulang_cepat'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ]
        });
        $('#table_shift2').hide();
        var table2 = $('#table_shift2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/shift-datatable') }}@else {{ url('shift-datatable') }}@endif" +
                    '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'MASTER CUTI',
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
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'jam_min_masuk',
                    name: 'jam_min_masuk'
                },
                {
                    data: 'jam_masuk',
                    name: 'jam_masuk'
                },
                {
                    data: 'jam_terlambat',
                    name: 'jam_terlambat'
                },
                {
                    data: 'jam_keluar',
                    name: 'jam_keluar'
                },
                {
                    data: 'jam_pulang_cepat',
                    name: 'jam_pulang_cepat'
                },
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
        $(document).on("click", "#btn_edit_shift", function() {
            let id = $(this).data('id');
            let shift = $(this).data("shift");
            let jammasuk = $(this).data("jammasuk");
            let jamkeluar = $(this).data("jamkeluar");
            let holding = $(this).data("holding");
            // console.log(jamkeluar);
            $('#id_shift').val(id);
            $('#nama_shift_update').val(shift);
            $('#jam_masuk_update').val(jammasuk);
            $('#jam_keluar_update').val(jamkeluar);
            $('#modal_edit_shift').modal('show');

        });
        $(document).on("click", "#btn_simpan_shift", function() {
            let shift = $('#nama_shift').val();
            let jammasuk = $('#jam_masuk').val();
            let jamkeluar = $('#jam_keluar').val();
            let jamminmasuk = $('#jam_min_masuk').val();
            let jamterlambat = $('#jam_terlambat').val();
            let jampulangcepat = $('#jam_pulang_cepat').val();
            let url =
                "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/shift/store/' . $holding->holding_code) }}@else {{ url('/shift/store/' . $holding->holding_code) }} @endif";
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data akan disimpan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            nama_shift: shift,
                            jam_masuk: jammasuk,
                            jam_keluar: jamkeluar,
                            jam_min_masuk: jamminmasuk,
                            jam_terlambat: jamterlambat,
                            jam_pulang_cepat: jampulangcepat,
                        },
                        error: function(data) {
                            let errors = data.responseJSON.message;
                            // console.log(errors);
                            let message = "<ul>";
                            Object.keys(errors).forEach(function(key) {
                                message += "<li>" + errors[key][0] + "</li>";
                            });
                            message += "</ul>";
                            Swal.fire({
                                title: 'Error',
                                html: message,
                                icon: 'error',
                                customClass: {
                                    htmlContainer: 'swal-left' // kasih class custom
                                },
                                timer: 4500
                            });
                        },
                        success: function(data) {
                            // console.log(data);
                            if (data.code === 200) {
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 4500
                                });
                            } else if (data.code === 400) {
                                let errors = data.responseJSON.errors;
                                let message = "";
                                Object.keys(errors).forEach(function(key) {
                                    message += errors[key][0] + "\n";
                                });
                                Swal.fire({
                                    title: 'Error!',
                                    text: message,
                                    icon: 'error',
                                    timer: 4500
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    timer: 4500
                                });
                            }
                            $('#modal_tambah_shift').modal('hide');
                            $('#form_tambah_shift').trigger('reset');
                            $('#table_shift').DataTable().ajax.reload();
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
        $(document).on('click', '#btn_delete_shift', function() {
            var id = $(this).data('id');
            let holding = $(this).data("holding");
            // console.log(id, holding);
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
                        url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/shift/delete/') }} @else {{ url('/shift/delete/') }}@endif" +
                            '/' + id + '/' + holding,
                        type: "GET",
                        error: function() {
                            alert('Something is wrong');
                        },
                        success: function(data) {
                            if (data.code === 200) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 4500
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    timer: 4500
                                });
                            }
                            $('#table_shift').DataTable().ajax.reload();
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
    </script>
@endsection
