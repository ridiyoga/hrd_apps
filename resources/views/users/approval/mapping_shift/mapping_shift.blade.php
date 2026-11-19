@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
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

        .coworker-card- {
            background: linear-gradient(135deg, #e9d5ff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #d8b4fe;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Peach/Cream (Lebih Warm) */
        .coworker-card1 {
            /* Gradien Hijau Emerald yang lebih kaya */
            background: linear-gradient(135deg, #dcfce7, #f0fdf4);
            border-radius: 14px;
            padding: 12px 14px;
            /* Border dipertegas agar tidak tenggelam */
            border: 1px solid #4ade80;
            /* Shadow lebih dalam untuk efek elevasi */
            box-shadow: 0 4px 15px rgba(5, 46, 22, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Pink/Rose (Lebih Hidup) */
        .coworker-card2 {
            background: linear-gradient(135deg, #fecdd3, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fda4af;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card3 {
            /* Gradasi abu-abu lembut ke putih agar tetap bersih */
            background: linear-gradient(135deg, #f1f5f9, #ffffff);

            border-radius: 14px;
            padding: 12px 14px;

            /* Border abu-abu yang sedikit lebih gelap agar tegas */
            border: 1px solid #cbd5e1;

            /* Shadow yang halus namun memberikan efek kedalaman */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

            /* Transition tetap sama */
            transition: transform .2s ease, box-shadow .2s ease;

            /* Pastikan warna teks utama sangat gelap agar kontras */
            color: #1e293b;
        }

        .coworker-card-history {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
        }

        .avatar-wrapper img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e6d9ff;
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

        .icon-box-history {
            width: 36px;
            height: 36px;
            min-width: 36px;
            /* Kunci lebar minimal */
            min-height: 36px;
            /* Kunci tinggi minimal */
            flex-shrink: 0;
            /* WAJIB: Mencegah icon menjadi lonjong/gepeng */

            border-radius: 50%;
            background: rgba(50, 153, 84, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3aac48;
            font-size: 14px;

            /* Tambahan agar lebih manis */
            transition: all 0.3s ease;
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

        .shift-tabs {
            background: #f1f5f9;
            /* Abu-abu sangat muda kebiruan (slate 100) */
            padding: 3px;
            border-radius: 10px;
            display: inline-flex;
            border: 1px solid #e2e8f0;
            gap: 2px;
            overflow: visible !important;
        }

        .shift-tabs .nav-item {
            margin-bottom: 0 !important;
            overflow: visible !important;
        }

        .shift-tabs .nav-link {
            overflow: visible !important;
            border: none;
            border-radius: 8px;
            padding: 5px 12px;
            /* Diperkecil agar lebih ramping */
            background: transparent;
            color: #64748b;
            /* Warna teks yang kalem */
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 12px;
            /* Ukuran font lebih kecil */
            transition: all 0.2s ease;
            white-space: nowrap;
            /* Mencegah teks turun ke bawah */
        }

        .badge {
            z-index: 10;
        }

        /* Efek Hover: Halus tanpa shadow berlebih */
        .shift-tabs .nav-link:hover:not(.active) {
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.05);
        }

        /* Efek Hover: Halus tanpa shadow berlebih */
        .shift-tabs .nav-link:hover:not(.active) {
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.05);
        }

        /* State Aktif: Fokus pada kebersihan visual */
        .shift-tabs .nav-link.active {
            background: #7c3aed;
            /* Putih bersih kontras dengan background abu */
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Shadow sangat tipis */
        }

        /* Styling Icon agar lebih kecil dan proporsional */
        .shift-tabs .nav-link i {
            font-size: 11px;
            opacity: 0.7;
        }

        .shift-tabs .nav-link.active i {
            opacity: 1;
        }

        /* Perbaikan untuk Mobile agar tidak sesak */
        @media (max-width: 576px) {
            .shift-tabs {
                display: flex;
                /* Memenuhi lebar layar di mobile */
                width: 100%;
                overflow-x: auto;
                /* Jika layar sangat kecil, bisa di-scroll */
            }

            .shift-tabs .nav-item {
                flex: 1;
            }

            .shift-tabs .nav-link {
                justify-content: center;
                padding: 6px 4px;
                font-size: 11px;
                /* Font lebih kecil lagi di mobile */
            }
        }

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

        .shift-legend {
            font-size: clamp(11px, 1.6vw, 14px);
        }

        .shift-legend .dot {
            width: clamp(8px, 1.5vw, 10px);
            height: clamp(8px, 1.5vw, 10px);
            border-radius: 50%;
        }

        .shift-legend p {
            margin: 0;
            line-height: 1.3;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        {{-- Header --}}
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-regular fa-folder-open"></i>
            </div>
            <h5 class="mt-3 mb-1">Informasi Approval</h5>
            <small class="text-muted">Data Approval & Histori Approval</small>
        </div>
        <div class="card-body px-3 px-md-4">
            <div class="alert_profile"></div>
            <input type="hidden" value="" id="start_date" name="start_date">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm" id="prevWeek">&lt;&nbsp;Prev</button>
                <h6 class="mb-0" id="weekLabel">Weekly Shift Overview</h6>
                <button class="btn btn-outline-primary btn-sm" id="nextWeek">Next&nbsp;&gt;</button>
            </div>
            <ul class="mb-3 nav shift-tabs" id="shiftTab" role="tablist">
                <li class="nav-item mf-1">
                    <button class="nav-link active position-relative d-flex align-items-center" data-bs-toggle="tab"
                        data-bs-target="#shift">
                        <i class="fa-solid fa-user-clock me-2"></i>
                        <span>Shift</span>
                        <!-- Badge diperkecil dengan padding minimal -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.55rem; padding: 0.25em 0.45em; line-height: 1;">
                            {{ $badge_shift }}
                        </span>
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link position-relative d-flex align-items-center" data-bs-toggle="tab"
                        data-bs-target="#pengajuan">
                        <i class="fa-regular fa-clock me-2"></i>
                        <span>Pegawai Ubah</span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.55rem; padding: 0.25em 0.45em; line-height: 1;">
                            {{ $badge_pegawai_ubah }}
                        </span>
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link position-relative d-flex align-items-center" data-bs-toggle="tab"
                        data-bs-target="#pengajuan_manager">
                        <i class="fa-regular fa-clock me-2"></i>
                        <span>Atasan Ubah</span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.55rem; padding: 0.25em 0.45em; line-height: 1;">
                            {{ $badge_atasan_ubah }}
                        </span>
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="shift">
                    <ul class="nav profile-tabs" id="profileTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#approval">
                                <i class="fa-solid fa-file-lines"></i>
                                <span>Approval</span>
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#histori">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Riwayat</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="approval" role="tabpanel"
                            aria-labelledby="nav-approval-tab">
                            <div class="list-approval">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="histori" role="tabpanel" aria-labelledby="nav-history-tab">
                            <div class="list-history">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pengajuan">
                    <div class="row shift-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-primary me-1"></span>
                            <p>Belum&nbsp;approve</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-1"></span>
                            <p>Approval&nbsp;HRD</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-danger me-1"></span>
                            <p>&nbsp;Ditolak</p>
                        </div>
                    </div>

                    <div id="tukarList">
                    </div>
                </div>
                <div class="tab-pane fade" id="pengajuan_manager">
                    <div class="row shift-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-primary me-1"></span>
                            <p>Belum&nbsp;approve</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-1"></span>
                            <p>Approval&nbsp;HRD</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-danger me-1"></span>
                            <p>&nbsp;Ditolak</p>
                        </div>
                    </div>
                    <div id="tukarAtasan">
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('content')
    {{-- tukar shift --}}
    <div class="offcanvas offcanvas-bottom rounded-top-5" style="height: auto;" data-bs-keyboard="false"
        data-bs-backdrop="true" id="off-canvas-tukar_shift_form">

        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Form Pengajuan Shift</h6>
                    <small class="text-muted">List shift yang diajukan</small>
                </div>
            </div>
        </div>

        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div class="mt-3 text-center">
                <div id="list_shift" class="d-flex flex-column gap-3"></div>
                <input type="text" name="id_mapping_tukar" id="id_mapping_tukar" class="form-control" hidden>
                <input type="text" name="menu_id" id="menu_id" class="form-control" value="{{ $menu_id }}"
                    hidden>
                <div id="detail_tukar_shift"></div>
                <div id="ttd_tukar_shift"></div>
                <div class="row g-2 d-flex align-items-stretch mb-2">
                    <div id="ttd_atasan" class="col-6"></div>
                    <div id="ttd_bawahan" class="col-6"></div>
                </div>
                <div class="d-flex justify-content-center">
                    <div id="div_btn_approval">
                        <button id="btn_update_shift" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-circle-check"></i>&nbsp;Approve
                        </button>
                        <button id="btn_tolak_update_shift" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-circle-xmark"></i>&nbsp;Tolak
                        </button>
                    </div>

                    <a href="javascript:void(0);" class="btn btn-sm light btn-danger ms-2" data-bs-dismiss="offcanvas"
                        aria-label="Close">
                        &nbsp;Batal
                    </a>
                </div>

            </div>
        </div>
    </div>
    {{-- tukar shift end --}}
    {{-- logout --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });


        $(document).ready(function() {
            var menu_active = '{{ $menu_active->menu_active }}';


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

            function formatISO(date) {
                return new Date(date).toISOString().split('T')[0];
            }

            function formatLabel(date) {
                return new Date(date).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            }

            // week
            const weekLabel = $("#weekLabel");
            const tukarList = $("#tukarList");
            const tukarAtasan = $("#tukarAtasan");

            let currentStart = new Date();
            currentStart.setDate(currentStart.getDate() - currentStart.getDay() + 1);
            loadWeek();

            $("#prevWeek").on("click", () => changeWeek(-1));
            $("#nextWeek").on("click", () => changeWeek(1));

            function changeWeek(direction) {
                currentStart.setDate(currentStart.getDate() + (direction * 7));
                loadWeek();
            }

            // ===============================
            // LOAD DATA (PER MINGGU)
            // ===============================
            function loadWeek() {
                const start = formatISO(currentStart);
                const end = formatISO(new Date(currentStart.getTime() + 6 * 86400000));

                load_data(start, end);

                weekLabel.text(
                    `${formatLabel(currentStart)} - ${formatLabel(new Date(currentStart.getTime() + 6 * 86400000))}`
                );

                $('#start_date').val(start);

                showSkeleton('#tukarList');
                showSkeleton('#tukarAtasan');
                globalStart = start;
                globalEnd = end;

                $.getJSON(`/tukar_shift/get_TukarShiftData_hrd?start=${start}&end=${end}`)
                    .done(function(data) {

                        let tukars = flattenData(data);

                        // console.log(tukars);
                        // globalTukars = tukars;
                        renderTukars(tukars, start, end);
                    })
                    .fail(function() {
                        tukarList.html('<p class="text-danger text-center">Gagal memuat data</p>');
                    });
                $.getJSON(`/tukar_shift/get_HRDShiftData?start=${start}&end=${end}`)
                    .done(function(data) {

                        let atasans = flattenData(data);

                        // console.log(atasans);
                        // globalTukars = tukars;
                        // hideSkeleton('#tukarAtasan', renderWeek());
                        renderAtasans(atasans, start, end);
                    })
                    .fail(function() {
                        tukarAtasan.html('<p class="text-danger text-center">Gagal memuat data</p>');
                    });
            }

            function flattenData(data) {
                if (Array.isArray(data)) return data;

                let result = [];

                Object.keys(data).forEach(date => {
                    data[date].forEach(item => {
                        result.push({
                            ...item,
                            tanggal: date
                        });
                    });
                });

                return result;
            }
            // end week



            function load_data(start, end) {
                $.ajax({
                    url: "{{ url('approval/get_list_approval_mapping_shift') }}" + "/" + menu_active,
                    method: "GET",
                    data: {
                        start: start,
                        end: end,
                    },
                    beforeSend: function() {
                        showSkeleton('.list-approval');
                        showSkeleton('.list-history');
                    },
                    success: function(data) {
                        Swal.close();
                        // console.log(data);
                        $('.list-approval').empty();

                        if (data.code == 200) {
                            let html = '';
                            if (data.data.length == 0) {
                                html += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum Ada Data Approval</p>
                                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    const date = new Date(element.created_at);

                                    const tanggal = date.toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                    html += `<a class="btn_klik" href="{{ url('/approval/approvalDetail/') }}/${element.id}/{{ $menu_id }}"><div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.legal_number_mapping}</h6>

                                            <div class="text-muted small">
                                                ${element.judul}
                                                <span class="mx-1">•</span>
                                                (${element.input_by_name})
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${tanggal}</span>
                                               </div>

                                            </div>
                                        </div>
                                        {{-- ICON --}}
                                        <div class="icon-box">
                                         <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                    </div></a>`;
                                });
                            }

                            hideSkeleton('.list-approval', html);
                            let history = '';
                            if (data.data_approved.length == 0) {
                                history += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum Ada Data Histori</p>
                                    </div>`;
                            } else {
                                data.data_approved.forEach(element => {
                                    if (element.status_approved == 0) {
                                        status = 'text-primary';
                                    } else if (element.status_approved == 1) {
                                        status = 'text-success';
                                    }
                                    const date = new Date(element.created_at);

                                    const tanggal = date.toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                    history += `<a class="btn_klik" href="{{ url('/approval/approvalDetail') }}/${element.id}/{{ $menu_id }}"><div class="coworker-card-history d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.legal_number_mapping}</h6>

                                            <div class="text-muted small">
                                                ${element.judul}
                                                <span class="mx-1">•</span>
                                                (${element.input_by_name})
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${tanggal}</span>
                                               </div>

                                            </div>
                                        </div>
                                        {{-- ICON --}}
                                        <div class="icon-box-history">
                                        <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                    </div></a>`;
                                    // history += `<a class="btn_klik" href="{{ url('/approval/approvalDetail') }}/${element.legal_number_mapping}"><div class="swiper-slide"><div class="card job-post" style="box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.1); margin-bottom: 4%;"><div class="card-body" style="padding: 6px;"><div class="media media-70"><i class="fa-regular fa-file-lines fa-5x ${status}"></i></div><div class="card-info"><h4 class="title" style="font-size: 13pt;">${element.legal_number_mapping}</h4><span"><b>${element.mapping_group}</b></span><span class="location"><b>(${element.karyawan_input.name})</b></span><div class="d-flex align-items-center"><i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${tanggal}</span></div></div></div></div></div></a>`;
                                })
                            }
                            hideSkeleton('.list-history', history);
                        } else {
                            Swal.fire({
                                title: 'error',
                                text: data.message,
                                icon: 'error',
                                timer: 6500
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            title: 'error',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            timer: 6500
                        })
                    }
                })
            }

            $(document).on('click', '.btn_klik', function(e) {
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

            function renderTukars(tukars, start, end) {

                let html = `<h6 class="mb-3 text-center">Daftar Pengajuan</h6>`;

                if (!tukars || tukars.length === 0) {
                    html += `<p class="text-center text-muted">Tidak ada pengajuan.</p>`;
                    return tukarList.html(html);
                }

                // 🔥 sorting by tanggal
                tukars.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

                // console.log(tukars);



                tukars.forEach(s => {

                    let approved = s.status_approved == 1 ? '-history' : '';

                    html += `
                            <button class="btn coworker-card${s.approval2} d-flex align-items-center mb-3 w-100"
                                data-id_mapping_tukar="${s.id_mapping}"                      
                                id="btn_tukar_shift_offcanvas">

                                 <div class="icon-box d-flex align-items-center justify-content-center" style="flex-shrink: 0;">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-0 fw-semibold text-truncate" style="line-height: 1.2;">${s.nama_pengaju}</h6>
                                    <div class="text-muted small mt-1">
                                        <span>No. Form</span>
                                        <span class="mx-1">•</span>
                                        <span>${s.no_form}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span>Alasan</span>
                                        <span class="mx-1">•</span>
                                        <span>${s.alasan}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span>Dibuat</span>
                                        <span class="mx-1">•</span>
                                        <span>${s.created_at}</span>
                                    </div>
                                </div>

                               

                            </button>`;

                    $(document).off('click', '#btn_tukar_shift_offcanvas');
                    $(document).on('click', '#btn_tukar_shift_offcanvas', function() {
                        let id_mapping_tukar = $(this).data('id_mapping_tukar');

                        loadGetDetailTukar(id_mapping_tukar)

                        $('#id_mapping_tukar').val(id_mapping_tukar);
                        // console.log(id_pengaju);
                        const offcanvasTukar = $('#off-canvas-tukar_shift_form');
                        const offcanvasShow = new bootstrap.Offcanvas(offcanvasTukar);
                        offcanvasShow.show();
                    });

                    // console.log(s.tukar_detail);

                });

                tukarList.html(html);
            }

            function renderAtasans(atasans, start, end) {

                let html = `<h6 class="mb-3 text-center">Daftar Pengajuan</h6>`;

                if (!atasans || atasans.length === 0) {
                    html += `<p class="text-center text-muted">Tidak ada pengajuan.</p>`;
                    return tukarAtasan.html(html);
                }

                // 🔥 sorting by tanggal
                atasans.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

                console.log(atasans);



                atasans.forEach(s => {

                    let approved = s.approved_signature == null ? '-null' : '';

                    html += `
                            <button class="btn coworker-card${s.approval1} d-flex align-items-center mb-3 w-100"
                                data-id="${s.id}"                      
                                id="btn_tukar_atasan">

                                 <div class="icon-box d-flex align-items-center justify-content-center" style="flex-shrink: 0;">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-0 fw-semibold text-truncate" style="line-height: 1.2;">${s.name}</h6>
                                    <div class="text-muted small mt-1">
                                        <span>${s.no_form}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span>Alasan</span>
                                        <span class="mx-1">•</span>
                                        <span>${s.alasan}</span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span>Dibuat</span>
                                        <span class="mx-1">•</span>
                                        <span>${s.created_at}</span>
                                    </div>
                                </div>

                               

                            </button>`;

                    // console.log(s.tukar_detail);

                });

                tukarAtasan.html(html);
            }
            $(document).on('click', '#btn_tukar_atasan', function() {
                const startDate = $('#start_date').val();
                const menu_active = '{{ $menu_active->menu_active }}';

                var id = $(this).data('id');

                window.location.href =
                    "{{ url('tukar_shift/detailTukarAtasanHRD') }}" + "/" + id + "/" + menu_active;
            });

            function loadGetDetailTukar(id_mapping_tukar) {
                // console.log(id_mapping);

                let id_map = id_mapping_tukar;
                console.log(id_map);


                $.ajax({
                    url: "{{ url('tukar_shift/get_detail_tukar') }}",
                    method: "GET",
                    data: {
                        id_map: id_map,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showSkeleton('#detail_tukar_shift');
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            // console.log(response.data_utama);
                            $('#detail_tukar_shift').empty();

                            let html = '';


                            response.data.forEach(s => {
                                html +=
                                    `<div class="card border-0 shadow-sm mb-3 overflow-hidden" style="border-radius: 15px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                
                                                <div class="text-start flex-grow-1">
                                                    <div class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3">Pengaju</div>
                                                    <h6 class="mb-1 fw-bold text-dark">${s.karyawan_penukar_name}</h6>
                                                    <div class="text-muted small">
                                                        <i class="fa-solid fa-calendar-day me-1"></i> ${s.mapping_shift_penukar.tanggal_masuk}
                                                    </div>
                                                    <div class="badge bg-light text-secondary border mt-1 fw-medium">
                                                        ${s.nama_shift_penukar}
                                                    </div>
                                                </div>

                                                <div class="mx-3 text-center">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fa-solid fa-right-left text-primary"></i>
                                                    </div>
                                                </div>

                                                <div class="text-end flex-grow-1">
                                                    <div class="badge bg-info-subtle text-info mb-2 rounded-pill px-3">Ditukar Dengan</div>
                                                    <h6 class="mb-1 fw-bold text-dark">${s.karyawan_tertukar_name}</h6>
                                                    <div class="text-muted small">
                                                        ${s.mapping_shift_tertukar.tanggal_masuk} <i class="fa-solid fa-calendar-day ms-1"></i>
                                                    </div>
                                                    <div class="badge bg-light text-secondary border mt-1 fw-medium">
                                                        ${s.nama_shift_tertukar}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>`
                            });
                            // $('#detail_tukar_shift').html(html);
                            hideSkeleton('#detail_tukar_shift', html)
                            $('#ttd_tukar_shift').empty();
                            if (response.data_utama.approval2 == null) {
                                $('#ttd_tukar_shift').append(
                                    `<div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                                    <div class="card-header bg-light border-0 py-2 text-center">
                                        <input type="hidden" name="id_karyawan" value="">
                                        <input type="text" name="name" id="name" value=""
                                            hidden>
                                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                            id="user_pengajuan">Disetujui oleh: ${response.data_utama.nama_approval2}</small>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="alert_signature"></div>
                                        <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                            <canvas id="signature-pad" name="signature_pad"
                                                style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                                hidden></canvas>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                                id="clear">
                                                <i class="fa-solid fa-eraser me-1"></i> Hapus
                                            </button>
                                            <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di
                                                atas</small>
                                        </div>
                                    </div>
                                </div>
                                `);
                                $('#div_btn_approval').show();
                            } else {
                                $('#ttd_tukar_shift').append(
                                    `
                                    <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                                    <div class="card-header bg-light border-0 py-2 text-center">
                                        <input type="hidden" name="id_karyawan" value="">
                                        <input type="text" name="name" id="name" value=""
                                            hidden>
                                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                            id="user_pengajuan">Disetujui oleh: ${response.data_utama.nama_approval2}</small>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="alert_signature"></div>
                                        <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                             <img src="/storage/signatures/${response.data_utama.sign_approval2}" 
                                                style="width:100%; height:200px; object-fit:contain;" />
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                                id="clear">
                                                <i class="fa-solid fa-eraser me-1"></i> Hapus
                                            </button>
                                            <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di
                                                atas</small>
                                        </div>
                                    </div>
                                </div>
                                
                                </div>
                                `);
                                $('#div_btn_approval').hide();
                            }


                            $('#ttd_bawahan').empty();
                            $('#ttd_bawahan').append(
                                `<div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">PENGAJU</small>

                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="/storage/signatures/${response.data_utama.sign_pengaju}" 
                                                style="width:100%; height:100%; object-fit:contain;" />
                                        </div>
                                        <small class="d-block text-muted" style="font-size: 8px;">
                                            ${response.data_utama.nama_pengaju}</small>
                                    </div>`);
                            $('#ttd_atasan').empty();
                            $('#ttd_atasan').append(
                                `
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">ATASAN LANGSUNG</small>

                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="/storage/signatures/${response.data_utama.sign_approval1}" 
                                                style="width:100%; height:100%; object-fit:contain;" />
                                        </div>
                                        <small class="d-block text-muted" style="font-size: 8px;">
                                            ${response.data_utama.nama_approval1}</small>
                                    </div>`);

                            var canvas = document.getElementById('signature-pad');
                            // console.log(canvas);
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


                        } else {
                            $('.alert_profile').empty();
                            $('.alert_profile').append(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            );
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
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
                            hideSkeleton('#detail_tukar_shift', karyawan);
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
            $('#btn_update_shift').on('click', function() {
                // let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX
                var formData = new FormData();

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }

                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('id_mapping_tukar', $('#id_mapping_tukar').val());
                formData.append('menu_id', $('#menu_id').val());
                formData.append('signature', signature);
                formData.append('start', globalStart);
                formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/ubah_shift_hrd') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
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
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                                '<strong>Success!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 6000); // 3 detik
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift_form');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                            console.log(response.grouped);
                            $('#tukarList').empty();

                            let tukars = flattenData(response.grouped).flat();
                            let start = globalStart
                            let end = globalEnd;
                            renderTukars(tukars, start, end);

                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('.alert_profile').empty();
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 60000);
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift_form');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                        }
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
            $('#btn_tolak_update_shift').on('click', function() {
                // let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX
                var formData = new FormData();

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }

                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('id_mapping_tukar', $('#id_mapping_tukar').val());
                formData.append('menu_id', $('#menu_id').val());
                formData.append('signature', signature);
                formData.append('start', globalStart);
                formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/tolak_ubah_shift_hrd') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
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
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                                '<strong>Success!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 6000); // 3 detik
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift_form');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                            console.log(response.grouped);
                            $('#tukarList').empty();

                            let tukars = flattenData(response.grouped).flat();
                            let start = globalStart
                            let end = globalEnd;
                            renderTukars(tukars, start, end);

                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('.alert_profile').empty();
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 60000);
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift_form');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                        }
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

        });
    </script>
@endsection
