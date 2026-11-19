@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
    <style>
        /* tinggi dropdown menyesuaikan layar */
        .select2-results__options {
            max-height: 45vh;
            /* cocok untuk mobile */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* SELECT2 WRAPPER */
        .form-input .select2-container {
            width: 100% !important;
        }

        /* SELECTION BOX */
        .form-input .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
            display: flex;
            align-items: center;
        }

        /* TEXT AREA (INI KUNCI 🔥) */
        .form-input .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 42px !important;
            /* ruang icon */
            padding-right: 32px !important;
            /* ruang arrow */
            line-height: 1.2;
        }

        /* scroll lebih halus */
        .select2-results__option {
            padding: 10px 14px;
            font-size: 14px;
        }

        /* highlight option */
        .select2-results__option--highlighted {
            background: linear-gradient(135deg, #6f4fd8, #8b6df0);
            color: #fff;
        }

        .select2-dropdown {
            border-radius: 12px;
            box-shadow: 0 10px 30px #7e7e7e;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 10px;
        }

        .form-input {
            position: relative;
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

        .profile-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            border-bottom: none;
        }

        .profile-tabs .nav-link {
            border: none;
            border-radius: 14px;
            padding: 8px 11px;
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
            font-size: 13px;
        }

        .profile-tabs .nav-link span {
            font-size: 12px;
        }

        .kbw-signature {
            width: fit-content;
            height: 100%;
        }

        #sig canvas {
            margin-top: 5px;
            width: 100%;
            height: auto;
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

        .coworker-card {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-0 {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-1 {
            /* Gradient oranye muda lembut ke putih */
            background: linear-gradient(135deg, #fff3e0, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan dengan tone warna oranye yang halus */
            box-shadow: 0 6px 16px rgba(253, 126, 20, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-2 {
            /* Gradient hijau lime/mint terang yang segar ke putih */
            background: linear-gradient(135deg, #e8fced, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan dengan tone hijau terang yang hidup dan bersih */
            box-shadow: 0 6px 18px rgba(40, 199, 111, 0.2);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-5 {
            /* Gradient merah terang benderang (semi-vibrant) ke putih */
            background: linear-gradient(135deg, #ffdde1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan merah terang yang lebih tegas dan hidup */
            box-shadow: 0 6px 18px rgba(255, 0, 43, 0.18);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .icon-box {
            width: 36px;
            height: 36px;
            min-width: 36px;
            /* Kunci lebar minimal */
            min-height: 36px;
            /* Kunci tinggi minimal */
            flex-shrink: 0;
            /* WAJIB: Mencegah icon menjadi lonjong/gepeng */

            border-radius: 50%;
            background: rgba(111, 66, 193, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6f42c1;
            font-size: 14px;

            /* Tambahan agar lebih manis */
            transition: all 0.3s ease;
        }

        .coworker-card-history {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
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

        .daterangepicker.ltr.show-calendar {
            top: 0px !important;
        }

        CSS

        /* Container Tab Utama */
        .nav-custom-tabs {
            border-bottom: 2px solid #f1f1f4;
            gap: 0.85rem;
            /* Jarak antar tab diperkecil dari 1.5rem */
        }

        /* Styling Tombol Tab - Font lebih kecil & padding disesuaikan */
        .nav-custom-tabs .nav-item .nav-link {
            color: #7e8299;
            background: none;
            border: none;
            padding: 0.5rem 0.4rem;
            /* Padding atas-bawah diperkecil agar lebih ramping */
            font-weight: 600;
            font-size: 0.85rem;
            /* Ukuran font diturunkan dari 0.95rem */
            transition: color 0.2s ease, border-color 0.2s ease;
            position: relative;
            letter-spacing: 0.3px;
            /* Memberikan sedikit ruang baca karena font mengecil */
        }

        /* Efek Hover */
        .nav-custom-tabs .nav-item .nav-link:hover {
            color: #3f4254;
        }

        /* State Tab Aktif */
        .nav-custom-tabs .nav-item .nav-link.active {
            color: #0d6efd;
            /* Silakan ganti dengan warna tema Anda */
            background: none;
        }

        /* Garis bawah aktif - Ketebalan disesuaikan dengan font yang lebih kecil */
        .nav-custom-tabs .nav-item .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            /* Diturunkan dari 3px agar terlihat lebih presisi dan tipis */
            background-color: #0d6efd;
            border-radius: 2px 2px 0 0;
        }

        /* Ikon di dalam Tab - Dibuat lebih kecil proporsional */
        .nav-custom-tabs .nav-link i {
            font-size: 0.95rem;
            /* Diperkecil agar seimbang dengan teks */
            vertical-align: middle;
        }

        /* Card Wrapper untuk Konten di Bawahnya */
        .card-custom-body {
            background: #ffffff;
            border: 1px solid #f1f1f4;
            border-radius: 0.65rem;
            padding: 1.5rem;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.02);
            /* Shadow super halus */
        }

        .cuti-legend {
            font-size: clamp(11px, 1.6vw, 14px);
        }

        .cuti-legend .dot {
            width: clamp(8px, 1.5vw, 10px);
            height: clamp(8px, 1.5vw, 10px);
            border-radius: 50%;
        }

        .cuti-legend p {
            margin: 0;
            line-height: 1.3;
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
                <i class="fa-solid fa-check-to-slot"></i>
            </div>
            <h5 class="mt-3 mb-1">APPROVAL TIDAK MASUK</h5>
            <small class="text-muted">Informasi Approval Pengajuan Izin Tidak Masuk</small>
        </div>

        <div class="alert_approval"></div>
        @if (session('approve_sukses'))
            <div class="alert alert-success alert-dismissible fade show alert_session_approval">
                {{ session('approve_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('approve_gagal'))
            <div class="alert alert-danger alert-dismissible fade show alert_session_approval">
                {{ session('approve_gagal') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card-body px-3 px-md-4">
            <div class="card filter-card mb-4 rounded-3 shadow-sm">
                <div class="card-body p-3">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label for="filterBulan" class="form-label small fw-semibold text-secondary mb-1">Bulan</label>
                            <select class="form-select form-select-sm form-select-purple py-2" id="filterBulan">
                                <option value="">-- Pilih Bulan --</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="filterTahun" class="form-label small fw-semibold text-secondary mb-1">Tahun</label>
                            <select class="form-select form-select-sm form-select-purple py-2" id="filterTahun">
                                <option value="">-- Pilih Tahun --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="button" id="btnSearch"
                                class="btn btn-sm btn-purple w-100 py-2 fw-medium rounded-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-magnifying-glass small"></i>
                                <span>Cari Data Periode</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>



            <ul class="nav nav-custom-tabs" id="cutiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cuti-pengajuan" type="button"
                        role="tab">
                        <i class="fa-solid fa-file-signature me-2"></i>
                        <span>Pengajuan</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#histori-cuti" type="button"
                        role="tab">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>
                        <span>Riwayat Tidak Masuk</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-4" id="cutiTabContent">

                <div class="tab-pane fade show active" id="cuti-pengajuan" role="tabpanel">
                    <div class="content-cuti">
                        <p class="text-muted mb-0">Konten form pengajuan cuti akan tampil di sini...</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="histori-cuti" role="tabpanel">
                    <div class="row cuti-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-warning me-2"></span>
                            <p>Approve&nbsp;Atasan</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-danger me-2"></span>
                            <p>Ditolak</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-2"></span>
                            <p>Approve&nbsp;Atasan&nbsp;Tertinggi</p>
                        </div>
                    </div>
                    <div class="riwayat_tidak_masuk">
                        <p class="text-muted mb-0">Belum ada riwayat pengajuan cuti.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('users.logout_confirm')
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            let menu_active = '{{ $menu_id }}';
            var session_sukses = "{{ session('approve_sukses') }}";
            var session_gagal = "{{ session('approve_gagal') }}";
            // SELECT 2 
            if (session_sukses) {
                setTimeout(function() {
                    $('.alert_session_approval').fadeOut('slow');
                }, 6000);
            } else if (session_gagal) {
                setTimeout(function() {
                    $('.alert_session_approval').fadeOut('slow');
                }, 6000);
            }

            // Filter Bulan dan tahun
            const $filterBulan = $("#filterBulan");
            const $filterTahun = $("#filterTahun");

            // 1. Dapatkan info Bulan dan Tahun saat ini secara realtime
            const dateSekarang = new Date();
            const tahunSekarang = dateSekarang.getFullYear();
            const bulanSekarang = String(dateSekarang.getMonth() + 1).padStart(2, '0'); // Hasilnya "01" - "12"

            // 2. Isi data dropdown Tahun (mengambil rentang 3 tahun ke belakang)
            for (let i = tahunSekarang; i >= 2024; i--) {
                $filterTahun.append(`<option value="${i}">${i}</option>`);
            }

            // 3. Otomatis pilih opsi (selected) sesuai bulan & tahun aktif saat ini
            $filterBulan.val(bulanSekarang);
            $filterTahun.val(tahunSekarang);
            // 4. Fungsi Utama pengkondisian filter data


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

            function muatData() {
                const bulanTerpilih = $filterBulan.val() || bulanSekarang;
                const tahunTerpilih = $filterTahun.val() || tahunSekarang;
                // --- LOGIKA MENCARI TANGGAL AKHIR DINAMIS ---
                // Konversi string bulan ke integer (basis 10)
                const intBulan = parseInt(bulanTerpilih, 10);
                const intTahun = parseInt(tahunTerpilih, 10);

                // Cari tahu tanggal terakhir di bulan & tahun tersebut (Otomatis deteksi Kabisat)
                const tglTerakhir = new Date(intTahun, intBulan, 0).getDate();

                // Format Tanggal Awal dan Akhir (Hasil: "01-01" s/d "31-01")
                const tanggalMulai = `${tahunTerpilih}-${bulanTerpilih}-01`;
                const tanggalSelesai = `${tahunTerpilih}-${bulanTerpilih}-${String(tglTerakhir).padStart(2, '0')}`;

                let awal = tanggalMulai;
                let akhir = tanggalSelesai;

                // Sekarang kita bisa console.log range tanggalnya!
                // console.log(`Periode: ${awal} s/d ${tanggalSelesai} (Tahun ${akhir})`);



                load_data_cuti(awal, akhir)
                load_riwayat_tidak_masuk(awal, akhir)

            }
            // end Filter bulan dan tahun
            $("#btnSearch").on("click", function() {
                const $btn = $(this);
                const originalText = $btn.html();

                // Beri efek loading spinner estetik agar user tahu data sedang di-refresh
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...'
                ).prop('disabled', true);

                // Eksekusi refresh data
                muatData();

                // Kembalikan tombol ke kondisi normal setelah AJAX diproses (simulasi jeda 500ms)
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 500);
            });

            muatData()


            function load_data_approval() {
                $.ajax({
                    url: "{{ url('/cuti/get_approval') }}" + '/' + menu_active,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('#atasan1');
                        showSkeleton('#atasan2');
                    },
                    error: function(data) {
                        $('.alert-approval').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseText + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-approval').fadeOut('slow');
                        }, 60000);
                    },
                    success: function(data) {
                        $('#atasan1').empty();
                        $('#atasan2').empty();
                        // console.log(data);
                        let atasan1 = '';
                        let atasan2 = '';
                        let id_atasan1 = '';
                        let id_atasan2 = '';
                        if (data.code == 200) {
                            atasan1 += data.atasan1 ? data.atasan1.karyawan ? data.atasan1.karyawan
                                .name : '-' : '-';
                            atasan2 += data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan
                                .name : '-' : '-';
                            id_atasan1 += data.atasan1 ? data.atasan1.karyawan ? data.atasan1.karyawan
                                .id : '' : '';
                            id_atasan2 += data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan
                                .id : '' : '';
                        } else {
                            atasan1 += '-';
                            atasan2 += '-';
                            id_atasan1 += '';
                            id_atasan2 += '';
                        }
                        $('#atasan_1').val(id_atasan1);
                        $('#atasan_2').val(id_atasan2);
                        hideSkeleton('#atasan1', atasan1);
                        hideSkeleton('#atasan2', atasan2);
                    }
                })
            }

            function load_data_karyawan_pengganti() {
                $.ajax({
                    url: "{{ url('/cuti/get_karyawan_pengganti') }}" + '/' + user_karyawan_id,
                    type: "GET",
                    // beforeSend: function() {
                    //     showSkeleton('.karyawan_pengganti');
                    // },
                    error: function(data) {
                        $('.alert-pengganti').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseText + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-pengganti').fadeOut('slow');
                        }, 6000);
                    },
                    success: function(data) {
                        // console.log(data);
                        let option = '<option value="">Pilih Karyawan Pengganti...</option>';
                        let select_karyawan =
                            ' <span class="input-icon"><i class="fa-solid fa-user"></i></span>';
                        if (data.code == 200) {
                            if (data.karyawan_pengganti.length == 0) {
                                $('.alert-pengganti').stop(true, true).hide().html(
                                    '<div class="alert alert-warning light alert-lg alert-dismissible fade show">Tidak ada karyawan pengganti tersedia</div>'
                                ).fadeIn(100);
                                setTimeout(function() {
                                    $('.alert-pengganti').fadeOut('slow');
                                }, 60000);
                            } else {
                                data.karyawan_pengganti.forEach((element, index) => {
                                    option +=
                                        `<option value="${element.karyawan?.id ?? '-'}">${element.karyawan?.name ?? '-'}</option>`;
                                });
                            }
                        } else {
                            $('.alert-pengganti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert-pengganti').fadeOut('slow');
                            }, 60000);
                        }
                        // hideSkeleton('.karyawan_pengganti', option);
                        $('#karyawan_pengganti').html(option).trigger('change');
                    }
                })
            }

            function load_data_cuti(awal, akhir) {
                let data_approve = '';
                $.ajax({
                    url: "{{ url('/izin/get_tidak_masuk_approval') }}",
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.content-cuti');
                    },
                    error: function(data) {
                        console.log('asoy', data);
                        $('.alert_approval').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseJSON.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_approval').fadeOut('slow');
                        }, 6000);
                        data_approve +=
                            `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.content-cuti', data_approve);
                    },
                    success: function(data) {
                        // console.log(data);
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                data_approve += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data cuti</p>
                                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    element.nama_cuti = element.nama_cuti
                                    data_approve += `<a class="btn_klik" href="{{ url('/izin/approve_tidak_masuk_detail') }}/${element.id}">
                                <div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">IZIN TIDAK MASUK</h6>
                                            <div class="text-muted small">
                                                <i class="fa fa-user"></i><span style="font-size: 9pt;" class="ms-2 price-item">
                                                ${element.nama_karyawan}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fa fa-calendar"></i><span style="font-size: 9pt;" class="ms-2 price-item">
                                                ${element.tanggal_tidak_masuk}
                                            </div>
                                        </div>
                                        {{-- ICON --}}
                                        <div class="icon-box">
                                         <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                    </div></a>`;
                                });

                            }
                        } else {
                            $('.alert_approval').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            data_approve +=
                                `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                            setTimeout(function() {
                                $('.alert_approval').fadeOut('slow');
                            }, 6000);
                        }
                        hideSkeleton('.content-cuti', data_approve);
                    }
                })
            }

            function load_riwayat_tidak_masuk(awal, akhir) {
                // console.log('load_data_cuti jalan');
                let data_approve = '';
                $.ajax({
                    url: "{{ url('/izin/get_tidak_masuk_riwayat_approval') }}",
                    type: "GET",
                    data: {
                        awal: awal,
                        akhir: akhir,
                    },
                    beforeSend: function() {
                        showSkeleton('.riwayat_tidak_masuk');
                    },
                    error: function(data) {
                        console.log('asoy', data);
                        $('.alert_approval').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseJSON.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_approval').fadeOut('slow');
                        }, 6000);
                        data_approve +=
                            `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.riwayat_tidak_masuk', data_approve);
                    },
                    success: function(data) {
                        // console.log(data);
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                data_approve += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data cuti</p>
                                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    data_approve += `<a class="btn_klik" href="{{ url('/izin/approve_tidak_masuk_detail') }}/${element.id}">
                                <div class="coworker-card-${element.status_tidak_masuk} d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">Izin Tidak Masuk</h6>
                                            <span class="text-muted small">${element.nama_karyawan}</span>
                                            <div class="text-muted small">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${element.tanggal_tidak_masuk}</span>
                                               </div>
                                            </div>
                                        </div>
                                    </div></a>`;
                                });

                            }
                        } else {
                            $('.alert_approval').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            data_approve +=
                                `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                            setTimeout(function() {
                                $('.alert_approval').fadeOut('slow');
                            }, 6000);
                        }
                        hideSkeleton('.riwayat_tidak_masuk', data_approve);
                    }
                })
            }

            function formatLabel(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();

                return `${day}-${month}-${year}`;
            }

            function formatLabel2(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                const hour = String(d.getHours()).padStart(2, '0');
                const minute = String(d.getMinutes()).padStart(2, '0');

                return `${day}-${month}-${year} ${hour}:${minute}`;
            }

            $('#month').on('change', function() {
                filter_month = $(this).val();
                // console.log(filter_month);
                $('.content-history').empty();
                load_data_history(filter_month);


            })
        });
    </script>
@endsection
