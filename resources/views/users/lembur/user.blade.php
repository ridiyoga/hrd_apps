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
            /* Gradient hijau lime/mint terang yang segar ke putih */
            background: linear-gradient(135deg, #e8fced, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan dengan tone hijau terang yang hidup dan bersih */
            box-shadow: 0 6px 18px rgba(40, 199, 111, 0.2);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-2 {
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
            <h5 class="mt-3 mb-1">PANTAU LEMBUR</h5>
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
                        <span>Lembur Diajukan</span>
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

            // Mengambil jam keluar shift ketika karyawan dipilih.

            // Menambahkan baris input karyawan baru ke dalam form.






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
                    url: "{{ url('/lembur/get_lembur_detail') }}" + '/' + menu_active,
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

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    lembur += `<a class="btn_klik" href="{{ url('/lembur/detail_user') }}/${menu_active}/${element.id}">
                            <div class="coworker-card coworker-card-${element.status} d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Lembur</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.tgl_lembur}</span>
                                    </div>
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-clock me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.jam_mulai}-
                                        <span class="price-item">${element.jam_selesai}</span>
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
                    url: "{{ url('/lembur/get_lembur_riwayat_user') }}",
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
                                    if (element.status == 1) {
                                        progressValue = 100;
                                        progressColor = 'bg-success';
                                    } else if (element.status == 2) {
                                        progressValue = 100;
                                        progressColor = 'bg-danger';
                                    }

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    lembur += `<a class="btn_klik" href="{{ url('/lembur/user_detail') }}/${menu_active}/${element.id}">
                            <div class="coworker-card coworker-card-${element.status} d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Lembur</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.tgl_lembur}</span>
                                    </div>
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-clock me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${element.jam_mulai}-
                                        <span class="price-item">${element.jam_selesai}</span>
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
