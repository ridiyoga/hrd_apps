@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        .swal2-container {
            z-index: 9999;
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

        /* Container Styling */
        #nav_profile {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_pendidikan {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_info_hr {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_jabatan2 {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_bank {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_pajak {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_bpjs {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_keahlian {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_riwayat {
            padding: 20px;
            background-color: #f8f9fa;
        }

        #nav_kesehatan {
            padding: 20px;
            background-color: #f8f9fa;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card.shadow-sm:hover {
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
        }

        .card-header {
            font-weight: 600;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
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
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y" style="font-size: small;">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">KARYAWAN /</span> DETAIL KARYAWAN</h4>

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-4 gap-2 gap-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"><i
                                class="mdi mdi-account-outline mdi-20px me-1"></i>{{ $karyawan->name }}&nbsp;<b>[{{ $karyawan->nomor_identitas_karyawan }}]</b></a>
                    </li>
                    <li class="nav-item">
                        {{-- <a class="btn btn-sm btn-info"
                            href="@if (Auth::user()->is_admin == 'hrd') {{ url('/hrd/karyawan/shift/' . $karyawan->id . '/' . $holding) }}@else{{ url('/karyawan/shift/' . $karyawan->id . '/' . $holding) }} @endif"><i
                        class="mdi mdi-clock-outline mdi-20px me-1"></i>Mapping Jadwal&nbsp;</a> --}}
                    </li>
                </ul>
                <div class="card mb-4">
                    <h4 class="card-header">Daftar Profil</h4>
                    <input type="hidden" id="menu_id_add" value="">

                    <!-- Account -->

                    @csrf
                    <input style="font-size: small;" type="hidden" value="{{ $karyawan->id }}" name="id_karyawan"
                        id="id_karyawan">
                    <div class="card-body">
                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-pills mb-3" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#nav_profile" aria-controls="nav_profile" aria-selected="true">
                                        <i class="tf-icons mdi mdi-account-outline me-1"></i>
                                        PROFILE
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button id="pendidikan_nav" type="button" class="nav-link" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#nav_pendidikan" aria-controls="nav_pendidikan"
                                        aria-selected="false">
                                        <i class="tf-icons mdi mdi-account-school-outline me-1"></i>
                                        PENDIDIKAN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" id="info_hr_nav" class="nav-link" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#nav_info_hr" aria-controls="nav_info_hr"
                                        aria-selected="false">
                                        <i class="tf-icons mdi mdi-account-cog-outline me-1"></i>
                                        INFO HR
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" id="jabatan2_nav" class="nav-link" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#nav_jabatan2" aria-controls="nav_jabatan2"
                                        aria-selected="false">
                                        <i class="tf-icons mdi mdi-medal-outline me-1"></i>
                                        JABATAN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#nav_bank" aria-controls="nav_bank" aria-selected="false">
                                        <i class="tf-icons mdi mdi-bank-circle me-1"></i>
                                        BANK
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#nav_pajak" aria-controls="nav_pajak" aria-selected="false">
                                        <i class="tf-icons mdi mdi-percent-outline me-1"></i>
                                        PAJAK
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#nav_bpjs" aria-controls="nav_bpjs" aria-selected="false">
                                        <i class="tf-icons mdi mdi-card-account-details-outline me-1"></i>
                                        BPJS
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button id="keahlian_nav" type="button" class="nav-link" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#nav_keahlian" aria-controls="nav_keahlian"
                                        aria-selected="false">
                                        <i class="mdi mdi-hammer-screwdriver me-1"></i>
                                        KEAHLIAN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button id="pengalaman_nav" type="button" class="nav-link" role="tab"
                                        data-bs-toggle="tab" data-bs-target="#nav_riwayat" aria-controls="nav_riwayat"
                                        aria-selected="false">
                                        <i class="mdi mdi-briefcase-outline me-1"></i>
                                        PENGALAMAN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#nav_kesehatan" aria-controls="nav_kesehatan"
                                        aria-selected="false">
                                        <i class="mdi mdi-medication-outline me-1"></i>
                                        KESEHATAN
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        id="inventaris_nav" data-bs-target="#nav_inventaris"
                                        aria-controls="nav_inventaris" aria-selected="false">
                                        <i class="mdi mdi-image-broken me-1"></i>
                                        INVENTARIS
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="nav_profile" role="tabpanel">
                                    <div class="col-md-3">
                                        <span class="mdi mdi-account-badge badge bg-label-secondary">&nbsp;Foto
                                            Profil</span>
                                    </div>
                                    <hr class="m-0">
                                    <div class="row mt-2 mb-4 gy-4">
                                        <div class="col-md-4">
                                            @if ($karyawan->foto_karyawan)
                                                <img src="{{ asset('storage/foto_karyawan/' . $karyawan->foto_karyawan) }}"
                                                    id="template_foto_karyawan" max-height="200" height="200"
                                                    class="rounded" alt="">
                                            @else
                                                <img src="{{ asset('storage/foto_karyawan/default_profil.jpg') }}"
                                                    id="template_foto_karyawan" max-height="200" height="200"
                                                    class="rounded" alt="">
                                            @endif
                                            <br>
                                            <input type="file" name="foto_karyawan" id="foto_karyawan" hidden
                                                value="" accept="image/png, image/jpeg">
                                            <input type="hidden" name="foto_karyawan_old" id="foto_karyawan_old"
                                                value="{{ $karyawan->foto_karyawan }}">
                                            <div id="group-button-foto"></div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mb-4">
                                        <div
                                            class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                                            <h6 class="mb-0 text-dark fw-bold">
                                                <i class="mdi mdi-account-details-outline me-2"></i>INFORMASI BIODATA &
                                                IDENTITAS
                                            </h6>
                                            <span class="badge bg-label-primary">Data Personal</span>
                                        </div>

                                        <div class="card-body pt-4">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input class="form-control border-0 border-bottom rounded-0 px-0"
                                                            type="number" id="nik" name="nik"
                                                            placeholder="Masukkan NIK"
                                                            value="{{ old('nik', $karyawan->nik) }}" />
                                                        <label for="nik"><i class="mdi mdi-id-card me-1"></i>NIK
                                                            (KTP)</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input class="form-control border-0 border-bottom rounded-0 px-0"
                                                            type="text" id="nomor_identitas_karyawan"
                                                            name="nomor_identitas_karyawan" placeholder="ID Karyawan"
                                                            value="{{ old('nomor_identitas_karyawan', $karyawan->nomor_identitas_karyawan) }}" />
                                                        <label for="nomor_identitas_karyawan"><i
                                                                class="mdi mdi-badge-account-outline me-1"></i>ID Karyawan
                                                            (Badge No.)</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input class="form-control border-0 border-bottom rounded-0 px-0"
                                                            type="number" id="id_finger" name="id_finger"
                                                            placeholder="ID Finger"
                                                            value="{{ old('id_finger', $karyawan->id_finger) }}" />
                                                        <label for="id_finger"><i
                                                                class="mdi mdi-badge-account-outline me-1"></i>ID Presensi
                                                            Jari</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('name') is-invalid @enderror"
                                                            id="name" name="name" placeholder="Nama Lengkap"
                                                            value="{{ old('name', $karyawan->name) }}">
                                                        <label for="name"><i
                                                                class="mdi mdi-account-outline me-1"></i>Nama
                                                            Lengkap</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="email"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('email') is-invalid @enderror"
                                                            id="email" name="email"
                                                            placeholder="email@perusahaan.com"
                                                            value="{{ old('email', $karyawan->email) }}">
                                                        <label for="email"><i
                                                                class="mdi mdi-email-outline me-1"></i>E-mail</label>
                                                    </div>
                                                    @error('email')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="email"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('email_kantor') is-invalid @enderror"
                                                            id="email_kantor" name="email_kantor"
                                                            placeholder="email@perusahaan.com"
                                                            value="{{ old('email_kantor', $karyawan->email_kantor) }}">
                                                        <label for="email_kantor"><i
                                                                class="mdi mdi-email-outline me-1"></i>E-mail
                                                            Kantor</label>
                                                    </div>
                                                    @error('email_kantor')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('no_kk') is-invalid @enderror"
                                                            id="no_kk" name="no_kk" placeholder="Nomor KK"
                                                            value="{{ old('no_kk', $karyawan->no_kk) }}">
                                                        <label for="no_kk"><i class="mdi mdi-family-tree me-1"></i>No.
                                                            Kartu Keluarga</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="date"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('tgl_terbit_kk') is-invalid @enderror"
                                                            id="tgl_terbit_kk" name="tgl_terbit_kk"
                                                            value="{{ old('tgl_terbit_kk', optional($karyawan->tgl_terbit_kk)->format('Y-m-d')) }}">
                                                        <label for="tgl_terbit_kk">Tanggal Terbit KK</label>
                                                    </div>
                                                    @error('tgl_terbit_kk')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('telepon') is-invalid @enderror"
                                                            id="telepon" name="telepon" placeholder="0812..."
                                                            value="{{ old('telepon', $karyawan->telepon) }}">
                                                        <label for="telepon"><i
                                                                class="mdi mdi-phone-outline me-1"></i>Nomor
                                                            Telepon</label>
                                                    </div>
                                                    @error('telepon')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nomor_wa_kantor') is-invalid @enderror"
                                                            id="nomor_wa_kantor" name="nomor_wa_kantor"
                                                            placeholder="0812..."
                                                            value="{{ old('nomor_wa_kantor', $karyawan->nomor_wa_kantor) }}">
                                                        <label for="nomor_wa_kantor"><i
                                                                class="mdi mdi-phone-outline me-1"></i>Nomor
                                                            Telepon Kantor</label>
                                                    </div>
                                                    @error('nomor_wa_kantor')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div id="content_nomor_wa" class="col-md-6 mt-3" style="display: none;">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number" name="nomor_wa" id="nomor_wa"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nomor_wa') is-invalid @enderror"
                                                            placeholder="0812..." style="font-size: 0.95rem;"
                                                            value="{{ old('nomor_wa', $karyawan->nomor_wa) }}" />
                                                        <label for="nomor_wa" class="text-success"><i
                                                                class="mdi mdi-whatsapp me-1"></i>Nomor WhatsApp
                                                            Aktif</label>
                                                    </div>

                                                    @error('nomor_wa')
                                                        <div class="text-danger small mt-1">
                                                            <i class="ri-error-warning-line"></i> {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="date"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('tgl_join') is-invalid @enderror"
                                                            id="tgl_join" name="tgl_join"
                                                            value="{{ old('tgl_join', optional($karyawan->tgl_join)->format('Y-m-d')) }}">
                                                        <label for="tgl_join">Tanggal Bergabung (Join Date)</label>
                                                    </div>
                                                    @error('tgl_join')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('tempat_lahir') is-invalid @enderror"
                                                            id="tempat_lahir" name="tempat_lahir"
                                                            value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}"
                                                            placeholder="Tempat Lahir">
                                                        <label for="tempat_lahir">Tempat Lahir</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input class="form-control border-0 border-bottom rounded-0 px-0"
                                                            type="date" id="tgl_lahir" name="tgl_lahir"
                                                            value="{{ old('tgl_lahir', $karyawan->tgl_lahir) }}" />
                                                        <label for="tgl_lahir">Tanggal Lahir</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select
                                                            class="form-control border-0 border-bottom rounded-0 px-0 select2"
                                                            id="agama" name="agama">
                                                            name="agama">
                                                            <option @if (old('agama', $karyawan->agama) == '') selected @else @endif
                                                                disabled value=""> ~Pilih Agama~ </option>
                                                            <option @if (old('agama', $karyawan->agama) == '1') selected @else @endif
                                                                value="1">ISLAM</option>
                                                            <option @if (old('agama', $karyawan->agama) == '2') selected @else @endif
                                                                value="2">KRISTEN PROTESTAN</option>
                                                            <option @if (old('agama', $karyawan->agama) == '3') selected @else @endif
                                                                value="3">KRISTEN KATOLIK</option>
                                                            <option @if (old('agama', $karyawan->agama) == '4') selected @else @endif
                                                                value="4">HINDU</option>
                                                            <option @if (old('agama', $karyawan->agama) == '5') selected @else @endif
                                                                value="5">BUDDHA</option>
                                                            <option @if (old('agama', $karyawan->agama) == '6') selected @else @endif
                                                                value="6">KHONGHUCU</option>
                                                        </select>
                                                        <label for="agama">Agama</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <?php $gender = [
                                                            [
                                                                'gender_id' => '1',
                                                                'gender_name' => 'LAKI-LAKI',
                                                            ],
                                                            [
                                                                'gender_id' => '2',
                                                                'gender_name' => 'PEREMPUAN',
                                                            ],
                                                        ];
                                                        ?>
                                                        <select name="gender" id="gender"
                                                            class="form-control border-0 border-bottom rounded-0 px-0">
                                                            @foreach ($gender as $g)
                                                                <option value="{{ $g['gender_id'] }}"
                                                                    {{ old('gender', $karyawan->gender) == $g['gender_id'] ? 'selected' : '' }}>
                                                                    {{ $g['gender_name'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="gender">Jenis Kelamin</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <?php $sNikah = [
                                                            [
                                                                'status_id' => '1',
                                                                'status_name' => 'Belum Kawin',
                                                            ],
                                                            [
                                                                'status_id' => '2',
                                                                'status_name' => 'Sudah Kawin',
                                                            ],
                                                            [
                                                                'status_id' => '3',
                                                                'status_name' => 'Cerai Hidup',
                                                            ],
                                                            [
                                                                'status_id' => '4',
                                                                'status_name' => 'Cerai Mati',
                                                            ],
                                                        ];
                                                        ?>
                                                        <select name="status_nikah" id="status_nikah"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 selectpicker"
                                                            data-live-search="true">
                                                            @foreach ($sNikah as $s)
                                                                <option value="{{ $s['status_id'] }}"
                                                                    {{ old('status_nikah', $karyawan->status_nikah) == $s['status_id'] ? 'selected' : '' }}>
                                                                    {{ $s['status_name'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="status_nikah">Status Nikah</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            id="jumlah_anak" name="jumlah_anak"
                                                            value="{{ old('jumlah_anak', $karyawan->jumlah_anak) }}"
                                                            placeholder="0">
                                                        <label for="jumlah_anak">Jumlah Anak</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm modern-selector-card p-4 mb-4">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="label-modern d-block mb-3 fw-bold small text-uppercase">
                                                    <i class="ri-whatsapp-line me-1"></i> Terhubung WhatsApp?
                                                </label>
                                                <div class="modern-btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="status_nomor"
                                                        id="btn_status_no_ya" value="ya"
                                                        {{ old('status_nomor', $karyawan->status_nomor) == 'ya' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success" for="btn_status_no_ya">Ya,
                                                        Terhubung</label>

                                                    <input type="radio" class="btn-check" name="status_nomor"
                                                        id="btn_status_no_tidak" value="tidak"
                                                        {{ old('status_nomor', $karyawan->status_nomor) == 'tidak' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary"
                                                        for="btn_status_no_tidak">Tidak</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="label-modern d-block mb-3 fw-bold small text-uppercase">
                                                    <i class="ri-map-pin-user-line me-1"></i> Alamat Domisili Sama?
                                                </label>
                                                <div class="modern-btn-group" role="group">
                                                    <input type="radio" class="btn-check"
                                                        name="pilihan_alamat_domisili" id="btnradio_ya" value="ya"
                                                        {{ old('pilihan_alamat_domisili', $karyawan->status_alamat) == 'ya' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success" for="btnradio_ya">Ya,
                                                        Sama</label>

                                                    <input type="radio" class="btn-check"
                                                        name="pilihan_alamat_domisili" id="btnradio_tidak" value="tidak"
                                                        {{ old('pilihan_alamat_domisili', $karyawan->status_alamat) == 'tidak' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-primary" for="btnradio_tidak">Tidak,
                                                        Berbeda</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mb-4">
                                        <div
                                            class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                                            <h6 class="mb-0 text-dark fw-bold">
                                                <i class="mdi mdi-map-marker-outline me-2"></i>ALAMAT SESUAI KTP
                                            </h6>
                                        </div>
                                        <div class="card-body pt-4">
                                            <div class="row g-3">
                                                <?php
                                                $kab = App\Models\Cities::where('province_code', old('provinsi', $karyawan->provinsi))->orderBy('name', 'ASC')->get();
                                                $kec = App\Models\District::where('city_code', old('kabupaten', $karyawan->kabupaten))->orderBy('name', 'ASC')->get();
                                                $desa = App\Models\Village::where('district_code', old('kecamatan', $karyawan->kecamatan))->orderBy('name', 'ASC')->get();
                                                ?>

                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('provinsi') is-invalid @enderror"
                                                            id="id_provinsi" name="provinsi">
                                                            <option value="">Pilih Provinsi</option>
                                                            @foreach ($data_provinsi as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('provinsi', $karyawan->provinsi) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_provinsi">Provinsi</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select class="form-control border-0 border-bottom rounded-0 px-0"
                                                            id="id_kabupaten" name="kabupaten">
                                                            <option value="">Pilih Kota</option>
                                                            @foreach ($kab as $kabupaten)
                                                                <option value="{{ $kabupaten->code }}"
                                                                    {{ $kabupaten->code == old('kabupaten', $karyawan->kabupaten) ? 'selected' : '' }}>
                                                                    {{ $kabupaten->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_kabupaten">Kabupaten</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select class="form-control border-0 border-bottom rounded-0 px-0"
                                                            id="id_kecamatan" name="kecamatan">
                                                            <option value="">Pilih Kecamatan</option>
                                                            @foreach ($kec as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('kecamatan', $karyawan->kecamatan) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_kecamatan">Kecamatan</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select class="form-control border-0 border-bottom rounded-0 px-0"
                                                            id="id_desa" name="desa">
                                                            <option value="">Pilih Desa</option>
                                                            @foreach ($desa as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('desa', $karyawan->desa) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_desa">Desa</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number" name="rt" id="rt"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            value="{{ old('rt', $karyawan->rt) }}" placeholder="RT">
                                                        <label>RT</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number" name="rw" id="rw"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            value="{{ old('rw', $karyawan->rw) }}" placeholder="RW">
                                                        <label>RW</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="number" name="kode_pos" id="kode_pos"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            value="{{ old('kode_pos', $karyawan->kode_pos) }}"
                                                            placeholder="Kode Pos">
                                                        <label>Kode Pos</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text" name="alamat" id="alamat"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            value="{{ old('alamat', $karyawan->detail_alamat) }}"
                                                            placeholder="Nama Jalan/Dusun">
                                                        <label>Keterangan Alamat (Jalan/Dusun)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mb-4" id="content_alamat_domisili">
                                        <div
                                            class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                                            <h6 class="mb-0 text-dark fw-bold">
                                                <i class="mdi mdi-map-marker-outline me-2"></i>ALAMAT SESUAI DOMISILI
                                            </h6>
                                        </div>
                                        <div class="card-body pt-4">
                                            <div class="row g-4">
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control @error('provinsi_domisili') is-invalid @enderror"
                                                            id="id_provinsi_domisili" name="provinsi_domisili"
                                                            style="font-size: small;">
                                                            <option value=""> Pilih Provinsi </option>
                                                            @foreach ($data_provinsi as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('provinsi_domisili', $karyawan->provinsi_domisili) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_provinsi_domisili">Provinsi</label>
                                                        <div class="select-loader d-none">
                                                            <div class="dot-loader">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('provinsi_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <?php
                                                    $kab_domisili = App\Models\Cities::Where('province_code', old('provinsi_domisili', $karyawan->provinsi_domisili))->orderBy('name', 'ASC')->get();
                                                    $kec_domisili = App\Models\District::Where('city_code', old('kabupaten_domisili', $karyawan->kabupaten_domisili))->orderBy('name', 'ASC')->get();
                                                    $desa_domisili = App\Models\Village::Where('district_code', old('kecamatan_domisili', $karyawan->kecamatan_domisili))->orderBy('name', 'ASC')->get();
                                                    // echo $kab;
                                                    ?>
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control @error('kabupaten_domisili') is-invalid @enderror"
                                                            id="id_kabupaten_domisili" name="kabupaten_domisili"
                                                            style="font-size: small;">
                                                            <option value=""> Pilih Kabupaten / Kota</option>
                                                            @foreach ($kab_domisili as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('kabupaten_domisili', $karyawan->kabupaten_domisili) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_kabupaten_domisili">Kabupaten</label>
                                                        <div class="select-loader d-none">
                                                            <div class="dot-loader">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('kabupaten_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control @error('kecamatan_domisili') is-invalid @enderror"
                                                            id="id_kecamatan_domisili" name="kecamatan_domisili"
                                                            style="font-size: small;">
                                                            <option value=""> Pilih Kecamatan</option>
                                                            @foreach ($kec_domisili as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('kecamatan_domisili', $karyawan->kecamatan_domisili) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_kecamatan_domisili">kecamatan_domisili</label>
                                                        <div class="select-loader d-none">
                                                            <div class="dot-loader">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('kecamatan_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('desa_domisili') is-invalid @enderror"
                                                            id="id_desa_domisili" name="desa_domisili"
                                                            style="font-size: small;">
                                                            <option value=""> Pilih Desa</option>
                                                            @foreach ($desa_domisili as $data)
                                                                <option value="{{ $data->code }}"
                                                                    {{ $data->code == old('desa_domisili', $karyawan->desa_domisili) ? 'selected' : '' }}>
                                                                    {{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="id_desa_domisili">Desa</label>
                                                        <div class="select-loader d-none">
                                                            <div class="dot-loader">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('desa_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" style="font-size: small;"
                                                            type="number" id="rt_domisili" name="rt_domisili"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('rt_domisili') is-invalid @enderror"
                                                            placeholder="Masukkan RT"
                                                            value="{{ old('rt_domisili', $karyawan->rt_domisili) }}" />
                                                        <label for="rt_domisili">RT</label>
                                                    </div>
                                                    @error('rt_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" style="font-size: small;"
                                                            type="number" id="rw_domisili" name="rw_domisili"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('rw_domisili') is-invalid @enderror"
                                                            placeholder="Masukkan RW"
                                                            value="{{ old('rw_domisili', $karyawan->rw_domisili) }}" />
                                                        <label for="rw_domisili">RW</label>
                                                    </div>
                                                    @error('rw_domisili')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" style="font-size: small;"
                                                            type="number" id="kode_pos_domisili"
                                                            name="kode_pos_domisili"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            placeholder="Kode pos"
                                                            value="{{ old('kode_pos_domisili', $karyawan->kode_pos_domisili) }}" />
                                                        <label for="kode_pos_domisili">Kode Pos</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" style="font-size: small;"
                                                            type="text" id="alamat_domisili" name="alamat_domisili"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('alamat_domisili') is-invalid @enderror"
                                                            placeholder="Masukkan Alamat"
                                                            value="{{ old('detail_alamat_domisili', $karyawan->detail_alamat_domisili) }}" />
                                                        <label for="detail_alamat_domisili">Keterangan Alamat(Jalan /
                                                            Dusun)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mb-4 border-start border-primary border-3" i>
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark fw-bold">
                                                <i class="mdi mdi-file-document-outline me-2"></i>BERKAS & DOKUMEN KARYAWAN
                                            </h6>
                                            <small class="text-muted">Format: JPG, PNG (Maks. 2MB)</small>
                                        </div>
                                        <div class="card-body p-4" id="berkas_karyawan">

                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center py-2">
                                        <button class="btn btn-primary" type="button" id="btn_update_profil">
                                            <span class="spinner-border spinner-border-sm d-none"
                                                id="loader_update_profil" role="status" aria-hidden="true"></span>
                                            <span id="text_update_profil">Simpan Data</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="nav_pendidikan" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-document-multiple-outline me-2"></i>RIWAYAT
                                                PENDIDIKAN</h6>
                                            <button class="btn btn-sm btn-outline-primary" id="btn_tambah_pendidikan"
                                                type="button">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Pendidikan
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <p class="text-danger" id="pesan_disabled_pendidikan">*Silakan
                                                    edit atau hapus data untuk
                                                    menambahkan
                                                    data lain
                                                    (max : 3)
                                                </p>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" id="table_pendidikan"
                                                    style="width: 100%;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Aksi</th>
                                                            <th>No</th>
                                                            <th>Nama&nbsp;Instansi</th>
                                                            <th>Jenjang</th>
                                                            <th>jurusan</th>
                                                            <th>Tahun&nbsp;Masuk</th>
                                                            <th>Tahun&nbsp;Lulus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-document-multiple-outline me-2"></i>UPLOAD DOKUMEN
                                                PENDIDIKAN</h6>
                                            <small class="text-muted">Format: PDF (Maks. 10MB)</small>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row mt-2 g-4">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input type="text" name="ipk" id="ipk"
                                                            class="form-control border-0 border-bottom rounded-0 px-0"
                                                            value="{{ old('ipk', $karyawan->ipk) }}">
                                                        <label for="ipk">IPK</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2 g-4" id="berkas_pendidikan_karyawan">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center py-2">
                                        <button class="btn btn-sm btn-primary" type="button"
                                            id="btn_update_doc_pend">Simpan
                                            Data Pendidikan</button>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav_info_hr" role="tabpanel">

                                    <div class="card shadow-sm mb-4">
                                        <div class="card-header bg-white">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-briefcase-outline me-2"></i>INFORMASI PENEMPATAN &
                                                STATUS KERJA</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-4">
                                                <div id="form_kontrak" class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select
                                                            class="form-select border-0 shadow-none border-bottom rounded-0"
                                                            id="kontrak_kerja_add" name="kontrak_kerja">
                                                            @foreach ($holdingAll as $data)
                                                                <option value="{{ $data->id }}"
                                                                    {{ $data->id == old('kontrak_kerja', $karyawan->kontrak_kerja) ? 'selected' : '' }}>
                                                                    {{ $data->holding_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="kontrak_kerja">Kontrak Kerja (Holding)</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select
                                                            class="form-select border-0 shadow-none border-bottom rounded-0"
                                                            id="penempatan_kerja_add" name="penempatan_kerja">
                                                            <option disabled value="">Pilih Lokasi Penempatan
                                                            </option>
                                                            @foreach ($site as $a)
                                                                <option value="{{ $a['id'] }}"
                                                                    {{ old('penempatan_kerja', $karyawan->penempatan_kerja) == $a['id'] ? 'selected' : '' }}>
                                                                    {{ $a['site_name'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="penempatan_kerja">Penempatan Site Kerja</label>
                                                    </div>
                                                    @error('penempatan_kerja')
                                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-4">
                                                <button class="btn btn-sm btn-primary px-4 shadow-sm" type="button"
                                                    id="btn_update_hr">
                                                    <i class="mdi mdi-check-outline me-1"></i> Update Data HR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mb-4 border-start border-info border-3">
                                        <div class="card-header bg-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-dark"><i
                                                        class="mdi mdi-calendar-clock me-2"></i>MANAJEMEN SALDO CUTI</h6>
                                                <h6 class="fw-bold">
                                                    <span class="badge bg-label-info">
                                                        {{ $cuti_period->nama_period }}
                                                    </span>
                                                </h6>
                                                <button class="btn btn-sm btn-outline-primary" id="btn_log_cuti"
                                                    type="button"><i class="mdi mdi-history me-1"></i>Log
                                                    Penggunaan</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active btn-sm" id="pills-tahunan-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-tahunan"
                                                        type="button" role="tab">Cuti Tahunan</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link btn-sm" id="pills-khusus-tab"
                                                        data-bs-toggle="pill" data-bs-target="#pills-khusus"
                                                        type="button" role="tab">Cuti Di luar Tahunan</button>
                                                </li>
                                            </ul>

                                            <div class="tab-content p-0" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="pills-tahunan"
                                                    role="tabpanel">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <div
                                                                class="d-flex align-items-center border rounded p-3 bg-light-subtle">
                                                                <div class="avatar-initial bg-label-primary rounded me-3">
                                                                    <i class="mdi mdi-database mdi-24px"></i>
                                                                </div>
                                                                <div><small class="text-muted d-block">Total Kuota</small>
                                                                    <h5 class="mb-0 fw-bold">
                                                                        {{ $cuti_karyawan ? $cuti_karyawan->kuota : 0 }}
                                                                        Hari
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div
                                                                class="d-flex align-items-center border rounded p-3 bg-light-subtle">
                                                                <div class="avatar-initial bg-label-danger rounded me-3"><i
                                                                        class="mdi mdi-calendar-minus mdi-24px"></i></div>
                                                                <div><small class="text-muted d-block">Terpakai</small>
                                                                    <h5 class="mb-0 fw-bold">
                                                                        {{ $cuti_karyawan ? $cuti_karyawan->terpakai : 0 }}
                                                                        Hari
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div
                                                                class="d-flex align-items-center border rounded p-3 bg-light-subtle shadow-sm border-success">
                                                                <div class="avatar-initial bg-label-success rounded me-3">
                                                                    <i class="mdi mdi-check-decagram mdi-24px"></i>
                                                                </div>
                                                                <div><small class="text-muted d-block">Sisa Saldo</small>
                                                                    <h5 class="mb-0 fw-bold text-success">
                                                                        {{ $cuti_karyawan ? $cuti_karyawan->sisa : 0 }}
                                                                        Hari
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="pills-khusus" role="tabpanel">
                                                    <div class="table-responsive border rounded">
                                                        <table class="table table-sm table-hover mb-0"
                                                            id="table_cuti_diluar_tahunan" style="width: 100%">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-3">
                                                                        Jenis&nbsp;Cut&nbsp;Diluar&nbsp;Tahunan</th>
                                                                    <th class="text-center">Jatah&nbsp;(Max)</th>
                                                                    <th class="text-center">Terpakai</th>
                                                                    <th class="text-center">Sisa</th>
                                                                    <th>Status(Gender,&nbsp;Status&nbsp;Nikah)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div
                                                        class="mt-3 p-2 bg-warning-subtle rounded border-start border-warning border-3">
                                                        <small class="text-dark"><i
                                                                class="mdi mdi-alert-circle-outline me-1"></i> Cuti Diluar
                                                            Tahunan di atas bersifat <strong>per kejadian</strong> dan
                                                            memerlukan lampiran bukti pendukung (Sertifikat Nikah/Surat
                                                            Kematian).</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card shadow-sm">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-document-multiple-outline me-2"></i>RIWAYAT KONTRAK
                                                KERJA</h6>
                                            <button class="btn btn-sm btn-outline-info" id="btn_tambah_kontrak"
                                                type="button">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Kontrak Baru
                                            </button>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" id="tabel_kontrak"
                                                    style="width: 100%;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">Aksi</th>
                                                            <th>File Upload</th>
                                                            <th>File Download</th>
                                                            <th>Mulai</th>
                                                            <th>Lama</th>
                                                            <th>Selesai</th>
                                                            <th>Dibuat Pada</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="nav_jabatan2" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-document-multiple-outline me-2"></i>KATEGORI
                                                KARYAWAN</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row mt-2 g-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;" name="kategori"
                                                            id="kategori_add"
                                                            class="form-control border-0 border-bottom rounded-0 px-0">
                                                            <option value=""> Pilih Kategori</option>
                                                            <option value="Karyawan Bulanan"
                                                                {{ $karyawan->kategori == 'Karyawan Bulanan' ? 'selected' : '' }}>
                                                                Karyawan Bulanan</option>
                                                            <option value="Karyawan Harian"
                                                                {{ $karyawan->kategori == 'Karyawan Harian' ? 'selected' : '' }}>
                                                                Karyawan Harian</option>
                                                        </select>
                                                        <label for="kategori">Kategori Karyawan</label>
                                                    </div>
                                                    @error('kategori')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;" name="shift" id="shift_add"
                                                            class="form-control border-0 border-bottom rounded-0 px-0">
                                                            <option value="">~~ Pilih Shift ~~</option>
                                                            <option value="NON SHIFT"
                                                                {{ $karyawan->shift == 'NON SHIFT' ? 'selected' : '' }}>
                                                                NON SHIFT
                                                            </option>
                                                            <option value="SHIFT"
                                                                {{ $karyawan->shift == 'SHIFT' ? 'selected' : '' }}> SHIFT
                                                            </option>
                                                        </select>
                                                        <label for="shift">Shift</label>
                                                    </div>
                                                    @error('shift')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-4">
                                                <button class="btn btn-sm btn-primary px-4 shadow-sm" type="button"
                                                    id="btn_edit_kategori">
                                                    <i class="mdi mdi-check-outline me-1"></i> Simpan Kategori
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-clipboard-account-outline me-2"></i>DAFTAR JABATAN</h6>
                                            <button class="btn btn-sm btn-outline-info" id="btn_tambah_jabatan"
                                                type="button">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Jabatan Baru
                                            </button>
                                        </div>
                                        <div class="card-body p-4">

                                            <div class="row g-4">
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0" id="tabel_jabatan"
                                                        style="width: 100%;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>&nbsp;&nbsp;Aksi&nbsp;&nbsp;</th>
                                                                <th>Holding</th>
                                                                <th>Departemen</th>
                                                                <th>Divisi</th>
                                                                <th>Bagian</th>
                                                                <th>Jabatan</th>
                                                                <th>Jabatan&nbsp;Utama?</th>
                                                                <th>Aktif</th>
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

                                <div class="tab-pane fade" id="nav_bank" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i class="mdi mdi-bank me-2"></i>INFORMASI BANK
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row mb-2 gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <?php $bank = [
                                                            [
                                                                'kode_bank' => 'BBRI',
                                                                'bank' => 'BANK RAKYAT INDONESIA (BRI)',
                                                            ],
                                                            [
                                                                'kode_bank' => 'BBCA',
                                                                'bank' => 'BANK CENTRAL ASIA (BCA)',
                                                            ],
                                                            [
                                                                'kode_bank' => 'BOCBC',
                                                                'bank' => 'BANK OCBC',
                                                            ],
                                                            [
                                                                'kode_bank' => 'CIMB',
                                                                'bank' => 'BANK CIMB NIAGA',
                                                            ],
                                                            [
                                                                'kode_bank' => 'BMANDIRI',
                                                                'bank' => 'BANK MANDIRI',
                                                            ],
                                                        ];
                                                        ?>
                                                        <select style="font-size: small;" name="nama_bank" id="nama_bank"
                                                            onchange="bankCheck(this);"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nama_bank') is-invalid @enderror">
                                                            <option value="">Pilih Bank</option>
                                                            @foreach ($bank as $bank)
                                                                @if (old('nama_bank', $karyawan->nama_bank) == $bank['kode_bank'])
                                                                    <option value="{{ $bank['kode_bank'] }}" selected>
                                                                        {{ $bank['bank'] }}
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $bank['kode_bank'] }}">
                                                                        {{ $bank['bank'] }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <label for="nama_bank">Nama Bank</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" type="text"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nama_pemilik_rekening') is-invalid @enderror"
                                                            id="nama_pemilik_rekening" name="nama_pemilik_rekening"
                                                            value="{{ old('nama_pemilik_rekening', $karyawan->nama_pemilik_rekening) }}"
                                                            placeholder="Nama Pemilik Rekening" />
                                                        <label for="nama_pemilik_rekening">Nama Pemilik Rekening</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;" type="number"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nomor_rekening') is-invalid @enderror"
                                                            id="nomor_rekening" name="nomor_rekening"
                                                            value="{{ old('nomor_rekening', $karyawan->nomor_rekening) }}"
                                                            placeholder="Nomor Rekening" />
                                                        <label for="nomor_rekening">Nomor Rekening</label>
                                                    </div>
                                                    @error('nomor_rekening')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-center py-2">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    id="btn_update_bank">Simpan
                                                    Data Bank</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav_pajak" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-certificate me-2"></i>INFORMASI PAJAK</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('ptkp') is-invalid @enderror"
                                                            id="ptkp" name="ptkp">
                                                            <option @if (old('ptkp', $karyawan->ptkp) == '') selected @else @endif
                                                                value="">Pilih PKTP</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'TK/0') selected @else @endif
                                                                value="TK/0">TK/0</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'TK/1') selected @else @endif
                                                                value="TK/1">TK/1</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'TK/2') selected @else @endif
                                                                value="TK/2">TK/2</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'TK/3') selected @else @endif
                                                                value="TK/3">TK/3</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/0') selected @else @endif
                                                                value="K/0">K/0</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/1') selected @else @endif
                                                                value="K/1">K/1</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/2') selected @else @endif
                                                                value="K/2">K/2</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/I/0') selected @else @endif
                                                                value="K/I/0">K/I/0</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/I/1') selected @else @endif
                                                                value="K/I/1">K/I/1</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/I/2') selected @else @endif
                                                                value="K/I/2">K/I/2</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/I/3') selected @else @endif
                                                                value="K/I/3">K/I/3</option>
                                                            <option
                                                                @if (old('ptkp', $karyawan->ptkp) == 'K/3') selected @else @endif
                                                                value="K/3">K/3</option>
                                                        </select>
                                                        <label for="ptkp">PTKP</label>
                                                    </div>
                                                    @error('ptkp')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mt-2 gy-4">
                                                <div class="col mb-12">
                                                    <label class="form-check-label" for="status_npwp">Punya NPWP</label>
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <div class="row gy-4" style="margin-left: 2%;">
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="status_npwp_ya" name="status_npwp"
                                                                    class="form-check-input" value="on"
                                                                    @if (old('status_npwp', $karyawan->status_npwp) == 'on') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="status_npwp_ya">Ya</label>
                                                            </div>
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="status_npwp_tidak" name="status_npwp"
                                                                    class="form-check-input" value="off"
                                                                    @if (old('status_npwp', $karyawan->status_npwp) == 'off') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="status_npwp_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="row_npwp" class="row mt-2 gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nama_pemilik_npwp') is-invalid @enderror"
                                                            type="text" placeholder="Nama Pemilik NPWP"
                                                            id="nama_pemilik_npwp" name="nama_pemilik_npwp"
                                                            value="{{ old('nama_pemilik_npwp', $karyawan->nama_pemilik_npwp) }}" />
                                                        <label for="nama_pemilik_npwp">Nama Pemilik NPWP</label>
                                                    </div>
                                                    @error('nama_pemilik_npwp')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('npwp') is-invalid @enderror"
                                                            type="number" id="npwp" name="npwp"
                                                            value="{{ old('npwp', $karyawan->npwp) }}" />
                                                        <label for="npwp">NPWP</label>
                                                    </div>
                                                    @error('npwp')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center py-2">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    id="btn_update_pajak">Simpan
                                                    Data Pajak</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="nav_bpjs" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-smart-card-outline me-2"></i>INFORMASI BPJS</h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row gy-4">
                                                <div class="col mb-12">
                                                    <label class="form-check-label" for="bpjs_ketenagakerjaan">BPJS
                                                        Ketenagakerjaan</label>
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <div class="row gy-4" style="margin-left: 2%;">
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_ketenagakerjaan_ya"
                                                                    name="bpjs_ketenagakerjaan" class="form-check-input"
                                                                    value="on"
                                                                    @if (old('bpjs_ketenagakerjaan', $karyawan->bpjs_ketenagakerjaan) == 'on') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_ketenagakerjaan_ya">Ya</label>
                                                            </div>
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_ketenagakerjaan_tidak"
                                                                    name="bpjs_ketenagakerjaan" class="form-check-input"
                                                                    value="off"
                                                                    @if (old('bpjs_ketenagakerjaan', $karyawan->bpjs_ketenagakerjaan) == 'off') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_ketenagakerjaan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="row_bpjs_ketenagakerjaan" class="row mt-2 gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nama_pemilik_bpjs_ketenagakerjaan') is-invalid @enderror"
                                                            placeholder="Nama Pemilik BPJS Ketenagakerjaan" type="text"
                                                            id="nama_pemilik_bpjs_ketenagakerjaan"
                                                            name="nama_pemilik_bpjs_ketenagakerjaan"
                                                            value="{{ old('nama_pemilik_bpjs_ketenagakerjaan', $karyawan->nama_pemilik_bpjs_ketenagakerjaan) }}"
                                                            autofocus />
                                                        <label for="nama_pemilik_bpjs_ketenagakerjaan">Nama Pemilik BPJS
                                                            Ketenagakerjaan</label>
                                                    </div>
                                                    @error('nama_pemilik_bpjs_ketenagakerjaan')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('no_bpjs_ketenagakerjaan') is-invalid @enderror"
                                                            type="number" placeholder="No. BPJS Ketenagakerjaan"
                                                            id="no_bpjs_ketenagakerjaan" name="no_bpjs_ketenagakerjaan"
                                                            value="{{ old('no_bpjs_ketenagakerjaan', $karyawan->no_bpjs_ketenagakerjaan) }}"
                                                            autofocus />
                                                        <label for="no_bpjs_ketenagakerjaan">No. BPJS
                                                            Ketenagakerjaan</label>
                                                    </div>
                                                    @error('no_bpjs_ketenagakerjaan')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mt-2 gy-4">
                                                <div class="col mb-12">
                                                    <label class="form-check-label" for="bpjs_pensiun_ya">BPJS
                                                        Pensiun</label>
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <div class="row gy-4" style="margin-left: 2%;">
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_pensiun_ya" name="bpjs_pensiun"
                                                                    class="form-check-input" value="on"
                                                                    @if (old('bpjs_pensiun', $karyawan->bpjs_pensiun) == 'on') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_pensiun_ya">Ya</label>
                                                            </div>
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_pensiun_tidak" name="bpjs_pensiun"
                                                                    class="form-check-input" value="off"
                                                                    @if (old('bpjs_pensiun', $karyawan->bpjs_pensiun) == 'off') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_pensiun_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2 gy-4">
                                                <div class="col mb-12">
                                                    <label class="form-check-label" for="bpjs_kesehatan">BPJS
                                                        Kesehatan</label>
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <div class="row gy-4" style="margin-left: 2%;">
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_kesehatan_ya" name="bpjs_kesehatan"
                                                                    class="form-check-input" value="on"
                                                                    @if (old('bpjs_kesehatan', $karyawan->bpjs_kesehatan) == 'on') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_kesehatan_ya">Ya</label>
                                                            </div>
                                                            <div class="col-lg-2 form-check">
                                                                <input style="font-size: small;" type="radio"
                                                                    id="bpjs_kesehatan_tidak" name="bpjs_kesehatan"
                                                                    class="form-check-input" value="off"
                                                                    @if (old('bpjs_kesehatan', $karyawan->bpjs_kesehatan) == 'off') checked @else @endif>
                                                                <label class="form-check-label"
                                                                    for="bpjs_kesehatan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="row_bpjs_kesehatan" class="row mt-2 gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('nama_pemilik_bpjs_kesehatan') is-invalid @enderror"
                                                            type="text" id="nama_pemilik_bpjs_kesehatan"
                                                            placeholder="Nama Pemilik BPJS Kesehatan"
                                                            name="nama_pemilik_bpjs_kesehatan"
                                                            value="{{ old('nama_pemilik_bpjs_kesehatan', $karyawan->nama_pemilik_bpjs_kesehatan) }}"
                                                            autofocus />
                                                        <label for="nama_pemilik_bpjs_kesehatan">Nama Pemilik BPJS
                                                            Kesehatan</label>
                                                    </div>
                                                    @error('nama_pemilik_bpjs_kesehatan')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <input style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('no_bpjs_kesehatan') is-invalid @enderror"
                                                            type="number" id="no_bpjs_kesehatan"
                                                            name="no_bpjs_kesehatan"
                                                            value="{{ old('no_bpjs_kesehatan', $karyawan->no_bpjs_kesehatan) }}"
                                                            autofocus />
                                                        <label for="no_bpjs_kesehatan">No. BPJS Kesehatan</label>
                                                    </div>
                                                    @error('no_bpjs_kesehatan')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div id="row_kelas_bpjs" class="row mt-2 gy-4">
                                                <div class="col-md-6">
                                                    <div class="form-floating form-floating-outline custom-form-group">
                                                        <select style="font-size: small;"
                                                            class="form-control border-0 border-bottom rounded-0 px-0 @error('kelas_bpjs') is-invalid @enderror"
                                                            id="kelas_bpjs" name="kelas_bpjs"
                                                            value="{{ old('kelas_bpjs', $karyawan->kelas_bpjs) }}">
                                                            <option
                                                                @if (old('kelas_bpjs', $karyawan->kelas_bpjs) == '') selected @else @endif
                                                                value="">Pilih Kelas BPJS</option>
                                                            <option
                                                                @if (old('kelas_bpjs', $karyawan->kelas_bpjs) == 'Kelas 1') selected @else @endif
                                                                value="Kelas 1">Kelas 1</option>
                                                            <option
                                                                @if (old('kelas_bpjs', $karyawan->kelas_bpjs) == 'Kelas 2') selected @else @endif
                                                                value="Kelas 2">Kelas 2</option>
                                                            <option
                                                                @if (old('kelas_bpjs', $karyawan->kelas_bpjs) == 'Kelas 3') selected @else @endif
                                                                value="Kelas 3">Kelas 3</option>
                                                        </select>
                                                        <label for="kelas_bpjs">Kelas BPJS</label>
                                                    </div>
                                                    @error('kelas_bpjs')
                                                        <p class="alert alert-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center py-2">
                                        <button class="btn btn-sm btn-primary" type="button"
                                            id="btn_update_bpjs">Simpan
                                            Data BPJS</button>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav_keahlian" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-wrench-outline me-2"></i>INFORMASI KEAHLIAN</h6>
                                            <button class="btn btn-sm btn-outline-primary" id="btn_tambah_keahlian"
                                                type="button">
                                                <i class="mdi mdi-plus me-2"></i>Tambah Keahlian</button>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row gy-4">
                                                <div class="d-flex justify-content-center">
                                                    <p class="" id="pesan_disabled_keahlian">
                                                        *Silakan
                                                        edit atau hapus data untuk
                                                        menambahkan
                                                        data lain
                                                        (max : 5)
                                                    </p>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0"
                                                        id="table_keahlian" style="width: 100%;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Aksi</th>
                                                                <th>No</th>
                                                                <th>Nama&nbsp;Keahlian</th>
                                                                <th>File</th>
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
                                <div class="tab-pane fade" id="nav_riwayat" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-file-document-multiple-outline me-2"></i>INFORMASI
                                                RIWAYAT PEKERJAAN</h6>
                                            <button class="btn btn-sm btn-outline-primary" id="btn_tambah_riwayat"
                                                type="button">
                                                <i class="mdi mdi-plus me-2"></i>
                                                Tambah Riwayat Pekerjaan</button>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-center">
                                                <p class="" id="pesan_disabled">*Silakan edit
                                                    atau
                                                    hapus data untuk menambahkan data lain
                                                    (max : 3)
                                                </p>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <p class="text-danger">*Kosongkan apabila belum punya
                                                    pengalaman kerja</p>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0" id="tabel_riwayat"
                                                    style="width: 100%;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Aksi</th>
                                                            <th>Nama Perusahaan</th>
                                                            <th>Alamat Perusahaan</th>
                                                            <th>Posisi</th>
                                                            <th>Gaji Terakhir</th>
                                                            <th>Tanggal Masuk</th>
                                                            <th>Tanggal Keluar</th>
                                                            <th>Alasan Keluar</th>
                                                            <th>Surat Keterangan</th>
                                                            <th>Kontak Referensi</th>
                                                            <th>Jabatan Referensi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav_kesehatan" role="tabpanel">
                                    <div class="card shadow-sm mb-3">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-shield-account-outline me-2"></i>INFORMASI KESEHATAN
                                            </h6>

                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah anda
                                                        perokok?</label>
                                                    <select class="form-select" name="perokok" id="perokok_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->perokok) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->perokok) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                    <input type="hidden" value="{{ $karyawan->id }}"
                                                        id="id_user_kesehatan_add" name="id_karyawan">
                                                </div>
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah Anda
                                                        mengonsumsi
                                                        alkohol?</label>
                                                    <select class="form-select" name="alkohol" id="alkohol_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->alkohol) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->alkohol) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah Anda memiliki
                                                        alergi?</label>
                                                    <select class="form-select" name="alergi" id="alergi_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->alergi) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->alergi) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2" id="">
                                                    <div id="sebutkan_alergi">
                                                        <label class="text-muted" for="">Jika Ya,
                                                            sebutkan:</label>
                                                        <input type="text" name="sebutkan_alergi"
                                                            id="sebutkan_alergi_add" value=""
                                                            class="form-control"
                                                            placeholder="Contoh :  alergi kulit, alergi debu, alergi obat">
                                                    </div>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah anda memiliki
                                                        Phobia ?</label>
                                                    <select class="form-select" name="phobia" id="phobia_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->phobia) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->phobia) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                    <div id="sebutkan_phobia">
                                                        <label class="text-muted" for="">Jika Ya,
                                                            sebutkan:</label>
                                                        <input type="text" name="sebutkan_phobia"
                                                            id="sebutkan_phobia_add"
                                                            placeholder="Contoh : ketinggian, laba-laba, petir"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah anda memiliki
                                                        Keterbatasan Fisik ?</label>
                                                    <select class="form-select" name="keterbatasan_fisik"
                                                        id="keterbatasan_fisik_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->keterbatasan_fisik) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->keterbatasan_fisik) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                    <div id="sebutkan_keterbatasan_fisik">
                                                        <label class="text-muted" for="">Jika Ya,
                                                            sebutkan:</label>
                                                        <input type="text" name="sebutkan_keterbatasan_fisik"
                                                            id="sebutkan_keterbatasan_fisik_add" value=""
                                                            placeholder="Contoh : kehilangan anggota tubuh (amputasi), cerebral palsy"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah Anda sedang
                                                        dalam
                                                        pengobatan rutin?</label>
                                                    <select class="form-select" name="pengobatan_rutin"
                                                        id="pengobatan_rutin_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pengobatan_rutin) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pengobatan_rutin) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div id="sebutkan_pengobatan_rutin">
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Sebutkan
                                                            Jenis
                                                            Obat</label>
                                                        <input type="hidden" value="{{ $karyawan->id }}"
                                                            id="id_user_pengobatan_add" name="id_karyawan">
                                                        <input type="text" name="obat" id="obat_add"
                                                            value="" class="form-control">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Sebutkan
                                                            Alasan</label>
                                                        <input type="text" name="alasan" id="alasan_add"
                                                            value="" class="form-control">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <button class="btn btn-primary" type="button"
                                                            id="tambah_pengobatan">Tambah
                                                            Pengobatan</button>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <div class="col-lg p-2">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="table py-2">
                                                        <table id="tabel_pengobatan"
                                                            class="text-left text-nowrap display" style="width: 100%;">
                                                            <thead class="">
                                                                <tr>
                                                                    <th>Jenis Obat</th>
                                                                    <th>Alasan</th>
                                                                    <th>Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-border-bottom-0">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                Apakah Anda pernah atau sedang menderita penyakit kronis berikut
                                                ini?
                                                (Centang jika pernah/sedang)
                                            </div>
                                            <div class="p-3">
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="asma" id="asma_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->asma) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Asma
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="diabetes" id="diabetes_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->diabetes) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Diabetes
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="hipertensi" id="hipertensi_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->hipertensi) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Hipertensi (Tekanan Darah Tinggi)
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="jantung" id="jantung_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->jantung) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Jantung
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="tbc" id="tbc_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->tbc) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            TBC
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="hepatitis" id="hepatitis_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->hepatitis) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Hepatitis
                                                        </label>
                                                    </div>

                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="epilepsi" id="epilepsi_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->epilepsi) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Epilepsi
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="gangguan_mental" id="gangguan_mental_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->gangguan_mental) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Gangguan Mental (depresi, bipolar, dll)
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="gangguan_pengelihatan" id="gangguan_pengelihatan_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->gangguan_pengelihatan) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Gangguan penglihatan/pendengaran
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="gangguan_lainnya" id="gangguan_lainnya_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->gangguan_lainnya) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Lainnya
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div id="gangguan_sebutkan">
                                                    <div class="row">
                                                        <div class="col-lg p-2">
                                                            <label class="text-muted" for="">Sebutkan</label>
                                                            <input type="text" name="sebutkan_gangguan"
                                                                id="sebutkan_gangguan_add" value=""
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-lg p-2">

                                                        </div>
                                                        <div class="col-lg p-2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Pernahkah Anda
                                                        dirawat di
                                                        rumah sakit?</label>
                                                    <select class="form-select" name="pernah_dirawat_rs"
                                                        id="pernah_dirawat_rs_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pernah_dirawat_rs) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pernah_dirawat_rs) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div id="pernah_dirawat_sebutkan">
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Tahun</label>
                                                        <input type="hidden" value="{{ $karyawan->id }}"
                                                            id="id_user_rs_add" name="id_karyawan">
                                                        <input type="text" name="tahun_rs" id="tahun_rs_add"
                                                            value="" class="form-control" maxlength="4">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Penyebab</label>
                                                        <input type="text" name="penyebab_rs" id="penyebab_rs_add"
                                                            value="" class="form-control">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <button class="btn btn-primary" type="button"
                                                            id="tambah_rs">Tambah</button>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="table py-2">
                                                        <table id="tabel_rs" class="text-left text-nowrap display"
                                                            style="width: 100%;">
                                                            <thead class="">
                                                                <tr>
                                                                    <th>Tahun</th>
                                                                    <th>Penyebab</th>
                                                                    <th>Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-border-bottom-0">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Pernahkah Anda
                                                        mengalami
                                                        kecelakaan serius?
                                                    </label>
                                                    <select class="form-select" name="kecelakaan_serius"
                                                        id="kecelakaan_serius_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->kecelakaan_serius) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->kecelakaan_serius) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div id="kecelakaan_serius_sebutkan">
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Tahun</label>
                                                        <input type="hidden" value="{{ $karyawan->id }}"
                                                            id="id_user_kecelakaan_add" name="id_karyawan">
                                                        <input type="text" name="tahun_kecelakaan"
                                                            id="tahun_kecelakaan_add" value=""
                                                            class="form-control" maxlength="4">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <label class="text-muted" for="">Penyebab</label>
                                                        <input type="text" name="penyebab_kecelakaan"
                                                            id="penyebab_kecelakaan_add" value=""
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <button class="btn btn-primary" type="button"
                                                            id="tambah_kecelakaan">Tambah</button>
                                                        <div class="col-lg p-2">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="table py-2">
                                                        <table id="tabel_kecelakaan"
                                                            class="text-left text-nowrap display" style="width: 100%;">
                                                            <thead class="">
                                                                <tr>
                                                                    <th>Tahun</th>
                                                                    <th>Penyebab</th>
                                                                    <th>Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="table-border-bottom-0">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah Anda mampu
                                                        bekerja
                                                        dalam shift malam/lembur?</label>
                                                    <select class="form-select" name="mampu_shift"
                                                        id="mampu_shift_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->mampu_shift) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->mampu_shift) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg p-2">
                                                    <label class="text-muted" for="">Apakah Anda pernah
                                                        menjalani pemeriksaan kesehatan kerja sebelumnya?</label>
                                                    <select class="form-select" name="pemeriksaan_kerja_sebelumnya"
                                                        id="pemeriksaan_kerja_sebelumnya_add">
                                                        <option value="2"
                                                            {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pemeriksaan_kerja_sebelumnya) ? 'selected' : '' }}>
                                                            Tidak</option>
                                                        <option value="1"
                                                            {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pemeriksaan_kerja_sebelumnya) ? 'selected' : '' }}>
                                                            Ya</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg p-2">
                                                    <div id="pemeriksaan_sebelumnya_hasil">
                                                        <label class="text-muted" for="">Jika Ya, apakah
                                                            dinyatakan layak bekerja? </label>
                                                        <select class="form-select" name="pemeriksaan_sebelumnya_hasil"
                                                            id="pemeriksaan_sebelumnya_hasil_add">
                                                            <option value="2"
                                                                {{ '2' == old('2', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pemeriksaan_sebelumnya_hasil) ? 'selected' : '' }}>
                                                                Tidak</option>
                                                            <option value="1"
                                                                {{ '1' == old('1', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pemeriksaan_sebelumnya_hasil) ? 'selected' : '' }}>
                                                                Ya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg p-2">
                                                </div>
                                            </div>
                                            <div class="row mt-2">

                                                <div class="row">
                                                    Riwayat Vaksinasi (Opsional)
                                                </div>
                                            </div>
                                            <div class="row p-3">
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="covid" id="covid_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->covid) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            COVID-19 (dosis lengkap)
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="hepatitis" id="hepatitis_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->hepatitis) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Hepatitis B
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="tetanus" id="tetanus_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->tetanus) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Tetanus
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg p-2">
                                                        <input class="form-check-input border" type="checkbox"
                                                            name="vaksin_lainnya" id="vaksin_lainnya_add"
                                                            {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->vaksin_lainnya) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark"
                                                            for="flexCheckDefault">
                                                            Lainnya
                                                        </label>
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                    <div class="col-lg p-2">
                                                    </div>
                                                </div>
                                                <div id="sebutkan_vaksin_lainnya">
                                                    <div class="row">
                                                        <div class="col-lg p-2">
                                                            <label class="text-muted" for="">Sebutkan</label>
                                                            <input type="text" name="sebutkan_vaksin_lainnya"
                                                                id="sebutkan_vaksin_lainnya_add" class="form-control">
                                                        </div>
                                                        <div class="col-lg p-2">
                                                        </div>
                                                        <div class="col-lg p-2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                "Saya menyatakan bahwa semua informasi yang saya berikan adalah
                                                benar
                                                dan dapat dipertanggungjawabkan. Saya bersedia menjalani tes
                                                kesehatan
                                                jika dibutuhkan oleh perusahaan."
                                            </div>
                                            <div class="row px-3">
                                                <div class="col-lg p-2">
                                                    <input class="form-check-input border" type="checkbox"
                                                        name="persetujuan_kesehatan" id="persetujuan_kesehatan_add"
                                                        {{ 'on' == old('on', $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->persetujuan_kesehatan) ? 'checked' : '' }}>
                                                    <label class="form-check-label text-dark" for="flexCheckDefault">
                                                        Setuju
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    id="btn_update_kesehatan">Simpan
                                                    Data</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav_inventaris" role="tabpanel">
                                    <div class="bg-light-subtle mb-3 p-4">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark"><i
                                                    class="mdi mdi-image-broken me-1"></i>INVENTARIS</h6>
                                            {{-- <button class="btn btn-sm btn-outline-primary" id="btn_tambah_keahlian"
                                                type="button">
                                                <i class="mdi mdi-plus me-2"></i>Tambah Keahlian</button> --}}
                                        </div>
                                        <div class="card-body bg-white p-4">
                                            <div class="row gy-4">

                                                <div class="table-responsive">
                                                    <table class="table" id="table_inventaris"
                                                        style="width: 100%; font-size: smaller;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No.</th>
                                                                {{-- <th>Opsi</th> --}}
                                                                <th>Nama&nbsp;Inventaris</th>
                                                                <th>Foto</th>
                                                                <th>Nomor&nbsp;Seri</th>
                                                                <th>ID&nbsp;Aset</th>
                                                                <th>Nama&nbsp;Kategori</th>
                                                                <th>Holding&nbsp;Pemilik</th>
                                                                <th>Site</th>
                                                                <th>Nama&nbsp;Gedung</th>
                                                                <th>Nama&nbsp;Ruangan</th>
                                                                <th>Status</th>
                                                                <th>Kondisi</th>
                                                                <th>Keterangan</th>
                                                                <th>Nama&nbsp;Karyawan</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal   -->
    <div class="modal fade" id="modal_tambah_riwayat" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Riwayat
                        Pekerjaan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" value="{{ $karyawan->id }}" id="id_karyawan_add" name="id_karyawan">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Nama
                                Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan_add"
                                name="nama_perusahaan">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Alamat
                                Perusahaan</label>
                            <input type="text" class="form-control" id="alamat_perusahaan_add"
                                name="alamat_perusahaan">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Posisi</label>
                            <input type="text" class="form-control" id="posisi_add" name="posisi">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Gaji
                                Terakhir</label>
                            <input type="text" class="form-control" id="gaji_add" name="gaji_terakhir">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Masuk</label>
                            <input type="date" class="form-control" id="tanggal_masuk_add" name="tanggal_masuk">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Keluar</label>
                            <input type="date" class="form-control" id="tanggal_keluar_add"
                                name="tanggal_keluar">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Alasan
                                Keluar</label>
                            <input type="text" class="form-control" id="alasan_keluar_add" name="alasan_keluar">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Surat
                                Keterangan Pernah Bekerja
                                *PDF
                                (Opsional), Max: 5 MB</label>
                            <input type="file" accept="application/pdf" class="form-control"
                                id="surat_keterangan_add" name="surat_keterangan">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Kontak
                                Referensi Pengalaman Kerja
                                (Opsional)</label>
                            <input type="text" class="form-control" id="nomor_referensi_add"
                                name="nomor_referensi">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Jabatan
                                Referensi
                                (Opsional)</label>
                            <input type="text" class="form-control" id="jabatan_referensi_add"
                                name="jabatan_referensi">
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_save_riwayat">Masukkan
                            Riwayat</button>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_riwayat" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Riwayat
                        Pekerjaan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" value="" id="id_riwayat_update" name="id_riwayat">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Nama
                                Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan_update"
                                name="nama_perusahaan">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Alamat
                                Perusahaan</label>
                            <input type="text" class="form-control" id="alamat_perusahaan_update"
                                name="alamat_perusahaan">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Posisi</label>
                            <input type="text" class="form-control" id="posisi_update" name="posisi">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Gaji
                                Terakhir</label>
                            <input type="text" class="form-control" id="gaji_update" name="gaji">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Masuk</label>
                            <input type="date" class="form-control" id="tanggal_masuk_update"
                                name="tanggal_masuk">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Keluar</label>
                            <input type="date" class="form-control" id="tanggal_keluar_update"
                                name="tanggal_keluar">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Alasan
                                Keluar</label>
                            <input type="text" class="form-control" id="alasan_keluar_update"
                                name="alasan_keluar">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Surat
                                Keterangan Pernah Bekerja
                                *PDF
                                (Opsional), Max: 5 MB</label>
                            <input type="file" class="form-control" id="surat_keterangan_update"
                                name="surat_keterangan" accept="application/pdf">
                            <input type="hidden" class="form-control" id="old_file_update" name="old_file">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Kontak
                                Referensi Pengalaman Kerja
                                (Opsional)</label>
                            <input type="text" class="form-control" id="nomor_referensi_update"
                                name="nomor_referensi">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Jabatan
                                Referensi
                                (Opsional)</label>
                            <input type="text" class="form-control" id="jabatan_referensi_update"
                                name="jabatan_referensi">
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_update_riwayat">Masukkan
                            Riwayat</button>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_add_pendidikan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_add_pendidikan">
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="jenjang" id="jenjang" class="form-control">
                                    <option value="">--Pilih Jenjang--</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                                <label for="jenjang">Jenjang</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <input type="text" name="nama_instansi" id="nama_instansi" class="form-control"
                                    value="">
                                <label for="nama_instansi">Nama Instansi</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <input type="text" name="jurusan" id="jurusan" class="form-control"
                                    value="">
                                <label for="jurusan">Jurusan</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="tahun_masuk" id="tahun_masuk" class="form-control">
                                    <option value="">--Pilih Tahun Masuk--</option>
                                    @for ($i = date('Y'); $i >= 1900; $i--)
                                        <option value="{{ $i }}">
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <label for="tahun_masuk">Tahun Masuk</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="tahun_lulus" id="tahun_lulus" class="form-control">
                                    <option value="">--Pilih Tahun Lulus--</option>
                                    @for ($i = date('Y'); $i >= 1900; $i--)
                                        <option value="{{ $i }}">
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <label for="tahun_lulus">Tahun Lulus</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="button" id="btn_simpan_pendidikan" class="btn btn-sm btn-primary"><i
                                    class="mdi mdi-content-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_pendidikan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Pendidikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_edit_pendidikan">
                        <div class="col-md-12 mb-3">
                            <input type="hidden" name="id_pendidikan" id="id_pendidikan" value="">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="jenjang_update" id="jenjang_update" class="form-control">
                                    <option value="">--Pilih Jenjang--</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                                <label for="jenjang_update">Jenjang</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <input type="text" name="nama_instansi_update" id="nama_instansi_update"
                                    class="form-control" value="">
                                <label for="nama_instansi_update">Nama Instansi</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <input type="text" name="jurusan_update" id="jurusan_update" class="form-control"
                                    value="">
                                <label for="jurusan_update">Jurusan</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="tahun_masuk_update" id="tahun_masuk_update" class="form-control">
                                    <option value="">--Pilih Tahun Masuk--</option>
                                    @for ($i = date('Y'); $i >= 1900; $i--)
                                        <option value="{{ $i }}">
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <label for="tahun_masuk_update">Tahun Masuk</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline custom-form-group">
                                <select name="tahun_lulus_update" id="tahun_lulus_update" class="form-control">
                                    <option value="">--Pilih Tahun Lulus--</option>
                                    @for ($i = date('Y'); $i >= 1900; $i--)
                                        <option value="{{ $i }}">
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <label for="tahun_lulus_update">Tahun Lulus</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="button" id="btn_simpan_edit_pendidikan" class="btn btn-sm btn-primary"><i
                                    class="mdi mdi-content-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_tambah_kontrak" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Kontrak</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="kontrak_form">
                        <input type="hidden" value="{{ $karyawan->id }}" id="id_karyawan_add" name="id_karyawan">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">
                                File Kontrak
                                *PDF
                                (Opsional), Max: 5 MB</label>
                            <input type="file" accept="application/pdf" class="form-control"
                                id="file_kontrak_add" name="file_kontrak">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai_add" name="tanggal_mulai">
                        </div>
                        <div class="mb-5">
                            <label for="recipient-name" class="col-form-label">Durasi
                                Kontrak</label>
                            <select class="form-select" id="lama_kontrak_add" name="lama_kontrak">
                                <option value="" selected>Pilih Kontrak</option>
                                <option value="1bln">1 Bulan</option>
                                <option value="3bln">3 Bulan</option>
                                <option value="6bln">6 Bulan</option>
                                <option value="1thn">1 Tahun</option>
                                <option value="2thn">2 Tahun</option>
                                <option value="tetap">Karyawan Tetap</option>
                            </select>
                        </div>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_save_kontrak">Masukkan
                            Kontrak</button>
                    </form>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_kontrak" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="jabatan_update_form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Kontrak
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="" id="id_kontrak_update" name="id_kontrak">
                        <input type="hidden" value="" id="old_file_update" name="old_file">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">
                                File Kontrak
                                *PDF
                                (Opsional), Max: 5 MB</label>
                            <input type="file" accept="application/pdf" class="form-control"
                                id="file_kontrak_update" name="file_kontrak">
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Tanggal
                                Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai_update"
                                name="tanggal_mulai">
                        </div>
                        <div class="mb-5">
                            <label for="recipient-name" class="col-form-label">Durasi
                                Kontrak</label>
                            <select class="form-select" id="lama_kontrak_update" autofocus name="lama_kontrak">
                                <option value="" selected>Pilih Kontrak</option>
                                <option value="1bln">1 Bulan</option>
                                <option value="3bln">3 Bulan</option>
                                <option value="6bln">6 Bulan</option>
                                <option value="1thn">1 Tahun</option>
                                <option value="2thn">2 Tahun</option>
                                <option value="tetap">Karyawan Tetap</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_update_kontrak">Update</button>

                    </div>
                    <div class="modal-footer">

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_tambah_jabatan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Jabatan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="jabatan_form" method="post">
                        <div class="form-floating form-floating-outline custom-form-group mb-4 mt-2">
                            <select class="form-select select-jabatan" id="holding_id_add" name="holding_id">
                                <option value="" disabled selected>Pilih Penempatan
                                </option>
                                @foreach ($holdingAll as $hh)
                                    <option value="{{ $hh->id }}">
                                        {{ $hh->holding_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="holding_id_add" class="col-form-label">Penempatan&nbsp;Holding</label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select select-jabatan" id="departemen_id_add" name="departemen_id">
                            </select>
                            <label for="departemen_id_add" class="col-form-label">Departemen</label>
                            <div class="select-loader d-none">
                                <div class="dot-loader">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select select-jabatan" id="divisi_id_add" name="divisi_id"></select>
                            <label for="divisi_id_add" class="col-form-label">Divisi</label>
                            <div class="select-loader d-none">
                                <div class="dot-loader">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select select-jabatan" id="bagian_id_add" name="bagian_id"></select>
                            <label for="bagian_id_add" class="col-form-label">Bagian</label>
                            <div class="select-loader d-none">
                                <div class="dot-loader">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select select-jabatan" id="jabatan_id_add" name="jabatan_id"></select>
                            <label for="jabatan_id_add" class="col-form-label">Jabatan</label>
                            <div class="select-loader d-none">
                                <div class="dot-loader">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Jabatan
                                Utama?</label>
                            <select class="form-select select-jabatan" id="main_add" name="main_id">
                                <option value="" disabled selected>Pilih</option>
                                <option value="1">Ya</option>
                                <option value="2">Tidak</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_save_jabatan">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_jabatan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="jabatan_update_form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Jabatan
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="" id="id_jbt_karyawan_update" name="id_jbt_karyawan">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Aktif</label>
                            <select class="form-control" id="is_active_update" name="is_active" autofocus
                                value="">
                                <option value="1">Aktif</option>
                                <option value="2">Non Aktif</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn_update_jabatan">Update</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_add_keahlian" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Keahlian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_add_keahlian" enctype="multipart/form-data">
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" name="nama_keahlian" id="nama_keahlian" class="form-control"
                                    value="">
                                <label for="nama_keahlian">Nama Keahlian</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="file" name="file_keahlian" id="file_keahlian" class="form-control"
                                    hidden value="" accept=".pdf">
                                <button type="button" id="btn_upload_keahlian" class="btn btn-sm btn-secondary"><i
                                        class="mdi mdi-upload"></i> Upload</button>
                                <p class="text-primary">format: PDF</p>
                                <div class="group-button-keahlian align-items-center">
                                </div>
                                <label for="file_keahlian">File</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="button" id="btn_simpan_keahlian" class="btn btn-sm btn-primary"><i
                                    class="mdi mdi-content-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_keahlian" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Keahlian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_edit_keahlian">
                        <div class="col-md-12 mb-3">
                            <input type="hidden" name="id_keahlian" id="id_keahlian" value="">
                            <div class="form-floating form-floating-outline">
                                <input type="text" name="nama_keahlian_update" id="nama_keahlian_update"
                                    class="form-control" value="">
                                <label for="nama_keahlian_update">Nama Keahlian</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="file_keahlian_update">File Keahlian</label>
                            <input type="file" name="file_keahlian_update" id="file_keahlian_update" hidden
                                accept=".pdf" class="form-control" value="">
                            <input type="hidden" name="file_keahlian_old_update" id="file_keahlian_old_update"
                                value="">
                            <div class="group-update-keahlian align-items-center">
                            </div>

                        </div>
                        <div class="col-md-12 mb-3">
                            <button type="button" id="btn_simpan_edit_keahlian" class="btn btn-sm btn-primary"><i
                                    class="mdi mdi-content-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- / Modal -->
@endsection
@section('js')
    @include('admin.karyawan.detail_karyawan_js')
@endsection
