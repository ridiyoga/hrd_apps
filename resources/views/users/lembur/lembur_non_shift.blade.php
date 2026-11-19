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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .form-input {
            position: relative;
        }

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

        .coworker-card-3 {
            /* Menggunakan hex asli kamu, tapi disempurnakan gradient-nya */
            background: linear-gradient(135deg, #b4edf7, #e0f2fe);
            border-radius: 14px;
            padding: 12px 14px;
            /* Box shadow diubah ke tone biru transparan */
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;

            /* Tambahan untuk teks */
            color: #0369a1;
            /* Biru gelap yang solid dan nyaman dibaca */
        }

        .coworker-card-3 .avatar-wrapper i {
            color: #0ea5e9;
            /* Aksan cyan terang */
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

        .border-dashed {
            border-style: dashed !important;
            border-color: #cbd5e1 !important;
            transition: all 0.3s ease;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <h5 class="mt-3 mb-1">LEMBUR NON SHIFT</h5>
            <small class="text-muted">Informasi Pengajuan Lembur</small>
        </div>
        <div class="alert alert-info d-flex align-items-center gap-2 rounded-3">
            <i class="fa-solid fa-info-circle fs-5"></i>
            <div class="alert-content">
                <strong>Informasi</strong><br>
                Pastikan data atasan Anda sudah terisi pada menu profil karyawan untuk proses approval lembur.
            </div>
        </div>
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

        <div class="alert_add_cuti"></div>
        <div id="alert_pengajuan_cuti"></div>
        @if (session('hapus_lembur_sukses'))
            <div class="alert alert-success alert-dismissible fade show alert_pengajuan_cuti">
                {{ session('hapus_lembur_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('hapus_lembur_gagal'))
            <div class="alert alert-danger alert-dismissible fade show alert_pengajuan_cuti">
                {{ session('hapus_lembur_gagal') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card-body px-3 px-md-4">
            <ul class="nav profile-tabs" id="profileTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-lembur" role="tab"
                        aria-controls="nav-lembur" aria-selected="true">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Lembur</span>
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#histori" role="tab"
                        aria-controls="histori" aria-selected="false">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lembur Histori</span>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="nav-lembur" role="tabpanel" aria-labelledby="nav-lembur-tab">

                    <button class="btn btn-sm light btn-primary mb-3" type="button" id="btn_lembur"><i
                            class="fa-solid fa-plus me-2"></i>Tambah</button>
                    <div class="content-lembur"></div>
                </div>
                <div class="tab-pane fade" id="histori" role="tabpanel" aria-labelledby="nav-history-tab">
                    <div class="alert_history_cuti"></div>
                    <div class="riwayat_lembur">
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off_canvas_lembur" style="height: 95vh;">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Form Pengajuan Lembur</h6>
                    <small class="text-muted">Pengajuan Lembur Karyawan </small>
                </div>

            </div>

            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <form class="my-2" method="post" id="form_lembur_lembur" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
                <input type="hidden" name="awal" value="2">
                <input type="hidden" name="lembur_shift" value="2">
                <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
                <!-- Form input -->
                <div class="col-12">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">1</span>
                            <h6 class="mb-0 fw-bold">Tanggal dan Dokumen</h6>
                        </div>

                        <label for="tanggal_lembur" class="form-label">Tanggal Lembur</label>

                        <div class="form-input tanggal_lembur mb-2 d-flex align-items-center">
                            <span class="input-icon">
                                <i class="fa-solid fa-calendar-days"></i>
                            </span>
                            <input type="date" id="tanggal_lembur" name="tanggal_lembur"
                                class="form-control datepicker" placeholder="Pilih Tanggal Lembur">

                            <button type="button" class="btn btn-danger btn-sm ms-2 btn-hapus" style="display:none;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        <div class="form-group tanggal_berulang">
                            <label for="id_karyawan_lembur_1" class="form-label">Karyawan</label>
                            <div id="container_karyawan">
                                <div class="karyawan-row border rounded-3 p-2 mb-3">
                                    <div class="form-input id_karyawan_lembur mb-2 d-flex align-items-center">
                                        <span class="input-icon">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        <select name="id_karyawan_lembur[]" id="id_karyawan_lembur_1"
                                            class="select-karyawan"></select>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label for="mulai_1" class="form-label small fw-semibold mb-1">
                                                <i class="fa-solid fa-clock me-1 text-primary"></i>Mulai
                                            </label>
                                            <input type="time" id="mulai_1" name="mulai[]"
                                                class="form-control mulai-karyawan">
                                        </div>
                                        <div class="col-6">
                                            <label for="selesai_1" class="form-label small fw-semibold mb-1">
                                                <i class="fa-solid fa-clock me-1 text-danger"></i>Selesai
                                            </label>
                                            <input type="time" id="selesai_1" name="selesai[]"
                                                class="form-control selesai-karyawan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="btn_tambah_tanggal" class="btn btn-primary btn-sm mb-2">
                                <i class="fa-solid fa-plus"></i> Tambah Karyawan
                            </button>
                        </div>


                        <div class="form-input keterangan_lembur">
                            <span class="input-icon">
                                <i class="fa-solid fa-note-sticky"></i>
                            </span>
                            <textarea name="keterangan_lembur" id="keterangan_lembur" cols="30" rows="3" class="form-control"
                                placeholder="Alasan Lembur"></textarea>
                        </div>
                        <div class="alert_keterangan_lembur"></div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">2</span>
                                <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                            </div>

                            <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                                <div class="card-header bg-light border-0 py-2 text-center">
                                    <input type="text" name="name" id="name"
                                        value="{{ $user_karyawan->name }}" hidden>
                                    <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                        id="user_pengajuan">Diajukan Oleh: {{ $user_karyawan->name }}</small>
                                </div>
                                <div class="card-body p-2">
                                    <div class="alert_signature"></div>
                                    <div id="sig" class="kbw-signature rounded-3"
                                        style="width: 100%; height: 200px;">
                                        <canvas id="signature-pad"
                                            style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                            hidden></canvas>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                        <button type="button"
                                            class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                            id="clear">
                                            <i class="fa-solid fa-eraser me-1"></i> Hapus
                                        </button>
                                        <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di
                                            atas</small>
                                    </div>
                                    <textarea id="signature" name="signature" style="display: none"></textarea>
                                </div>
                            </div>

                            <div class="row g-2 d-flex align-items-stretch">
                                <div class="alert_atasan_1"></div>
                                <div class="alert_atasan_2"></div>

                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN
                                            OLEH-1:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        </div>
                                        <input type="hidden" name="atasan_1" id="atasan_1" value="">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">ATASAN LANGSUNG</small>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN
                                            OLEH-2:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        </div>
                                        <input type="hidden" name="atasan_2" id="atasan_2" value="">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan2">MANAGER</small>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">HRD:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        </div>
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="hrd_selected">HRD</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert_error_cuti col-12"></div>
                        <div id="btn_pengajuan"></div>
                        <div id="btn_pengajuan_luar">
                            <button type="button" id="btn_simpan_lembur"
                                class="btn btn-primary btn-sm w-100 py-3 rounded-pill fw-bold mb-2 shadow">
                                AJUKAN LEMBUR
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    @include('users.logout_confirm')
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
        // Menutup SweetAlert ketika halaman kembali dari browser cache.
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        // Menjalankan inisialisasi halaman setelah seluruh DOM siap digunakan.
        $(document).ready(function() {
            let menu_active = '{{ $menu_id }}';

            var session_sukses = "{{ session('hapus_lembur_sukses') }}";
            var session_gagal = "{{ session('hapus_lembur_gagal') }}";

            var user_karyawan_id = '{{ $user_karyawan->id }}';

            let karyawanOptions = '';
            let karyawanIndex = 1;

            // Mengaktifkan konfigurasi Select2 pada dropdown karyawan.
            function initSelectKaryawan($select) {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    dropdownParent: $('#off_canvas_lembur'),
                    width: '100%',
                    theme: 'bootstrap-5',
                    placeholder: "Pilih Karyawan",
                    allowClear: true
                });
            }

            initSelectKaryawan($('#id_karyawan_lembur_1'));

            // Mengganti option seluruh dropdown karyawan dengan data terbaru.

            // 2. Fungsi ketika tombol "Tambah Tanggal" diklik
            // Memuat daftar karyawan yang memiliki shift pada tanggal terpilih.


            // Tampilkan SweetAlert Loading Spinner sebelum request AJAX dikirim
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('/lembur/get_non_shift_lembur') }}",
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // Mengisi dropdown setelah daftar karyawan berhasil diterima.
                success: function(response) {
                    karyawanOptions = response;
                    $('.select-karyawan').each(function() {
                        $(this).html(response);
                    });

                    // Tutup SweetAlert setelah data berhasil dimuat
                    Swal.close();
                },
                // Menampilkan pesan ketika daftar karyawan gagal dimuat.
                error: function(data) {

                    // Ambil pesan error (dengan fallback jika responseJSON/message kosong)
                    let errorMessage = data.responseJSON && data.responseJSON.message ?
                        data.responseJSON.message :
                        'Terjadi kesalahan saat mengambil data.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                        timer: 4200,
                        showConfirmButton: false
                    });
                }
            });
            // Menambahkan baris input karyawan baru ke dalam form.
            $('#btn_tambah_tanggal').click(function() {
                if (!karyawanOptions) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih tanggal terlebih dahulu',
                        text: 'Data karyawan harus dimuat berdasarkan tanggal lembur.'
                    });
                    return;
                }

                karyawanIndex++;
                var htmlBaru = `
                        <div class="karyawan-row border rounded-3 p-2 mb-3 id_karyawan_lembur">
                            <div class="form-input  mb-2 d-flex align-items-center">
                                <span class="input-icon">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <select name="id_karyawan_lembur[]" id="id_karyawan_lembur_${karyawanIndex}" class="select-karyawan">${karyawanOptions}</select>
                                <button type="button" class="btn btn-danger btn-sm ms-2 btn-hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                            <div class="row g-2 mulai_selesai">
                                <div class="col-6">
                                    <label for="mulai_${karyawanIndex}" class="form-label small fw-semibold mb-1">
                                        <i class="fa-solid fa-clock me-1 text-primary"></i>Mulai
                                    </label>
                                    <input type="time" id="mulai_${karyawanIndex}" name="mulai[]"
                                        class="form-control mulai-karyawan">
                                </div>
                                <div class="col-6">
                                    <label for="selesai_${karyawanIndex}" class="form-label small fw-semibold mb-1">
                                        <i class="fa-solid fa-clock me-1 text-danger"></i>Selesai
                                    </label>
                                    <input type="time" id="selesai_${karyawanIndex}" name="selesai[]"
                                        class="form-control selesai-karyawan">
                                </div>
                            </div>
                        </div>
                    `;
                // Masukkan template baru ke dalam container
                $('#container_karyawan').append(htmlBaru);
                initSelectKaryawan($(`#id_karyawan_lembur_${karyawanIndex}`));

            });

            // 3. Fungsi untuk menghapus baris input tertentu
            // Menghapus baris karyawan yang dipilih.
            $(document).on('click', '.btn-hapus', function() {
                $(this).closest('.id_karyawan_lembur').remove();
            });


            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas);

            // Menghapus isi tanda tangan dari canvas.
            $('#clear').on('click', () => signaturePad.clear());
            // Membuka form lembur dan menyiapkan canvas tanda tangan.
            $('#btn_lembur').on('click', function() {
                // console.log('asoy');
                load_data_approval();
                $('#signature-pad').removeAttr('hidden');

                // Menyesuaikan resolusi canvas dengan ukuran layar perangkat.
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
                const offcanvasEl = document.getElementById('off_canvas_lembur');
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();

            });



            // Menampilkan placeholder animasi saat data sedang dimuat.
            function showSkeleton(selector) {
                const el = $(selector);
                el.html(
                    `<span class="skeleton skeleton-number" style="width:100%"></span>`
                );
            }

            // Mengganti placeholder dengan isi data yang sudah dimuat.
            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }

            // Mengambil dan menampilkan data atasan untuk alur approval.
            function load_data_approval() {
                $.ajax({
                    url: "{{ url('/cuti/get_approval') }}" + '/' + menu_active,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('#atasan1');
                        showSkeleton('#atasan2');
                    },
                    // Menampilkan alert jika data approval gagal diambil.
                    error: function(data) {
                        $('.alert-approval').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseText + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-approval').animate({
                                opacity: 0,
                                marginTop: '-10px'
                            }, 1000, function() {
                                $(this).slideUp('slow', function() {
                                    $(this).html(
                                        ''); // Kosongkan isi setelah hilang
                                    $(this).css({
                                        'opacity': '',
                                        'margin-top': ''
                                    }); // Reset style untuk penggunaan berikutnya
                                });
                            });
                        }, 6000);
                    },
                    // Mengisi nama dan ID atasan dari response server.
                    success: function(data) {
                        console.log(data, menu_active);
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

            // Mengirim form pengajuan lembur beserta tanda tangan ke server.
            $(document).on('click', '#btn_simpan_lembur', function(e) {
                e.preventDefault(); // Mencegah submit default jika tombol ada di dalam form

                let btn = $(this);
                let originalText = btn.html();

                let signature_value = '';
                if (typeof signaturePad !== 'undefined' && !signaturePad.isEmpty()) {
                    signature_value = signaturePad.toDataURL();
                }
                $('#signature').val(signature_value);

                // GANTI SERIALIZE DENGAN FORMDATA DI SINI
                let formElement = $('#form_lembur_lembur')[0]; // Ambil elemen DOM form
                let formData = new FormData(formElement); // Bungkus semua input + file + @csrf
                $('#signature').val(signature_value);
                $.ajax({
                    url: "{{ url('/lembur/proses_pengajuan_lembur') }}" + '/' + menu_active,
                    type: "POST",
                    data: formData, // Gunakan objek FormData
                    processData: false, // WAJIB: Mencegah jQuery mengubah FormData jadi string
                    contentType: false, // WAJIB: Biarkan browser mengatur header multipart/form-data
                    // Menampilkan loading selama proses pengajuan berlangsung.
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Memuat Data...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    // Menampilkan pesan ketika pengajuan gagal diproses server.
                    error: function(xhr) {
                        Swal.close();
                        // Tampilkan detail error jika CSRF atau server error terjadi
                        // console.error("XHR Response:", xhr.responseText);
                        alert('Terjadi kesalahan pada server!');
                    },
                    // Menangani response berhasil, validasi, atau penolakan pengajuan.
                    success: function(data) {
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: data.message,
                                icon: 'success',
                                timer: 5000
                            }).then(() => {
                                location.reload();
                            });
                        } else if (data.code == 400) {
                            let errors = data.errors;
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            Swal.fire({
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            });
                        } else if (data.code == 510) {
                            Swal.fire({
                                text: data.message,
                                icon: 'warning',
                                timer: 4500
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: data.error,
                                icon: 'error',
                                timer: 4500
                            });
                        }
                    }
                });
            });
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


            // Menampilkan placeholder animasi pada daftar lembur.
            function showSkeleton(selector) {
                const el = $(selector);
                el.html(
                    `<span class="skeleton skeleton-number" style="width:100%"></span>`
                );
            }

            // Mengganti placeholder daftar lembur dengan data hasil request.
            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }
            load_data_lembur();

            // Mengubah objek tanggal menjadi format DD-MM-YYYY.
            function formatLabel(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();

                return `${day}-${month}-${year}`;
            }

            // Memuat daftar pengajuan lembur yang masih aktif.
            function load_data_lembur() {
                let lembur = '';
                $.ajax({
                    url: "{{ url('/lembur/get_lembur') }}" + '/' + menu_active,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.content-lembur');
                    },
                    // Menampilkan pesan jika daftar lembur aktif gagal dimuat.
                    error: function(data) {
                        // console.log(data);
                        showAlertDanger(data.responseJSON.message);
                        lembur +=
                            `<div class="text-center my-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.content-lembur', lembur);
                    },
                    // Membuat kartu lembur dari data yang diterima server.
                    success: function(data) {
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                lembur += `<div class="text-center text-muted py-3">
                        <i class="fa-solid fa-file-lines fa-bounce fa-xl"></i>
                        <p class="mb-0 mt-2 small">Belum ada data</p>
                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    // console.log(element.status_lembur);
                                    let progressValue = 25,
                                        progressColor = 'bg-primary';
                                    if (element.status_lembur == 1) {
                                        progressValue = 50;
                                    } else if (element.status_lembur == 2) {
                                        progressValue = 75;
                                        progressColor = 'bg-success';
                                    } else if (element.status_lembur == 3) {
                                        progressValue = 100;
                                        progressColor = 'bg-info';
                                    }

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    lembur += `<a class="btn_klik" href="{{ url('/lembur/detail_lembur') }}/${menu_active}/${element.id}">
                            <div class="coworker-card d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Lembur</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.tanggal_lembur}</span>
                                    </div>
                                    
                                    
                                    ${indicator}
                                </div>
                                <div class="icon-box" style="font-size: 12pt;"><i class="fa-regular fa-file-lines"></i></div>
                            </div>
                        </a>`;
                                });
                            }
                        } else {
                            showAlertDanger(data.message);
                            lembur +=
                                `<div class="text-center my-3"><small class="text-muted">${data.message}</small></div>`;
                        }
                        hideSkeleton('.content-lembur', lembur);
                    }
                });
            }

            // Menampilkan alert error sementara pada halaman.
            function showAlertDanger(message) {
                $('.alert_add_cuti').stop(true, true).hide().html(
                    `<div class="alert alert-danger light alert-md alert-dismissible fade show py-2-5 mb-3">${message}</div>`
                ).fadeIn(100);
                setTimeout(function() {
                    $('.alert_add_cuti').animate({
                        opacity: 0,
                        marginTop: '-8px'
                    }, 1000, function() {
                        $(this).slideUp('slow', function() {
                            $(this).html('').css({
                                'opacity': '',
                                'margin-top': ''
                            });
                        });
                    });
                }, 5000);
            }

            // Fungsi helper untuk menghindari duplikasi kode animasi alert yang panjang
            // Menampilkan alert error dengan ukuran dan durasi yang lebih ringkas.
            function showAlertDanger(message) {
                $('.alert_add_cuti').stop(true, true).hide().html(
                    `<div class="alert alert-danger light alert-sm alert-dismissible fade show py-2 mb-2">${message}</div>`
                ).fadeIn(100);
                setTimeout(function() {
                    $('.alert_add_cuti').animate({
                        opacity: 0,
                        marginTop: '-5px'
                    }, 1000, function() {
                        $(this).slideUp('slow', function() {
                            $(this).html('').css({
                                'opacity': '',
                                'margin-top': ''
                            });
                        });
                    });
                }, 4000); // Dikurangi sedikit durasinya agar lebih efisien
            }

            // Menjalankan pencarian histori berdasarkan filter bulan dan tahun.
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
                // Mengembalikan tampilan tombol setelah proses refresh dimulai.
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 500);
            });

            muatData()

            // Menghitung rentang tanggal dari filter bulan dan tahun terpilih.
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

                load_riwayat_lembur(awal, akhir)

            }

            // Memuat dan menampilkan histori lembur berdasarkan rentang tanggal.
            function load_riwayat_lembur(awal, akhir) {
                let lembur = '';
                $.ajax({
                    url: "{{ url('/lembur/get_lembur_riwayat') }}",
                    type: "GET",
                    data: {
                        awal: awal,
                        akhir: akhir,
                    },
                    beforeSend: function() {
                        showSkeleton('.riwayat_lembur');
                    },
                    // Menampilkan pesan jika histori lembur gagal dimuat.
                    error: function(data) {
                        showAlertDanger(data.responseJSON.message);
                        lembur +=
                            `<div class="text-center my-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.riwayat_lembur', lembur);
                    },
                    // Membuat kartu histori dan progress approval dari response server.
                    success: function(data) {
                        // console.log(data);
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                lembur += `<div class="text-center text-muted py-3">
                        <i class="fa-solid fa-file-lines fa-bounce fa-xl"></i>
                        <p class="mb-0 mt-2 small">Belum ada data</p>
                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    let progressValue = 25,
                                        progressColor = 'bg-primary';
                                    if (element.status_lembur == 1) {
                                        progressValue = 50;
                                    } else if (element.status_lembur == 2) {
                                        progressValue = 75;
                                        progressColor = 'bg-success';
                                    } else if (element.status_lembur == 3) {
                                        progressValue = 100;
                                        progressColor = 'bg-info';
                                    } else if (element.status_lembur == 5) {
                                        progressValue = 100;
                                        progressColor = 'bg-danger';
                                    }

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    lembur += `<a class="btn_klik" href="{{ url('/lembur/detail_lembur') }}/${menu_active}/${element.id}">
                            <div class="coworker-card-${element.status_lembur} d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1  ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Lembur</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.tanggal_lembur}</span>
                                    </div>
                                    
                                    
                                    ${indicator}
                                </div>
                                <div class="icon-box" style="font-size: 12pt;"><i class="fa-regular fa-file-lines"></i></div>
                            </div>
                        </a>`;
                                });
                            }
                        } else {
                            showAlertDanger(data.message);
                            lembur +=
                                `<div class="text-center my-3"><small class="text-muted">${data.message}</small></div>`;
                        }
                        hideSkeleton('.riwayat_lembur', lembur);
                    }
                });
            }

        });
    </script>
@endsection
