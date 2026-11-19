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
                                                                                                                                                                                       LIST KARYAWAN BAWAHAN (PREMIUM MOBILE UI)
                                                                                                                                                                                       ===================== */
        .content-absensi-bawahan {
            display: flex;
            flex-direction: column;
            gap: 12px;
            /* Jarak antar card */
            padding: 10px 0;
        }

        .bawahan-card {
            display: flex;
            align-items: center;

            /* 1. BACKGROUND BARU: Gradasi sangat halus agar memisah dari background utama yang putih */
            background: linear-gradient(145deg, #ffffff, #f8f6fc);

            border-radius: 18px;
            padding: 16px;
            gap: 14px;

            /* 2. BORDER BARU: Garis tepi ungu sangat pucat untuk mempertegas bentuk card */
            border: 1px solid rgba(124, 58, 237, 0.12);

            /* 3. SHADOW BARU: Sedikit lebih gelap agar efek melayang (floating) lebih terasa */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);

            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        /* Tetap pertahankan efek hover & active ini */
        .bawahan-card:hover {
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.15);
            transform: translateY(-2px);
            border-color: rgba(124, 58, 237, 0.3);
        }

        .bawahan-card:active {
            transform: scale(0.97);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            background: #f0eaff;
            /* Warna sedikit lebih gelap saat ditap */
        }

        /* Styling Avatar */
        .bawahan-avatar {
            position: relative;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .bawahan-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Titik Indikator Online/Offline */
        .status-dot {
            position: absolute;
            bottom: 0;
            right: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        /* Bagian Info Teks */
        .bawahan-info {
            flex: 1;
            min-width: 0;
            /* Mencegah teks overlap */
        }

        .bawahan-name {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bawahan-role {
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }

        /* Bagian Aksi & Status */
        .bawahan-action {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
        }

        .time-text {
            font-size: 11px;
            font-weight: 500;
        }

        /* Ikon Chevron (Panah Kanan) */
        .bawahan-chevron {
            color: #d1d5db;
            font-size: 14px;
            margin-left: 4px;
            transition: 0.3s;
        }

        .bawahan-card:hover .bawahan-chevron {
            color: #7c3aed;
            /* Berubah ungu saat di-hover/tap */
            transform: translateX(3px);
        }

        /* =====================
                                                                                                                                                                                       STATUS BADGE REFINED
                                                                                                                                                                                       ===================== */
        .status-badge {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            /* Sudut sedikit melengkung, tidak full bulat agar modern */
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            display: inline-block;
        }

        /* Menggunakan warna dari CSS Anda sebelumnya dengan penyesuaian */
        .status-badge.TEPATWAKTU {
            background: #ecfdf5;
            color: #059669;
        }

        .status-badge.TELAT {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-badge.TIDAKHADIR {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-badge.IZIN {
            background: #eff6ff;
            color: #3b82f6;
        }

        .status-badge.CUTI {
            background: #fdf4ff;
            color: #c026d3;
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
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <h5 class="mt-3 mb-1">DAFTAR BAWAHAN</h5>
            <small class="text-muted">Informasi daftar Bawahan</small>
        </div>

        <div class="alert_approval"></div>
        <div class="px-3 pb-2">
            <label for="holdingSelect" class="form-label small fw-bold mb-1">Pilih Holding</label>
            <select id="holdingSelect" class="form-select form-select-sm">
                <option value="">Pilih holding terlebih dahulu</option>
                @foreach ($holdings as $holding)
                    <option value="{{ $holding->id }}">{{ $holding->holding_name }}</option>
                @endforeach
            </select>
            <div class="row g-2 mt-1">
                <div class="col-6">
                    <label for="exportStartDate" class="form-label small mb-1">Tanggal awal</label>
                    <input type="date" id="exportStartDate" class="form-control form-control-sm">
                </div>
                <div class="col-6">
                    <label for="exportEndDate" class="form-label small mb-1">Tanggal akhir</label>
                    <input type="date" id="exportEndDate" class="form-control form-control-sm">
                </div>
            </div>
            <button type="button" id="btnExportExcel" class="btn btn-sm btn-success w-100 mt-2" disabled>
                <i class="fa-solid fa-file-excel me-1"></i>Download Excel Presensi Bawahan
            </button>
        </div>
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
        <div class="content-absensi-bawahan">

            <!-- Item Karyawan 1 -->


            <!-- Item Karyawan 2 -->
            <!-- <a href="#" class="bawahan-card text-decoration-none">
                                                                                                                                                                                                <div class="bawahan-avatar">
                                                                                                                                                                                                    <img src="https://ui-avatars.com/api/?name=Siti+Aminah&background=f3e8ff&color=7c3aed&rounded=true" alt="Siti Aminah">
                                                                                                                                                                                                    <span class="status-dot bg-danger"></span>
                                                                                                                                                                                                </div>

                                                                                                                                                                                                <div class="bawahan-info">
                                                                                                                                                                                                    <h6 class="bawahan-name mb-0">Siti Aminah</h6>
                                                                                                                                                                                                    <span class="bawahan-role text-muted">UI/UX Designer</span>
                                                                                                                                                                                                </div>

                                                                                                                                                                                                <div class="bawahan-action text-end">
                                                                                                                                                                                                    <span class="status-badge TIDAKHADIR mb-1">Alpha</span>
                                                                                                                                                                                                    <div class="text-muted small time-text">-</div>
                                                                                                                                                                                                </div>

                                                                                                                                                                                                <div class="bawahan-chevron">
                                                                                                                                                                                                    <i class="fa-solid fa-chevron-right"></i>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            </a> -->

        </div>
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
            let route_prefix = '{{ $routePrefix ?? "absensi_bawahan" }}';
            let selected_holding = '';
            const today = '{{ now()->toDateString() }}';
            $('#exportStartDate').val(today);
            $('#exportEndDate').val(today);
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
            var user_karyawan_id = '{{ $user_karyawan->id }}';


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




            function cb(start, end) {
                $('#reportrange #currentMonth').html(start.format('D MMMM YYYY') + ' - ' + end.format(
                    'D MMMM YYYY'));
                lstart = start.format('YYYY-MM-DD');
                lend = end.format('YYYY-MM-DD');
                $('#start_date').val(lstart);
                $('#end_date').val(lend);
            }

            $('#holdingSelect').on('change', function() {
                selected_holding = this.value;
                $('#btnExportExcel').prop('disabled', !selected_holding);
                load_data_absensi_bawahan();
            });

            $('#btnExportExcel').on('click', function() {
                if (!selected_holding) return;
                const start = $('#exportStartDate').val();
                const end = $('#exportEndDate').val();
                if (!start || !end || start > end) {
                    Swal.fire({ icon: 'warning', title: 'Tanggal tidak valid', text: 'Periksa tanggal awal dan tanggal akhir.' });
                    return;
                }
                $.ajax({
                    url: "{{ url('/') }}/" + route_prefix + "/export/" + selected_holding,
                    method: 'GET',
                    data: { start_date: start, end_date: end },
                    xhrFields: { responseType: 'blob' },
                    success: function(data, status, xhr) {
                        const disposition = xhr.getResponseHeader('Content-Disposition') || '';
                        const match = disposition.match(/filename[^;=\\n]*=((['"]).*?\\2|[^;\\n]*)/i);
                        const fileName = match ? match[1].replace(/['"]/g, '') : 'laporan-presensi-bawahan.xlsx';
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(data);
                        link.download = fileName;
                        link.click();
                        window.URL.revokeObjectURL(link.href);
                    },
                    error: function(xhr) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            let message = 'Download gagal. Periksa konfigurasi bawahan pada holding ini.';
                            try { message = JSON.parse(reader.result).message || message; } catch (error) {}
                            Swal.fire({ icon: 'warning', title: 'Belum dapat download', text: message });
                        };
                        reader.readAsText(xhr.response);
                    }
                });
            });

            function load_data_absensi_bawahan() {
                let data_approve = '';
                if (!selected_holding) {
                    $('.content-absensi-bawahan').html('<div class="text-center mt-3 mb-3"><small class="text-muted">Pilih holding untuk melihat bawahan.</small></div>');
                    return;
                }
                $.ajax({
                    url: "{{ url('/') }}/" + route_prefix + "/get_dataAbsensiBawahan/" + menu_active + "/" + selected_holding,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.content-absensi-bawahan');
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
                        hideSkeleton('.content-absensi-bawahan', data_approve);
                    },
                    success: function(res) {
                        // console.log(res);
                        var karyawan = '';
                        if (res.code == 200) {

                            $('.content-absensi-bawahan').empty();
                            if (!res.data || res.data.length === 0) {
                                $('.content-absensi-bawahan').html(`<div class="text-center mt-3 mb-3"><small class="text-muted">${res.message || 'Belum ada bawahan yang disetting untuk holding ini.'}</small></div>`);
                                return;
                            }
                            res.data.forEach(function(item) {
                                // console.log(item.kuota_cuti?.kuota ?? '-');
                                var jabatan = item.karyawan_jabatan_one ? item
                                    .karyawan_jabatan_one.jabatan ? item.karyawan_jabatan_one
                                    .jabatan.nama_jabatan : '-' : '-';
                                var bagian = item.karyawan_jabatan_one ? item
                                    .karyawan_jabatan_one.bagian ? item.karyawan_jabatan_one
                                    .bagian.nama_bagian : '-' : '-';
                                if (item.foto_karyawan == '' || item.foto_karyawan == null) {
                                    var profile =
                                        `<img src="/admin/assets/img/avatars/1.png" alt="${item.name}">`;
                                } else {
                                    var profile =
                                        `<img src="https://hrd.sumberpangan.store/storage/foto_karyawan/${item.foto_karyawan}" alt="Ahmad Faisal">`;
                                }
                                karyawan += ` <a href="{{ url('/') }}/${route_prefix}/detail_absensi_bawahan/${item.id}/${menu_active}" class="bawahan-card text-decoration-none">
                                <div class="bawahan-avatar">
                                    <!-- Bisa diganti dengan foto profil asli dari database -->
                                    ${profile}
                                    <!-- Indikator status (opsional) -->
                                    <span class="status-dot bg-success"></span>
                                </div>

                                <div class="bawahan-info">
                                    <h6 class="bawahan-name mb-0">${item.name}</h6>
                                    <span class="bawahan-role text-muted">${jabatan}-${bagian}</span>
                                    <!-- Badge status absensi hari ini -->
                                    <span class="status-badge TEPATWAKTU mb-1">ACTIVE</span>
                                    <div class="text-muted small time-text">KUOTA CUTI : ${item.kuota_cuti?.kuota ?? '-'} | TERPAKAI : ${item.kuota_cuti?.terpakai ?? '-'} | SISA : ${item.kuota_cuti?.sisa ?? '-'}</div>
                                    <div class="text-muted small time-text">Ketuk untuk melihat presensi</div>
                                </div>

                                <div class="bawahan-chevron">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </a>`;
                            });
                            $('.content-absensi-bawahan').html(karyawan);
                        } else if (res.code == 500) {
                            $('.content-absensi-bawahan').empty();

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
        });
    </script>
@endsection
