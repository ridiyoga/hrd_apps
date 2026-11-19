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

        .border-dashed {
            border: 2px dashed #d8b4fe !important;
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
            padding: 10px 16px;
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
            font-size: 16px;
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

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-reject {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #981515, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
    </style>
@endsection
@section('content_top')
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <h5 class="mt-3 mb-1">LEMBUR</h5>
        <small class="text-muted">Detail Pengajuan Lembur</small>
    </div>
    <div id="alert_edit_cuti">
    </div>
    <form class="my-2" method="post" id="form_edit_cuti" enctype="multipart/form-data">
        @csrf
        {{-- <input type="hidden" name="id_karyawan" value="{{ $data->Lembur->karyawan_id }}"> --}}
        <input type="text" name="id_cuti" id="id_cuti" value="{{ $data->id }}" hidden>
        <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
        <!-- Form input -->
        <div class="p-2 bg-white border rounded-3 shadow-sm mb-3">
            <div class="row g-3">
                <!-- Pengaju -->
                <div class="col-12">
                    <div class="border-start border-3 border-primary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Karyawan</small>
                        <span class="fs-6 fw-bold text-dark">{{ $data->nama_karyawan }}</span>
                    </div>
                </div>

                <!-- Tanggal Lembur -->
                <div class="col-12">
                    <div class="border-start border-3 border-primary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Tanggal Lembur</small>
                        <span class="fs-6 fw-bold text-dark">
                            <i class="fa-regular fa-calendar me-1 text-primary"></i>
                            {{ \Carbon\Carbon::parse($data->tgl_lembur)->format('d-m-Y') }}
                        </span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="col-12 my-1">
                    <hr class="my-0 text-muted opacity-25">
                </div>

                <!-- Keterangan -->
                <div class="col-12">
                    <div class="border-start border-3 border-secondary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Keterangan</small>
                        <p class="mb-0 fs-6 text-dark fw-normal">
                            {{ $data->Lembur->keterangan_lembur ?? '-' }}
                        </p>
                    </div>
                </div>
                <!-- Keterangan -->
                <div class="col-12">
                    <div class="border-start border-3 border-secondary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Presensi Masuk</small>
                        <p class="mb-0 fs-6 text-dark fw-normal">
                            {{ $data->presensi_masuk ?? '-' }}
                        </p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border-start border-3 border-secondary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Presensi Keluar</small>
                        <p class="mb-0 fs-6 text-dark fw-normal">
                            {{ $data->presensi_pulang ?? '-' }}
                        </p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border-start border-3 border-secondary ps-2">
                        <small class="text-muted d-block fw-semibold text-uppercase style-8px">Realisasi Lembur
                            (Jam)</small>
                        <p class="mb-0 fs-6 text-dark fw-normal">
                            {{ $data->selisih_convert ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <!-- Header Sub-seksi -->
            <div class="d-flex align-items-center mb-2 text-muted small fw-bold text-uppercase">
                <i class="fa-solid fa-clock me-2 text-primary"></i> Detail Jadwal Lembur
            </div>

            <!-- Container Card List -->
            <div class="d-flex flex-column gap-2">
                <div class="p-3 bg-light border rounded-3 shadow-sm">
                    <!-- Baris Nama Karyawan & Durasi -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark fs-6 text-truncate">
                            <i class="fa-solid fa-user-tag me-1 text-secondary small"></i>
                            {{ $data->nama_karyawan }}
                        </span>

                        <!-- Badge Durasi Lembur -->

                    </div>
                    <span
                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small fw-semibold">
                        <i class="fa-solid fa-business-time me-1"></i>
                        Perkiraan Lembur (Jam) : {{ \Carbon\Carbon::parse($data->perkiraan)->format('H:i') }}
                    </span>

                    <!-- Baris Jam Operasional -->
                    <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border small">
                        <!-- Jam Mulai -->
                        <div class="text-center flex-fill border-end">
                            <span class="text-muted d-block style-8px text-uppercase fw-semibold">Mulai</span>
                            <span class="fw-bold text-success">
                                <i
                                    class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($data->jam_mulai)->format('H:i') }}
                            </span>
                        </div>

                        <!-- Jam Selesai -->
                        <div class="text-center flex-fill">
                            <span class="text-muted d-block style-8px text-uppercase fw-semibold">Selesai</span>
                            <span class="fw-bold text-danger">
                                <i
                                    class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($data->jam_selesai)->format('H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert_keterangan_cuti"></div>
        @if ($data->status_lembur == 3)
            <div class="action-buttons my-4">
                <a href="{{ url('lembur/cetak_form_lembur') . '/' . $data->id . '/' . $data->karyawan_id }}"
                    class="btn btn-reject" id="btn_unduh_pdf">
                    <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                </a>
            </div>
        @endif
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">2</span>
                <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                @if ($data->status == 1)
                    <span class="badge bg-success mx-2">Diterima HRD</span>
                @elseif ($data->status == 2)
                    <span class="badge bg-danger mx-2">Ditolak</span>
                @endif
            </div>


            <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                <div class="card-header bg-light border-0 py-2 text-center">
                    <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                        id="user_pengajuan">Diajukan Oleh: {{ $data->Lembur->nama_karyawan }}</small>
                </div>
                <div class="card-body p-2">
                    <div class="alert_signature"></div>
                    <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                        <canvas id="signature-pad"
                            style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                            hidden></canvas>
                        <img src="{{ asset('storage/signatures/' . $data->Lembur->ttd_user) }}" alt="">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                        <small class="text-muted italic" style="font-size: 10px;">Tanda tangan</small>
                    </div>
                    <textarea id="signature" name="signature" style="display: none"></textarea>
                </div>
            </div>

            <div class="row g-2 d-flex align-items-stretch">
                <div class="col-6">
                    <div
                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if ($data->Lembur->ttd_atasan)
                                <!-- Jika ttd_atasan ada nilainya, tampilkan gambarnya -->
                                <img src="{{ asset('storage/signatures/' . $data->Lembur->ttd_atasan) }}"
                                    alt="Tanda Tangan">
                            @else
                                <!-- Jika ttd_atasan kosong/tidak ada, tampilkan icon clock-rotate-left -->
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            @endif
                        </div>
                        <input type="hidden" name="atasan_1" id="atasan_1"
                            value="{{ $data->Lembur->id_user_atasan }}">
                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                            id="atasan1">{{ $data->Lembur->nama_atasan ?? '-' }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div
                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if ($data->Lembur->ttd_atasan2)
                                <!-- Jika ttd_atasan ada nilainya, tampilkan gambarnya -->
                                <img src="{{ asset('storage/signatures/' . $data->Lembur->ttd_atasan2) }}"
                                    alt="Tanda Tangan">
                            @else
                                <!-- Jika ttd_user kosong/tidak ada, tampilkan icon clock-rotate-left -->
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            @endif
                        </div>
                        <input type="hidden" name="atasan_2" id="atasan_2" value="{{ $data->id_user_atasan2 }}">
                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                            id="atasan2">{{ $data->Lembur->nama_atasan2 ?? '-' }}</small>
                    </div>
                </div>
                <div class="col-6">
                    <div
                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-3:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if ($data->Lembur->ttd_hrd)
                                <!-- Jika ttd_atasan ada nilainya, tampilkan gambarnya -->
                                <img src="{{ asset('storage/signatures/' . $data->Lembur->ttd_hrd) }}"
                                    alt="Tanda Tangan">
                            @else
                                <!-- Jika ttd_user kosong/tidak ada, tampilkan icon clock-rotate-left -->
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            @endif
                        </div>
                        {{-- <input type="hidden" name="atasan_2" id="atasan_2"
                                        value="{{ $data->Lembur->id_user_hrd }}"> --}}
                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                            id="hrd">{{ $data->Lembur->nama_hrd ?? 'HRD' }}</small>
                    </div>
                </div>

            </div>
        </div>

        <div class="alert_error_cuti col-12"></div>
        <div class="mt-3 text-center">
            @if ($data->status_lembur == 0)
                <button type="button" id="btn_konfirmasi_delete_lembur" data-id_hapus="{{ $data->id }}"
                    class="btn light btn-danger btn-sm">
                    <i class="fa fa-trash me-1"></i>Hapus
                </button>
            @endif
            <!-- <div class="dz-spinner d-flex align-items-center flex-wrap"> -->
            {{-- <button type="button" id="btn_simpan_cuti" class="btn light btn-success btn-sm me-2">
                            <i class="fa fa-save me-1"></i>Simpan&nbsp;Perubahan
                        </button> --}}
            <!-- </div> -->
        </div>
        </div>
    </form>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom" style="height: 60%;" data-bs-keyboard="false" data-bs-backdrop="true"
        id="off-canvas-ttd">

        <div class="offcanvas-header">
            <h5 class="offcanvas-title">FORM TTD</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas">X</button>
        </div>
        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div class="lihat_ttd text-center">
                <img src="" id="ttd_user" alt="Lihat TTD" class="img-fluid" style="width: 100%">
                <h6 id="nama_users"></h6>
                <span id="tgl_users"></span>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_hapus" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah Anda Ingin Hapus Pengajuan Lembur Ini ?</p>
            <form method="POST" id="form_hapus_lembur" action="{{ url('lembur/proses_hapus_lembur/' . $menu_id) }}">
                @csrf
                <input type="hidden" id="id_lembur_hapus" name="id_lembur_hapus" value="">
                <button type="button" id="btn_delete_lembur" class="btn btn-sm btn-danger light pwa-btn">
                    <i class="fa-solid fa-trash me-1"></i>
                    &nbsp;Hapus
                </button>
                <button type="button" class="btn btn-sm light btn-primary me-1" data-bs-dismiss="offcanvas"
                    aria-label="Close">Batal</button>
            </form>
        </div>
    </div>
    @include('users.logout_confirm')
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        $(document).ready(function() {
            let id = '{{ $data->id }}';

            $(document).on('click', '#btn_konfirmasi_delete_lembur', function(e) {
                var id_hapus = $(this).data('id_hapus');
                $('#id_lembur_hapus').val(id_hapus);
                const offcanvasEl = document.getElementById('offcanvas_hapus');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            });
            $(document).on('click', '#btn_delete_lembur', function(e) {
                $('#form_hapus_lembur').submit();
            });

        });
    </script>
@endsection
