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
        /* =====================
           ABSENSI LIST
            ===================== */
        .absensi-list {
            border-radius: 18px;
            background: #fff;
        }

        .absensi-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .absensi-item:active {
            background-color: #d9cdfb;
        }

        .absensi-item:last-child {
            border-bottom: none;
        }

        /* Date box */
        .date-box {
            width: 44px;
            height: 44px;
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
        }

        .date-box .month {
            font-size: 10px;
        }

        /* INFO */
        .absensi-info {
            flex: 1;
        }

        #reportrange {
            border-radius: 14px;
            height: 35px;
            background: linear-gradient(135deg, #eee9f9, #ffffff);
            border: 1px dashed #d1d5db;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            cursor: pointer;

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

        .absensi-info .time {
            font-weight: 600;
            font-size: 14px;
        }

        .absensi-info .divider {
            margin: 0 6px;
            color: #9ca3af;
        }

        /* Status badge */
        .status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 600;
        }

        .status.TIDAKHADIR {
            background: #fff7ed;
            color: #ea1b0c;
        }

        .status.BELUMDIJADWALKAN {
            background: #fff7ed;
            color: #ea1b0c;
        }

        .status.TEPATWAKTU {
            background: #ecfdf5;
            color: #059669;
        }

        .status.TELAT {
            background: #fff7ed;
            color: #ea580c;
        }

        .status.IZIN {
            background: #eef2ff;
            color: #4f46e5;
        }

        .status.DATANGLEBIHAWAL {
            background: #eef2ff;
            color: #4f46e5;
        }

        .status {
            background: #eef2ff;
            color: #4f46e5;
        }

        .status.CUTI {
            background: #fdf4ff;
            color: #a21caf;
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

        .skeleton {
            background: linear-gradient(90deg,
                    #ede9fe 25%,
                    /* purple-100 */
                    #f5f3ff 37%,
                    /* purple-50  */
                    #ede9fe 63%);
            /* Warna dasar abu-abu terang */
            overflow: hidden;
            position: relative;
            border-radius: 4px;
        }

        /* Membuat elemen semu untuk efek kilauan */
        .skeleton::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg,
                    #ede9fe 25%,
                    /* purple-100 */
                    #f5f3ff 37%,
                    /* purple-50  */
                    #ede9fe 63%);
            animation: shimmerSmooth 1.5s linear infinite;
        }

        /* Mendefinisikan animasi kilauan yang bergerak ke kanan */
        @keyframes shimmerSmooth {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .attendance-page .floating-wrapper > .container,
        .attendance-page .dashboard-area > .container {
            width: calc(100% - 2rem);
            max-width: 1480px;
        }

        .attendance-page .fixed-content {
            padding: 0;
            box-shadow: none;
        }

        .attendance-panel {
            overflow: hidden;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(31, 41, 55, 0.08);
        }

        .attendance-panel .futuristic-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 24px;
            text-align: left !important;
            background: linear-gradient(135deg, #faf8ff, #fff);
        }

        .attendance-panel .futuristic-icon {
            flex: 0 0 56px;
            width: 56px;
            height: 56px;
            margin: 0;
            font-size: 23px;
        }

        .attendance-panel .futuristic-header h5 {
            margin: 0 0 4px !important;
            color: #312e81;
            font-weight: 700;
        }

        .attendance-panel .futuristic-header small {
            color: #6b7280;
        }

        .attendance-panel .card-body {
            padding: 20px 24px 24px;
        }

        .attendance-panel #reportrange {
            min-height: 48px;
            height: auto;
            border-style: solid;
            border-color: #e5e7eb;
            background: #fff;
        }

        .attendance-panel .content-absensi {
            overflow: hidden;
            border: 1px solid #eef0f4;
            border-radius: 16px;
            background: #fff;
        }

        .attendance-panel .absensi-item {
            min-width: 0;
            padding: 16px 18px;
            border-bottom: 2px solid #d6dbe5;
        }

        .attendance-panel .absensi-item:last-child {
            border-bottom: 0;
        }

        .attendance-panel .absensi-info {
            min-width: 0;
        }

        .attendance-panel .attendance-times {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eef0f4;
        }

        .attendance-panel .attendance-time {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .attendance-panel .attendance-time-label {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .attendance-panel .attendance-time-value {
            overflow-wrap: anywhere;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
        }

        .attendance-panel .status {
            flex: 0 0 auto;
            text-align: center;
        }

        @media (max-width: 768px) {
            .attendance-page .floating-wrapper > .container,
            .attendance-page .dashboard-area > .container {
                width: 100%;
                padding-right: 12px;
                padding-left: 12px;
            }

            .attendance-panel {
                border-radius: 16px;
            }

            .attendance-panel .futuristic-header {
                padding: 18px 16px;
            }

            .attendance-panel .card-body {
                padding: 14px;
            }

            .attendance-panel .absensi-item {
                flex-wrap: wrap;
                gap: 10px 12px;
                padding: 14px;
            }

            .attendance-panel .date-box {
                flex: 0 0 44px;
            }

            .attendance-panel .absensi-info {
                flex: 1 1 calc(100% - 68px);
            }

            .attendance-panel .status {
                margin-left: 56px;
            }
        }
    </style>
@endsection
@section('body_class', 'attendance-page')
@section('content_top')
    <div class="card attendance-panel">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-check-to-slot"></i>
            </div>
            <div>
                <h5 class="mt-3 mb-1">PRESENSI</h5>
                <small>Informasi Data Presensi</small>
            </div>
        </div>

        <div class="card-body">
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
            <div class="mb-3">
                <div id="reportrange"
                    class="rounded-pill px-3 py-2 py-md-2 px-md-4 d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center text-truncate">
                        <i class="fa-solid fa-calendar text-primary fs-5 me-2"></i>
                        <span id="currentMonth" class="fw-medium text-truncate" style="font-size: 0.9rem;"></span>
                    </div>

                    <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 0.8rem;"></i>

                    <input type="hidden" id="start_date" name="start_date" value="">
                    <input type="hidden" id="end_date" name="end_date" value="">
                </div>
            </div>
            <div class="content-absensi"></div>
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="detailAbsensi"
        style="height: auto; min-height: 40vh;">
        <div class="offcanvas-header pb-0 justify-content-center">
            <div style="width: 40px; height: 5px; background: #e2e8f0; border-radius: 10px;"></div>
        </div>
        <div class="offcanvas-body pt-4" id="detailContent">
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

            // DATE RANGE PICKER
            let cutiPicker;
            var user_karyawan_id = '{{ $user_karyawan->id }}';
            var start = moment().subtract(-1, 'days');
            cutiPicker = $('#daterangepicker_cuti').daterangepicker({
                drops: 'down',
                minDate: start,
                startDate: start,
                endDate: start,
                autoApply: true,
            }, function(start, end, label) {
                // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });

            $(document).on('click', '#btn_klik', function(e) {
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
            });
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
            var today = moment().format('YYYY-MM-DD');
            var month = moment().format('MM');
            var day = moment().format('D');
            var year = moment().format('YYYY');

            // console.log(month);
            $('.month').val(month);
            const monthNames = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember"
            ];


            function showSkeleton(selector) {
                $(selector).each(function() {
                    const el = $(this);

                    if (!el.data('original-content')) {
                        el.data('original-content', el.html());
                    }

                    const child = el.children().first();
                    const actualWidth = child.length ? child.width() : el.width();

                    const finalWidth = actualWidth > 0 ? actualWidth : 100; // Fallback jika 0

                    // PERUBAHAN: Ubah display menjadi "block" dan tambahkan margin-bottom
                    el.html(
                        `<span class="skeleton" style="width: ${finalWidth}px; height: 1em; display: block; margin-bottom: 8px; border-radius: 4px;"></span>`
                    );
                });
            }

            function formatDay(date) {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                });
            }

            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }
            let currentDate = new Date();


            function renderMonth() {
                const today = new Date(currentDate);
                const year = today.getFullYear();
                const month = today.getMonth(); // 0-11
                const day = today.getDate();

                let startDate, endDate;

                if (day <= 20) {
                    // Periode: 21 bulan lalu - 20 bulan ini
                    startDate = new Date(year, month - 1, 21);
                    endDate = new Date(year, month, 20);
                } else {
                    // Periode: 21 bulan ini - 20 bulan depan
                    startDate = new Date(year, month, 21);
                    endDate = new Date(year, month + 1, 20);
                }

                // Format untuk ditampilkan
                const formatDisplay = (date) => {
                    return `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
                };

                document.getElementById('currentMonth').innerText =
                    `${formatDisplay(startDate)} - ${formatDisplay(endDate)}`;

                // Format untuk dikirim ke backend
                const formatSend = (date) => {
                    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                };


                $('#reportrange').daterangepicker({
                    startDate: moment(startDate),
                    endDate: moment(endDate),
                    opens: 'center', // Penting: Pop-up akan mencoba berada di tengah
                    drops: 'down', // Selalu drop ke bawah
                    autoApply: true, // Opsional: Otomatis teraplikasi tanpa klik tombol "Terapkan" (Sangat smooth untuk HP)
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')]
                    },
                    locale: {
                        format: 'DD/MM/YYYY',
                        applyLabel: 'Pilih',
                        cancelLabel: 'Batal',
                        customRangeLabel: 'Tentukan Sendiri'
                    }
                }, cb);

                // ... (bagian AJAX tetap sama) ...
                load_data_absensi(formatSend(startDate), formatSend(endDate));
            }

            function cb(start, end) {
                $('#reportrange #currentMonth').html(start.format('D MMMM YYYY') + ' - ' + end.format(
                    'D MMMM YYYY'));
                lstart = start.format('YYYY-MM-DD');
                lend = end.format('YYYY-MM-DD');
                $('#start_date').val(lstart);
                $('#end_date').val(lend);
                load_data_absensi(lstart, lend);
            }
            renderMonth();

            function load_data_absensi(startDate, endDate) {
                var karyawan = '';
                let data_approve = '';
                $.ajax({
                    url: "{{ url('/absen/get_dataAbsensi') }}" + '/' + menu_active,
                    type: "GET",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    beforeSend: function() {
                        $('.content-absensi').empty();
                        karyawan += ` <div class="absensi-item">
                    <div class="tanggal-box">
                        <span class="day"></span>
                        <span class="month"></span>
                    </div>
                    <div class="absensi-info">
                        <div class="attendance-times">
                            <div class="attendance-time">
                                <span class="attendance-time-label">Masuk</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                            <div class="attendance-time">
                                <span class="attendance-time-label">Pulang</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                        </div>
                        <small class="text-muted"></small>
                    </div>
                </div>
                <div class="absensi-item">
                    <div class="tanggal-box">
                        <span class="day"></span>
                        <span class="month"></span>
                    </div>
                    <div class="absensi-info">
                        <div class="attendance-times">
                            <div class="attendance-time">
                                <span class="attendance-time-label">Masuk</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                            <div class="attendance-time">
                                <span class="attendance-time-label">Pulang</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                        </div>
                        <small class="text-muted"></small>
                    </div>
                </div>
                <div class="absensi-item">
                    <div class="tanggal-box">
                        <span class="day"></span>
                        <span class="month"></span>
                    </div>
                    <div class="absensi-info">
                        <div class="attendance-times">
                            <div class="attendance-time">
                                <span class="attendance-time-label">Masuk</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                            <div class="attendance-time">
                                <span class="attendance-time-label">Pulang</span>
                                <span class="attendance-time-value skeleton"></span>
                            </div>
                        </div>
                        <small class="text-muted"></small>
                    </div>
                </div>`;
                        $('.content-absensi').html(karyawan);
                        showSkeleton('.absensi-item .tanggal-box .day');
                        showSkeleton('.absensi-item .tanggal-box .month');
                        showSkeleton('.absensi-info');
                        // $('.content-absensi').empty();
                    },
                    error: function(data) {
                        // console.log(data);
                        $('.alert_approval').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseJSON.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_approval').fadeOut('slow');
                        }, 6000);
                        data_approve +=
                            `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.content-absensi', data_approve);
                    },
                    success: function(res) {
                        // console.log(res.data_absensi);
                        // continue;
                        if (res.code == 200) {
                            $('.content-absensi').empty();
                            karyawan = '';
                            res.data_absensi.forEach(function(item) {
                                const detail_masuk = JSON.stringify(item.detail_masuk);
                                const detail_pulang = JSON.stringify(item.detail_pulang);
                                // console.log(detail_masuk, detail_pulang);
                                karyawan += `
                            <div class="absensi-item" data-detailmasuk='${detail_masuk}' data-detailpulang='${detail_pulang}' data-tanggal="${item.tanggal}" data-masuk="${item.masuk}" data-pulang="${item.pulang}" data-shift="${item.shift}" data-status="${item.status}" data-type="${item.type}">
                                <div class="date-box">
                                    <span class="day">${formatDay(item.tanggal)}</span>
                                    <span class="month">${formatMonth(item.tanggal)}</span>
                                </div>
                                <div class="absensi-info">
                                    <div class="attendance-times">
                                        <div class="attendance-time">
                                            <span class="attendance-time-label">Masuk</span>
                                            <span class="attendance-time-value">${item.masuk || '-'}</span>
                                        </div>
                                        <div class="attendance-time">
                                            <span class="attendance-time-label">Pulang</span>
                                            <span class="attendance-time-value">${item.pulang || '-'}</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">${item.shift}</small>
                                </div>
                                <div class="status ${item.status.replace(/\s+/g, '')}">${item.status}</div>
                            </div>`;
                            });
                            $('.content-absensi').html(karyawan);
                        } else if (res.code == 500) {
                            $('.content-absensi').empty();
                            $('#count_absen_hadir').html('<span class="text-danger">Error!</span>');
                            $('#count_absen_izin').html('<span class="text-danger">Error!</span>');
                            $('#count_absen_cuti').html('<span class="text-danger">Error!</span>');
                            $('#count_absen_telat').html('<span class="text-danger">Error!</span>');
                            $('#absen_masuk').html('<span class="text-danger">Error!</span>');
                            $('#absen_pulang').html('<span class="text-danger">Error!</span>');
                            $('#jam_kerja').html('<span class="text-danger">Error!</span>');
                            $('#penempatan').html('<span class="text-danger">Error!</span>');
                            $('.alert_quick_panel').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                res.error + '</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert_quick_panel').fadeOut('slow');
                            }, 100000);
                        }
                    },
                })
            }

            function formatMonth(date) {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    month: 'short'
                });
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
            $(document).on('click', '.absensi-item', function() {
                // 1. Ambil data dari atribut data-
                const data = {
                    detail_masuk: $(this).data('detailmasuk'),
                    detail_pulang: $(this).data('detailpulang'),
                    tanggal: $(this).data('tanggal'),
                    masuk: $(this).data('masuk'),
                    pulang: $(this).data('pulang'),
                    shift: $(this).data('shift'),
                    status: $(this).data('status'),
                    type: $(this).data('type')
                };
                let raw_detailmasuk = $(this).data('detailmasuk');
                let raw_detailpulang = $(this).data('detailpulang');
                let allValues = Object.values(raw_detailmasuk);
                let allValues_pulang = Object.values(raw_detailpulang);
                let uniqueValues = [...new Set(allValues)];
                let uniqueValues_pulang = [...new Set(allValues_pulang)];

                // console.log(raw_detailmasuk, raw_detailpulang);

                // 3. Render ke Offcanvas
                let htmlDetail = uniqueValues.map(val => `
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 text-primary"></i>
                    <span class="text-success small">${val}</span>
                </div>
            `).join('');
                let htmlDetail_pulang = uniqueValues_pulang.map(val => `
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 text-primary"></i>
                    <span class="text-success small">${val}</span>
                </div>
            `).join('');
                // console.log(data.type);
                // 2. Isi konten Offcanvas secara dinamis
                if (data.type == 'is_calendar') {
                    const content = `
                <div class="text-center mb-3">
                    <h6 class="tex-primary mb-1">${moment(data.tanggal).format('dddd, D MMMM YYYY')}</h6>
                    <span class="status rounded-pill p-2 px-3 ${data.status.replace(/\s+/g, '')}">${data.status}</span>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="detail-card text-center p-3 rounded-4 bg-light">
                            <small class="text-muted d-block mb-1"><i class="bi bi-box-arrow-in-right"></i> Jam Masuk</small>
                            <span class="fw-bold fs-5 text-dark">${data.masuk ? data.masuk : '-'}</span>
                            <small class="text-muted d-block mb-1">${htmlDetail}</small>
                            </div>
                            </div>
                            <div class="col-6">
                            <div class="detail-card text-center p-3 rounded-4 bg-light">
                            <small class="text-muted d-block mb-1"><i class="bi bi-box-arrow-left"></i> Jam Pulang</small>
                            <span class="fw-bold fs-5 text-dark">${data.pulang ? data.pulang : '-'}</span>
                            <small class="text-muted d-block mb-1">${htmlDetail_pulang}</small>
                        </div>
                    </div>
                </div>

                <div class="p-3 row rounded-4 d-flex text-center mb-2">
                   <button class="btn btn-sm light btn-danger py-3 fw-bold">
                   <i class="fa-solid fa-download me-2"></i>
                   Unduh Pemberitahuan
                   </button>
                </div>

                <button type="button" class="btn btn-sm btn-primary w-100 rounded-pill py-3 fw-bold" data-bs-dismiss="offcanvas">
                    Tutup
                </button>`;
                    $('#detailContent').html(content);
                } else {
                    const content = `
                <div class="text-center mb-3">
                    <h6 class="tex-primary mb-1">${moment(data.tanggal).format('dddd, D MMMM YYYY')}</h6>
                    <span class="status rounded-pill p-2 px-3 ${data.status.replace(/\s+/g, '')}">${data.status}</span>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="detail-card text-center p-3 rounded-4 bg-light">
                            <small class="text-muted d-block mb-1"><i class="bi bi-box-arrow-in-right"></i> Jam Masuk</small>
                            <span class="fw-bold fs-5 text-dark">${data.masuk ? data.masuk : '-'}</span>
                            <small class="text-muted d-block mb-1">${htmlDetail}</small>
                            </div>
                            </div>
                            <div class="col-6">
                            <div class="detail-card text-center p-3 rounded-4 bg-light">
                            <small class="text-muted d-block mb-1"><i class="bi bi-box-arrow-left"></i> Jam Pulang</small>
                            <span class="fw-bold fs-5 text-dark">${data.pulang ? data.pulang : '-'}</span>
                            <small class="text-muted d-block mb-1">${htmlDetail_pulang}</small>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-4 bg-light d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge me-2 text-primary"></i>
                        <span class="text-muted">Shift Kerja</span>
                    </div>
                    <span class="fw-bold">${data.shift ? data.shift : '-'}</span>
                </div>

                <button type="button" class="btn btn-sm btn-primary w-100 rounded-pill py-3 fw-bold" data-bs-dismiss="offcanvas">
                    Tutup
                </button>`;
                    $('#detailContent').html(content);
                }

                // 3. Masukkan ke container detail dan munculkan

                // Trigger Bootstrap Offcanvas
                var myOffcanvas = document.getElementById('detailAbsensi');
                var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(myOffcanvas);
                bsOffcanvas.show();
            });
        });
    </script>
@endsection
