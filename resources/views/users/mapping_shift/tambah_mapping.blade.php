@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        @media (max-width: 768px) {
            .daterangepicker {
                position: fixed !important;
                top: 10% !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                width: 90% !important;
                margin: 0 auto !important;
                z-index: 9999 !important;
            }

            .daterangepicker .drp-calendar {
                float: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .daterangepicker .drp-buttons {
                text-align: center !important;
            }
        }

        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1051 !important;
        }

        .date-circle {
            min-width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            transition: all .2s ease;
        }

        .date-circle small {
            font-size: 12px;
            color: #666;
        }

        .date-circle.active {
            background: #0d6efd;
            color: #fff;
        }

        .date-circle.active small {
            color: #fff;
        }

        .btn-add-shift {
            width: 36px;
            height: 36px;
            border-radius: 50% !important;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .btn-add-shift:hover {
            background: #38399eff;
        }

        .user-card {
            width: 100%;
            padding: 5px;
            border: 2px solid #ded2f278;
            background-color: #ffffffff;
            border-radius: 10px;
            transition: 0.3s;
            cursor: pointer;
        }

        /* dropdown elegan */
        .shift-select {
            min-width: 110px;
            max-width: 150px;
            max-height: fit-content;
            padding: 6px 10px;
            font-size: 10px;
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            background-color: #fff;
            cursor: pointer;
        }

        .shift-select:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, .1);
        }

        .user-card:hover {
            border-color: #38399eff;
        }

        .user-checkbox {
            border-color: #673ab7ff;
            background-color: #e7e8ffff;
            box-shadow: 0 0 10px rgba(97, 13, 253, 0.42);
        }

        .user-checkbox:checked+label .user-card {
            border-color: #673ab7ff;
            background-color: #e7e8ffff;
            box-shadow: 0 0 10px rgba(97, 13, 253, 0.42);
        }

        .user-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            font-size: 13px;
        }

        /* Kartu modal */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }



        .modal-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .input-group {
            margin-bottom: 10px;
        }

        .input-group .form-control[readonly] {
            background-color: #f1f1f1;
            /* font-weight: 600;
                                                                                                                                    width: 150px; */
            border-right: none;
            text-align: center;
        }

        .input-group select,
        .input-group input:not([readonly]) {
            border-left: none;
            font-weight: 600;
        }

        .table th {
            background-color: #0d6efd !important;
            color: white !important;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .date-box {
            width: 50px;
            height: 50px;
            min-width: 50px;
            /* mencegah mengecil */
            flex-shrink: 0;
            /* penting supaya tidak tertekan flex */
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .date-box .day {
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
        }

        .date-box .month {
            font-size: 10px;
            line-height: 1;
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

        .highlight-keyword {
            background-color: #fff3cd;
            color: #856404;
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 600;
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

        .karyawan-list {
            padding-left: 16px;
            margin: 0;
            font-size: 12px;
        }

        .karyawan-list li {
            line-height: 1.5;
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



        .duration-badge {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            padding: 6px 14px;
            border-radius: 12px;
            color: var(--primary);
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 15px;
            border: 1px solid #eef2ff;
        }

        /* Container Utama */
        .swal2-popup.premium-swal {
            border-radius: 28px !important;
            /* Squircle style */
            padding: 1.5rem !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
            width: 85% !important;
            /* Pas untuk mobile */
            max-width: 400px !important;
        }

        /* Judul */
        .swal2-title.premium-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700 !important;
            color: #1a202c !important;
            font-size: 1.25rem !important;
            padding-top: 10px !important;
        }

        /* Isi Teks */
        .swal2-html-container.premium-text {
            color: #718096 !important;
            font-size: 0.95rem !important;
            line-height: 1.5 !important;
        }

        /* Tombol Konfirmasi (Utama) */
        .swal2-confirm.premium-confirm {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%) !important;
            border-radius: 14px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
            font-size: 0.9rem !important;
        }

        /* Tombol Batal/Cancel */
        .swal2-cancel.premium-cancel {
            background: #f1f5f9 !important;
            color: #64748b !important;
            border-radius: 14px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 0.9rem !important;
        }

        /* Ikon */
        .swal2-icon {
            border-width: 3px !important;
            scale: 0.8;
            /* Mengecilkan sedikit agar proporsional di mobile */
        }

        /* Animasi Masuk Smooth */
        .swal2-show {
            animation: swal-smooth-show 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }

        @keyframes swal-smooth-show {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .simple-error-wrapper {
            margin-top: 20px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }

        .simple-error-item {
            display: flex;
            align-items: center;
            padding: 12px 5px;
            border-bottom: 1px solid #f8fafc;
            /* Garis pemisah sangat tipis */
            text-align: left;
        }

        .simple-error-item:last-child {
            border-bottom: none;
        }

        /* Dot minimalis sebagai pengganti ikon besar */
        .error-dot {
            width: 6px;
            height: 6px;
            background-color: #f87171;
            border-radius: 50%;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .error-msg {
            font-size: 14px;
            color: #475569;
            line-height: 1.4;
            font-weight: 500;
        }

        /* Penyesuaian Popup agar lebih mungil */
        .swal2-popup.premium-swal {
            border-radius: 20px !important;
            padding: 2rem 1rem !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
@endsection
@section('content_top')
    <div class="card">
        {{-- Header --}}
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <h5 class="mt-3 mb-1">Tambah Jadwal</h5>
            <small class="text-muted">Tambah Jadwal Karyawan Shift</small>
        </div>
        @if (Session::has('mappingshiftsuccess'))
            <div id="alert_addmappingsuccess" class="alert alert-success light alert-lg alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                    <line x1="9" y1="9" x2="9.01" y2="9"></line>
                    <line x1="15" y1="9" x2="15.01" y2="9"></line>
                </svg>
                <strong>Success!</strong> Anda Berhasil Menyimpan Data
                <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @elseif(Session::has('mappingshiftupdatesuccess'))
            <div id="alert_statusmappingeditsuccess" class="alert alert-success light alert-lg alert-dismissible fade show">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <strong>Success!</strong> Anda Berhasil Mengedit Data Mapping
                <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="card-body">
            <div id="alert_wrapper">
            </div>
            <div class="mb-2">
                <button type="button" class="btn_lihat_jadwal btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-calendar"></i>&nbsp;
                    Lihat Jadwal
                </button>
            </div>
            <form id="form_add_mapping_shift" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu_id }}">
                <div class="row mt-1 mb-4">
                    {{-- Info Durasi --}}
                    <div class="duration-badge">
                        <i class="fa-regular fa-calendar-days me-2"></i>
                        {{ Carbon\Carbon::parse($start_date)->format('d M Y') }}
                        <span class="mx-2 opacity-50">/</span>
                        {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">1</span>
                        <h6 class="mb-0 fw-bold">Pengajuan Mapping Shift</h6>
                    </div>
                    <div class="basic-form style-1 col-lg-12 col-md-12 col-sm-12">
                        <div class="mb-3 form-input">
                            <span class="input-icon">
                                <i class="fa-solid fa-thumbtack"></i>
                            </span>
                            <input type="text" value="" id="mapping_title" name="mapping_title"
                                class="form-control" placeholder="Judul Mapping Shift (Opsional)">
                        </div>
                    </div>
                    <input type="hidden" value="{{ Carbon\Carbon::parse($start_date)->format('Y-m-d') }}"
                        name="tanggal_mulai" id="tanggal_mulai">
                    <input type="hidden" value="{{ Carbon\Carbon::parse($end_date)->format('Y-m-d') }}"
                        name="tanggal_selesai" id="tanggal_selesai">
                    <div class="text-center mt-1">
                        <h5>List Tanggal</h5>
                    </div>
                    <div id="get_karyawan" class="d-flex flex-column gap-3"></div>


                </div>
                <div class="row">
                    <input type="text" name="signature" id="signature" value="" hidden>
                    <input type="text" name="legal_number_update" id="legal_number_update" value="" hidden>
                    <input type="text" name="pengajuan_signature" id="pengajuan_signature" value="" hidden>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">2</span>
                        <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                    </div>
                    {{-- <div class="pengajuan-wrapper" style="margin-bottom: 5%; margin-top: 5%;">
                        <h6>Pengajuan HRD,</h6>
                        <div class="basic-form style-1">
                            <div class="mb-3 form-input">
                                <span class="input-icon">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </span>
                                <select class="form-control" name="approve_hrd" id="approve_hrd" required>
                                    <option value="">Pilih Approval HRD</option>

                                </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="card_pengajuan">
                    </div>
                    <div class="row g-2 d-flex align-items-stretch" id="card_approved">
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="d-block text-muted" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center ttd_user">

                                </div>
                                <input type="hidden" name="atasan_1" id="atasan_1" value="">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="user_name">USER NAME</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH HRD:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center ttd_hrd">

                                </div>
                                <input type="hidden" name="atasan_2" id="atasan_2" value="">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="user_hrd">HRD</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <button type="button" class="btn light btn-danger btn-sm btn_lihat_jadwal">
                            <i class="fa-solid fa-arrow-left"></i>&nbsp;Kembali
                        </button>

                        <button type="button" id="btnAddMappingShift" class="btn light btn-success btn-sm">
                            <i class="fa-solid fa-save"></i>&nbsp;Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" style="height: 85vh;" data-bs-keyboard="false"
        data-bs-backdrop="true" id="off-canvas-mapping_shift">

        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Daftar Karyawan</h6>
                    <small class="text-muted">Karyawan yang akan dijadwalkan </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="text-center mb-2 p-2">
            <div id="alert_add_karyawan"></div>
            <span class="fw-bold" id="tanggal_selected"></span>
            <input type="text" id="search_karyawan" class="form-control form-control-sm"
                placeholder="🔍 Cari nama / jabatan karyawan...">
        </div>
        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div class="form-karyawan">
                <form class="my-2" method="post" id="form_add_karyawan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_user" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="menu_id" value="{{ $menu_id }}">
                    <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
                    <!-- Form input -->
                    <div class="col-12">

                        <div class="mt-3 text-center">
                            <input type="text" name="tanggal" id="tanggal" class="form-control" hidden>
                            <button type="button" id="btnAddKaryawan" class="btn btn-success btn-sm">
                                <i class="fa fa-save"></i>&nbsp;Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
            <form method="POST" action="{{ route('logout_user') }}">
                @csrf
                <a onclick="event.preventDefault(); this.closest('form').submit();"
                    class="btn_klik btn btn-sm btn-danger light pwa-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    &nbsp;Logout
                </a>
                <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                    aria-label="Close">Batal</a>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        $(document).ready(function() {
            var mapping_shift = @json($mapping_shift);
            const signatureBaseUrl = "{{ asset('storage/signatures') }}";
            var menu_active = "{{ Auth::user()->menu_active }}";
            var approve_hrd = "{{ $mapping_shift ? $mapping_shift->approved_by : '' }}";
            const $btnSelected = $('#btnSelected');
            var start_date = "{{ $start_date }}";
            var end_date = "{{ $end_date }}";

            if (mapping_shift) {
                var title = "{{ $mapping_shift ? $mapping_shift->mapping_group : '' }}";
                $('#mapping_title').val(title);
                const date_input = new Date(mapping_shift.created_at);
                const date_approved = new Date(mapping_shift.approved_at);
                const tanggal_input = date_input.toISOString().slice(0, 10).split('-').reverse().join('-');
                const tanggal_approve = mapping_shift.approved_at == null ? '-' : date_approved.toISOString().slice(
                    0, 10).split('-').reverse().join('-');
                if (mapping_shift.status_approved == 1) {
                    $('.pengajuan-wrapper').attr('hidden', true);
                    $('#approve_hrd').attr('disabled', true);
                    $('#btnAddMappingShift').attr('hidden', true);
                    $('.shift-select').attr('disabled', true);
                    $('.user-checkbox').attr('disabled', true);
                    $('#btnAddKaryawan').attr('disabled', true);
                    $('#alert_wrapper').stop(true, true).hide().html(
                        '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                        '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                        '<strong>&nbsp;Data Mapping Shift Sudah Disetujui HRD</strong><button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                    ).fadeIn();
                    $('.ttd_user').stop(true, true).hide().html(
                        `${mapping_shift.pengajuan_signature ? `<img src="${signatureBaseUrl}/${mapping_shift.pengajuan_signature}" width="150px" alt="Tanda Tangan">`:`-`}`
                    ).show();
                    $('.ttd_hrd').stop(true, true).hide().html(
                        `${mapping_shift.approved_signature ? `<img src="${signatureBaseUrl}/${mapping_shift.approved_signature}" width="150px" alt="Tanda Tangan">`:`-`}`
                    ).show();
                    $('#user_name').stop(true, true).hide().html(
                        `${mapping_shift.karyawan_input.name}<br>(${tanggal_input})`).show();
                    $('#user_hrd').stop(true, true).hide().html(
                        `${mapping_shift.karyawan_approved.name}<br>(${tanggal_approve})`).show();

                } else {
                    $('.pengajuan-wrapper').removeAttr('hidden');
                    $('#approve_hrd').removeAttr('disabled');
                    $('#btnAddMappingShift').removeAttr('disabled');
                    $('.shift-select').removeAttr('disabled');
                    $('.user-checkbox').removeAttr('disabled');
                    $('.ttd_user').stop(true, true).hide().html(
                        `${mapping_shift.pengajuan_signature ? `<img src="${signatureBaseUrl}/${mapping_shift.pengajuan_signature}" width="150px" alt="Tanda Tangan">`:`-`}`
                    ).show();
                    $('.ttd_hrd').stop(true, true).hide().html(
                        `<i class="fa-solid fa-clock-rotate-left text-muted"></i>`).show();
                }
                $('#card_approved').removeClass('d-none');
                $('.card_pengajuan').stop(true, true).hide().html('').show();
                $('#clear').attr('hidden', true);
                $('#btnAddMappingShift').html('<i class="fa fa-save"></i>&nbsp;Update');

            } else {
                $('#card_approved').addClass('d-none');
                $('.card_pengajuan').stop(true, true).hide().html(`<div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-light border-0 py-2 text-center">
                            <input type="text" name="name" id="name" value="{{ $user_karyawan->name }}"
                                hidden>
                            <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                id="user_pengajuan">Diajukan Oleh: {{ $user_karyawan->name }}</small>
                        </div>
                        <div class="card-body p-2">
                            <div class="alert_signature"></div>
                            <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                <canvas id="signature-pad"
                                    style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                    hidden></canvas>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <button type="button"
                                    class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="clear">
                                    <i class="fa-solid fa-eraser me-1"></i> Hapus
                                </button>
                                <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                            </div>
                           
                        </div>
                    </div>
                    `).show();

                var canvas = document.getElementById('signature-pad');
                var signaturePad = new SignaturePad(canvas);
                $('#signature-pad').removeAttr('hidden');
                $(document).on('click', '#clear', function() {
                    signaturePad.clear();
                })

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);

                    // ambil ukuran CSS asli
                    const width = canvas.offsetWidth;
                    const height = canvas.offsetHeight;

                    // set ukuran canvas sebenarnya
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;

                    const ctx = canvas.getContext('2d');
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.scale(ratio, ratio);

                    signaturePad.clear();
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
            }

            function showSkeleton(selector) {
                const el = $(selector);
                el.html(
                    `<span class="skeleton skeleton-number" style="width:100%"></span>`
                );
            }

            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }

            function updateSelectedCount() {
                const count = $('.user-checkbox:checked').length;
                $btnSelected.html(`<i class="fa fa-plus"></i>&nbsp;Mapping ${count} Karyawan`);
            }

            // EVENT DELEGATION
            $(document).on('change', '.user-checkbox', function() {
                updateSelectedCount();
            });

            updateSelectedCount();


            // let table = new DataTable('#table_mapping_shift');
            $(".btn_lihat_jadwal").on('click', function() {
                var url = "{{ url('mapping_shift/dashboard') }}" + "/" + menu_active;
                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        window.location.href = url;
                    },
                    error: function() {

                        Swal.close();
                    }
                })

            });
            $(".btnSelected").on('click', function() {
                let count = document.querySelectorAll('.user-checkbox:checked').length;
                // console.log('ok', count);
                if (count == 0) {
                    // console.log('no selected');
                    $('#alert_wrapper').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                        '<strong>Info!</strong>&nbsp;Anda Belum Memilih Karyawan' +
                        '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                    ).fadeIn();
                    setTimeout(function() {
                        $('#alert_wrapper').fadeOut('slow');
                    }, 6000); // 3 detik
                } else {
                    var selectedUsers = [];
                    document.querySelectorAll('.user-checkbox:checked').forEach(function(checkbox) {
                        selectedUsers.push(checkbox.value);
                    });
                    // console.log('selected users:', selectedUsers);
                    var id = $(this).data('id');
                    var shift = $(this).data('shift');
                    var tanggal = $(this).data('tanggal');
                    $('#container_selected_users').empty(); // kosongkan dulu container
                    $('#karyawan_list').empty(); // kosongkan dulu container
                    $('#tanggal_update').val(tanggal);

                    $.ajax({
                        url: "{{ url('mapping_shift/getKaryawanMappingShift') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            user: selectedUsers,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                allowOutsideClick: false,
                                background: 'transparent',
                                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                                showCancelButton: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    // Swal.showLoading()
                                },
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            response.data.forEach(function(userId, index) {
                                $('#container_selected_users').append(
                                    `<input type="text" class="form-control mb-2" name="users[]" value="${userId.id}" hidden>`
                                );
                                $('#karyawan_list').append(`<li>${userId.name}</li>`)
                            });
                            $('#form_add_mapping_shift').trigger("reset");
                            const offcanvasEl = document.getElementById(
                                'off-canvas-mapping_shift');
                            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                            offcanvas.show();
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            $('#form_add_mapping_shift').trigger("reset");

                            Swal.fire({
                                title: 'Oops...',
                                text: xhr.responseJSON.message,
                                icon: 'error',
                                iconColor: '#b54a4a',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                        }
                    })
                }

            });
            $('#btnAddKaryawan').on('click', function() {
                var formData = $('#form_add_karyawan').serialize();
                // console.log(formData);
                $.ajax({
                    url: "{{ url('mapping_shift/addMKaryawan') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('success');
                            $('#alert_wrapper').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                                '<strong>Success!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 6000); // 3 detik
                            const offcanvasEl = document.getElementById(
                                'off-canvas-mapping_shift');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                        } else if (response.code == 400) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Tunggu Approval HRD.',
                                icon: 'error',
                                iconColor: '#3caa47',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },


                                timer: 6000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href =
                                    "{{ url('mapping_shift/dashboard') }}/" +
                                    menu_active;
                            });
                        } else {
                            $('#alert_add_karyawan').empty();
                            $('#alert_add_karyawan').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_add_karyawan').fadeOut('slow');
                            }, 60000);
                        }
                        loadGetDate();
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Oops...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })

            });
            $('#btnAddMappingShift').on('click', function() {
                let signature_value = null;
                // console.log(mapping_shift);
                if (mapping_shift == null) {
                    if (!signaturePad.isEmpty()) {
                        signature_value = signaturePad.toDataURL();
                    }
                } else {
                    signature_value = 'edit';
                    $('#legal_number_update').val(mapping_shift ? mapping_shift.legal_number_mapping : '');
                    $('#pengajuan_signature').val(mapping_shift ? mapping_shift.pengajuan_signature : '');
                }
                var signature = $('#signature').val(signature_value);

                var formData = $('#form_add_mapping_shift').serialize();
                // console.log(formData, signature);
                $.ajax({
                    url: "{{ url('mapping_shift/addMappingShift') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(response) {
                        // console.log(response);

                        Swal.close();
                        if (response.code == 200) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: 'Mapping shift Anda telah berhasil disimpan.',
                                icon: 'success',
                                iconColor: '#3caa47',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                            window.location.href = "{{ url('mapping_shift/dashboard') }}" +
                                "/" + menu_active;
                        } else if (response.code == 400) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Tunggu Approval HRD.',
                                icon: 'error',
                                iconColor: '#3caa47',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },


                                timer: 6000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href =
                                    "{{ url('mapping_shift/dashboard') }}/" +
                                    menu_active;
                            });
                        } else if (response.code == 401) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Isi lengkap mapping shift Senin-Minggu.',
                                icon: 'error',
                                iconColor: '#3caa47',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },


                                timer: 6000,
                                timerProgressBar: true
                            })
                        } else {

                            var errorText = '';

                            if (typeof response.message === 'object') {
                                errorText = '<div class="simple-error-wrapper">';

                                Object.values(response.message).forEach(msgArray => {
                                    const messages = Array.isArray(msgArray) ?
                                        msgArray : [msgArray];
                                    messages.forEach(msg => {
                                        errorText += `
                                <div class="simple-error-item">
                                    <span class="error-dot"></span>
                                    <span class="error-msg">${msg}</span>
                                </div>`;
                                    });
                                });

                                errorText += '</div>';
                            } else {
                                errorText = `
                            <div class="simple-error-wrapper">
                                <div class="simple-error-item justify-content-center">
                                    <span class="error-msg fw-bold text-danger">${response.message}</span>
                                </div>
                            </div>`;
                            }

                            Swal.fire({
                                title: 'Ada yang kurang',
                                html: errorText,
                                icon: 'warning', // Gunakan warning agar warna lebih soft (kuning/orange) daripada merah error
                                iconColor: '#f87171',
                                showConfirmButton: false,
                                confirmButtonText: 'Perbaiki',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    confirmButton: 'premium-confirm' // Gunakan class gradient ungu tadi
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();
                        Swal.fire({
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            });
            // Klik item tanggal
            $(document).on('click', '.date-item', function(e) {

                // kalau klik tombol +, stop di sini
                if ($(e.target).closest('.btn-add-shift').length) return;
                const offcanvasEl = document.getElementById('off-canvas-mapping_shift');
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
                $('.date-item').removeClass('active');
                $(this).addClass('active');

                const date = $(this).data('date');
                let formatdate = new Date(date);
                formatdate = formatdate.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                // console.log('Tanggal aktif:', date);
                $('#tanggal').val(date);
                $('#tanggal_selected').text(formatdate);
                loadGetPApproval(date);

            });
            $(document).on('keyup', '#search_karyawan', function() {
                let keyword = $(this).val().toLowerCase();

                $('.user-item').each(function() {
                    let searchText = $(this).data('search');

                    let nameEl = $(this).find('strong')[0]; // nama karyawan
                    let jabatanEls = $(this).find('small'); // jabatan

                    if (searchText.includes(keyword)) {
                        $(this).show();

                        // highlight nama
                        if (nameEl) highlightText(nameEl, keyword);

                        // highlight jabatan
                        jabatanEls.each(function() {
                            highlightText(this, keyword);
                        });

                    } else {
                        $(this).hide();
                    }

                    // reset highlight jika kosong
                    if (keyword === '') {
                        if (nameEl) nameEl.innerHTML = nameEl.textContent;
                        jabatanEls.each(function() {
                            this.innerHTML = this.textContent;
                        });
                    }
                });
            });

            function highlightText(element, keyword) {
                let originalText = element.textContent;

                if (!keyword) {
                    element.innerHTML = originalText;
                    return;
                }

                const regex = new RegExp(`(${keyword})`, 'gi');
                element.innerHTML = originalText.replace(
                    regex,
                    `<span class="highlight-keyword">$1</span>`
                );
            }
            loadGetDate();

            function loadGetDate() {
                $.ajax({
                    url: "{{ url('mapping_shift/getDate') }}" + "/" + menu_active,
                    method: "GET",
                    data: {
                        start_date: start_date,
                        end_date: end_date
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showSkeleton('#get_karyawan');
                        showSkeleton('#approve_hrd');

                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            var karyawan = '';
                            var option = '<option value="">Pilih Approval...</option>';
                            if (response.hrd != null) {

                                option += '<option value="' + response.hrd.karyawan.id + '" ' + (
                                        response.hrd.karyawan.id == approve_hrd ? 'selected' : '') +
                                    '>' + response.hrd.karyawan.name + '</option>';
                            }
                            if (response.data.length > 0) {

                                response.data.forEach((head, index) => {
                                    var shift = head.mapping_shift.length + ' Karyawan';
                                    // console.log(head.date);
                                    let karyawanList = '';

                                    if (head.mapping_shift.length > 0) {

                                        let maxShow = 3;
                                        let total = head.mapping_shift.length;

                                        let visible = head.mapping_shift.slice(0, maxShow);

                                        karyawanList = visible.map(ms => `
                                        <span class="badge light badge-primary me-1 mb-1">
                                            ${ms.nama_karyawan}
                                        </span>
                                    `).join('');

                                        if (total > maxShow) {
                                            karyawanList += `
                                            <span class="badge light badge-secondary">
                                                +${total - maxShow} lainnya
                                            </span>
                                        `;
                                        }

                                    } else {

                                        karyawanList =
                                            `<small class="text-muted">Belum ada karyawan yang di-mapping</small>`;
                                    }
                                    karyawan += `  <div class="user-card date-item ${index === 0 ? 'active' : ''}"
                                data-date="${head.date}">

                                <div class="d-flex align-items-center justify-content-between">

                                <div class="d-flex align-items-center gap-3">
                                 <div class="date-box">
                                    <span class="day">${head.header_date}</span>
                                    <span class="month">${head.header_day}</span>
                                </div>
                                <div>
                                    <div class="fw-semibold">
                                        ${shift}
                                    </div>
                                   <div class="karyawan-wrapper">
                                        ${karyawanList}
                                    </div>
                                </div>
                                </div>                 
                                </div>
                                </div>
                                `;
                                });
                            } else {
                                karyawan =
                                    '<div class="col-12 mb-3"><p class="text-center">Tidak ada Tanggal</p></div>';
                            }
                            hideSkeleton('#get_karyawan', karyawan);
                            hideSkeleton('#approve_hrd', option);
                            // $('#alert_wrapper').empty();
                            // $('#alert_add_karyawan').empty();
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.fire({
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            }


            function loadGetPApproval(date) {
                $.ajax({
                    url: "{{ url('mapping_shift/getApproval') }}/" + menu_active,
                    method: "GET",
                    data: {
                        date: date,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showSkeleton('#list_karyawan');
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.close();
                        if (response.code == 200) {
                            var karyawan = '';
                            let mappingShiftMap = {};
                            let mappingShiftId = {};
                            if (response.user_shift.length > 0) {
                                response.mapping_shift.forEach(ms => {
                                    mappingShiftMap[ms.karyawan_id] = ms.karyawan_id;
                                    mappingShiftId[ms.karyawan_id] = ms.shift_id;
                                });
                                // console.log('Mapping Shift Map:', mappingShiftId);
                                response.user_shift.forEach(kar => {
                                    const userId = kar.id;

                                    // cek apakah user sudah ada di mapping
                                    const selectedkaryaawan = mappingShiftMap[userId] ?? '';
                                    const selectedShift = mappingShiftId[userId] ?? '';
                                    // console.log(selectedShift + 'User ' + userId);
                                    const isChecked = selectedkaryaawan !== '' ? 'checked' : '';
                                    const isSelected = selectedShift !== '' ? 'selected' : '';
                                    let shiftOptions = `<option value="">Pilih Shift</option>`;
                                    var karyawan_jabatan = '';
                                    kar.karyawan_jabatan.forEach(jab => {
                                        karyawan_jabatan +=
                                            `<small>${jab.jabatan.nama_jabatan}</small><br>`;
                                    })
                                    var list_jabatan = karyawan_jabatan
                                    response.master_shift.forEach(s => {
                                        shiftOptions +=
                                            `<option value="${s.id}" ${s.id == selectedShift ? 'selected' : ''}> ${s.nama_shift} (${s.jam_masuk} - ${s.jam_keluar})</option>`;
                                    });
                                    karyawan += `<div class="col-12 mb-1 user-item" data-search="${kar.name.toLowerCase()} ${karyawan_jabatan.toLowerCase()}"><input type="checkbox" id="user_${kar.id}" class="user-checkbox d-none" name="selected_users[]" value="${kar.id}" ${isChecked}>
                                    <label for="user_${kar.id}" class="w-100">
                                        <div class="user-card w-100 p-1 d-flex align-items-center">
                                            <div class="me-3">
                                            <img src="${ kar.foto_karyawan  ? 'https://hrd.sumberpangan.store/storage/foto_karyawan/' + kar.foto_karyawan: '/admin/assets/img/avatars/1.png'}"
                                    class="user-image"
                                    alt="/">
                                    </div>
                                    <div class="user-info flex-grow-1">
                                    <strong>${kar.name}</strong><br>
                                    ${list_jabatan}
                                    <span class="badge bg-success mt-1" style="padding: 3px !important;"><i class="fa fa-check"></i> Aktif</span>
                                    </div>
                                       <div class="ms-3">
                                        <div class="basic-form style-1"> <select class="form-select shift-select" name="shift[${kar.id}]"> ${shiftOptions} </select> </div> </div>
                                    </div>
                                    </label>
                                    </div>`;
                                });
                            } else {
                                $('#btnAddMappingShift').prop('disabled', true);
                                karyawan =
                                    '<div class="col-12 mb-3"><p class="text-center">Tidak ada Karyawan</p></div>';
                            }
                            $('#list_karyawan').html(karyawan);
                        } else {
                            $('#alert_wrapper').empty();
                            $('#alert_wrapper').append(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            );
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 50000);
                            Swal.fire({
                                title: 'Oops...',
                                text: response.message,
                                icon: 'error',
                                iconColor: '#b54a4a',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                            karyawan =
                                '<div class="card col-12 mb-3 bg-danger"><span class="text-center text-white">' +
                                response.message + '</span></div>';
                            $('#btnAddKaryawan').attr('hidden', true);
                            hideSkeleton('#list_karyawan', karyawan);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();
                        Swal.fire({
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            }
            window.onbeforeunload = function() {
                Swal.fire({
                    allowOutsideClick: false,
                    background: 'transparent',
                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                    showCancelButton: false,
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        // Swal.showLoading()
                    },
                });
            };
        });
    </script>
@endsection
