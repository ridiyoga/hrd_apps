@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/home_user.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .info-card {

            border-radius: 18px;
            padding: 14px 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* LEFT & RIGHT */
        .info-left,
        .info-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ICON BADGE */
        .icon-badge {
            width: 25px;
            height: 25px;
            border-radius: 12px;
            background: rgba(111, 79, 216, 0.12);
            color: #6f4fd8;

            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .icon-badge.clock {
            background: rgba(255, 193, 7, 0.15);
            color: #f1a900;
        }

        /* TEXT */
        .info-text .label {
            font-size: 11px;
            display: block;
        }

        .info-text .value {
            font-size: 0.68rem;
            font-weight: 700 !important;
            line-height: 1.2;
        }

        .attendance-card {
            border-radius: 22px;
            padding: 16px;
        }

        .info-row {
            font-size: 13px;
            font-weight: 500;
        }



        .btn-absen {
            width: 100%;
            border-radius: 16px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            background: #f8f9fa;
        }

        .btn-absen.masuk {
            /* background: #e9f9f0; */
            color: #1b8a5a;
        }

        .btn-absen.pulang {
            /* background: #fdeaea; */
            color: #c0392b;
        }

        .btn-absen i {
            font-size: 22px;
        }

        .section-title.accent-inline span {
            display: inline-block;
            padding: 6px 16px;
            font-size: 14px;
            font-weight: 600;
            background: linear-gradient(90deg,
                    rgba(111, 79, 216, 0.35),
                    rgba(111, 79, 216, 0.15));
            border-radius: 0 999px 999px 0;
        }

        /* SWIPER FIX WIDTH */
        .approvalSwiper {
            padding-left: 4px;
        }

        .approvalSwiper .swiper-slide {
            width: 80% !important;
            /* 🔥 tidak full */
        }

        /* APPROVAL PANEL */
        .approval-card {
            border-radius: 20px;
            padding: 14px;
            background: linear-gradient(145deg, #ffffff, #f8f9fc);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
        }

        .approval-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .approval-header strong {
            font-size: 15px;
        }

        .approval-header span {
            font-size: 11px;
            background: rgba(111, 79, 216, 0.1);
            color: #6f4fd8;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .approval-body {
            font-size: 12px;
            margin-bottom: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .approval-body i {
            margin-right: 6px;
            color: #6f4fd8;
        }

        .approval-action {
            display: flex;
            gap: 10px;
        }

        .btn-approve,
        .btn-reject {
            flex: 1;
            border: none;
            padding: 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-approve {
            background: linear-gradient(135deg, #40c057, #2b8a3e);
            color: white;
        }

        .btn-reject {
            background: linear-gradient(135deg, #fa5252, #c92a2a);
            color: white;
        }

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
            border-bottom: 2px solid #d6dbe5;
        }

        .absensi-item:active {
            background-color: #d9cdfb;
        }

        .absensi-item:last-child {
            border-bottom: none;
        }

        .absensi-list .absensi-info {
            flex: 1;
            min-width: 0;
        }

        .absensi-list .attendance-times {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eef0f4;
        }

        .absensi-list .attendance-time {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .absensi-list .attendance-time-label {
            color: #6b7280;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .absensi-list .attendance-time-value {
            overflow-wrap: anywhere;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
        }

        .absensi-list .attendance-shift {
            display: block;
            margin-top: 7px;
        }

        .absensi-list .status {
            flex: 0 0 auto;
            text-align: center;
        }

        @media (max-width: 480px) {
            .absensi-list .absensi-item {
                align-items: flex-start;
                gap: 10px;
                padding: 12px;
            }

            .absensi-list .attendance-times {
                gap: 8px;
            }

            .absensi-list .attendance-time-value {
                font-size: 13px;
            }
        }
    </style>
@endsection
@section('alert')
    <div class="alert_quick_panel"></div>
@endsection
@section('content_top')
    <div class="info-card">
        <div class="info-left">
            <div class="icon-badge">
                <i class="fa-regular fa-building"></i>
            </div>
            <div class="info-text">
                <span class="label">Penempatan</span>
                <strong class="value" id="penempatan">

                </strong>
            </div>
        </div>

        <div class="info-right">
            <div class="icon-badge clock">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="info-text text-end">
                <span class="label">Jam Kerja</span>
                <strong class="value" id="jam_kerja">

                </strong>
            </div>
        </div>
    </div>
@endsection
@section('absensi')
    <!-- ABSENSI -->
    <div class="row g-3">
        <div class="col-6">
            <button class="btn-absen masuk">
                <i class="fa-solid fa-right-to-bracket"></i>
                <div>
                    <strong>Masuk</strong>
                    <br>
                    <strong id="absen_masuk"></strong>
                </div>
            </button>
        </div>

        <div class="col-6">
            <button class="btn-absen pulang">
                <i class="fa-solid fa-right-from-bracket"></i>
                <div>
                    <strong>Pulang</strong>
                    <br>
                    <strong id="absen_pulang"></strong>
                </div>
            </button>
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
    <!-- {{ Auth::user()->id }} -->
    <!-- Features -->
    @include('sweetalert::alert')
    <!-- <div class="features-box">

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div> -->
    <!-- Categorie -->
    <div class="categorie-section" style="margin-bottom: 5%; padding: 0%;">
        <h6 class="section-title accent-inline">
            <span>Menu Utama</span>
        </h6>
        <div class="mini-menu-grid">
            @php
                // Palet warna yang lebih soft dan modern
                $color_main = [
                    'icon-soft-purple',
                    'icon-soft-blue',
                    'icon-soft-green',
                    'icon-soft-red',
                    'icon-soft-yellow',
                    'icon-soft-skyblue',
                    'icon-soft-pink',
                ];
            @endphp

            @foreach ($all_menu_main as $index => $main)
                @if ($main->Menu != null)
                    @php
                        // Memilih warna berdasarkan urutan agar distribusi warna merata
                        $selectedColor = $color_main[$index % count($color_main)];
                    @endphp

                    <a href="{{ url($main->Menu->url . '/' . $main->Menu->id) }}" class="mini-menu-item btn_klik">
                        <div class="mini-icon {{ $selectedColor }}">
                            @php($menuIcon = trim($main->Menu->icon ?? ''))
                            @if (str_starts_with($menuIcon, '<'))
                                {!! $menuIcon !!}
                            @else
                                <i class="{{ $menuIcon }}"></i>
                            @endif
                        </div>
                        <span class="mini-label">{!! $main->Menu->name !!}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    <!-- Categorie End -->

    <div class="approval-panel mt-2">

    </div>
    <!-- Features End -->
    <div class="categorie-section mt-3">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="month-nav mx-auto d-flex align-items-center justify-content-between">
                    <button id="prevMonth" class="btn-nav">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <span id="currentMonth" class="month-text"></span>

                    <button id="nextMonth" class="btn-nav">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="col-6">
                <div class="card stat-card hadir">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="stat-icon">
                            <i class="fa-solid fa-user-check"></i>
                        </div>

                        <div class="stat-content text-end">
                            <h6 class="stat-title">HADIR</h6>
                            <h3 id="count_absen_hadir" class="stat-value">
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card stat-card izin">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="stat-icon">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>

                        <div class="stat-content text-end">
                            <h6 class="stat-title">SAKIT</h6>
                            <h3 id="count_absen_izin" class="stat-value">
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card stat-card cuti">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="stat-icon">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>

                        <div class="stat-content text-end">
                            <h6 class="stat-title">CUTI</h6>
                            <h3 id="count_absen_cuti" class="stat-value">
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card stat-card telat">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="stat-icon">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>

                        <div class="stat-content text-end">
                            <h6 class="stat-title">TELAT</h6>
                            <h3 id="count_absen_telat" class="stat-value">
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorie -->
    <div class="categorie-section">
        <h6 class="section-title accent-inline">
            <span>1 Minggu Terakhir</span>
        </h6>
        <div class="card p-1 absensi-list">

        </div>
    </div>


    <!-- Categorie End -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        $(document).ready(function() {
            // Swal.close();
            // load_data();

            const message = localStorage.getItem('success_message');
            // console.log(message);
            if (message) {
                $('.alert_quick_panel').stop(true, true).hide().html(
                    '<div class="alert alert-success light alert-lg alert-dismissible fade show">' + message +
                    '</div>').fadeIn(100);

                setTimeout(function() {
                    $('.alert_quick_panel').fadeOut('slow');
                    localStorage.removeItem('success_message');
                }, 8000);
            }

            let currentDate = new Date();

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

                loadAbsensiData(formatSend(startDate), formatSend(endDate));
            }

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

            function formatMonth(date) {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    month: 'short'
                });
            }
            var swiper = new Swiper(".approvalSwiper", {
                slidesPerView: "auto",
                spaceBetween: 14,
                grabCursor: true,
                freeMode: true,
            });
            loadDataApproval();

            function formatDate(dateString) {
                if (!dateString) return '';

                var date = new Date(dateString);

                if (isNaN(date)) return '';

                var day = String(date.getDate()).padStart(2, '0');
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var year = date.getFullYear();

                return day + '-' + month + '-' + year;
            }

            function loadDataApproval() {
                $.ajax({
                    url: "{{ route('getdata_quick_approval') }}",
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.approval-panel');
                    },
                    success: function(data) {
                        // console.log(data);
                        $('.approval-panel').empty();
                        let approval = '';
                        let approval_panel = '';

                        if (data.code == 200) {
                            if (data.data.length == 0) {
                                approval_panel += ``;
                            } else {
                                approval_panel += ` <h6 class="section-title accent-inline">
                                         <span>Approval</span>
                                       </h6>
                                    <div class="swiper approvalSwiper">
                                        <div class="swiper-wrapper list_quick_approval">
                                        </div>
                                    </div>`;
                            }
                            data.data.forEach(function(item) {
                                var tanggal_mulai = '';
                                var tanggal_selesai = '';
                                var tanggal = '';
                                var created = '';
                                if (item.quick_type == 'cuti') {
                                    if (item.cuti != null) {
                                        var start = new Date(item.cuti.tanggal_mulai.replace(
                                            ' ', 'T'));
                                        var end = new Date(item.cuti.tanggal_selesai.replace(
                                            ' ', 'T'));
                                        tanggal_mulai += formatDate(item.cuti.tanggal_mulai);
                                        tanggal_selesai += formatDate(item.cuti
                                            .tanggal_selesai);
                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${tanggal_mulai} - ${tanggal_selesai}
                                            <br>`;
                                    } else {
                                        tanggal += '-';
                                    }
                                } else if (item.quick_type == 'izin_terlambat') {

                                    if (item.izin_terlambat != null) {
                                        console.log(item);

                                        created += formatDate(item.izin_terlambat
                                            .tanggal_terlambat);
                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${created}
                                            <br>
                                            <i class="fa-solid fa-clock"></i>${item.izin_terlambat.jam_terlambat}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_pulang') {

                                    if (item.izin_pulang != null) {
                                        // console.log(item);

                                        created += formatDate(item.izin_pulang.created_at);
                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${created}
                                            <br>
                                            <i class="fa-solid fa-clock"></i>${item.izin_pulang.jam_pulang}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_keluar') {

                                    if (item.izin_keluar != null) {
                                        // console.log(item);

                                        created += formatDate(item.izin_keluar.created_at);
                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${created}
                                            <br>
                                            <i class="fa-solid fa-clock"></i>${item.izin_keluar.jam_keluar} - ${item.izin_keluar.jam_kembali} 
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_libur') {

                                    // console.log(item);
                                    if (item.izin_libur != null) {

                                        // created += formatDate(item.izin_libur.tanggal_libur);
                                        // tukar += formatDate(item.izin_libur.tanggal_libur);
                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${formatDate(item.izin_libur.tanggal_libur)}
                                            <br>
                                            <i class="fa-solid fa-retweet"></i>${formatDate(item.izin_libur.tanggal_tukar)}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_sakit') {
                                    // console.log(data.izin_sakit);

                                    if (item.izin_sakit != null) {
                                        // console.log(item);

                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${item.izin_sakit.tanggal_sakit}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_tidak_masuk') {
                                    // console.log(data.izin_tidak_masuk);

                                    if (item.izin_tidak_masuk != null) {
                                        // console.log(item);

                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${item.izin_tidak_masuk.tanggal_tidak_masuk}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'izin_tidak_masuk') {
                                    // console.log(data.izin_tidak_masuk);

                                    if (item.izin_tidak_masuk != null) {
                                        // console.log(item);

                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${item.izin_tidak_masuk.tanggal_tidak_masuk}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'lembur') {

                                    if (item.lembur != null) {
                                        // console.log(item);

                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${item.lembur.tanggal_lembur}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                } else if (item.quick_type == 'perdin') {

                                    if (item.perdin != null) {
                                        // console.log(item);

                                        tanggal +=
                                            `<br>
                                            <i class="fa-regular fa-calendar"></i> ${item.perdin.tanggal_mulai} - ${item.perdin.tanggal_selesai}
                                            `;
                                    } else {
                                        tanggal += '-';

                                    }
                                }

                                approval += `
                                 <div class="swiper-slide">
                                 <a href="{{ url('/quick/approve') }}/${item.id_quick}">
                        <div class="approval-card">

                            <div class="approval-header">
                                <strong>${item.title_quick}</strong>
                            </div>
                            <div class="approval-body">
                                <div>
                                <span>${item.message_quick}</span>
                                
                                    ${tanggal}
                                </div>
                                
                            </div>

                            <!-- <div class="approval-action">
                                <button class="btn-approve">Approve</button>
                                <button class="btn-reject">Reject</button>
                            </div> -->

                        </div>
                        </a>
                    </div>`;
                            });

                            $('.approval-panel').html(approval_panel).fadeIn(100);
                            $('.list_quick_approval').html(approval).fadeIn(100);
                            // 🔥 INIT SWIPER SETELAH HTML TERPASANG
                            setTimeout(function() {

                                if (window.quickSwiper) {
                                    window.quickSwiper.destroy(true, true);
                                }

                                window.quickSwiper = new Swiper(".approvalSwiper", {
                                    slidesPerView: 1.2,
                                    spaceBetween: 14,
                                    grabCursor: true,
                                });

                            }, 1000);
                        }
                    }
                });
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

            function loadAbsensiData(startDate, endDate) {
                // 🔥 Contoh AJAX
                var karyawan = '';
                $.ajax({
                    url: "{{ route('get_count_absensi_home') }}",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    type: "GET",
                    beforeSend: function() {
                        $('.absensi-list').empty();
                        karyawan = `<div class="absensi-item">
                        <div class="absensi-box">
                            <span class="day"></span>
                            <span class="month"></span>
                        </div>
                        <div class="absensi-info">
                        </div>
                    </div>
                    <div class="absensi-item">
                        <div class="absensi-box">
                            <span class="day"></span>
                            <span class="month"></span>
                        </div>
                        <div class="absensi-info">
                        </div>
                    </div>
                    <div class="absensi-item">
                        <div class="absensi-box">
                            <span class="day"></span>
                            <span class="month"></span>
                        </div>
                        <div class="absensi-info">
                        </div>
                        <div class="status"></div>
                    </div>`;
                        $('.absensi-list').html(karyawan);
                        showSkeleton('#count_absen_hadir');
                        showSkeleton('#count_absen_izin');
                        showSkeleton('#count_absen_cuti');
                        showSkeleton('#count_absen_telat');
                        showSkeleton('#penempatan');
                        showSkeleton('#absen_masuk');
                        showSkeleton('#absen_pulang');
                        showSkeleton('#jam_kerja');
                        showSkeleton('.absensi-box .day');
                        showSkeleton('.absensi-box .month');
                        showSkeleton('.absensi-info');
                    },
                    success: function(res) {
                        // console.log(res);

                        if (res.code == 200) {

                            $('#count_absen_hadir').text(res.count_absen_hadir);
                            $('#count_absen_izin').text(res.count_izin);
                            $('#count_absen_cuti').text(res.count_cuti);
                            $('#count_absen_telat').text(res.count_telat);
                            if (res.jam_kerja == null || res.jam_kerja == '') {
                                var jam_kerja = '__ - __';
                            } else if (res.jam_kerja == 'LIBUR') {
                                var jam_kerja = 'LIBUR';
                            } else {
                                var jam_kerja = res.jam_kerja;
                            }
                            if (res.checkIn == null || res.checkIn == '') {
                                var checkIn = '-';
                            } else {
                                var checkIn = res.checkIn;
                            }
                            if (res.checkOut == null || res.checkOut == '') {
                                var checkOut = '-';
                            } else {
                                var checkOut = res.checkOut;
                            }
                            if (res.penempatan != null || res.penempatan != '') {
                                var penempatan = res.penempatan.site_name;
                            } else {
                                var penempatan = '-';
                            }
                            $('#penempatan').html(penempatan);
                            $('#absen_masuk').html(checkIn);
                            $('#absen_pulang').html(checkOut);
                            $('#jam_kerja').html(jam_kerja);
                            $('.absensi-list').empty();
                            karyawan = '';
                            res.data_absensi.forEach(function(item) {
                                const detail_masuk = JSON.stringify(item.detail_masuk);
                                const detail_pulang = JSON.stringify(item.detail_pulang);
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
                                <small class="attendance-shift text-muted">${item.shift || '-'}</small>
                                <div class="status ${item.status.replace(/\s+/g, '')}">${item.status}</div>
                            </div>
                        </div>`;
                            });
                            $('.absensi-list').html(karyawan);
                        } else if (res.code == 500) {
                            $('.absensi-list').empty();
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
                    error: function(err) {
                        console.error('Error fetching data:', err);
                    }
                });

            }

            document.getElementById('prevMonth').onclick = function() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderMonth();
            };

            document.getElementById('nextMonth').onclick = function() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderMonth();
            };

            // INIT
            renderMonth();
            var offcanvasEl = document.getElementById('home_index')


            $(document).on('click', '.btn_klik', function(e) {
                // console.log('oke');
                Swal.fire({
                    allowOutsideClick: false,
                    background: 'transparent',
                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                    showCancelButton: false,
                    showConfirmButton: false,

                });
            });


            // setInterval(get_notif, 2000);
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
