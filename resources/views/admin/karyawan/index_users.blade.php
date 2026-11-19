@extends('admin.layouts.dashboard')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style type="text/css">
    #card-profile::backdrop {
        background-color: red;

    }

    .swal2-container {
        z-index: 9999;
    }

    .myFont {
        font-size: 4pt !important;
    }

    .select2-container--bootstrap-5 .select2-selection.is-invalid {
        border-color: #dc3545;
    }

    .img_resign {
        position: absolute;
        top: 50%;
        left: 80%;
        transform: translate(-50%, -50%);
    }

    .select2-container {
        z-index: 9998 !important;
    }

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



    /* ===============================
               FORM FLOATING LOOK
                ================================ */
    .form-floating-outline .form-control {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background-color: #fafafa;
        transition: all 0.25s ease;
        font-size: 0.9rem;
    }

    .form-floating-outline .form-control:focus {
        background-color: #fff;
        border-color: #7367f0;
        box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12);
    }

    /* Label lebih halus */
    .form-floating-outline label {
        font-size: 0.78rem;
        color: #6b7280;
    }

    /* posisi loader */
    .select-loader {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        z-index: 20;
        /* INI PENTING */
        pointer-events: none;
    }

    /* dot animation */
    .dot-loader {
        display: flex;
        gap: 4px;
    }

    .dot-loader span {
        width: 6px;
        height: 6px;
        background-color: #7367f0;
        border-radius: 50%;
        opacity: 0.3;
        animation: dotPulse 1.2s infinite ease-in-out;
    }

    .dot-loader span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot-loader span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dotPulse {
        0% {
            opacity: 0.3;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.3;
        }
    }

    .index-users-shell,
    .index-users-tabs,
    .index-users-tab-content,
    .index-users-pane.active {
        min-height: 0;
    }

    .index-users-shell {
        display: flex;
        height: 100%;
        padding: 0;
    }

    .index-users-card {
        flex: 1 1 auto;
        min-width: 0;
    }

    .index-users-header,
    .index-users-toolbar,
    .index-users-divider,
    .index-users-tabs-nav {
        flex: 0 0 auto;
    }

    .index-users-toolbar {
        display: block !important;
    }

    .index-users-tabs {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        overflow: hidden;
    }

    .index-users-tab-content {
        flex: 1 1 auto;
        overflow: hidden;
    }

    .index-users-pane.active {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
</style>
@endsection
@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y master-table-page" style="font-size: small;">
    <div class="row gy-4">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="modal fade" id="modal_non_aktif_user" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" action="{{ url('users/user_app/non_aktif_proses') }}" class="modal-content"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Form Non Aktif User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mt-2">
                                <div class="col-md-12" style="position: relative;">
                                    <div class="card mb-4" id="card-profile">
                                        <img id="icon_resign" style="visibility: hidden;"
                                            src="{{ asset('admin/assets/img/resign.png') }}" alt=""
                                            class="img_resign d-block w-px-150 h-px-120 rounded" />
                                        <!-- Account -->
                                        <div class="card-body">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                <input type="hidden" name="id_nonactive" id="id_nonactive"
                                                    value="">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                                    alt="user-avatar" class="d-block w-px-120 h-px-120 rounded"
                                                    id="template_foto_karyawan" />
                                                <table>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_nama"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Divisi</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_divisi"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jabatan</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_jabatan"></td>
                                                    <tr>
                                                        <th>Kontrak Kerja</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_kontrak_kerja"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Penempatan Kerja</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_penempatan_kerja"> </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tgl Mulai Kontrak</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_mulai_kontrak"> </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tgl Selesai Kontrak</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_selesai_kontrak"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline">
                                        <textarea rows="10" id="alasan_non_aktif" name="alasan_non_aktif"
                                            class="form-control @error('alasan_non_aktif') is-invalid @enderror" placeholder="Alasan"></textarea>
                                        <label for="alasan_non_aktif">Alasan User Non Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-xs btn-success">
                                Save
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modal_aktif_user" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" action="{{ url('users/user_app/aktif_proses') }}" class="modal-content"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Form Aktif User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mt-2">
                                <div class="col-md-12" style="position: relative;">
                                    <div class="card mb-4" id="card-profile">
                                        <img id="icon_resign" style="visibility: hidden;"
                                            src="{{ asset('admin/assets/img/resign.png') }}" alt=""
                                            class="img_resign d-block w-px-150 h-px-120 rounded" />
                                        <!-- Account -->
                                        <div class="card-body">
                                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                <input type="hidden" name="id_active" id="id_active" value="">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                                    alt="user-avatar" class="d-block w-px-120 h-px-120 rounded"
                                                    id="template_foto_karyawan" />
                                                <table>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_nama_active"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Divisi</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_divisi_active"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jabatan</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_jabatan_active"></td>
                                                    <tr>
                                                        <th>Kontrak Kerja</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_kontrak_kerja_active"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Penempatan Kerja</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_penempatan_kerja_active"> </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tgl Mulai Kontrak</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_mulai_kontrak_active"> </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tgl Selesai Kontrak</th>
                                                        <td>&nbsp;</td>
                                                        <td>:</td>
                                                        <td id="td_selesai_kontrak_active"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating form-floating-outline">
                                        <textarea rows="10" id="alasan_aktif" name="alasan_aktif"
                                            class="form-control @error('alasan_aktif') is-invalid @enderror" placeholder="Alasan"></textarea>
                                        <label for="alasan_aktif">Alasan User Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-xs btn-success">
                                Save
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modal_edit_karyawan" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" id="form_edit_karyawan" class="modal-content"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="" id="id_user" name="id_user">
                        <input type="hidden" name="id_karyawan_access" id="id_karyawan_access" value="">
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Edit Karyawan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>&nbsp;Data User</h4>
                                    </div>
                                    <!-- Account -->
                                    <div class="card-body">

                                        <div class="row g-2 mt-3">
                                            <div class="col-sm-12 col-md-3 col-lg-2">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                                    alt="user-avatar" class="d-block w-px-120 h-px-120 rounded"
                                                    id="template_foto_karyawan" />
                                            </div>
                                            <div class="col-sm-12 col-lg-10 col-md-9">
                                                <div class="form-floating form-floating-outline position-relative p-2">
                                                    <input type="text" value="" id="username_update"
                                                        id="username_update" class="form-control">
                                                    <label for="username_update">Username : </label>
                                                </div>
                                                <div
                                                    class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                    <input type="text" value="**********" class="form-control"
                                                        readonly>
                                                    <label for="password_update">Password : </label>
                                                </div>
                                                <div
                                                    class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                    <select class="form-control" id="level_update"
                                                        name="level_update">
                                                        <option value="">Pilih Level</option>
                                                        <option value="user">MOBILE</option>
                                                        <option value="admin">WEB</option>
                                                    </select>
                                                    <label for="level_update">Level Akses : </label>
                                                </div>
                                                <div
                                                    class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                    <select class="form-control" id="karyawan"
                                                        name="karyawan"></select>
                                                    <!-- loader -->
                                                    <label for="karyawan">Pilih Karyawan : </label>
                                                    <div class="select-loader d-none">
                                                        <div class="dot-loader">
                                                            <span></span>
                                                            <span></span>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn_save_edit_karyawan btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modal_tambah_approval_karyawan" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" id="form_add_approval_karyawan" class="modal-content"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="" id="id_approval" name="id_approval">
                        <input type="hidden" name="id_karyawan_approval" id="id_karyawan_approval" value="">
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Tambah Approval Karyawan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h4 class="card-header">&nbsp;Profil</h4>
                                    <!-- Account -->
                                    <div class="card-body">
                                        <div class="d-flex align-items-start align-items-sm-center gap-4">

                                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="user-avatar"
                                                class="d-block w-px-120 h-px-120 rounded"
                                                id="template_foto_karyawan" />

                                            <table>
                                                <tr>
                                                    <th>Nama</th>
                                                    <td>&nbsp;</td>
                                                    <td>:</td>
                                                    <td id="td_name_approval"></td>
                                                </tr>
                                                <tr>
                                                    <th>Departemen</th>
                                                    <td>&nbsp;</td>
                                                    <td>:</td>
                                                    <td id="td_departemen_approval"> </td>
                                                </tr>
                                                <tr>
                                                    <th>Divisi</th>
                                                    <td>&nbsp;</td>
                                                    <td>:</td>
                                                    <td id="td_divisi_approval"> </td>
                                                </tr>
                                                <tr>
                                                    <th>Jabatan</th>
                                                    <td>&nbsp;</td>
                                                    <td>:</td>
                                                    <td id="td_jabatan_approval"></td>
                                                <tr>
                                                    <th>Kontrak Kerja</th>
                                                    <td>&nbsp;</td>
                                                    <td>:</td>
                                                    <td id="td_kontrak_kerja_approval"> </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="col-lg-12">
                                <select name="approval_role[]" id="approval_role" class="form-control" multiple
                                    style="width: 100%;" data-placeholder="Pilih Role Approval">


                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn_save_add_approval btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modal_tambah_users" role="dialog" data-bs-backdrop="static"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" id="form_add_karyawan" class="modal-content" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Tambah User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">

                                    <div class="row g-2 mt-3">
                                        <div class="col-sm-12 col-md-3 col-lg-2">
                                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="user-avatar"
                                                class="d-block w-px-120 h-px-120 rounded"
                                                id="template_foto_karyawan" />
                                        </div>
                                        <div class="col-sm-12 col-lg-10 col-md-9">
                                            <div class="form-floating form-floating-outline position-relative p-2">
                                                <input type="text" value="" id="username" name="username"
                                                    class="form-control">
                                                <label for="username">Username : </label>
                                                <div class="alert alert-danger invalid-feedback"><i
                                                        class="tf-icons mdi mdi-close me-1"></i></div>
                                            </div>
                                            <div
                                                class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                <input type="text" value="" class="form-control"
                                                    id="password" name="password">
                                                <label for="password">Password : </label>
                                                <div class="alert alert-danger invalid-feedback"><i
                                                        class="tf-icons mdi mdi-close me-1"></i></div>
                                            </div>

                                            <div
                                                class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                <input type="text" value="" class="form-control"
                                                    id="password_confirmation" name="password_confirmation">
                                                <label for="password_confirmation">Konfirmasi Password : </label>
                                                <div class="alert alert-danger invalid-feedback"><i
                                                        class="tf-icons mdi mdi-close me-1"></i></div>
                                            </div>

                                            <div
                                                class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                <select class="form-control" id="level" name="level">
                                                    <option value="">Pilih Level</option>
                                                    <option value="user">MOBILE</option>
                                                    <option value="admin">WEB</option>
                                                </select>
                                                <label for="level">Level Akses : </label>
                                                <div class="alert alert-danger invalid-feedback"></div>
                                            </div>
                                            <div
                                                class="form-floating form-floating-outline position-relative p-2 mt-2">
                                                <select class="form-control" id="karyawan_user"
                                                    name="karyawan_user"></select>
                                                <!-- loader -->
                                                <label for="karyawan_user">Pilih Karyawan : </label>
                                                <div class="select-loader d-none">
                                                    <div class="dot-loader">
                                                        <span></span>
                                                        <span></span>
                                                        <span></span>
                                                    </div>
                                                </div>
                                                <div class="alert alert-danger invalid-feedback"><i
                                                        class="tf-icons mdi mdi-close me-1"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn_save_add_karyawan btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal fade" id="modal_import_user_karyawan" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" action="{{ url('/users/user_app/ImportUser/' . $holding) }}"
                        class="modal-content" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Import Add User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mt-2">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="file" id="file_excel" name="file_excel"
                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                            class="form-control" placeholder="Masukkan File" />
                                        <label for="file_excel">File Excel</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <a href="{{ asset('admin/template_import/TEMPLATE IMPORT TAMBAH KARYAWAN BULANAN SP_SPS.xlsx') }}"
                                    type="button" download="" class="btn btn-xs btn-primary"> Download Format
                                    Excel</a>
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
            <div class="modal fade" id="modal_import_update_user_karyawan" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <form method="post" action="{{ url('/users/user_app/ImportUpdateUser/' . $holding) }}"
                        enctype="multipart/form-data" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Import Update User Karyawan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mt-2">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="file" id="file_excel_update" name="file_excel_update"
                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                            class="form-control" placeholder="Masukkan File" />
                                        <label for="file_excel_update">File Excel</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <a href="{{ asset('admin/template_import/TEMPLATE IMPORT UPDATE KARYAWAN BULANAN SP_SPS.xlsx') }}"
                                    type="button" download="" class="btn btn-xs btn-primary"> Download Format
                                    Excel</a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <form>
                </div>
            </div>
            <div class="modal fade" id="modal_export_user_karyawan" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="backDropModalTitle">Export Excel User Karyawan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-2 mt-2">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <h6>Download File Excel Data User Karyawan</h6>
                                        <a href="{{ url('users/user_app/ExportUser/' . $holding) }}" type="button"
                                            class="btn btn-xs btn-success"> Download Excel</a>
                                    </div>
                                </div>
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
            <div class="card-body index-users-shell">
                <div class="card master-table-card index-users-card">
                    <div class="card-header index-users-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA USER KARYAWAN</h5>
                        </div>
                    </div>
                    <div class="card-body index-users-toolbar">
                        <a type="button" href="javascript:void(0)" id="btn_tambah_users"
                            class="btn btn-xs btn-primary waves-effect waves-light"><i
                                class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</a>

                        <button class="btn btn-xs btn-success waves-effect waves-light dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="menu-icon tf-icons mdi mdi-file-excel"></i> Excel
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#modal_import_user_karyawan" href="">Import Add Excel</a>
                            </li>
                            <li><a class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#modal_import_update_user_karyawan" href="">Import Update
                                    Excel</a></li>
                            <li><a class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#modal_export_user_karyawan" href="#">Export Excel</a></li>
                        </ul>
                        <a type="button" href="{{ url('users/user_app/pdfUserKaryawan/' . $holding) }}"
                            class="btn btn-xs btn-danger waves-effect waves-light"><i
                                class="menu-icon tf-icons mdi mdi-file-pdf-box"></i>PDF</a>
                    </div>

                    <hr class="my-3 index-users-divider">
                    <div class="nav-align-top index-users-tabs">
                        <div class="row index-users-tabs-nav">
                            <div class="col-6">
                                <ul class="nav nav-pills nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <a type="button" style="width: auto;" class="nav-link active"
                                            role="tab" data-bs-toggle="tab" href="#navs-pills-justified-home">
                                            <i class="tf-icons mdi mdi-account-tie me-1"></i><span
                                                class="d-none d-sm-block">Karyawan Bulanan</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a type="button" style="width: auto;" class="nav-link" role="tab"
                                            data-bs-toggle="tab" href="#navs-pills-justified-profile">
                                            <i class="tf-icons mdi mdi-account me-1"></i><span
                                                class="d-none d-sm-block">Karyawan Harian</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content index-users-tab-content">
                            <div class="tab-pane fade show active index-users-pane" id="navs-pills-justified-home" role="tabpanel">
                                <div class="master-table-area">
                                <table class="table" id="table_karyawan_bulanan"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Divisi</th>
                                            <th>Jabatan</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="tab-pane fade index-users-pane" id="navs-pills-justified-profile" role="tabpanel">
                                <div class="master-table-area">
                                <table class="table" id="table_karyawan_harian"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nomor&nbsp;ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nama&nbsp;Karyawan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Username&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Aksess&nbsp;Menu&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
                                            <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </th>
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        var menu_id = $('#asoy_sub').val();
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
        var holding = '{{ $holding->holding_code }}';
        // console.log(holding);
        var table = $('#table_karyawan_bulanan').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('users_bulanan-datatable') }}" + '/' + holding,
            },
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
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'user_aktif',
                    name: 'user_aktif'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [5, 'asc']
            ]
        });
        var table1 = $('#table_karyawan_harian').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('users_harian-datatable') }}" + '/' + holding,
            },
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
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'akses',
                    name: 'akses'
                },
                {
                    data: 'user_aktif',
                    name: 'user_aktif'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        $('#approval_role').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Approval...',
            allowClear: true,
            minimumResultsForSearch: 0,
            closeOnSelect: false
        });
        $('#karyawan').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Karyawan...',
            allowClear: true,
            minimumResultsForSearch: 0,
            closeOnSelect: false,
            dropdownParent: $('#modal_edit_karyawan')
        });
        $('#karyawan_user').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Karyawan...',
            allowClear: true,
            minimumResultsForSearch: 0,
            closeOnSelect: false,
            dropdownParent: $('#modal_tambah_users')
        });

        function showSelectLoading(id) {
            const $select = $(id);
            const $container = $select.next('.select2-container');

            $select.prop('disabled', true);
            $container.css('position', 'relative');

            if ($container.find('.select-loader').length === 0) {
                $container.append(`
            <div class="select-loader">
                <div class="dot-loader">
                    <span></span><span></span><span></span>
                </div>
            </div>`);
            }
        }

        function hideSelectLoading(id) {
            const $select = $(id);
            const $container = $select.next('.select2-container');

            $select.prop('disabled', false);
            $container.find('.select-loader').remove();
        }

        function resetValidation(form) {
            $(form).find('.form-control').removeClass('is-invalid');
            $(form).find('.invalid-feedback').html('');
        }

        function showValidationErrors(form, errors) {
            // console.log(errors, form);
            $.each(errors, function(field, messages) {
                let input = $(form).find('[name="' + field + '"]');

                input.addClass('is-invalid');
                if (input.hasClass('select2-hidden-accessible')) {
                    input.next('.select2-container')
                        .find('.select2-selection')
                        .addClass('is-invalid');
                }
                input.closest('.form-floating')
                    .find('.invalid-feedback')
                    .html(messages[0]);
            });
        }
        $(document).on('click', '.btn_add_access_karyawan', function() {
            let id = $(this).data('id');
            let idkaryawan = $(this).data('idkaryawan');
            let holding = $(this).data("holding");
            let name = $(this).data("name");
            let jabatan = $(this).data("jabatan");
            let divisi = $(this).data("divisi");
            let departemen = $(this).data("departemen");
            let kontrak_kerja = $(this).data("kontrak");
            // console.log('id: ' + id + ', idkaryawan: ' + idkaryawan + ', holding: ' + holding + ', name: ' + name + ', jabatan: ' + jabatan + ', divisi: ' + divisi + ', departemen: ' + departemen + ', kontrak_kerja: ' + kontrak_kerja);
            $('#table-roles').DataTable().destroy();
            var table1 = $('#table-roles').DataTable({
                pageLength: 50,
                "scrollY": true,
                "scrollX": true,
                "autoWidth": false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('access/role_access_datatable') }}" + '/' + id + '/' +
                        holding,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_akses',
                        name: 'nama_akses'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'list_menu',
                        name: 'list_menu'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                ]
            });
            setTimeout(function() {
                table1.columns.adjust().draw();
            }, 50);

            // console.log(id, idkaryawan, holding, name, jabatan, divisi, departemen, kontrak_kerja);
            $('#modal_edit_karyawan').modal('show');


            $('#td_name_access').text(name);
            $('#td_departemen_access').text(departemen);
            $('#td_jabatan_access').text(jabatan);
            $('#td_divisi_access').text(divisi);
            if (kontrak_kerja == null) {
                $('#td_kontrak_kerja_access').text('-');
            } else {
                $('#td_kontrak_kerja_access').text(kontrak_kerja);
            }

            $('#id_karyawan_access').val(idkaryawan);
            $('#id_access').val(id);

        });
        $(document).on('click', '#btn_edit_karyawan', function() {
            showSelectLoading('#karyawan');
            let id = $(this).data('id');
            let username = $(this).data('username');
            let level = $(this).data('level');
            let holding = '{{ $holding->holding_code }}';
            let karyawan = $(this).data('karyawan');
            $.ajax({
                url: "{{ url('users/user_app/edit_karyawan') }}" + '/' + id + '/' + holding,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    // console.log(data);
                    if (data.code == 200) {

                        var option = '<option value="">Pilih Karyawan</option>';

                        data.data.forEach(element => {
                            if (karyawan == element.nomor_identitas_karyawan) {
                                selected = 'selected';
                            } else {
                                selected = '';
                            }
                            option += '<option value="' + element
                                .nomor_identitas_karyawan + '" ' + selected + '>' +
                                element.name + '</option>';
                        })
                        $('#username_update').val(username);
                        $('#karyawan').html(option).trigger('change.select2');
                        $('#level_update').val(level);
                        $('#id_user').val(id);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
                        })
                    }
                    hideSelectLoading('#karyawan');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: jqXHR.responseJSON.message,
                    })
                }
            })
            $('#modal_edit_karyawan').modal('show');


            $('#td_name_access').text(name);
            $('#id_karyawan').val(id);

        });
        $(document).on('click', '.btn_save_edit_karyawan', function() {
            var id_user = $('#id_user').val();
            var username = $('#username_update').val();
            var level = $('#level_update').val();
            var karyawan = $('#karyawan').val();
            var url = "{{ url('users/user_app/update_user_karyawan') }}/" + holding;
            // console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id_user': id_user,
                    'karyawan': karyawan,
                    'level': level,
                    'username': username,
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    // console.log(data);
                    $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    $('#form_edit_karyawan')[0].reset();
                    $('#modal_edit_karyawan').modal('hide');
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
                        })
                    }
                },
                error: function(data) {
                    // console.log(data);
                    $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    $('#form_edit_karyawan')[0].reset();
                    $('#modal_edit_karyawan').modal('hide');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.responseJSON.message,
                        icon: 'error',
                    })
                }
            })
        });
        $(document).on('click', '.btn_add_approval_karyawan', function() {
            let id = $(this).data('id');
            let idkaryawan = $(this).data('idkaryawan');
            let holding = $(this).data("holding");
            let level = $(this).data("level");
            let name = $(this).data("name");
            let jabatan = $(this).data("jabatan");
            let divisi = $(this).data("divisi");
            let departemen = $(this).data("departemen");
            let kontrak_kerja = $(this).data("kontrak");
            let approvalRaw = $(this).attr("data-approval");
            let approval = [];

            try {
                let parsed = JSON.parse(approvalRaw);

                // Jika hasilnya object tunggal → jadikan array
                if (Array.isArray(parsed)) {
                    approval = parsed;
                } else if (typeof parsed === 'object' && parsed !== null) {
                    approval = [parsed];
                }

            } catch (e) {
                // console.log("JSON Parse Error:", e);
            }


            // console.log(approval, approvalRaw);


            $('#td_name_approval').text(name);
            $('#td_departemen_approval').text(departemen);
            $('#td_jabatan_approval').text(jabatan);
            $('#td_divisi_approval').text(divisi);
            if (kontrak_kerja == null) {
                $('#td_kontrak_kerja_approval').text('-');
            } else {
                $('#td_kontrak_kerja_approval').text(kontrak_kerja);
            }

            $('#id_karyawan_approval').val(idkaryawan);
            $('#id_approval').val(id);
            var url = "{{ url('users/user_app/approval_role_option') }}" + '/' + level + '/' + holding;
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    // console.log(data);
                    $('#approval_role').empty();
                    if (data.code == 200) {
                        $.each(data.data, function(key, value) {
                            $('#approval_role').append('<option value="' + value
                                .id_approval + '">' + value.nama_approval +
                                '</option>');
                        });

                        if (approval != null || approval != '') {
                            // Ambil semua ID approval dari JSON
                            let selectedIds = approval.map(function(a) {
                                return a.approval_id ?? a.id ?? a.id_approval;
                            });

                            // console.log("Selected IDs =", selectedIds);

                            // Set selected ke select2
                            $('#approval_role').val(selectedIds).trigger('change');
                        }

                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            timer: 450000
                        })
                    }
                },
                error: function(data) {
                    // console.log('Error:', data);
                }
            });
            $('#modal_tambah_approval_karyawan').modal('show');

        });
        $(document).on('click', '.btn_save_add_approval', function() {
            var id_approval = $('#id_approval').val();
            var id_user_approval = $('#id_user_approval').val();
            var id_karyawan_approval = $('#id_karyawan_approval').val();
            var approval_role = $('#approval_role').val();
            var url = "{{ url('users/user_app/approval_save_add') }}/" + holding;
            // console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'id_approval': id_approval,
                    'id_karyawan_approval': id_karyawan_approval,
                    'approval_role': approval_role,
                    'id_user_approval': id_user_approval
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
                    $('#table_approval_karyawan').DataTable().ajax.reload();
                    $('#form_add_approval_karyawan')[0].reset();
                    $('#modal_tambah_approval_karyawan').modal('hide');
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
                    $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    $('#table_karyawan_harian').DataTable().ajax.reload();
                },
                error: function(data) {
                    // console.log(data);
                    $('#form_add_approval_karyawan')[0].reset();
                    $('#modal_tambah_approval_karyawan').modal('hide');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.responseJSON.message,
                        icon: 'error',
                        timer: 4500
                    })
                    $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    $('#table_karyawan_harian').DataTable().ajax.reload();
                }
            })
        });
        $('#nama_karyawan').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#modal_tambah_users .modal-content'),
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            dropdownCssClass: "myFont"
        });
        $(document).on("click", "#btn_edit_password", function() {
            let id = $(this).data('id');
            // console.log(holding);
            let url = "{{ url('/users/user_app/edit-password/') }}" + '/' + id + '/' + holding;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        allowOutsideClick: false,
                        background: 'transparent',
                        html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                        showCancelButton: false,
                        showConfirmButton: false,
                    })
                },
                success: function(response) {
                    Swal.close();
                    window.location.assign(url);
                },
                error: function(data) {
                    // console.log(data.responseJSON);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Error : ' + data.responseJSON.message +
                            ' ' + data.responseJSON.line + ' ' + data.responseJSON
                            .file,
                        showConfirmButton: true,
                    });
                },

            })
        });
        $(document).on('click', '#btn_non_aktif_karyawan', function() {
            var id = $(this).data('id');
            var holding = $(this).data("holding");
            var nama = $(this).data('nama');
            var divisi = $(this).data('divisi');
            var jabatan = $(this).data('jabatan');
            var bagian = $(this).data('bagian');
            var foto = $(this).data('foto');
            var tgl_mulai_kontrak = $(this).data('tgl_mulai_kontrak');
            var tgl_selesai_kontrak = $(this).data('tgl_selesai_kontrak');
            var kontrak_kerja = $(this).data('kontrak_kerja');
            var penempatan_kerja = $(this).data('penempatan_kerja');
            var status_aktif = $(this).data('status_aktif');
            // console.log(status_aktif);
            if (status_aktif == 'NON AKTIF') {
                $('.img_resign').show();
            } else {
                $('.img_resign').hide();
            }
            if (foto == '' | foto == null) {
                $('#template_foto_karyawan').attr('src',
                    "{{ asset('admin/assets/img/avatars/1.png') }}");
            } else {
                $('#template_foto_karyawan').attr('src',
                    "{{ url('storage/app/public/foto_karyawan/') }}" + foto);
            }
            $('#td_nama').html(nama);
            $('#td_divisi').html(divisi);
            $('#td_jabatan').html(jabatan);
            $('#td_bagian').html(bagian);
            $('#td_jabatan').html(jabatan);
            $('#td_mulai_kontrak').html(tgl_mulai_kontrak);
            $('#td_selesai_kontrak').html(tgl_selesai_kontrak);
            $('#td_kontrak_kerja').html(kontrak_kerja);
            $('#td_penempatan_kerja').html(penempatan_kerja);
            $('#id_nonactive').val(id);
            $('#modal_non_aktif_user').modal('show');
        });
        $(document).on('click', '#btn_tambah_users', function() {
            showSelectLoading('#karyawan_user');
            $.ajax({
                url: "{{ url('users/user_app/get_user_karyawan') }}" + '/' + holding,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    // console.log(data.data);
                    $('#karyawan_user').empty();
                    if (data.code == 200) {

                        var option = '<option value="">Pilih Karyawan</option>';

                        data.data.forEach(element => {

                            option += '<option value="' + element
                                .nomor_identitas_karyawan + '">' + element.name +
                                ' </option>';
                        })
                        $('#karyawan_user').append(option);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message,
                        })
                    }
                    hideSelectLoading('#karyawan_user');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: jqXHR.responseJSON.message,
                    })
                }
            })
            $('#modal_tambah_users').modal('show');
        });
        $(document).on('click', '#btn_aktif_karyawan', function() {

            var status_aktif = $(this).data('status_aktif');
            if (status_aktif == 'NON AKTIF') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Info',
                    text: 'Karyawan Non Aktif',
                    showConfirmButton: true,
                });
                $('.img_resign').show();
            } else {
                $('.img_resign').hide();
                var id = $(this).data('id');
                var holding = $(this).data("holding");
                var nama = $(this).data('nama');
                var divisi = $(this).data('divisi');
                var jabatan = $(this).data('jabatan');
                var bagian = $(this).data('bagian');
                var foto = $(this).data('foto');
                var tgl_mulai_kontrak = $(this).data('tgl_mulai_kontrak');
                var tgl_selesai_kontrak = $(this).data('tgl_selesai_kontrak');
                var kontrak_kerja = $(this).data('kontrak_kerja');
                var penempatan_kerja = $(this).data('penempatan_kerja');
                // console.log(id);

                if (foto == '' | foto == null) {
                    $('#template_foto_karyawan').attr('src',
                        "{{ asset('admin/assets/img/avatars/1.png') }}");
                } else {
                    $('#template_foto_karyawan').attr('src',
                        "{{ url('storage/app/public/foto_karyawan/') }}" + foto);
                }
                $('#td_nama_active').html(nama);
                $('#td_divisi_active').html(divisi);
                $('#td_jabatan_active').html(jabatan);
                $('#td_bagian_active').html(bagian);
                $('#td_jabatan_active').html(jabatan);
                $('#td_mulai_kontrak_active').html(tgl_mulai_kontrak);
                $('#td_selesai_kontrak_active').html(tgl_selesai_kontrak);
                $('#td_kontrak_kerja_active').html(kontrak_kerja);
                $('#td_penempatan_kerja_active').html(penempatan_kerja);
                $('#id_active').val(id);
                $('#modal_aktif_user').modal('show');
            }
        });
        $('#form_add_karyawan').on('input change', '.form-control', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.form-floating')
                .find('.invalid-feedback')
                .html('');
        });
        $(document).on('click', '.btn_save_add_karyawan', function() {
            var username = $('#username').val();
            var level = $('#level').val();
            var karyawan_user = $('#karyawan_user').val();
            var password = $('#password').val();
            var password_confirmation = $('#password_confirmation').val(); //password
            var url = "{{ url('users/user_app/prosesTambahUser') }}/" + holding;
            // console.log(url);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'karyawan_user': karyawan_user,
                    'level': level,
                    'username': username,
                    'password': password,
                    'password_confirmation': password_confirmation
                },
                beforeSend: function() {
                    resetValidation('#form_add_karyawan');
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    // console.log(data);
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 4500
                        })
                        $('#table_karyawan_bulanan').DataTable().ajax.reload();
                        $('#form_add_karyawan')[0].reset();
                        $('#modal_tambah_users').modal('hide');
                    } else if (data.code == 500) {
                        showValidationErrors('#form_add_karyawan', data.message);
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'warning',
                        })
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                        })
                    }
                },
                error: function(data) {
                    showValidationErrors('#formUser', data.responseJSON.errors);
                    $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    $('#form_add_karyawan')[0].reset();
                    // $('#modal_tambah_users').modal('hide');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.responseJSON.message,
                        icon: 'error',
                    })
                }
            })
        });
    });
</script>
@endsection
