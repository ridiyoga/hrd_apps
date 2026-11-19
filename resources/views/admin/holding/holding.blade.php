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
                            <h5 class="card-title m-0 me-2">DATA MASTER HOLDING</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- modal add Holding -->
                        <div class="modal fade" id="modal_add_holding" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form id="tambah_form" class="modal-content">>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="backDropModalTitle">Tambah Holding</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="nama_holding_add" name="nama_holding"
                                                            class="form-control" placeholder="Masukkan Nama Holding"
                                                            value="" />
                                                        <label for="nama_holding_add">Nama Holding</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="holding_category_add"
                                                            name="holding_category" class="form-control"
                                                            placeholder="Masukkan Kategori" value="" />
                                                        <label for="holding_category_add">Kategori Holding</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" id="holding_number_add" name="holding_number"
                                                            class="form-control" placeholder="Masukkan Nomor"
                                                            value="" />
                                                        <label for="holding_number_add">Nomor Holding</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="holding_name_hint_add"
                                                            name="holding_name_hint" class="form-control"
                                                            placeholder="Singkatan" value="" />
                                                        <label for="holding_name_hint_add">Singkatan Holding</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="file" id="holding_image_add" name="holding_image"
                                                            class="form-control" placeholder="Masukkan Nomor"
                                                            value="" />
                                                        <label for="holding_image_add">Logo Holding</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="kota_add" name="kota_add"
                                                            class="form-control"
                                                            placeholder="Masukkan Kota / Kabupaten Holding"
                                                            value="" />
                                                        <label for="kota_add">Kota / Kabupaten Holding (untuk keperluan ttd
                                                            kontrak)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="alamat_add" name="alamat_add"
                                                            class="form-control" placeholder="Masukkan Alamat Lengkap"
                                                            value="" />
                                                        <label for="alamat_add">Alamat</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="telepon_add" name="telepon"
                                                            class="form-control" placeholder="Telepon" value="" />
                                                        <label for="telepon_add">Telepon</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="row g-2">
                                                <div class="col mb-2">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" id="website_add" name="website"
                                                            class="form-control" placeholder="Website" value="" />
                                                        <label for="website_add">Website</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="button" id="btn_save_holding"
                                                class="btn btn-primary">Save</button>
                                        </div>
                                        <div class="modal-footer">

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- modal edit Holding-->
                        <div class="modal fade" id="modal_edit_holding" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Holding</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <input type="hidden" name="id_holding" id="id_holding" value="">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="nama_holding_update" name="nama_holding"
                                                        class="form-control" placeholder="Masukkan Nama Holding"
                                                        value="" />
                                                    <label for="nama_holding_update">Nama Holding</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="kategori_holding_update"
                                                        name="kategori_holding" class="form-control"
                                                        placeholder="Masukkan Jam Masuk" value="" />
                                                    <label for="kategori_holding_update">Kategori Holding</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" id="holding_number_update"
                                                        name="holding_number" class="form-control"
                                                        placeholder="Masukkan Nomor" value="" />
                                                    <label for="holding_number_update">Nomor Holding</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="holding_name_hint_update"
                                                        name="holding_name_hint" class="form-control"
                                                        placeholder="Singkatan" value="" />
                                                    <label for="holding_name_hint_update">Singkatan Holding</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="hidden" id="old_image_update">
                                                    <input type="file" id="holding_image_update" name="holding_image"
                                                        class="form-control" placeholder="Masukkan Nomor"
                                                        value="" />
                                                    <label for="holding_image_update">Logo Holding</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="kota_update" name="kota_update"
                                                        class="form-control"
                                                        placeholder="Masukkan Kota / Kabupaten Holding" value="" />
                                                    <label for="kota_update">Kota / Kabupaten Holding (untuk keperluan ttd
                                                        kontrak)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="alamat_update" name="alamat_update"
                                                        class="form-control" placeholder="Masukkan Alamat Lengkap"
                                                        value="" />
                                                    <label for="alamat_update">Alamat</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="telepon_update" name="telepon"
                                                        class="form-control" placeholder="Telepon" value="" />
                                                    <label for="telepon_update">Telepon</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="website_update" name="website"
                                                        class="form-control" placeholder="Website" value="" />
                                                    <label for="website_update">Website</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>

                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="btn_update_holding"
                                            class="btn btn-primary">Save</button>
                                    </div>
                                    <div class="modal-footer">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex master-table-toolbar">
                            <button type="button" id="btn_add_holding"
                                class="btn btn-sm btn-info waves-effect waves-light me-1"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_holding2" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Holding</th>
                                        <th>Kategori&nbsp;Holding</th>
                                        <th>Singkatan</th>
                                        <th>Kota / Kabupaten</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Website</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_holding" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Holding</th>
                                    <th>Kategori&nbsp;Holding</th>
                                    <th>Singkatan</th>
                                    <th>Logo</th>
                                    <th>Kota / Kabupaten</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Website</th>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = '{{ $holding->holding_code }}';
        var table = $('#table_holding').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('holding-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'holding_name',
                    name: 'holding_name'
                },
                {
                    data: 'holding_category',
                    name: 'holding_category'
                },
                {
                    data: 'holding_name_hint',
                    name: 'holding_name_hint'
                },
                {
                    data: 'holding_image',
                    name: 'holding_image'
                },
                {
                    data: 'kota',
                    name: 'kota'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'telepon',
                    name: 'telepon'
                },

                {
                    data: 'website',
                    name: 'website'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ]
        });
        $('#table_holding2').hide();
        var table2 = $('#table_holding2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('holding-datatable') }}" + '/' + holding,
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
                    data: 'holding_name',
                    name: 'holding_name'
                },
                {
                    data: 'holding_category',
                    name: 'holding_category'
                },
                {
                    data: 'holding_name_hint',
                    name: 'holding_name_hint'
                },
                {
                    data: 'kota',
                    name: 'kota'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'telepon',
                    name: 'telepon'
                },

                {
                    data: 'website',
                    name: 'website'
                },
            ]
        });
    </script>
    <script>
        $(document).on("click", "#btn_add_holding", function() {
            $('#modal_add_holding').modal('show');
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
        $(document).on("click", "#btn_edit_holding", function() {
            let id = $(this).data('id');
            let holding = $(this).data("holding");
            let kategori = $(this).data("category");
            let holding_name_hint = $(this).data("holding_name_hint");
            let holding_number = $(this).data("holding_number");
            let holding_image = $(this).data("holding_image");
            let kota = $(this).data("kota");
            let alamat = $(this).data("alamat");
            let telepon = $(this).data("telepon");
            let website = $(this).data("website");
            // console.log(jamkeluar);
            $('#id_holding').val(id);
            $('#nama_holding_update').val(holding);
            $('#kategori_holding_update').val(kategori);
            $('#holding_name_hint_update').val(holding_name_hint);
            $('#holding_number_update').val(holding_number);
            $('#old_image_update').val(holding_image);
            $('#kota_update').val(kota);
            $('#alamat_update').val(alamat);
            $('#telepon_update').val(telepon);
            $('#website_update').val(website);
            $('#modal_edit_holding').modal('show');

        });
        $('#btn_save_holding').on('click', function(e) {
            $('#modal_add_holding').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_holding', $('#nama_holding_add').val());
            formData.append('holding_category', $('#holding_category_add').val());
            formData.append('holding_number', $('#holding_number_add').val());
            formData.append('holding_name_hint', $('#holding_name_hint_add').val());
            var fileInput = $('#holding_image_add')[0];
            if (fileInput.files.length > 0) {
                formData.append('holding_image', fileInput.files[0]);
            }
            formData.append('kota', $('#kota_add').val());
            formData.append('alamat', $('#alamat_add').val());
            formData.append('telepon', $('#telepon_add').val());
            formData.append('website', $('#website_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            $.ajax({
                type: "POST",

                url: "{{ url('/holding/post') }}",
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
                        $('#tambah_form').trigger('reset')
                        $('#modal_add_holding').modal('hide');
                        $('#table_holding').DataTable().ajax.reload();
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
        $('#btn_update_holding').on('click', function(e) {
            $('#modal_edit_holding').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_holding', $('#id_holding').val());
            formData.append('nama_holding', $('#nama_holding_update').val());
            formData.append('holding_category', $('#kategori_holding_update').val());
            formData.append('holding_number', $('#holding_number_update').val());
            formData.append('holding_name_hint', $('#holding_name_hint_update').val());
            formData.append('old_image', $('#old_image_update').val());
            var fileInput = $('#holding_image_update')[0];
            if (fileInput.files.length > 0) {
                formData.append('holding_image', fileInput.files[0]);
            }
            formData.append('kota', $('#kota_update').val());
            formData.append('alamat', $('#alamat_update').val());
            formData.append('telepon', $('#telepon_update').val());
            formData.append('website', $('#website_update').val());
            formData.append('menu_id', $('#menu_id_add').val());


            $.ajax({
                type: "POST",

                url: "{{ url('/holding/update') }}",
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
                        $('#modal_edit_holding').modal('hide');
                        $('#table_holding').DataTable().ajax.reload();
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
        $(document).on('click', '#btn_delete_holding', function() {
            // console.log('ddd');
            // $('#modal_delete_riwayat').modal('show');
            var id = $(this).data('id');
            var holding_image = $(this).data('holding_image');
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
                    // console.log(id_kontrak);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id,
                            holding_image: holding_image,
                            menu_id: menu_id,
                        },
                        url: "{{ url('/holding/delete') }}",
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
                                $('#table_holding').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                                // console.log(data.data_riwayat);

                            } else {
                                $('#table_holding').DataTable()
                                    .ajax
                                    .reload();
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
    </script>
@endsection
