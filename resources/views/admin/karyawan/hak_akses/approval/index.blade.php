@extends('admin.layouts.dashboard')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.min.css">
<link rel="stylesheet" href="{{ asset('assets/assets_users/css/org_modern.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
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

    /* BIKIN NODE AUTO SIZE */
    .orgchart .node {
        width: auto !important;
        min-width: 180px;
        max-width: 360px;
        height: auto !important;
        padding: 6px 10px;
    }

    /* BIAR TEKS TURUN BARIS */
    .orgchart .node .title,
    .orgchart .node .content {
        white-space: normal !important;
        width: auto !important;
        max-width: none !important;
        word-break: break-word;
        height: auto !important;
        min-height: 30px;
        line-height: 1.2;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* OPTIONAL: biar kelihatan rapi */
    .orgchart .node .title {
        font-weight: 600;
        background-color: #9055fd !important;
    }

    .orgchart .node .content {
        border: 2px solid #9055fd !important;
    }

    .orgchart .lines .topLine {
        border-top: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .downLine {
        background-color: #9055fd !important;
        border: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .rightLine {
        border-right: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .leftLine {
        border-left: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    /* HAPUS BACKGROUND GRADIENT LINE */
    .orgchart {
        background-image: none !important;
        background: none !important;
    }

    .org-wrapper {
        border: 1px solid #9055fd !important;
        border-radius: 0.375rem !important;
    }

    /* ===============================
                                               TABLE LOADING OVERLAY
                                            ================================ */
    .table-loading {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(4px);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        color: #6b7280;
    }

    /* Spinner modern */
    .spinner {
        width: 36px;
        height: 36px;
        border: 4px solid #e5e7eb;
        border-top-color: #7367f0;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }


    /* Base Setup */
    #karyawan-app {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8f9fc;
        padding: 2rem 0;
    }

    /* Grid Layout */
    .emp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        padding: 1rem;
    }

    /* Card Design */
    .emp-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 24px;
        padding: 1.5rem;
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
        user-select: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .emp-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(108, 92, 231, 0.12);
        border-color: #6c5ce7;
    }

    /* Selection State */
    .emp-card.selected {
        background: #f8f7ff;
        border: 2px solid #6c5ce7;
    }

    /* Custom Checkbox Indicator */
    .check-indicator {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 24px;
        height: 24px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .emp-card.selected .check-indicator {
        background-color: #6c5ce7;
        border-color: #6c5ce7;
    }

    .emp-card.selected .check-indicator::after {
        /* Kode hex MDI untuk 'check' */
        content: "\F012C";
        font-family: "Material Design Icons";
        color: white;
        font-size: 18px;
        /* MDI biasanya terlihat lebih bagus sedikit lebih besar */
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    /* Profile Section */
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .avatar-box {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        margin-right: 1rem;
        box-shadow: 0 8px 16px rgba(108, 92, 231, 0.2);
    }

    .name-main {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3436;
        line-height: 1.2;
    }

    .emp-id {
        font-size: 0.75rem;
        color: #adb5bd;
        font-weight: 500;
    }

    /* Info Section */
    .info-group {
        margin-top: 1rem;
    }

    .label-caps {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #adb5bd;
        font-weight: 800;
        margin-bottom: 8px;
        display: block;
    }

    .list-unstyled {
        margin: 0;
        padding: 0;
    }

    .list-unstyled li {
        font-size: 0.85rem;
        color: #495057;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
    }

    /* Badge Utama */
    .badge-primary-soft {
        background-color: #eef2ff;
        color: #6366f1;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        margin-left: 8px;
    }
</style>
@endsection
@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">DATA HAK AKSES APPROVAL</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="modal fade" id="modal_tambah_karyawan" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                            <form method="post" id="form_add_approval" class="modal-content"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="approval_id" id="approval_id" value="">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="karyawan-app">
                                        <div class="container">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <div>
                                                    <h5 class="fw-bold mb-1"
                                                        style="font-family: 'Plus Jakarta Sans'; color: #2d3436;">Pilih
                                                        Karyawan</h5>
                                                    <div class="btn-group mt-2">
                                                        <button type="button" id="btn-select-all"
                                                            class="btn btn-sm btn-outline-primary border-0 fw-bold"
                                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                                            <i class="mdi mdi-check-all me-1"></i> SELECT ALL
                                                        </button>
                                                        <button type="button" id="btn-unselect-all"
                                                            class="btn btn-sm btn-outline-danger border-0 fw-bold ms-2"
                                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                                            <i class="mdi mdi-minus-circle-outline me-1"></i> UNSELECT
                                                            ALL
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="position-relative">
                                                    <span id="selected-count"
                                                        class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 me-3 d-none"
                                                        style="background: #eeebff; font-weight: 700;">
                                                        0 Terpilih
                                                    </span>
                                                    <input type="text" id="searchKaryawan"
                                                        class="form-control border-0 shadow-sm px-4 d-inline-block"
                                                        placeholder="Cari nama..."
                                                        style="border-radius: 12px; width: 200px; font-size: 13px;">
                                                </div>
                                            </div>

                                            <div id="karyawan-grid" class="emp-grid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                        <i class="mdi mdi-close"></i>&nbsp;Close
                                    </button>
                                    <button type="button" class="btn_save_add_karyawan btn btn-sm btn-primary"><i
                                            class="mdi mdi-content-save"></i>&nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="tableLoading" class="table-loading d-none">
                        <div class="loading-content">
                            <div class="spinner"></div>
                            <span>Memuat data...</span>
                        </div>
                    </div>
                    <div class="tree-wrapper mt-4 ">
                        <div id="approval-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Transactions -->
    <!--/ Data Tables -->
</div>
@endsection
@section('js')
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/js/jquery.orgchart.min.js"></script>
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

        let holding = "{{ $holding->holding_code }}";
        let holding_id = "{{ $holding->id }}";


        function showTableLoading() {
            $('#tableLoading').removeClass('d-none');
        }

        function hideTableLoading() {
            $('#tableLoading').addClass('d-none');
        }
        $('#group_id_update').on('change', function() {
            let get_group = $(this).val();
            let url = "{{ url('/approval/get_group') }}" + "/" + get_group + "/" + holding_id;
            // console.log(get_group);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    // console.log(response);
                    $('#parent_approval_id_update').empty();
                    let options = '<option value="">Pilih Parent...</option>';
                    for (let i = 0; i < response.data.length; i++) {
                        options += '<option value="' + response.data[i].id_approval + '">' +
                            response.data[i].nama_approval + '</option>';
                    }
                    $('#parent_approval_id_update').html(options);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.responseJSON.message,
                        icon: 'error',
                        timer: 4500
                    })

                },

            })
        });

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
        $(document).on('click', '.btn_save_add_karyawan', function() {
            var get_holding = '{{ $holding->holding_code }}';
            var approval_id = $('#approval_id').val();
            var menu = [];
            $('input[name="menu_id[]"]:checked').each(function() {
                menu.push($(this).val());
            });
            var url = "{{ url('users/hak_akses/save_approval') }}/" + get_holding;
            // console.log(menu);
            if (menu.length < 0) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Anda belum memilih menu',
                    icon: 'error',
                    timer: 4500
                })
                return false;
            } else {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id': approval_id,
                        'menu_id': menu,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        console.log(data);
                        $('#form_add_approval')[0].reset();
                        $('#modal_tambah_karyawan').modal('hide');
                        $('#table_akses_karyawan').DataTable().ajax.reload();
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
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_add_approval')[0].reset();
                        $('#modal_tambah_karyawan').modal('hide');
                        $('#table_akses_karyawan').DataTable().ajax.reload();
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            }
        })
        $(document).on('click', '#btn-edit-approval', function() {
            var id_approval = $(this).data('id_approval');
            var nama_approval = $(this).data('nama_approval');
            var level_approval = $(this).data('level_approval');
            var departemen = $(this).data('departemen');
            var holding = $(this).data('holding');
            var parent_approval_id = $(this).data('parent_approval_id');
            var group_id = $(this).data('group_id');
            let url = "{{ url('/approval/get_parent') }}" + "/" + id_approval + "/" + group_id + "/" +
                holding_id;
            // console.log(departemen);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
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
                success: function(response) {
                    // console.log(response);
                    Swal.close();
                    $('#departemen_id_update').empty();
                    $('#parent_approval_id_update').empty();
                    let options = '<option value="">Pilih Parent...</option>';
                    for (let i = 0; i < response.data.length; i++) {
                        options += '<option value="' + response.data[i].id_approval + '" ' +
                            (response.data[i].id_approval === parent_approval_id ?
                                'selected' : '') + '>' + response.data[i].nama_approval +
                            '</option>';
                    }
                    if (!group_id) {
                        group_id = [];
                    }
                    if (!holding) {
                        holding = [];
                    }
                    if (!departemen) {
                        departemen = [];
                    }
                    let options_group = '<option value="">Pilih Group...</option>';
                    response.group.forEach(grp => {
                        const selected = group_id.includes(grp.id_group) ?
                            'selected' : '';
                        options_group +=
                            `<option value="${grp.id_group}" ${selected}>${grp.nama_group}</option>`;
                    });
                    let options_departemen =
                        '<option value="">Pilih Departemen...</option>';
                    response.departemen.forEach(dep => {
                        const selected = departemen.includes(dep.id) ? 'selected' :
                            '';
                        options_departemen +=
                            `<option value="${dep.id}" ${selected}>${dep.nama_departemen}</option>`;
                    });
                    $('#group_id_update').html(options_group);
                    $('#parent_approval_id_update').html(options);
                    $('#departemen_id_update').html(options_departemen);
                },
                error: function(data) {
                    Swal.close();

                    Swal.fire({
                        title: 'Gagal',
                        text: data.responseJSON.message,
                        icon: 'error',
                        timer: 4500
                    })
                },

            });
            $('#id_approval_update').val(id_approval);
            $('#nama_approval_update').val(nama_approval);
            $('#level_approval_update').val(level_approval);
            // $('#parent_approval_id_update').val(parent_approval_id);
            $('#group_id_update').val(group_id);
            $('#departemen_id_update').val(departemen);
            $('#modal_edit_approval').modal('show');
        });
        $(document).on('click', '.btn_save_update_approval', function() {
            var get_holding = '{{ $holding->holding_code }}';
            var id_approval = $('#id_approval_update').val();
            var nama_approval = $('#nama_approval_update').val();
            var level_approval = $('#level_approval_update').val();
            var parent_approval_id = $('#parent_approval_id_update').val();
            var departemen_id_update = $('#departemen_id_update').val();
            var group_id = $('#group_id_update').val();
            var url = "{{ url('approval/approval_save_update') }}/" + get_holding;
            // console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id_approval': id_approval,
                    'nama_approval': nama_approval,
                    'level_approval': level_approval,
                    'parent_approval_id': parent_approval_id,
                    'group_id': group_id,
                    'departemen_id_update': departemen_id_update,
                },
                beforeSend: function() {
                    Swal.fire({
                        allowOutsideClick: false,
                        background: 'transparent',
                        html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                        showCancelButton: false,
                        showConfirmButton: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    // console.log(data);
                    loadTreeview();
                    $('#form_edit_approval')[0].reset();
                    $('#modal_edit_approval').modal('hide');
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
                            text: data.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                },
                error: function(data) {
                    // console.log(data);
                    $('#form_edit_approval')[0].reset();
                    $('#modal_edit_approval').modal('hide');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.responseJSON.message,
                        icon: 'error',
                        timer: 4500
                    })
                }
            })
        });
        $(document).on('click', '#btn-delete-approval', function() {
            let id_approval = $(this).data('id_approval'); // ambil id dari tombol
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data approval akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                    $.ajax({
                        url: "{{ url('/approval/approval_delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_approval: id_approval,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                allowOutsideClick: false,
                                background: 'transparent',
                                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                                showCancelButton: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.code == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                loadTreeview();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });
                }
            });
        });
        loadTreeview();

        function loadTreeview() {
            showTableLoading();
            $.ajax({
                url: "{{ url('users/hak_akses/approvalChart') }}/" + holding,
                method: "GET",
                success: function(response) {
                    // console.log(response);
                    $('#approval-container').empty();
                    response.forEach(function(g) {

                        let chartId = 'chart-' + g.id;

                        $('#approval-container').append(
                            `<div class="mb-5"><h5>${g.nama_group}</h5><div id="${chartId}" class="org-wrapper"></div></div>`
                        );

                        $('#' + chartId).orgchart({
                            'data': g.tree[0], // root
                            'nodeContent': 'title',
                            createNode: function($node, data) {
                                const customHtml = `
                                    <div class="modern-card">
                                        <div class="card-header-gradient">
                                        </div>
                                        <div class="card-body card-chart">
                                            <div class="avatar-wrapper">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="avatar-img">
                                            </div>
                                            <div class="info-wrapper">
                                                <div class="emp-name">${data.name}</div>
                                                <div class="emp-title">${data.title}</div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                `;
                                $node.html(customHtml);
                                // SIMPAN DATA KE NODE
                                $node.attr('data-id', data.id);
                                $node.attr('data-name', data.name);
                                $node.attr('data-title', data.title);
                                $node.attr('data-level', data.level ?? '');
                                $node.attr('data-group', data.group_id);

                                // EVENT CLICK
                                $node.on('click', function(e) {
                                    e.stopPropagation(); // WAJIB

                                    showApprovalDetail({
                                        id: data.id,
                                        name: data.name,
                                        title: data.title,
                                        level: data.level,
                                        group: data.group_id
                                    });
                                });
                            },
                            'pan': true,
                            'zoom': true
                        });
                    });
                    hideTableLoading();
                },
                error: function() {
                    hideTableLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server'
                    });
                }
            });
        }

        function showApprovalDetail(data) {
            // console.log(data);
            var id = data.id;
            var group = data.group;
            $('#approval_id').val(id);

            $.ajax({
                url: "{{ url('users/hak_akses/karyawan-datatable') }}" + '/' + id + '/' + group +
                    '/' + holding,
                method: "GET",
                success: function(response) {
                    const grid = $('#karyawan-grid');
                    grid.empty();
                    $('#modal_tambah_karyawan').modal('show');
                    response.data.forEach(function(emp) {
                        console.log(emp);
                        const initial = emp.name.charAt(0);
                        const isSelected = emp.is_checked ? 'selected' : '';
                        const isChecked = emp.is_checked ? 'checked' : '';

                        const card = `
                        <div class="emp-card ${isSelected}" data-id="${emp.id}">
                            <div class="check-indicator"></div>
                            
                            <div>
                                <div class="profile-header">
                                    <div class="avatar-box">${initial}</div>
                                    <div>
                                        <div class="name-main">${emp.name}</div>
                                        <div class="emp-id">EMP-${emp.nomor_identitas_karyawan}</div>
                                    </div>
                                </div>

                                <div class="info-group">
                                    <span class="label-caps">Departemen</span>
                                    <ul class="list-unstyled">
                                        ${renderDepartment(emp.karyawan_jabatan)}
                                    </ul>
                                </div>

                                <div class="info-group">
                                    <span class="label-caps">Jabatan & Bagian</span>
                                    <ul class="list-unstyled">
                                        ${renderJabatan(emp.karyawan_jabatan)}
                                    </ul>
                                </div>
                            </div>

                            <input type="checkbox" name="menu_id[]" value="${emp.id}" 
                                   class="d-none emp-checkbox" ${isChecked}>
                        </div>
                    `;

                        grid.append(card);
                    });
                    updateCount();
                    $('#searchKaryawan').off('keyup').on('keyup', function() {

                        let value = $(this).val().toLowerCase();

                        $('#karyawan-grid .emp-card').filter(function() {

                            $(this).toggle(
                                $(this).text().toLowerCase().indexOf(value) > -1
                            );

                        });
                    });
                }
            });
        }
        // --- Logika SELECT ALL ---
        $('#btn-select-all').on('click', function() {
            $('.emp-card').each(function() {
                const card = $(this);
                const indicator = card.find('.check-indicator');
                const checkbox = card.find('.emp-checkbox');

                // Set ke kondisi terpilih
                card.addClass('selected');
                checkbox.prop('checked', true);

                // Masukkan icon MDI jika belum ada
                if (indicator.is(':empty')) {
                    indicator.html(
                        '<i class="mdi mdi-check text-white" style="font-size: 18px;"></i>');
                }
            });
            updateCount();
        });

        // --- Logika UNSELECT ALL ---
        $('#btn-unselect-all').on('click', function() {
            $('.emp-card').each(function() {
                const card = $(this);
                const indicator = card.find('.check-indicator');
                const checkbox = card.find('.emp-checkbox');

                // Reset ke kondisi awal
                card.removeClass('selected');
                checkbox.prop('checked', false);
                indicator.empty();
            });
            updateCount();
        });
        $('#searchKaryawan').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $("#karyawan-grid .col-md-4").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        function renderDepartment(jabatans) {
            if (!jabatans || jabatans.length === 0) return '<li>-</li>';
            let items = [];
            let printedDepts = [];

            jabatans.forEach(j => {
                let deptName = j.departemen ? j.departemen.nama_departemen : null;
                if (deptName && !printedDepts.includes(deptName)) {
                    printedDepts.push(deptName);
                    let badge = j.main == 1 ? '<span class="badge-primary-soft">UTAMA</span>' : '';
                    items.push(`<li>${deptName} ${badge}</li>`);
                }
            });
            return items.join('');
        }

        // Helper: Render Jabatan - Bagian
        function renderJabatan(jabatans) {
            if (!jabatans || jabatans.length === 0) return '<li>-</li>';
            return jabatans.map(j => {
                let label = `${j?.jabatan?.nama_jabatan} - ${j?.bagian?.nama_bagian}`;
                let style = j.main == 1 ? 'style="font-weight: 600; color: #2d3436;"' : '';
                return `<li ${style}><i class="bi bi-dot me-1"></i> ${label}</li>`;
            }).join('');
        }
        $(document).on('click', '.emp-card', function() {
            const card = $(this);
            const checkbox = card.find('.emp-checkbox');

            card.toggleClass('selected');
            checkbox.prop('checked', !checkbox.prop('checked'));

            updateCount();
        });

        function updateCount() {
            const count = $('.emp-card.selected').length;
            const badge = $('#selected-count');
            if (count > 0) {
                badge.removeClass('d-none').text(count + ' Terpilih');
            } else {
                badge.addClass('d-none');
            }
        }
    });
</script>
@endsection