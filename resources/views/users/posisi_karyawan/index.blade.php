@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
<link rel="stylesheet" type="text/css" href="{{asset('assets/assets_users/css/select2.css')}}">
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
    #off-canvas-posisi {
        z-index: 1060 !important;
    }

    .offcanvas-backdrop+.offcanvas-backdrop {
        z-index: 1070 !important;
        opacity: 0.2 !important;
        /* Buat backdrop kedua lebih tipis agar tidak gelap total */
    }

    .form-input .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #a855f7;
        /* Gunakan warna tema tapi lebih cerah */
        font-size: 14px;
        z-index: 5;
        opacity: 0.7;
        /* Biar lebih smooth */
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

    /* #sig canvas {
        margin-top: 5px;
        width: 100%;
        height: auto;
    } */

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
</style>
@endsection
@section('content_top')
<div class="card">
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-users-gear"></i>
        </div>
        <h5 class="mt-3 mb-1">UPDATE POSISI</h5>
        <small class="text-muted">Informasi Update Posisi Tim (Mutasi, Demosi, Promosi, Rotasi)</small>
    </div>

    <div class="alert_update_posisi"></div>

    <div class="card-body px-3 px-md-4">
        <ul class="nav profile-tabs" id="profileTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-posisi" role="tab" aria-controls="nav-posisi" aria-selected="true">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Pengajuan</span>
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-histori" role="tab" aria-controls="nav-histori" aria-selected="false">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>History</span>
                </button>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="nav-posisi" role="tabpanel" aria-labelledby="nav-posisi-tab">
                <button class="btn btn-sm light btn-primary mb-3" type="button" id="btn_tambah_posisi"><i class="fa-solid fa-plus me-2"></i>Tambah</button>
                <div class="content-posisi"></div>
            </div>
            <div class="tab-pane fade" id="nav-histori" role="tabpanel" aria-labelledby="nav-history-tab">
                <div class="content-history">
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-posisi" style="height: 95vh;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Form Perubahan Status</h6>
                <small class="text-muted">Pengajuan Perubahan Status Karyawan...</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-4">
        <form id="formUpdatePosisi" method="POST">
            @csrf
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">1</span>
                    <h6 class="mb-0 fw-bold">Data Karyawan</h6>
                </div>

                <div class="form-input mb-3">
                    <i class="fa-solid fa-user input-icon"></i>
                    <select class="form-control select2-ins" name="user_dituju" id="user_dituju">
                        <option value="">Pilih Nama Karyawan...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="alert-karyawan"></div>
                </div>
                <div id="info_karyawan_lama" class="p-3 bg-light rounded-4 d-none">
                    <div class="row g-2 small">
                        <div class="col-6 text-muted">NIK: <small class="text-dark fw-bold" id="old_nik">-</small></div>
                        <div class="col-6 text-muted">Penempatan: <small class="text-dark fw-bold" id="old_penempatan">-</small></div>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6 text-muted">Dept: <small class="text-dark fw-bold" id="old_departemen">-</small></div>
                        <div class="col-6 text-muted">Divisi: <small class="text-dark fw-bold" id="old_divisi">-</small></div>
                        <div class="col-6 text-muted">Bagian: <small class="text-dark fw-bold" id="old_bagian">-</small></div>
                        <div class="col-6 text-muted">Jabatan: <small class="text-dark fw-bold" id="old_jabatan">-</small></div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">2</span>
                    <h6 class="mb-0 fw-bold">Jenis Perubahan Posisi</h6>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="perubahan_posisi" id="mutasi" value="Mutasi">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="mutasi">Mutasi</label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="perubahan_posisi" id="rotasi" value="Rotasi">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="rotasi">Rotasi</label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="perubahan_posisi" id="demosi" value="Demosi">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="demosi">Demosi</label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="perubahan_posisi" id="promosi" value="Promosi">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="promosi">Promosi</label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">3</span>
                    <h6 class="mb-0 fw-bold">Detail Posisi Baru</h6>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-building input-icon"></i>
                    <select class="form-control select2-ins" name="penempatan_holding_baru" id="penempatan_holding_baru">
                        <option value="">Pilih Penempatan Baru...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="alert-penempatan"></div>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-location-dot input-icon"></i>
                    <select class="form-control select2-ins" name="lokasi_baru" id="lokasi_baru">
                        <option value="">Pilih Lokasi</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="alert-lokasi_baru"></div>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-sitemap input-icon"></i>
                    <select id="departemen_baru" class="form-control" name="departemen_baru" placeholder="Departemen Baru">
                        <option value="">Pilih Departemen Baru...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-sitemap input-icon"></i>
                    <select id="divisi_baru" class="form-control" name="divisi_baru" placeholder="Divisi Baru">
                        <option value="">Pilih Divisi Baru...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-sitemap input-icon"></i>
                    <select id="bagian_baru" class="form-control" name="bagian_baru" placeholder="Bagian Baru">
                        <option value="">Pilih Bagian Baru...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-briefcase input-icon"></i>
                    <select id="jabatan_baru" class="form-control" name="jabatan_baru" placeholder="Jabatan Baru">
                        <option value="">Pilih Jabatan Baru...</option>
                    </select>
                    <!-- loader -->
                    <div class="select-loader d-none">
                        <div class="dot-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">4</span>
                    <h6 class="mb-0 fw-bold">Gaji & Masa Percobaan</h6>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-calendar input-icon"></i>
                    <select class="form-control" name="masa_percobaan" id="masa_percobaan">
                        <option value="">-- Pilih Masa Percobaan --</option>
                        <option value="1"> 1 Bulan</option>
                        <option value="2"> 2 Bulan</option>
                        <option value="3"> 3 Bulan</option>
                        <option value="4"> 4 Bulan</option>
                        <option value="5"> 5 Bulan</option>
                        <option value="6"> 6 Bulan</option>
                        <option value="7"> 7 Bulan</option>
                        <option value="8"> 8 Bulan</option>
                        <option value="9"> 9 Bulan</option>
                        <option value="10"> 10 Bulan</option>
                        <option value="11"> 11 Bulan</option>
                        <option value="12"> 12 Bulan</option>
                    </select>
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-calendar-days input-icon"></i>
                    <input type="text" class="form-control" name="periode" id="periode" placeholder="Mulai Percobaan">
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-money-bill-wave input-icon"></i>
                    <input type="text" class="form-control" name="gaji" id="gaji" placeholder="Gaji Baru (Rp)">
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-hand-holding-dollar input-icon"></i>
                    <input type="text" class="form-control" name="tunjangan" id="tunjangan" placeholder="Tunjangan Baru (Rp)">
                </div>
                <div class="form-input">
                    <i class="fa-solid fa-box-open input-icon"></i>
                    <input type="text" class="form-control" name="fasilitas" id="fasilitas" placeholder="Fasilitas Lainnya">
                </div>
            </div>
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">5</span>
                    <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                </div>

                <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-light border-0 py-2 text-center">
                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;" id="manager_selected">Diajukan Oleh: </small>
                    </div>
                    <div class="card-body p-2">
                        <div class="alert_signature"></div>
                        <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                            <canvas id="signature-pad" style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;" hidden></canvas>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="clear">
                                <i class="fa-solid fa-eraser me-1"></i> Hapus
                            </button>
                            <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                        </div>
                        <textarea id="signature" name="signature" style="display: none"></textarea>
                    </div>
                </div>

                <div class="row g-2 d-flex align-items-stretch">
                    <div class="col-4">
                        <div class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                            <small class="d-block text-muted" style="font-size: 8px;">DISETUJUI:</small>
                            <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            </div>
                            <input type="hidden" name="id_direktur_selected" id="id_direktur_selected" value="">
                            <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="direktur_selected">DIREKTUR</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                            <small class="d-block text-muted" style="font-size: 8px;">DIKETAHUI:</small>
                            <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            </div>
                            <input type="hidden" name="id_manager_hrd_selected" id="id_manager_hrd_selected" value="">
                            <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="manager_hrd_selected">MGR HRD</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                            <small class="d-block text-muted" style="font-size: 8px;">PERNYATAAN:</small>
                            <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                            </div>
                            <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="karyawan_selected">KARYAWAN</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="btn_save_update_posisi" class="btn btn-primary btn-sm w-100 py-3 rounded-pill fw-bold mb-2 shadow">
                AJUKAN PERUBAHAN STATUS
            </button>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-konfirmasi-save" style="height: 50vh;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi</h6>
                <small class="text-muted">Pengajuan Perubahan Status Karyawan...</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-2">
        <div class="p-3 rounded-4" style="background-color: #f8f7ff; border: 1px solid #e9e5ff;">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-circle-info text-primary me-2" style="font-size: 14px;"></i>
                <h6 class="mb-0 fw-bold" style="font-size: 12px; color: #5b21b6;">KETENTUAN PERUBAHAN STATUS</h6>
            </div>

            <div class="description-text">
                <p class="mb-2">
                    Apabila selama <strong>masa percobaan</strong> dinyatakan <span class="text-danger">gagal</span>, maka ketentuan jabatan/fasilitas akan <strong>dikembalikan ke posisi semula</strong>.
                </p>
                <div class="d-flex gap-2 align-items-start bg-white p-2 rounded-3 border-light shadow-sm">
                    <i class="fa-solid fa-bullseye mt-1 text-primary" style="font-size: 10px;"></i>
                    <p class="mb-0">
                        Target dan tanggung jawab baru akan diberikan langsung oleh <strong>Manager Departemen</strong> terkait dan dievaluasi oleh <strong>Direktur Operasional</strong>.
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-3 text-center">
            <button type="button" id="btn_simpan_posisi" class="btn light btn-success btn-sm me-2">
                <i class="fa-solid fa-save me-2"></i>Simpan
            </button>
            <button type="button" class="btn light btn-danger btn-sm" data-bs-dismiss="offcanvas" data-bs-target="#off-canvas-konfirmasi-save">
                <i class="fa-solid fa-arrow-left me-2"></i>Batal
            </button>
            <!-- <div class="dz-spinner d-flex align-items-center flex-wrap"> -->
            <!-- </div> -->
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();" class="btn_klik btn btn-sm btn-danger light pwa-btn">
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
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);
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
        $('#masa_percobaan').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Masa Percobaan",
            allowClear: true
        });
        $('#penempatan_holding_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Penempatan",
            allowClear: true
        });
        $('#departemen_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Departemen",
            allowClear: true
        });
        $('#lokasi_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Lokasi",
            allowClear: true
        });
        $('#divisi_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Divisi",
            allowClear: true
        });
        $('#bagian_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Bagian",
            allowClear: true
        });
        $('#jabatan_baru').select2({
            dropdownParent: $('#off-canvas-posisi'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Jabatan",
            allowClear: true
        });
        // Simulasi penarikan data karyawan lama saat nama dipilih
        $('#user_dituju').on('change', function() {
            if ($(this).val() != "") {
                $('#info_karyawan_lama').removeClass('d-none').hide().fadeIn();
                // Contoh data statis, nanti ganti dengan hasil AJAX
                $.ajax({
                    url: "{{ url('update_posisi/get_change_karyawan') }}" + '/' + $(this).val(),
                    type: "GET",
                    data: {
                        id: $(this).val(),
                    },
                    dataType: 'json',
                    success: function(data) {
                        // console.log(data);
                        if (data.code == 200) {
                            $('#old_nik').html('<br>' + data.data.nik);
                            $('#old_penempatan').html('<br>' + data.data.penempatan);
                            $('#old_departemen').html('<br>' + data.data.departemen);
                            $('#old_divisi').html('<br>' + data.data.divisi);
                            $('#old_bagian  ').html('<br>' + data.data.bagian);
                            $('#old_jabatan').html('<br>' + data.data.jabatan);
                            $('#karyawan_selected').html(data.data.name);
                        } else {
                            $('#old_nik').html('<br>' + '-');
                            $('#old_penempatan').html('<br>' + '-');
                            $('#old_departemen').html('<br>' + '-');
                            $('#old_divisi').html('<br>' + '-');
                            $('#old_bagian  ').html('<br>' + '-');
                            $('#old_jabatan').html('<br>' + '-');
                        }
                    }
                })
            } else {
                $('#info_karyawan_lama').addClass('d-none');
            }
        });
        $('#penempatan_holding_baru').on('change', function() {
            // console.log('ok');
            showSelectLoading('#departemen_baru');
            showSelectLoading('#divisi_baru');
            showSelectLoading('#bagian_baru');
            showSelectLoading('#jabatan_baru');
            showSelectLoading('#lokasi_baru');
            // Contoh data statis, nanti ganti dengan hasil AJAX
            $.ajax({
                url: "{{ url('update_posisi/get_departemen') }}",
                type: "GET",
                data: {
                    id: $(this).val(),
                },
                dataType: 'json',
                error: function(err) {
                    Swal.fire({
                        title: 'error',
                        text: err.responseJSON.message,
                        icon: 'error',
                        timer: 6000
                    })
                },
                success: function(data) {
                    hideSelectLoading('#departemen_baru');
                    hideSelectLoading('#lokasi_baru');
                    if (data.code == 200) {
                        let opt_dept = '<option value="">-- Pilih Departemen --</option>';
                        let opt_lokasi = '<option value="">-- Pilih Lokasi --</option>';
                        data.data.forEach(element => {
                            opt_dept += '<option value="' + element.id + '">' + element.nama_departemen + '</option>';
                        })
                        data.site.forEach(element => {
                            opt_lokasi += '<option value="' + element.id + '">' + element.site_name + '</option>';
                        })
                        $('#departemen_baru').html(opt_dept);
                        $('#lokasi_baru').html(opt_lokasi);
                        $('#divisi_baru').html('<option value="">-- Pilih Divisi --</option>');
                        $('#bagian_baru').html('<option value="">-- Pilih Bagian --</option>');
                        $('#jabatan_baru').html('<option value="">-- Pilih Jabatan --</option>');
                    }
                }
            })
        });
        $('#departemen_baru').on('change', function() {
            // console.log('ok');
            showSelectLoading('#divisi_baru');
            showSelectLoading('#bagian_baru');
            showSelectLoading('#jabatan_baru');
            // Contoh data statis, nanti ganti dengan hasil AJAX
            $.ajax({
                url: "{{ url('update_posisi/get_divisi') }}",
                type: "GET",
                data: {
                    id: $(this).val(),
                },
                dataType: 'json',
                error: function(err) {
                    Swal.fire({
                        title: 'error',
                        text: err.responseJSON.message,
                        icon: 'error',
                        timer: 6000
                    })
                },
                success: function(data) {
                    hideSelectLoading('#divisi_baru');
                    if (data.code == 200) {
                        let opt_divisi = '<option value="">-- Pilih Divisi --</option>';
                        data.data.forEach(element => {
                            opt_divisi += '<option value="' + element.id + '">' + element.nama_divisi + '</option>';
                        })
                        $('#divisi_baru').html(opt_divisi);
                        $('#bagian_baru').html('<option value="">-- Pilih Bagian --</option>');
                        $('#jabatan_baru').html('<option value="">-- Pilih Jabatan --</option>');
                    }
                }
            })

        });
        $('#divisi_baru').on('change', function() {
            // console.log('ok');
            showSelectLoading('#bagian_baru');
            showSelectLoading('#jabatan_baru');
            // Contoh data statis, nanti ganti dengan hasil AJAX
            $.ajax({
                url: "{{ url('update_posisi/get_bagian') }}",
                type: "GET",
                data: {
                    id: $(this).val(),
                },
                dataType: 'json',
                error: function(err) {
                    Swal.fire({
                        title: 'error',
                        text: err.responseJSON.message,
                        icon: 'error',
                        timer: 6000
                    })
                },
                success: function(data) {
                    hideSelectLoading('#bagian_baru');
                    if (data.code == 200) {
                        let opt_bagian = '<option value="">-- Pilih Bagian --</option>';
                        data.data.forEach(element => {
                            opt_bagian += '<option value="' + element.id + '">' + element.nama_bagian + '</option>';
                        })
                        $('#bagian_baru').html(opt_bagian);
                    }
                }
            })

        });
        $('#bagian_baru').on('change', function() {
            // console.log('ok');
            showSelectLoading('#jabatan_baru');
            // Contoh data statis, nanti ganti dengan hasil AJAX
            $.ajax({
                url: "{{ url('update_posisi/get_jabatan') }}",
                type: "GET",
                data: {
                    id: $(this).val(),
                },
                dataType: 'json',
                error: function(err) {
                    Swal.fire({
                        title: 'error',
                        text: err.responseJSON.message,
                        icon: 'error',
                        timer: 6000
                    })
                },
                success: function(data) {
                    hideSelectLoading('#jabatan_baru');
                    if (data.code == 200) {
                        let opt_jabatan = '<option value="">-- Pilih Jabatan --</option>';
                        data.data.forEach(element => {
                            opt_jabatan += '<option value="' + element.id + '">' + element.nama_jabatan + '</option>';
                        })
                        $('#jabatan_baru').html(opt_jabatan);
                    }
                }
            })

        });

        function initSelect2() {
            $('.select2-ins').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#off-canvas-posisi'),
                width: '100%'
            });
        }
        // DATE RANGE PICKER
        let cutiPicker;
        var user_karyawan_id = '{{ $user_karyawan->id }}';
        var start = moment();
        $('#periode').daterangepicker({
            drops: 'down',
            autoApply: false,
            singleDatePicker: true,
            startDate: start,
            endDate: start,
            minDate: start,
            locale: {
                format: 'YYYY-MM-DD'
            }
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

        function formatRibuan(angka) {
            var number_string = angka.replace(/[^0-9]/g, '').toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        // Terapkan ke input gaji dan tunjangan
        $(document).on('keyup', 'input[name="gaji"], input[name="tunjangan"]', function() {
            var val = $(this).val();
            $(this).val(formatRibuan(val));
        });

        function showSelectLoading(selectId) {
            $(selectId).prop('disabled', true)
                .closest('.form-input')
                .find('.select-loader')
                .removeClass('d-none');
        }

        function hideSelectLoading(selectId) {
            $(selectId).prop('disabled', false)
                .closest('.form-input')
                .find('.select-loader')
                .addClass('d-none');
        }
        load_data_history_approval();
        load_data_posisi();

        function load_data_approval() {
            $.ajax({
                url: "{{ url('/cuti/get_approval') }}" + '/' + menu_active,
                type: "GET",
                beforeSend: function() {
                    showSkeleton('#atasan1');
                    showSkeleton('#atasan2');
                },
                error: function(data) {
                    $('.alert-approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseText + '</div>').fadeIn(100);
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
                        atasan1 += data.atasan1 ? data.atasan1.karyawan ? data.atasan1.karyawan.name : '-' : '-';
                        atasan2 += data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan.name : '-' : '-';
                        id_atasan1 += data.atasan1 ? data.atasan1.karyawan ? data.atasan1.karyawan.id : '' : '';
                        id_atasan2 += data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan.id : '' : '';
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

        function load_data_tim() {
            showSelectLoading('#user_dituju');
            showSelectLoading('#penempatan_holding_baru');
            $.ajax({
                url: "{{ url('/update_posisi/get_tim_karyawan') }}" + '/' + user_karyawan_id,
                type: "GET",

                error: function(data) {
                    hideSelectLoading('#user_dituju');
                    $('.alert-karyawan').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseText + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert-karyawan').fadeOut('slow');
                    }, 6000);
                },
                success: function(data) {
                    console.log(data);
                    hideSelectLoading('#user_dituju');
                    hideSelectLoading('#penempatan_holding_baru');
                    // KARYAWAN 
                    let option = '<option value="">Pilih Karyawan...</option>';
                    let option_penempatan = '<option value="">Pilih Penempatan...</option>';
                    if (data.code == 200) {
                        if (data.data.length == 0) {
                            $('.alert-karyawan').stop(true, true).hide().html('<div class="alert alert-warning light alert-lg alert-dismissible fade show">Tidak ada karyawan pengganti tersedia</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert-karyawan').fadeOut('slow');
                            }, 60000);
                        } else {
                            data.data.forEach((element, index) => {
                                option += `<option value="${element.id}">${element.name}</option>`;
                            });
                        }
                        if (data.penempatan_holding.length == 0) {
                            $('.alert-penempatan').stop(true, true).hide().html('<div class="alert alert-warning light alert-lg alert-dismissible fade show">Tidak ada penempatan tersedia</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert-penempatan').fadeOut('slow');
                            }, 60000);
                        } else {
                            data.penempatan_holding.forEach((element, index) => {
                                option_penempatan += `<option value="${element.id}">${element.holding_name}</option>`;
                            });
                        }

                    } else {
                        $('.alert-karyawan').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-karyawan').fadeOut('slow');
                        }, 60000);
                        $('.alert-penempatan').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-penempatan').fadeOut('slow');
                        }, 60000);

                    }
                    // hideSkeleton('.karyawan_pengganti', option);
                    $('#user_dituju').html(option).trigger('change');
                    $('#penempatan_holding_baru').html(option_penempatan);
                    $('#direktur_selected').html(data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan.name : '-' : '-');
                    $('#id_direktur_selected').val(data.atasan2 ? data.atasan2.karyawan ? data.atasan2.karyawan.id : '' : '');
                    $('#manager_hrd_selected').html(data.atasan ? data.atasan.karyawan ? data.atasan.karyawan.name : '-' : '-');
                    $('#id_manager_hrd_selected').val(data.atasan ? data.atasan.karyawan ? data.atasan.karyawan.id : '' : '');
                    $('#manager_selected').html(data.user ? 'Diajukan oleh : ' + data.user.name : 'Diajukan oleh : -');
                }
            })
        }

        function load_data_posisi() {
            let data_approve = '';
            $.ajax({
                url: "{{ url('/update_posisi/get_PosisiKaryawan') }}" + '/' + menu_active,
                type: "GET",
                beforeSend: function() {
                    showSkeleton('.content-posisi');
                },
                error: function(data) {
                    // console.log(data);
                    $('.alert_update_posisi').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_update_posisi').fadeOut('slow');
                    }, 6000);
                    data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                    hideSkeleton('.content-posisi', data_approve);
                },
                success: function(data) {
                    // console.log(data);
                    if (data.code == 200) {
                        if (data.data == '' || data.data == null) {
                            data_approve += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data Perubahan Posisi</p>
                                    </div>`;
                        } else {
                            data.data.forEach(element => {
                                if (element.nama_cuti == 1) {
                                    element.nama_cuti = 'Cuti Tahunan';
                                } else if (element.nama_cuti == 2) {
                                    element.nama_cuti = element.kategori_cuti.nama_cuti
                                }
                                data_approve += `<a class="btn_klik" href="{{ url('/approval/cuti/cutiDetail') }}/${element.id}">
                                <div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.nama_cuti}</h6>

                                            <div class="text-muted small">
                                                ${formatLabel(element.tanggal_mulai)}
                                                <span class="mx-1">-</span>
                                                ${formatLabel(element.tanggal_selesai)}
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${formatLabel(element.tanggal_pengajuan)}</span>
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
                    } else {
                        $('.alert_update_posisi').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                        setTimeout(function() {
                            $('.alert_update_posisi').fadeOut('slow');
                        }, 6000);
                    }
                    hideSkeleton('.content-posisi', data_approve);
                }
            })
        }

        function load_data_history_approval() {
            let data_approve = '';
            $.ajax({
                url: "{{ url('/approval/history/get_history_approval') }}" + '/' + menu_active,
                type: "GET",
                beforeSend: function() {
                    showSkeleton('.content-history');
                },
                error: function(data) {
                    // console.log(data);
                    $('.alert_update_posisi').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_update_posisi').fadeOut('slow');
                    }, 6000);
                    data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                    hideSkeleton('.content-history', data_approve);
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
                                if (element.cuti != null) {
                                    if (element.cuti.nama_cuti == 1) {
                                        element.nama_approve = 'Cuti Tahunan';
                                    } else if (element.cuti.nama_cuti == 2) {
                                        element.nama_approve = element.kategori_cuti.nama_cuti
                                    }
                                } else if (element.izin != null) {
                                    if (element.nama_izin == 1) {
                                        element.nama_izin = 'Cuti Tahunan';
                                    } else if (element.nama_izin == 2) {
                                        element.nama_izin = element.kategori_izin.nama_izin
                                    }
                                }
                                if (element.status_approval == 1) {
                                    element.status_approval = 'ANDA MENYETUJUI PENGAJUAN';
                                    var badge = 'badge-success';
                                    var card = 'coworker-card-history';
                                } else if (element.status_approval == 2) {
                                    element.status_approval = 'ANDA MENOLAK PENGAJUAN';
                                    var badge = 'badge-danger';
                                    var card = 'coworker-card-reject';
                                }
                                data_approve += `<a class="btn_klik" href="{{ url('/approval/history/historyDetail') }}/${element.id_approval}">
                                <div class="${card} d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.nama_approve}</h6>

                                            <div class="text-muted small">
                                              <span class="badge light badge-sm ${badge} me-1 mb-1">${element.status_approval}</span>
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${formatLabel2(element.created_at)}</span>
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
                    } else {
                        $('.alert_update_posisi').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                        setTimeout(function() {
                            $('.alert_update_posisi').fadeOut('slow');
                        }, 6000);
                    }
                    hideSkeleton('.content-history', data_approve);
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

        function load_data_history(filter_month = '') {
            $('.content-history').empty();
            $('.alert_history_cuti').empty();
            $('.alert_update_posisi').empty();
            $.ajax({
                url: "{{ url('/cuti/history/get_history_cuti') }}",
                data: {
                    filter_month: filter_month,
                },
                type: "GET",
                beforeSend: function() {
                    showSkeleton('.content-history');
                },
                error: function(data) {
                    $('.alert_history_cuti').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                        '<strong>Info!</strong>&nbsp;' + data.responseJSON.message +
                        '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>').fadeIn();
                    setTimeout(function() {
                        $('.alert_history_cuti').fadeOut('slow');
                    }, 6000); // 3 detik

                },
                success: function(data) {
                    // console.log(data);
                    $('.content-history').empty();
                    let history = '';
                    if (data.code == 200) {
                        if (data.history == '' || data.history == null) {
                            history += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-clock-rotate-left fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data History cuti</p>
                                    </div>`;
                        } else {
                            data.history.each(data, function(element) {
                                history += `<a class="btn_klik" href="{{ url('/cuti/history/history_detail') }}/${element.nama_cuti}"><div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.tanggal}</h6>

                                            <div class="text-muted small">
                                                ${element.tanggal}
                                                <span class="mx-1">•</span>
                                                (${element.tanggal})
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${element.tanggal}</span>
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
                    } else {
                        $('.alert_history_cuti').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_history_cuti').fadeOut('slow');
                        }, 6000);
                        history += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                    }
                    hideSkeleton('.content-history', history);
                }
            });
        }
        $('#month').on('change', function() {
            filter_month = $(this).val();
            // console.log(filter_month);
            $('.content-history').empty();
            load_data_history(filter_month);


        })
        $('#btn_tambah_posisi').on('click', function() {
            $('.kategori_cuti').hide();
            load_data_tim();
            load_data_approval();
            $('#signature-pad').removeAttr('hidden');

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
            const offcanvasEl = document.getElementById('off-canvas-posisi');
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            offcanvas.show();
        });
        $('#clear').on('click', () => signaturePad.clear());
        $(document).on('click', '#btn_save_update_posisi', function(e) {
            const offcanvasEl = document.getElementById('off-canvas-konfirmasi-save');
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            offcanvas.show();
        });

        $(document).on('click', '#btn_simpan_posisi', function(e) {
            let btn = $(this);
            let originalText = btn.html();

            let signature_value = '';
            if (!signaturePad.isEmpty()) {
                signature_value = signaturePad.toDataURL();
            }
            $('#signature').val(signature_value);
            $.ajax({
                url: "{{ url('update_posisi/proses_add_update_posisi') }}",
                type: "POST",
                data: $('#formUpdatePosisi').serialize(),
                beforeSend: function() {
                    btn.prop('disabled', true);
                    btn.html(`<span class="spinner-border spinner-border-sm me-2"></span>Loading... `);
                },
                error: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    $('.alert_error_cuti').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_error_cuti').fadeOut('slow');
                    }, 6000);
                    $('#alert_pengajuan_cuti').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('#alert_pengajuan_cuti').fadeOut('slow');
                    }, 6000);
                },
                success: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    $('#alert_pengajuan_cuti').empty();
                    if (data.code == 200) {
                        $('#alert_pengajuan_cuti').stop(true, true).hide().html('<div class="alert alert-success light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('#alert_pengajuan_cuti').fadeOut('slow');
                        }, 6000);
                        $('#formUpdatePosisi')[0].reset();
                        const offcanvasEl = document.getElementById('off-canvas-cuti');
                        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                        offcanvas.hide();
                        load_data_cuti();
                    } else if (data.code == 422) {
                        $.each(data.errors, function(key, value) {
                            let input = $('[name="' + key + '"]');
                            $('.alert_' + key).html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + value[0] + '</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert_' + key).fadeOut('slow');
                            }, 6000);
                        })
                    } else if (data.code == 500) {
                        $('.alert_error_cuti').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_error_cuti').fadeOut('slow');
                        }, 6000);
                    }
                }
            })
        });
    });
</script>
@endsection