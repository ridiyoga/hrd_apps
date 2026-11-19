<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>HRD-APP | ADMIN</title>

    <meta name="description" content="" />

    <link rel="apple-touch-icon" href="{{ asset('holding/assets/img/logosp.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/materialdesignicons.css') }}" />


    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets_users/vendor/swiper/swiper-bundle.min.css') }}">

    <style>
        :root {
            --primary-purple: #673ab7;
            /* Your original primary color */
            --light-purple: #c8b0f4;
            --text-dark: #343a40;
            --text-light: #f8f9fa;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fe;
            color: #1a1c2d;
        }

        /* Hero Section - Gradient Ungu Modern */
        .holding-hero-section {
            padding: 50px 0 50px;
            background: linear-gradient(0deg, var(--light-purple) 0%, var(--primary-purple) 100%);
            /* Ungu ke Biru */
            border-radius: 0 0 40px 40px;
        }


        .holding-title {
            /* font-family: 'Plus Jakarta Sans', sans-serif; */
            font-weight: 800;
            font-size: 3.0rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            color: #ffffff;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);

        }

        .sub-title {
            /* font-family: 'Plus Jakarta Sans', sans-serif; */
            font-weight: 600;
            font-size: 1.0rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            color: #ffffff;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
        }

        /* Link Wrapper agar tinggi sinkron */
        .holding-link {
            text-decoration: none !important;
            display: flex;
            /* Penting untuk h-100 */
        }

        /* Card Styling */
        .holding-card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: #fff;
            width: 100%;
        }

        /* Logo Wrapper - Dibuat lebih tipis */
        .card-logo-wrapper {
            padding: 25px 20px;
            /* Space dikurangi */
            background: #fcfcfc;
            border-bottom: 1px inset #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120px;
            /* Tinggi logo area dikunci */
        }

        .holding-logo-img {
            max-height: 70px;
            /* Ukuran logo lebih terkontrol */
            max-width: 180px;
            object-fit: contain;
        }

        .card-body-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            /* Mengambil sisa ruang agar footer terdorong ke bawah */
        }

        .holding-name {
            color: #333;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        /* Container Lokasi - Menjaga keseragaman tinggi */
        .location-scroll-container {
            min-height: 90px;
            /* Menjamin kartu yang lokasinya sedikit tetap punya tinggi yang sama */
        }

        .location-label {
            font-size: 0.7rem;
            color: #999;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .site-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .site-badge {
            font-size: 0.72rem;
            background: #f7f7f7;
            /* Ungu sangat muda */
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid #efeef7;
            font-weight: 500;
        }

        /* Footer Action */
        .card-footer-action {
            padding: 15px 20px;
            background: #fbfbfb;
            color: #6a11cb;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 0 0 16px 16px;
            border-top: 1px solid #f1f1f1;
        }

        /* Hover State */
        .holding-link:hover .holding-card-modern {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(106, 17, 203, 0.2);
        }

        .holding-link:hover .card-footer-action {
            background: #6a11cb;
            color: #fff;
        }

        .brand-text {
            font-weight: 800;
            color: #1a1c2d;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .version-text {
            color: #64748b;
            font-size: 0.8rem;
        }

        .v-num {
            font-weight: 700;
            color: #6d28d9;
        }

        /* ================= GLOBAL LOADING (MODERN) ================= */
        .global-loading {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .global-loading.d-none {
            display: none;
        }

        .loading-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px 34px;
            min-width: 220px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .12), 0 4px 12px rgba(0, 0, 0, .06);
            animation: loadingPop .25s ease;
        }

        @keyframes loadingPop {
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Container kustom untuk loader */
        .custom-loader-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
        }

        /* Animasi Double Ring yang Elegan */
        .modern-spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto 14px;
            border: 4px solid rgba(102, 108, 255, 0.1);
            border-left-color: #666cff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: relative;
        }

        .modern-spinner::before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 4px solid rgba(102, 108, 255, 0.1);
            border-left-color: #00cfe8;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Efek teks berdenyut (Pulse) */
        .pulse-text {
            animation: pulse 1.5s ease-in-out infinite;
            font-weight: 500;
            color: #5d596c;
            letter-spacing: 1px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>

</head>

<body>
    <!-- GLOBAL LOADING -->
    <div id="global-loading" class="global-loading d-none">
        <div class="loading-card">
            <div class="modern-spinner"></div>
            <div class="loading-title">
                <span id="loading-text">Memproses</span>
                <span class="loading-dots"></span>
            </div>
            <div class="loading-subtitle">Mohon tunggu sebentar</div>
        </div>
    </div>
    <div class="page-wraper bg-light" style="min-height: 100vh; padding-bottom: 80px;">
        <div class="page-content">
            @include('sweetalert::alert')

            <div class="content-body">
                <div class="holding-hero-section">
                    <div class="container text-center">
                        <h1 class="holding-title">Silahkan Pilih Holding</h1>
                        <p class="sub-title">Akses dashboard Manajemen HRD sesuai Holding yang anda pilih</p>
                    </div>
                </div>

                <div class="container mt-n5 mb-5">
                    <div class="row g-4 justify-content-center">
                        @foreach ($holding as $data)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <a href="{{ url('dashboard/switch/' . $data->holding_code) }}" class="holding-link h-100">
                                <div class="card holding-card-modern h-100">
                                    <div class="card-logo-wrapper">
                                        <img src="{{ asset('storage/holding_image/' . $data->holding_image) }}"
                                            alt="{{ $data->holding_name }} Logo"
                                            class="holding-logo-img">
                                    </div>
                                    <div class="card-body-content">
                                        <h5 class="holding-name text-center text-uppercase">{{ $data->holding_name }}</h5>

                                        <div class="location-scroll-container">
                                            <div class="location-label">
                                                <p>
                                                    <i class="mdi mdi-map-marker-outline"></i>
                                                    Alamat Holding
                                                </p>
                                            </div>
                                            <div class="site-list">
                                                @foreach ($data->Site as $site)
                                                <span class="site-badge">{{ $site->site_alamat }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer-action mt-auto">
                                        <span>Pilih Holding</span>
                                        <i class="mdi mdi-arrow-right-circle-outline"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <footer class="modern-sticky-footer">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="footer-left">
                    <span class="brand-text">HRD-APPS</span>
                </div>
                <div class="footer-right">
                    <span class="version-text">Versi Aplikasi <span class="v-num">V1.1.0</span></span>
                </div>
            </div>
        </footer>
    </div>
</body>
<script src="{{ asset('assets/assets_users/js/jquery.js') }}"></script>
<script src="{{ asset('assets/assets_users/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/assets_users/vendor/swiper/swiper-bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="{{ asset('/sw.js') }}"></script> -->
<script>
    (function() {

        const overlay = document.getElementById('global-loading');
        const loadingText = document.getElementById('loading-text');

        let shown = false;

        function showLoading(text = 'Memuat halaman') {
            if (!overlay || shown) return;

            shown = true;
            loadingText.textContent = text;

            overlay.classList.remove('d-none');
            document.body.style.pointerEvents = 'none';
        }

        function hideLoading() {
            if (!overlay) return;

            overlay.classList.add('d-none');
            document.body.style.pointerEvents = '';

            shown = false;
        }

        function getTextByHref(href) {

            if (!href) return 'Memuat halaman';

            if (href.includes('dashboard')) return 'Memuat Dashboard';
            if (href.includes('departemen')) return 'Memuat Departemen';
            if (href.includes('divisi')) return 'Memuat Divisi';
            if (href.includes('bagian')) return 'Memuat Bagian';
            if (href.includes('jabatan')) return 'Memuat Jabatan';
            if (href.includes('holding')) return 'Memuat Holding';
            if (href.includes('karyawan')) return 'Memuat Karyawan';
            if (href.includes('recruitment')) return 'Memuat Recruitment';
            if (href.includes('report')) return 'Memuat Laporan';

            return 'Memuat halaman';
        }

        /* ================= FORM SUBMIT ================= */

        document.addEventListener('submit', function(e) {

            const form = e.target;

            const btn = document.activeElement;

            const methodInput = form.querySelector('input[name="_method"]');

            const method = methodInput ?
                methodInput.value.toUpperCase() :
                (form.method || 'GET').toUpperCase();

            if (btn?.dataset?.loading) {
                return showLoading(btn.dataset.loading);
            }

            if (method === 'DELETE') return showLoading('Menghapus data');

            if (method === 'PUT' || method === 'PATCH')
                return showLoading('Memperbarui data');

            if (method === 'POST')
                return showLoading('Menyimpan data');

            showLoading('Memproses data');

        }, true);

        /* ================= LINK CLICK ================= */

        document.addEventListener('click', function(e) {

            const el = e.target.closest('a,button');

            if (!el) return;

            /* Skip Bootstrap UI */

            if (
                el.hasAttribute('data-bs-toggle') ||
                el.getAttribute('role') === 'tab' ||
                el.closest('.nav-tabs') ||
                el.closest('.nav-pills')
            ) {
                return;
            }

            /* Skip submit button */

            if (el.tagName === 'BUTTON' && el.type === 'submit')
                return;

            if (el.closest('form'))
                return;

            /* Anchor */

            if (el.tagName === 'A') {

                const href = el.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href.startsWith('#') ||
                    href.startsWith('javascript') ||
                    el.target === '_blank' ||
                    el.hasAttribute('download') ||
                    el.hasAttribute('data-no-loading')
                ) {
                    return;
                }

                const text = el.dataset.loading || getTextByHref(href);

                showLoading(text);

            }

        }, true);

        /* ================= PAGE LOAD ================= */

        window.addEventListener('load', function() {
            hideLoading();
        });

        window.addEventListener('pageshow', function() {
            hideLoading();
        });

        window.addEventListener('beforeunload', function() {

            if (overlay) {
                overlay.classList.remove('d-none');
            }

        });

        /* expose */

        window.showGlobalLoading = showLoading;
        window.hideGlobalLoading = hideLoading;

    })();
</script>

</html>