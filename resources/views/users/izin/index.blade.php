@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        .form-input {
            position: relative;
        }

        /* Offcanvas Kedua (Konfirmasi) */
        #off-canvas-konfirmasi-save {
            z-index: 1085 !important;
        }

        /* Backdrop untuk Offcanvas Kedua */
        /* Ini sedikit trik karena Bootstrap pakai satu class backdrop */
        #off-canvas-terlambat {
            z-index: 1060 !important;
        }

        .offcanvas-backdrop+.offcanvas-backdrop {
            z-index: 1070 !important;
            opacity: 0.2 !important;
            /* Buat backdrop kedua lebih tipis agar tidak gelap total */
        }

        /* ICON */
        .form-input .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 6;
            pointer-events: none;
        }

        .profile-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .profile-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            border-bottom: none;
        }

        .profile-tabs .nav-link {
            border: none;
            border-radius: 14px;
            padding: 8px 12px;
            background: #f3e8ff;
            color: #6b21a8;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .profile-tabs .nav-link.active {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: #fff;
            box-shadow: 0 6px 14px rgba(168, 85, 247, 0.4);
        }

        .profile-tabs .nav-link i {
            font-size: 12px;
        }

        @media (max-width: 576px) {
            .profile-card {
                border-radius: 12px;
            }

            .profile-tabs span {
                font-size: 10px;
            }
        }

        /* Memaksa kalender muncul di paling depan */
        .daterangepicker {
            z-index: 9999 !important;
            /* Angka tinggi agar di atas elemen apapun */
            border: none !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px !important;
            padding: 8px !important;
            margin-top: 5px !important;
        }

        /* Menghilangkan panah kecil di atas kalender agar lebih clean */
        .daterangepicker:before,
        .daterangepicker:after {
            display: none !important;
        }

        .daterangepicker .ranges li.active {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            color: #fff;
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            border-color: transparent;
            color: #fff;
        }

        .daterangepicker td.in-range {
            background-color: #dcd9fa;
            border-color: transparent;
            color: #000;
            border-radius: 0;
        }

        /* Responsif Mobile */
        @media (max-width: 768px) {
            .daterangepicker {
                width: 90% !important;
                left: 5% !important;
                right: 5% !important;
                position: fixed !important;
                /* Membuatnya 'floating' di tengah layar mobile */
                top: 20% !important;
            }

            .daterangepicker .drp-calendar {
                max-width: 100% !important;
            }
        }

        /* Style Signature Pad */
        .kbw-signature {
            width: 100%;
            height: 160px;
            border: 1px solid #f3e8ff !important;
            background: #ffffff;
        }

        #sig canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .border-dashed {
            border: 2px dashed #d8b4fe !important;
        }

        .opacity-75 {
            opacity: 0.75;
        }

        /* Agar tulisan di dalam kotak otorisasi rapi */
        .bg-light small {
            letter-spacing: 0.5px;
        }

        .futuristic-header {
            padding: 28px 20px 18px;
        }

        .futuristic-header h5 {
            color: --primary();
            font-weight: 600;
        }

        .futuristic-header small {
            color: rgba(255, 255, 255, 0.65);
        }

        .futuristic-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(145deg, #7b2ff7, #9b6dff);
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
        }

        .coworker-card {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-history {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .icon-box-success {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            width: 55px;
            height: 55px;
            line-height: 50px;
            text-align: center;
            border: 1px solid #8ae68f;
            border-radius: 50%;
        }

        .icon-box-success i {
            color: #2f7e32;
        }

        .icon-box-reject {
            background: linear-gradient(135deg, #fbcfcf, #ffffff);
            width: 55px;
            height: 55px;
            line-height: 50px;
            text-align: center;
            border: 1px solid #dc8888;
            border-radius: 50%;
        }

        .icon-box-reject i {
            color: #8b3838;
        }

        .coworker-card-reject {
            background: linear-gradient(135deg, #fbcfcf, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
        }

        .skeleton {
            display: inline-block;
            background: linear-gradient(90deg,
                    #ede9fe 25%,
                    #f5f3ff 37%,
                    #ede9fe 63%);
            background-size: 400% 100%;
            animation: pulse 1.4s ease-in-out infinite;
            border-radius: 6px;
        }

        .skeleton-number {
            display: inline-block;
            /* ⬅️ WAJIB */
            width: 2ch;
            height: 30px;
            /* ⬅️ samakan dengan h3 */
            vertical-align: middle;
            /* ⬅️ penting */
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: .5;
            }

            50% {
                opacity: 1;
            }
        }

        .form-control {
            border-radius: 12px;
            padding-left: 42px;
            height: 44px;
            border: 1px solid #e5e5e5;
        }

        .form-input {
            position: relative;
            margin-bottom: 14px;
        }

        .form-input .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            /* transform: translateY(-50%); */
            color: #6f42c1;
            font-size: 14px;
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

        .quota-card-sm {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 5px;
            border: 1px solid #edf2f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .highlight-purple {
            background: #f8f7ff;
            border: 1px solid #e9e5ff;
        }

        .q-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .q-value-wrap {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 2px;
        }

        .q-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
        }

        .q-unit {
            font-size: 9px;
            color: #a0aec0;
            font-weight: 500;
        }

        /* Warna Khusus */
        .text-purple {
            color: #6b46c1;
        }

        .text-purple-bold {
            color: #553c9a;
        }

        .bg-purple {
            background-color: #6b46c1;
        }

        /* Aksen Bar di bawah card */
        .q-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            opacity: 0.6;
        }



        .btn-quota-modern {
            background: linear-gradient(135deg, #6f42c1 0%, #a855f7 100%);
            border: none;
            border-radius: 16px;
            padding: 6px 6px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.3);
            transition: all 0.3s ease;
            width: 100%;
        }

        /* Efek saat tombol ditekan (Mobile Touch) */
        .btn-quota-modern:active {
            transform: scale(0.97);
            box-shadow: 0 4px 10px rgba(111, 66, 193, 0.2);
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(5px);
        }

        .text-box {
            display: flex;
            flex-direction: column;
        }

        .btn-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .btn-subtitle {
            font-size: 11px;
            opacity: 0.8;
            font-weight: 400;
        }

        .arrow-icon {
            font-size: 14px;
            opacity: 0.7;
        }

        /* Aksen Abstrak di dalam tombol */
        .btn-quota-modern::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .badge-status-right {
            position: absolute;
            /* Sesuaikan top agar sejajar lurus dengan teks "SALDO CUTI" */
            top: 20px;
            /* Jarak dari kanan (sebelum tombol close) */
            right: 5px;

            background-color: #e8fadf;
            color: #28a745;
            font-size: 10px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            /* Bentuk kapsul lebih elegan */
            text-transform: uppercase;
            border: 1px solid #c3e6cb;
        }

        .filter-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
        }

        /* Warna ungu lembut untuk select box */
        .form-select-purple {
            border-color: #d6bcfa !important;
            /* Soft purple border */
            color: #4a5568;
        }

        .form-select-purple:focus {
            border-color: #9f7aea !important;
            box-shadow: 0 0 0 0.25rem rgba(159, 122, 234, 0.25) !important;
        }

        /* Tombol warna ungu estetik */
        .btn-purple {
            background-color: #805ad5 !important;
            border-color: #805ad5 !important;
            color: #ffffff !important;
            transition: all 0.2s ease-in-out;
        }

        .btn-purple:hover {
            background-color: #6b46c1 !important;
            border-color: #6b46c1 !important;
            box-shadow: 0 4px 6px -1px rgba(128, 90, 213, 0.2), 0 2px 4px -1px rgba(128, 90, 213, 0.1) !important;
        }

        .btn-purple:active {
            background-color: #553c9a !important;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <h5 class="mt-3 mb-1">IZIN</h5>
            <small class="text-muted">Informasi Pengajuan Izin</small>
        </div>

        <div class="card-body px-3 px-md-4">
            <div class="row g-3 justify-content-start text-center my-1">

                <div class="col-4 d-flex flex-column align-items-center">
                    <a href="{{ url('/izin/terlambat/' . $menu_id) }}"
                        class="btn btn-primary d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 55px; height: 55px; border-radius: 50%;">
                        <span style="font-size: 1.2rem;">
                            <i class="fa-solid fa-business-time"></i>
                        </span>
                    </a>
                    <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Terlambat</span>
                </div>

                <div class="col-4 d-flex flex-column align-items-center">
                    <a href="{{ url('/izin/pulang/' . $menu_id) }}"
                        class="btn btn-success d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 55px; height: 55px; border-radius: 50%;">
                        <span style="font-size: 1.2rem;">
                            <i class="fa-solid fa-plane-departure"></i>
                        </span>
                    </a>
                    <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Pulang</span>
                </div>

                <div class="col-4 d-flex flex-column align-items-center">
                    <a href="{{ url('/izin/keluar/' . $menu_id) }}"
                        class="btn btn-warning text-white d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 55px; height: 55px; border-radius: 50%;">
                        <span style="font-size: 1.2rem;">
                            <i class="fa-solid fa-door-open"></i>
                        </span>
                    </a>
                    <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Keluar</span>
                </div>

            </div>
            <div class="row g-3 justify-content-start text-center my-1">

                <div class="col-4 d-flex flex-column align-items-center">
                    <a href="{{ url('/izin/sakit/' . $menu_id) }}"
                        class="btn btn-secondary d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 55px; height: 55px; border-radius: 50%;">
                        <span style="font-size: 1.2rem;">
                            <i class="fa-solid fa-stethoscope"></i>
                        </span>
                    </a>
                    <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Sakit</span>
                </div>

                <div class="col-4 d-flex flex-column align-items-center">
                    <a href="{{ url('/izin/tidak_masuk/' . $menu_id) }}"
                        class="btn btn-danger text-white d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 55px; height: 55px; border-radius: 50%;">
                        <span style="font-size: 1.2rem;">
                            <i class="fa-solid fa-door-open"></i>
                        </span>
                    </a>
                    <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Tidak Masuk</span>
                </div>

                @if ($user_karyawan->shift == 'NON SHIFT')
                    <div class="col-4 d-flex flex-column align-items-center">
                        <a href="{{ url('/izin/libur/' . $menu_id) }}"
                            class="btn btn-success d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 55px; height: 55px; border-radius: 50%;">
                            <span style="font-size: 1.2rem;">
                                <i class="fa-solid fa-retweet"></i>
                            </span>
                        </a>
                        <span class="mt-2 fw-medium" style="font-size: 8.5pt; line-height: 1.2;">Tukar Libur</span>
                    </div>
                @endif



            </div>
        </div>
    </div>
    @include('users.logout_confirm')
@endsection
@section('content')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            let menu_active = '{{ $menu_id }}';
        });
    </script>
@endsection
