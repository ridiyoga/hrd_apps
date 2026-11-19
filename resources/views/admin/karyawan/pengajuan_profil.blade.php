@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css">
    <style>
        .swal2-container {
            z-index: 9999;
        }

        /* Menghilangkan border default Select2 dan menyisakan border-bottom */
        .select2-container--bootstrap-5 .select2-selection,
        .select2-container--default .select2-selection--single {
            border: none !important;
            border-bottom: 1px solid #dee2e6 !important;
            /* Warna border-bottom Bootstrap */
            border-radius: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            background-color: transparent !important;
            height: calc(3.5rem + 2px) !important;
            /* Menyesuaikan tinggi form-floating */
        }

        /* Menghilangkan shadow saat fokus */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            box-shadow: none !important;
            border-bottom: 2px solid #666cff !important;
            /* Warna saat fokus (sesuaikan tema) */
        }

        /* Memperbaiki posisi teks di dalam Select2 agar lurus dengan label floating */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 4.5rem !important;
            /* Menurunkan posisi teks agar pas di tengah */
        }

        /* Menyesuaikan posisi panah dropdown */
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            right: 0 !important;
        }

        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           SELECT LOADING
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ================================ */
        .select-loader {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            z-index: 5;
            pointer-events: none;
        }

        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           DOT LOADER (3 DOT)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ================================ */
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

        .dot-loader span:nth-child(1) {
            animation-delay: 0s;
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

        .card {
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card.shadow-sm:hover {
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
        }

        .card-header {
            font-weight: 600;
            border-bottom: 2px solid rgba(0, 0, 0, .05);
            padding: 1rem 1.25rem;
        }

        /* Form Floating Outline Customization */
        .form-floating-outline {
            position: relative;
        }



        .form-select.border-bottom:focus {
            border-bottom: 2px solid #6f24b6 !important;
            /* Warna biru primary */
            box-shadow: none;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f2f4f7;
            color: #566a7f;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            border-top: none;
        }

        .table tbody td {
            font-size: 0.875rem;
            color: #697a8d;
            vertical-align: middle;
            padding: 12px 15px;
        }

        /* Button Styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 20px;
            transition: transform 0.1s ease;
        }

        .btn:active {
            transform: scale(0.96);
        }

        .btn-info {
            color: #fff;
            background-color: #03c3ec;
            border-color: #03c3ec;
        }

        /* Alert/Error Styling */
        .text-danger {
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 5px;
        }

        /* Badge-like Look for Table Actions */
        .table .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        /* Animasi untuk transisi tab atau konten */
        .fade {
            transition: opacity 0.15s linear;
        }

        /* Styling Tab Pills */
        .nav-pills .nav-link {
            color: #697a8d;
            background-color: #f0f2f4;
            border-radius: 6px;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background-color: #5a8dee;
            color: #fff;
            box-shadow: 0 2px 4px rgba(90, 141, 238, 0.4);
        }

        /* Background subtle untuk variasi */
        .bg-light-subtle {
            background-color: #fafbfc;
        }

        .bg-warning-subtle {
            background-color: #fff8e1;
        }

        /* Table Khusus */
        .table-sm td,
        .table-sm th {
            padding: 10px 8px;
            font-size: 0.85rem;
        }

        .badge.bg-label-secondary {
            background-color: #f1f2f4;
            color: #8592a3;
        }

        /* Styling Kartu Dokumen */
        .document-card {
            transition: all 0.2s ease-in-out;
            background-color: #ffffff;
        }

        .document-card:hover {
            border-color: #696cff !important;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.1);
        }

        /* Border putus-putus untuk area upload */
        .border-dashed {
            border: 2px dashed #d9dee3 !important;
            transition: background 0.2s;
        }

        .border-dashed:hover {
            background-color: #f5f5f9;
            border-color: #696cff !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Tombol kecil khusus icon saja */
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-label-secondary {
            background-color: #f0f2f4;
            color: #8592a3;
            border: none;
        }

        .btn-label-secondary:hover {
            background-color: #e1e4e8;
        }


        .document-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3) !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .bg-light-subtle {
            background-color: #f9fafb !important;
        }

        .modern-selector-card {
            border: 1px solid #e0e6ed !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            transition: all 0.3s ease;
        }

        /* Mengubah tampilan Button Group agar terlihat seperti toggle modern */
        .modern-btn-group {
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            display: flex;
        }

        .modern-btn-group .btn-check+.btn {
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            background: transparent;
            transition: all 0.2s ease-in-out;
            flex: 1;
        }

        /* Styling saat tombol terpilih (Checked) */
        .modern-btn-group .btn-check:checked+.btn-outline-success {
            background-color: #ffffff !important;
            color: #10b981 !important;
            shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        }

        .modern-btn-group .btn-check:checked+.btn-outline-primary {
            background-color: #ffffff !important;
            color: #3b82f6 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        }

        .modern-btn-group .btn-check:checked+.btn-outline-secondary {
            background-color: #ffffff !important;
            color: #475569 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        }

        /* Label styling */
        .label-modern {
            letter-spacing: 0.5px;
            color: #94a3b8;
        }

        .swal2-popup {
            font-family: 'Inter', 'Public Sans', sans-serif !important;
            border-radius: 15px !important;
        }

        .swal2-title {
            color: #5d596c !important;
        }

        /* Supaya tidak terlihat seperti spinner bawaan Bootstrap atau SweetAlert yang membosankan, */
        /* kita bisa mengganti loader default tersebut dengan CSS Loading Animation yang lebih modern (seperti gaya Double Ring atau Pulse). Berikut adalah cara merombak total visual loading-nya: 1. Custom CSS (Animasi Modern) Tambahkan CSS ini untuk menciptakan spinner kustom yang jauh lebih halus dan bergaya teknologi modern. CSS <style> */

        /* Menghilangkan spinner default Swal */
        .swal2-loader {
            display: none !important;
        }

        /* Container kustom untuk loader */
        .custom-loader-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
        }

        /* Animasi Double Ring yang Elegan */
        .modern-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(102, 108, 255, 0.1);
            border-left-color: #666cff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: relative;
        }

        .modern-spinner::before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 4px solid rgba(102, 108, 255, 0.1);
            border-left-color: #00cfe8;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Efek teks berdenyut (Pulse) */
        .pulse-text {
            animation: pulse 1.5s ease-in-out infinite;
            font-weight: 500;
            color: #5d596c;
            letter-spacing: 1px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .badge-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }

        /* Varian Warna Soft UI */
        .bg-dot-success {
            background-color: #28c76f;
            box-shadow: 0 0 8px rgba(40, 199, 111, 0.4);
        }

        .bg-dot-primary {
            background-color: #666cff;
            box-shadow: 0 0 8px rgba(102, 108, 255, 0.4);
        }

        .bg-dot-warning {
            background-color: #ff9f43;
            box-shadow: 0 0 8px rgba(255, 159, 67, 0.4);
        }

        .bg-dot-danger {
            background-color: #ea5455;
            box-shadow: 0 0 8px rgba(234, 84, 85, 0.4);
        }

        .bg-dot-secondary {
            background-color: #82868b;
        }

        /* Animasi berkedip untuk status Aktif/Live */
        .badge-dot-animate {
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(1.2);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .pengajuan-profil-page {
            display: flex;
            flex-direction: column;
        }

        .pengajuan-profil-title,
        .pengajuan-profil-nav,
        .pengajuan-profil-card > .card-header,
        .pengajuan-profil-pane > .card-header {
            flex: 0 0 auto;
        }

        .pengajuan-profil-card {
            flex: 1 1 auto;
            height: auto !important;
            min-height: 0;
        }

        .pengajuan-profil-body,
        .pengajuan-profil-content,
        .pengajuan-profil-pane.active {
            min-height: 0;
        }

        .pengajuan-profil-content {
            flex: 1 1 auto;
            overflow: hidden;
        }

        .pengajuan-profil-pane.active {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page pengajuan-profil-page"
        style="font-size: small;">
        <h4 class="py-3 mb-4 pengajuan-profil-title"><span class="text-muted fw-light">KARYAWAN /</span> PENGAJUAN PERUBAHAN DATA</h4>
        <div class="card mb-4 master-table-card pengajuan-profil-card">
            <h4 class="card-header">DETAIL PROFIL</h4>
            <input type="hidden" id="menu_id_add" value="">
            <div class="card-body p-4 pengajuan-profil-body">

                <div class="nav-align-top mb-4 pengajuan-profil-nav">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#nav_profil" aria-controls="nav_profil" aria-selected="true"
                                id="profil_nav">
                                <i class="tf-icons mdi mdi-account-outline me-1"></i>
                                UBAH PROFIL
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#nav_history" aria-controls="nav_history" aria-selected="false"
                                id="history_nav">
                                <i class="tf-icons mdi mdi-account-school-outline me-1"></i>
                                RIWAYAT UBAH PROFIL
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#nav_alamat" aria-controls="nav_alamat" aria-selected="true"
                                id="alamat_nav">
                                <i class="tf-icons mdi mdi-account-outline me-1"></i>
                                UBAH ALAMAT
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#nav_history_alamat" aria-controls="nav_history_alamat"
                                aria-selected="false" id="history_alamat_nav">
                                <i class="tf-icons mdi mdi-account-school-outline me-1"></i>
                                RIWAYAT UBAH ALAMAT
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content p-0 pengajuan-profil-content">
                    <div class="tab-pane fade show active pengajuan-profil-pane" id="nav_profil" role="tabpanel">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center px-0">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>PENGAJUAN
                                UBAH DATA DIRI</h6>
                        </div>

                        <div class="master-table-area">
                            <table class="table table-hover mb-0" id="table_profil" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aksi</th>
                                        <th>No</th>
                                        <th>Nama&nbsp;Lengkap</th>
                                        <th>Waktu&nbsp;Pengajuan</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th>Pengubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade pengajuan-profil-pane" id="nav_history" role="tabpanel">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center px-0">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>RIWAYAT
                                UBAH DATA DIRI</h6>
                        </div>

                        <div class="master-table-area">
                            <table class="table table-hover mb-0" id="table_profil_history" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aksi</th>
                                        <th>No</th>
                                        <th>Nama&nbsp;Lengkap</th>
                                        <th>Waktu&nbsp;Pengajuan</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th>Pengubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade pengajuan-profil-pane" id="nav_alamat" role="tabpanel">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center px-0">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>PENGAJUAN
                                UBAH ALAMAT</h6>
                        </div>

                        <div class="master-table-area">
                            <table class="table table-hover mb-0" id="table_alamat" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aksi</th>
                                        <th>No</th>
                                        <th>Nama&nbsp;Lengkap</th>
                                        <th>Waktu&nbsp;Pengajuan</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th>Pengubah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade pengajuan-profil-pane" id="nav_history_alamat" role="tabpanel">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center px-0">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>RIWAYAT
                                UBAH ALAMAT</h6>
                        </div>

                        <div class="master-table-area">
                            <table class="table table-hover mb-0" id="table_alamat_history" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aksi</th>
                                        <th>No</th>
                                        <th>Nama&nbsp;Lengkap</th>
                                        <th>Waktu&nbsp;Pengajuan</th>
                                        <th>Alasan</th>
                                        <th>Status</th>
                                        <th>Pengubah</th>
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
        <!-- Account -->
        <div class="modal fade" id="modal_edit_profil" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form_edit_keahlian">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" name="id" id="id_update" value="">
                                <input type="hidden" name="id_karyawan" id="id_karyawan_update" value="">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="nik" id="nik_update" class="form-control"
                                        value="" readonly>
                                    <label for="nik">NIK</label>
                                </div>

                            </div>
                            {{-- <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="ktp" id="ktp_update" class="form-control"
                                        value="">
                                    <label for="ktp">KTP</label>
                                </div>
                            </div> --}}
                            <div class="btn_ktp align-items-center mb-3">
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="name" id="name_update" class="form-control"
                                        value="" readonly>
                                    <label for="name">Nama Lengkap</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="tempat_lahir" id="tempat_lahir_update"
                                        class="form-control" value="" readonly>
                                    <label for="tempat_lahir">Tempat Lahir</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="tgl_lahir" id="tgl_lahir_update" class="form-control"
                                        value="" readonly>
                                    <label for="tgl_lahir">Tanggal Lahir</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="agama" id="agama_update" class="form-control"
                                        value="" readonly>
                                    <label for="agama">Agama</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="status_nikah" id="status_nikah_update"
                                        class="form-control" value="" readonly>
                                    <label for="status_nikah">Status Nikah</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="jumlah_anak" id="jumlah_anak_update"
                                        class="form-control" value="" readonly>
                                    <label for="jumlah_anak">Jumlah Anak</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="no_kk" id="no_kk_update" class="form-control"
                                        value="" readonly>
                                    <label for="no_kk">No. KK</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="tgl_terbit_kk" id="tgl_terbit_kk_update"
                                        class="form-control" value="" readonly>
                                    <label for="tgl_terbit_kk">Tanggal Terbit KK</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="kk" id="kk_update" class="form-control"
                                        value="" readonly>
                                    <label for="kk">KK</label>
                                </div>
                            </div> --}}
                            <div class="btn_kk align-items-center mb-3">
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="email" id="email_update" class="form-control"
                                        value="" readonly>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="nomor_wa" id="nomor_wa_update" class="form-control"
                                        value="" readonly>
                                    <label for="nomor_wa">No. Whatsapp</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="telepon" id="telepon_update" class="form-control"
                                        value="" readonly>
                                    <label for="telepon">Telepon</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <button type="button" id="btn_setujui_profil" class="btn btn-sm btn-primary"><i
                                        class="mdi mdi-content-save"></i> Setujui</button>
                                <button type="button" id="btn_ditolak_profil" class="btn btn-sm btn-danger"><i
                                        class="mdi mdi-content-save"></i> Ditolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- alamat --}}
        <div class="modal fade" id="modal_edit_alamat" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form_edit_keahlian">
                            <div class="col-md-12 mb-3">
                                <input type="hidden" name="id" id="id_alamat" value="">
                                <input type="hidden" name="id_karyawan" id="id_karyawan_alamat" value="">

                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="rt" id="rt_update" class="form-control"
                                        value="" readonly>
                                    <label for="rt">RT</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="rw" id="rw_update" class="form-control"
                                        value="" readonly>
                                    <label for="rw">RW</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="desa" id="desa_update" class="form-control"
                                        value="" readonly>
                                    <label for="desa">Desa</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="kecamatan" id="kecamatan_update" class="form-control"
                                        value="" readonly>
                                    <label for="kecamatan">Kecamatan</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="kabupaten" id="kabupaten_update" class="form-control"
                                        value="" readonly>
                                    <label for="kabupaten">Kabupaten</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="provinsi" id="provinsi_update" class="form-control"
                                        value="" readonly>
                                    <label for="provinsi">Provinsi</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="detail_alamat" id="detail_alamat_update"
                                        class="form-control" value="" readonly>
                                    <label for="detail_alamat">Detail Alamat</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="detail_alamat" id="detail_alamat_update"
                                        class="form-control" value="" readonly>
                                    <label for="detail_alamat">Sesuai KTP?</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="rt_domisili" id="rt_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="rt_domisili">RT Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="rw_domisili" id="rw_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="rw_domisili">RW Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="desa_domisili" id="desa_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="desa_domisili">Desa Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="kecamatan_domisili" id="kecamatan_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="kecamatan_domisili">Kecamatan Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="kabupaten_domisili" id="kabupaten_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="kabupaten_domisili">Kabupaten Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="provinsi_domisili" id="provinsi_domisili_update"
                                        class="form-control" value="" readonly>
                                    <label for="provinsi_domisili">Provinsi Domisili</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="detail_alamat_domisili"
                                        id="detail_alamat_domisili_update" class="form-control" value="" readonly>
                                    <label for="detail_alamat_domisili">Detail Alamat Domisili</label>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <button type="button" id="btn_setujui_alamat" class="btn btn-sm btn-primary"><i
                                        class="mdi mdi-content-save"></i> Setujui</button>
                                <button type="button" id="btn_ditolak_alamat" class="btn btn-sm btn-danger"><i
                                        class="mdi mdi-content-save"></i> Ditolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);
            $('#menu_id_tambah').val(menu_id);

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
            // var holding_id = "{{ $holding->id }}"
            var table_profil = $('#table_profil').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/dt_pengajuan_profil/' . $holding->id) }}",
                },
                columns: [{
                        data: "aksi",
                        name: "aksi"
                    },
                    {
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'alasan',
                        name: 'alasan'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'approval_name',
                        name: 'approval_name'
                    },
                ],
                order: [
                    [4, 'desc']
                ]
            });
            // $('#nav_profil').on('shown.bs.tab', function(e) {
            //     console.log('asoy');
            //     table_profil.columns.adjust().draw().responsive.recalc();
            // });


            var table_profil_history = $('#table_profil_history').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/dt_pengajuan_history/' . $holding->id) }}",
                },
                columns: [{
                        data: "aksi",
                        name: "aksi"
                    },
                    {
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'alasan',
                        name: 'alasan'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'approval_name',
                        name: 'approval_name'
                    },
                ],
                order: [
                    [4, 'desc']
                ]
            });
            // $('#nav_history').on('shown.bs.tab', function(e) {
            //     console.log('asoy');
            //     table_profil_history.columns.adjust().draw().responsive.recalc();
            // });
            var table_alamat = $('#table_alamat').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/dt_pengajuan_alamat/' . $holding->id) }}",
                },
                columns: [{
                        data: "aksi",
                        name: "aksi"
                    },
                    {
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'alasan',
                        name: 'alasan'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'approval_name',
                        name: 'approval_name'
                    },
                ],
                order: [
                    [4, 'desc']
                ]
            });
            var table_alamat_history = $('#table_alamat_history').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/dt_pengajuan_alamat_history/' . $holding->id) }}",
                },
                columns: [{
                        data: "aksi",
                        name: "aksi"
                    },
                    {
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
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'alasan',
                        name: 'alasan'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'approval_name',
                        name: 'approval_name'
                    },
                ],
                order: [
                    [4, 'desc']
                ]
            });




            $(document).on('click', '#btn_open_profil', function() {
                // console.log($(this).data());
                var id = $(this).data('id');
                var id_karyawan = $(this).data('id_karyawan');
                var nik = $(this).data('nik');
                var ktp = $(this).data('ktp');
                var name = $(this).data('name');
                var tempat_lahir = $(this).data('tempat_lahir');
                var tgl_lahir = $(this).data('tgl_lahir');
                var agama = $(this).data('agama');
                var status_nikah = $(this).data('status_nikah');
                var jumlah_anak = $(this).data('jumlah_anak');
                var no_kk = $(this).data('no_kk');
                var tgl_terbit_kk = $(this).data('tgl_terbit_kk');
                var kk = $(this).data('kk');
                var email = $(this).data('email');
                var nomor_wa = $(this).data('nomor_wa');
                var telepon = $(this).data('telepon');
                $('#id_update').val(id);
                $('#id_karyawan_update').val(id_karyawan);
                $('#nik_update').val(nik);
                $('#ktp_update').val(ktp);
                $('#name_update').val(name);
                $('#tempat_lahir_update').val(tempat_lahir);
                $('#tgl_lahir_update').val(tgl_lahir);
                $('#agama_update').val(agama);
                $('#status_nikah_update').val(status_nikah);
                $('#jumlah_anak_update').val(jumlah_anak);
                $('#no_kk_update').val(no_kk);
                $('#tgl_terbit_kk_update').val(tgl_terbit_kk);
                $('#kk_update').val(kk);
                $('#email_update').val(email);
                $('#telepon_update').val(telepon);
                $('#nomor_wa_update').val(nomor_wa);

                var baseUrl = "{{ url('/') }}";
                // console.log(ktp.length);
                if (ktp.length != 0) {
                    // console.log('ya');
                    $('#ktp_update').val(ktp);
                    $('.btn_ktp').empty();
                    $('.btn_ktp').append('<a href="' + baseUrl + '/storage/ktp/' + ktp +
                        '" target="_blank" class="btn btn-sm btn-info"><i class="mdi mdi-eye"></i>&nbsp;Lihat&nbsp;KTP</a>'
                    )
                }
                if (kk.length != 0) {
                    // console.log('ya');
                    $('#kk_update').val(kk);
                    $('.btn_kk').empty();
                    $('.btn_kk').append('<a href="' + baseUrl + '/storage/kk/' + kk +
                        '" target="_blank" class="btn btn-sm btn-info"><i class="mdi mdi-eye"></i>&nbsp;Lihat&nbsp;KK</a>'
                    )
                }


                $('#modal_edit_profil').modal('show');
            });
            $(document).on('click', '#btn_open_alamat', function() {
                // console.log($(this).data());
                var id = $(this).data('id');
                var id_karyawan = $(this).data('id_karyawan');
                var rt = $(this).data('rt');
                var rw = $(this).data('rw');
                var desa = $(this).data('desa');
                var kecamatan = $(this).data('kecamatan');
                var kabupaten = $(this).data('kabupaten');
                var provinsi = $(this).data('provinsi');
                var detail_alamat = $(this).data('detail_alamat');
                var status_alamat = $(this).data('status_alamat');
                var rt_domisili = $(this).data('rt_domisili');
                var rw_domisili = $(this).data('rw_domisili');
                var desa_domisili = $(this).data('desa_domisili');
                var kecamatan_domisili = $(this).data('kecamatan_domisili');
                var kabupaten_domisili = $(this).data('kabupaten_domisili');
                var provinsi_domisili = $(this).data('provinsi_domisili');
                var detail_alamat_domisili = $(this).data('detail_alamat_domisili');

                // console.log(id);

                $('#id_alamat').val(id);
                $('#id_karyawan_alamat').val(id_karyawan);
                $('#rt_update').val(rt);
                $('#rw_update').val(rw);
                $('#desa_update').val(desa);
                $('#kecamatan_update').val(kecamatan);
                $('#kabupaten_update').val(kabupaten);
                $('#provinsi_update').val(provinsi);
                $('#detail_alamat_update').val(detail_alamat);
                $('#status_alamat_update').val(status_alamat);
                $('#rt_domisili_update').val(rt_domisili);
                $('#rw_domisili_update').val(rw_domisili);
                $('#desa_domisili_update').val(desa_domisili);
                $('#kecamatan_domisili_update').val(kecamatan_domisili);
                $('#kabupaten_domisili_update').val(kabupaten_domisili);
                $('#provinsi_domisili_update').val(provinsi_domisili);
                $('#detail_alamat_domisili_update').val(detail_alamat_domisili);

                $('#modal_edit_alamat').modal('show');
            });
        });
        $('#btn_setujui_profil').click(function() {
            var menu_id = $('#asoy').val();

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', $('#id_update').val());
            formData.append('id_karyawan', $('#id_karyawan_update').val());
            formData.append('status', '1');
            formData.append('menu_id', menu_id);
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('pengajuan_profil_update') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_edit_profil').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#form_add_keahlian').trigger('reset');
                    $('#table_profil')
                        .DataTable()
                        .ajax
                        .reload();
                    // console.log(response.data_keahlian);
                    if (response.data_keahlian >= 5) {
                        $('#btn_tambah_keahlian').attr("disabled", true);
                        $('#pesan_disabled_keahlian').show();
                    }


                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_keahlian').trigger('reset');
                    $('#modal_edit_profil').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $('#btn_ditolak_profil').click(function() {
            var menu_id = $('#asoy').val();

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', $('#id_update').val());
            formData.append('id_karyawan', $('#id_karyawan_update').val());
            formData.append('status', '2');
            formData.append('menu_id', menu_id);
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('pengajuan_profil_ditolak') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_edit_profil').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#form_add_keahlian').trigger('reset');
                    $('#table_profil')
                        .DataTable()
                        .ajax
                        .reload();
                    // console.log(response.data_keahlian);
                    if (response.data_keahlian >= 5) {
                        $('#btn_tambah_keahlian').attr("disabled", true);
                        $('#pesan_disabled_keahlian').show();
                    }


                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_keahlian').trigger('reset');
                    $('#modal_edit_profil').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $('#btn_setujui_alamat').click(function() {
            var menu_id = $('#asoy').val();

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', $('#id_alamat').val());
            formData.append('id_karyawan', $('#id_karyawan_alamat').val());
            formData.append('status', '1');
            formData.append('menu_id', menu_id);
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('pengajuan_alamat_update') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_edit_alamat').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#form_add_keahlian').trigger('reset');
                    $('#table_alamat')
                        .DataTable()
                        .ajax
                        .reload();
                    // console.log(response.data_keahlian);
                    if (response.data_keahlian >= 5) {
                        $('#btn_tambah_keahlian').attr("disabled", true);
                        $('#pesan_disabled_keahlian').show();
                    }


                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_keahlian').trigger('reset');
                    $('#modal_edit_alamat').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $('#btn_ditolak_alamat').click(function() {
            var menu_id = $('#asoy').val();

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', $('#id_alamat').val());
            formData.append('id_karyawan', $('#id_karyawan_alamat').val());
            formData.append('status', '2');
            formData.append('menu_id', menu_id);
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('pengajuan_alamat_ditolak') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_edit_alamat').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#form_add_keahlian').trigger('reset');
                    $('#table_alamat')
                        .DataTable()
                        .ajax
                        .reload();
                    // console.log(response.data_keahlian);
                    if (response.data_keahlian >= 5) {
                        $('#btn_tambah_keahlian').attr("disabled", true);
                        $('#pesan_disabled_keahlian').show();
                    }


                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_keahlian').trigger('reset');
                    $('#modal_edit_alamat').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $(document).on('click', '#profil_nav', function() {
            console.log('asoy2');
            $('#table_profil').DataTable().ajax.reload();
        });
        $(document).on('click', '#history_nav', function() {
            console.log('asoy');
            $('#table_profil_history').DataTable().ajax.reload();
        });
        $(document).on('click', '#alamat_nav', function() {
            console.log('asoy2');
            $('#table_alamat').DataTable().ajax.reload();
        });
        $(document).on('click', '#history_alamat_nav', function() {
            console.log('asoy');
            $('#table_alamat_history').DataTable().ajax.reload();
        });
    </script>
@endsection
