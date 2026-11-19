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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <style>
        .alert_approve_cuti {
            max-width: 100%;
            overflow-x: hidden;
        }


        .futuristic-header {
            padding: 20px 20px 5px;
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

        .approve-card {
            margin-top: 15px;
            transition: 0.3s;
        }

        .approve-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(124, 58, 237, 0.15);
        }

        /* HEADER */
        .approve-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .approve-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 15px rgba(124, 58, 237, 0.4);
            overflow: hidden;
            /* Tambahkan ini agar gambar tidak tidak_masuk jalur */
        }

        .approve-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Menjaga foto tetap proporsional (tidak lonjong) */
            object-position: center;
            /* Memastikan wajah tetap di tengah */
        }

        .badge-status {
            margin-left: auto;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-status.pending {
            background: #fff7ed;
            color: #f97316;
        }

        h6 {
            font-size: 0.9rem !important;
        }

        .badge-status.approved {
            background: #ecfdf5;
            color: #16a34a;
        }

        .badge-status.rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        /* INFO BOX */
        .info-box small {
            font-size: 12px;
            color: #888;
        }

        .info-box p {
            margin: 2px 0 0;
            font-weight: 600;
        }

        /* FOOTER */
        .approve-footer {
            margin-top: 18px;
        }

        .approval-note {
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
            height: 90px;
        }

        /* BUTTONS */
        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-approve {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(34, 197, 94, 0.4);
        }

        .btn-reject {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-pdf {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(239, 68, 68, 0.4);
        }


        .daterangepicker.ltr.show-calendar {
            top: 0px !important;
        }

        .watermark-logo {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Menaruh titik tengah logo di titik tengah card */
            opacity: 0.08;
            /* Mengatur transparansi (0.0 - 1.0) */
            z-index: -1;
            /* Menaruh logo di belakang teks/konten */
            pointer-events: none;
            /* Penting! Agar user tetap bisa klik tombol/input di atasnya */
            user-select: none;
            /* Agar gambar tidak bisa di-drag/pilih */
            width: 50%;
            /* Atur ukuran logo sesuai keinginan */
        }

        .watermark-logo img {
            width: 100%;
            height: auto;
            /* filter: grayscale(100%); */
            /* Opsional: membuat logo jadi hitam putih agar lebih samar */
        }

        .approve-footer {
            margin-top: 18px;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon title_icon">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <h5 class="mt-3 mb-1">Detail Tidak Masuk</h5>
            <small class="text-muted">Informasi Detail Pengajuan Tidak Masuk</small>
        </div>
        <div class="alert_approve_cuti"></div>

        <div class="content-approve-cuti px-3 px-md-4">
        </div>
        <div class="card-body px-3 px-md-4">
            <form class="my-2" method="post" id="form_approve_tidak_masuk" enctype="multipart/form-data">
                @csrf
                <input type="text" name="id_tidak_masuk" id="id_tidak_masuk" value="{{ $data->id }}" hidden>
                <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
                <!-- Form input -->
                <div class="col-12">
                    <div class="p-3 bg-white rounded shadow-sm">
                        <!-- Item: Pengaju -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-user me-2 text-primary"></i> Pengaju Form Tidak Masuk
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                {{ $data->nama_karyawan }}
                            </div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-user me-2 text-primary"></i> Karyawan Pengganti
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                {{ $data->nama_karyawan_pengganti }}
                            </div>
                        </div>

                        <!-- Item: Jenis Cuti -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-tags me-2 text-primary"></i> Izin
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                IZIN TIDAK MASUK
                            </div>
                        </div>

                        <!-- Item: Tanggal -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i> Dibuat Pada
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                {{ $data->created_at }}
                            </div>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-clock me-2"></i> Tanggal Tidak Masuk
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                @foreach ($data->IzinTidakMasukDetail as $detail)
                                    <p>{{ $detail->tgl_tidak_masuk }},</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                                <i class="fa-solid fa-note-sticky me-2 text-primary"></i> Keterangan
                            </div>
                            <div class="fs-6 fw-semibold text-dark ps-4">
                                {{ $data->keterangan_tidak_masuk }}
                            </div>
                        </div>
                    </div>
                    <div class="alert_keterangan_cuti"></div>

                </div>

                <div class="alert_error_cuti col-12"></div>
                @if ($data->status_tidak_masuk == 0)
                    @if ($data->id_user_atasan == $user_karyawan->id && $data->ttd_atasan == null)
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">2</span>
                                <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                            </div>

                            <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                                <div class="card-header bg-light border-0 py-2 text-center">
                                    <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                        id="user_pengajuan">DISAHKAN OLEH-1 : {{ $user_karyawan->name }}</small>
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
                                        <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di
                                            atas</small>
                                    </div>
                                    <textarea id="signature" name="signature" style="display: none"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 d-flex align-items-stretch">
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_user) }}" alt="">
                                    </div>
                                    <input type="hidden" name="atasan_1" id="atasan_1" value="{{ $data->karyawan_id }}">
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan1">{{ $data->nama_karyawan ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                    </div>
                                    <input type="hidden" name="atasan_2" id="atasan_2"
                                        value="{{ $data->id_user_atasan2 }}">
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan2">{{ $data->nama_atasan2 ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-3:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan1">{{ $data->nama_hrd ?? 'HRD' }}</small>
                                </div>
                            </div>

                        </div>
                        <div class="mt-3 text-center">
                            <button type="button" id="btn_approve_konfirmasi_tidak_masuk"
                                class="btn light btn-success btn-sm">
                                <i class="fa-regular fa-circle-check me-1"></i>Setujui
                            </button>
                            <button type="button" id="btn_tolak_konfirmasi_tidak_masuk"
                                class="btn light btn-danger btn-sm">
                                <i class="fa fa-trash me-1"></i>Tolak
                            </button>
                        </div>
                    @endif
                @elseif ($data->status_tidak_masuk == 1)
                    @if ($data->id_user_atasan == $user_karyawan->id && $data->ttd_atasan != null)
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">2</span>
                                <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                                <span class="badge bg-success mx-2">Diterima Atasan 1</span>
                            </div>
                            <div class="row g-2 d-flex align-items-stretch">
                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('storage/signatures/' . $data->ttd_user) }}"
                                                alt="">
                                        </div>

                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">{{ $data->nama_karyawan ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('storage/signatures/' . $data->ttd_atasan) }}"
                                                alt="">
                                        </div>

                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan2">{{ $data->nama_atasan ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('storage/signatures/' . $data->ttd_atasan2) }}"
                                                alt="">
                                        </div>

                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">{{ $data->nama_atasan2 ?? '-' }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-3:</small>
                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('storage/signatures/' . $data->hrd) }}" alt="">
                                        </div>

                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">{{ $data->nama_hrd ?? 'HRD' }}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">2</span>
                                <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                            </div>

                            <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                                <div class="card-header bg-light border-0 py-2 text-center">
                                    <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                        id="user_pengajuan">DISAHKAN OLEH-2 : {{ $user_karyawan->name }}</small>
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
                        </div>

                        <div class="row g-2 d-flex align-items-stretch">
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_user) }}" alt="">
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan1">{{ $data->nama_karyawan ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_atasan) }}" alt="">
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan2">{{ $data->nama_atasan ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-3:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        @if ($data->ttd_hrd)
                                            <!-- Jika ttd_atasan ada nilainya, tampilkan gambarnya -->
                                            <img src="{{ asset('storage/signatures/' . $data->ttd_hrd) }}"
                                                alt="Tanda Tangan">
                                        @else
                                            <!-- Jika ttd_user kosong/tidak ada, tampilkan icon clock-rotate-left -->
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        @endif
                                    </div>
                                    {{-- <input type="hidden" name="atasan_2" id="atasan_2"
                                        value="{{ $data->id_user_hrd }}"> --}}
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="hrd">{{ $data->nama_hrd ?? 'HRD' }}</small>
                                </div>
                            </div>

                        </div>
                        <div class="mt-3 text-center">
                            <button type="button" id="btn_approve_konfirmasi_tidak_masuk"
                                class="btn light btn-success btn-sm">
                                <i class="fa-regular fa-circle-check me-1"></i>Setujui
                            </button>
                            <button type="button" id="btn_tolak_konfirmasi_tidak_masuk"
                                class="btn light btn-danger btn-sm">
                                <i class="fa fa-trash me-1"></i>Tolak
                            </button>
                        </div>
                    @endif
                @else
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">2</span>
                            <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                            @if ($data->status_tidak_masuk == 2)
                                <span class="badge bg-success mx-2">Diterima Atasan 2</span>
                            @elseif ($data->status_tidak_masuk == 3)
                                <span class="badge bg-success mx-2">Diterima HRD</span>
                            @else
                                <span class="badge bg-danger mx-2">Ditolak</span>
                            @endif
                        </div>
                        <div class="row g-2 d-flex align-items-stretch">
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_user) }}" alt="">
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan1">{{ $data->nama_karyawan ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_atasan) }}" alt="">
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan2">{{ $data->nama_atasan ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('storage/signatures/' . $data->ttd_atasan2) }}"
                                            alt="">
                                    </div>

                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="atasan1">{{ $data->nama_atasan2 ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-3:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        @if ($data->ttd_hrd)
                                            <!-- Jika ttd_atasan ada nilainya, tampilkan gambarnya -->
                                            <img src="{{ asset('storage/signatures/' . $data->ttd_hrd) }}"
                                                alt="Tanda Tangan">
                                        @else
                                            <!-- Jika ttd_user kosong/tidak ada, tampilkan icon clock-rotate-left -->
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        @endif
                                    </div>
                                    {{-- <input type="hidden" name="atasan_2" id="atasan_2"
                                        value="{{ $data->id_user_hrd }}"> --}}
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="hrd">{{ $data->nama_hrd ?? 'HRD' }}</small>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif



            </form>
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_setuju" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah anda ingin menyetujui pengajuan tidak_masuk Ini ?</p>
            <button type="button" id="btn_approve_tidak_masuk" class="btn btn-sm btn-success light pwa-btn"
                data-bs-dismiss="offcanvas">
                <i class="fa-regular fa-circle-check me-1"></i>&nbsp;Setujui
            </button>
            <button type="button" class="btn btn-sm light btn-danger me-1" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal</button>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_tolak" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah anda ingin menolak pengajuan tidak_masuk Ini ?</p>
            <button type="button" id="btn_tolak_tidak_masuk" class="btn btn-sm btn-danger light pwa-btn"
                data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-trash me-1"></i>
                &nbsp;Tolak
            </button>
            <button type="button" class="btn btn-sm light btn-primary me-1" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal</button>
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

    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });


        $(document).ready(function() {
            let id_tidak_masuk = '{{ $id }}';

            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas);

            $('#signature-pad').removeAttr('hidden');

            $('#clear').on('click', () => signaturePad.clear());

            $(document).on('click', '#btn_approve_konfirmasi_tidak_masuk', function(e) {
                const offcanvasEl = document.getElementById('offcanvas_setuju');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            });
            $(document).on('click', '#btn_tolak_konfirmasi_tidak_masuk', function(e) {
                const offcanvasEl = document.getElementById('offcanvas_tolak');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            });

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

            $(document).on('click', '#btn_tolak_tidak_masuk', function(e) {
                let btn = $(this);
                let originalText = btn.html();

                let signature_value = '';
                if (!signaturePad.isEmpty()) {
                    signature_value = signaturePad.toDataURL();
                }
                $('#signature').val(signature_value);
                $.ajax({
                    url: "{{ url('/izin/tolak_pengajuan_tidak_masuk') }}",
                    type: "POST",
                    data: $('#form_approve_tidak_masuk').serialize(),
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
                    error: function() {
                        Swal.close();
                        alert('Something is wrong!');
                        // console.log(formData);
                    },
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
                            // console.log(errors);
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            Swal.fire({
                                // title: data.message,
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            })

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
                            })

                        }
                    }

                })
            });
            $(document).on('click', '#btn_approve_tidak_masuk', function(e) {
                let btn = $(this);
                let originalText = btn.html();

                let signature_value = '';
                if (!signaturePad.isEmpty()) {
                    signature_value = signaturePad.toDataURL();
                }
                $('#signature').val(signature_value);
                $.ajax({
                    url: "{{ url('/izin/approve_pengajuan_tidak_masuk') }}",
                    type: "POST",
                    data: $('#form_approve_tidak_masuk').serialize(),
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
                    error: function() {
                        Swal.close();
                        alert('Something is wrong!');
                        // console.log(formData);
                    },
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
                            // console.log(errors);
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            Swal.fire({
                                // title: data.message,
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            })


                        } else if (data.code == 401) {

                            Swal.fire({
                                text: data.message,
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
                            })

                        }
                    }

                })
            });

        });
    </script>
@endsection
