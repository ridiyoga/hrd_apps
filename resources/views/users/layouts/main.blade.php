<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
    <meta name="theme-color" content="#2196f3">
    <meta name="author" content="DexignZone" />
    <meta name="keywords" content="" />
    <meta name="robots" content="" />
    <meta name="description" content="Jobie - Job Portal Mobile App Template ( Bootstrap 5 + PWA )" />
    <meta property="og:title" content="Jobie - Job Portal Mobile App Template ( Bootstrap 5 + PWA )" />
    <meta property="og:description" content="Jobie - Job Portal Mobile App Template ( Bootstrap 5 + PWA )" />
    <meta property="og:image" content="https://jobie.dexignzone.com/mobile-app/xhtml/social-image.png" />
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Expires" content="-1">

    <!-- Favicons Icon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png" />

    <!-- Title -->
    <title>APPS KARYAWAN</title>

    @include('users.layouts.css')
    <!-- Stylesheets -->
    @yield('css')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css" rel="stylesheet">
    <style>
        ::placeholder,
        .form-control::placeholder,
        input::placeholder,
        textarea::placeholder {
            color: #c4b5fd !important;
            opacity: 1;
        }

        .scrollable-menu {
            max-height: 70vh;
            /* tinggi maksimal 70% layar */
            overflow-y: auto;
            /* aktifkan scroll vertikal */
        }

        .dz-icon i {
            font-size: 30px !important;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 16px 16px 32px;
            background: linear-gradient(135deg, #6f4fd8, #8b6df0);
            padding-bottom: 60px;
            /* ruang untuk card */
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
        }

        /* LEFT */
        .header-left {
            flex: 0 0 auto;
        }

        /* CENTER */
        .header-center {
            flex: 1;
            text-align: left;
            padding-left: 12px;
        }

        .greeting {
            font-size: 13px;
            opacity: 0.9;
            display: block;
        }

        .username {
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.2;
        }

        /* RIGHT */
        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ICON BUTTON */
        .icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: none;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(6px);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* AVATAR */
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.4);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* CONTENT FLOAT EFFECT */
        .page-content {
            /* padding: 8px; */
            min-height: 100vh;
            box-sizing: border-box;
        }


        .floating-wrapper {
            margin-top: -48px;
            position: relative;
            z-index: 10;
        }

        .fixed-content {
            border-radius: 22px;
            padding: 10px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }


        .dashboard-area {
            margin-top: -10px;
        }

        /* Styling Offcanvas */
        #off-canvas-all-menu {
            height: auto;
            /* Batasi tinggi agar tidak menutupi layar penuh */
            border-radius: 30px 30px 0 0 !important;
        }

        .offcanvas-header-custom {
            padding: 20px 0 10px;
            position: relative;
        }

        /* Indikator tarik (Handle) di bagian atas offcanvas */
        .offcanvas-header-custom::after {
            content: "";
            width: 40px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 10px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Grid Khusus Menu Kecil */
        .mini-menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* 5 kolom agar lebih ringkas */
            gap: 12px 8px;
            padding: 5px;
        }

        .mini-menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        /* Icon Lebih Kecil & Flat */
        .mini-icon {
            width: 45px;
            height: 45px;
            min-width: 42px;
            min-height: 42px;
            flex-shrink: 0;
            border-radius: 14px;
            /* Squircle tumpul */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 6px;
            background: #f1f5f9;
            /* Warna dasar default */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03), 0 2px 2px rgba(0, 0, 0, 0.02);
            /* box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.8); */
            /* Memberikan aksen cahaya di pinggir */
            transition: transform 0.2s ease;
        }

        /* Label Mini */
        .mini-label {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            text-align: center;
            white-space: nowrap;
            /* Teks satu baris */
            overflow: hidden;
            text-overflow: ellipsis;
            /* Titik-titik jika kepanjangan */
            width: 100%;
        }

        /* Efek Hover/Aktif */
        .mini-menu-item:active .mini-icon {
            transform: scale(0.9);
            filter: brightness(0.9);
        }

        /* Variasi Warna Soft (Elegan) */
        .icon-soft-purple {
            background: #f3f0ff;
            color: #7048e8;
        }

        .icon-soft-blue {
            background: #e7f5ff;
            color: #1c7ed6;
        }

        .icon-soft-green {
            background: #ebfbee;
            color: #2b8a3e;
        }

        .icon-soft-red {
            background: #fff5f5;
            color: #e03131;
        }

        .icon-soft-yellow {
            background: #fff9db;
            color: #f08c00;
        }

        /* ── GLOBAL MODAL & OFFCANVAS STACKING (Above Fixed Menubar) ── */
        .menubar-area {
            z-index: 999 !important;
        }

        .modal {
            z-index: 1090 !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        .offcanvas {
            z-index: 1090 !important;
        }

        .offcanvas.offcanvas-bottom {
            z-index: 1090 !important;
        }

        .offcanvas-backdrop {
            z-index: 1040 !important;
        }

        .swal2-container {
            z-index: 2000 !important;
        }

        #oc-konfirmasi-checker,
        #off-canvas-konfirmasi-save,
        #off-canvas-konfirmasi-approve,
        #off-canvas-konfirmasi-reject {
            z-index: 1095 !important;
        }

        .modal-dialog {
            margin-bottom: 85px;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 100px);
        }

        .offcanvas-bottom .offcanvas-body {
            padding-bottom: 80px !important;
        }
    </style>
</head>

<body class="@yield('body_class')">
    <div class="page-wraper" style="overflow-y: hidden;">

        @include('sweetalert::alert')
        <!-- Header -->
        <header class="app-header">
            <div class="header-inner">

                <!-- LEFT -->
                <div class="header-left">
                    <button class="icon-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>

                <!-- CENTER -->
                <div class="header-center">
                    <span class="greeting">
                        <?php
                        date_default_timezone_set('Asia/Jakarta');
                        $time = date('H');
                        /* Set the $timezone variable to become the current timezone */
                        $timezone = date('e');
                        /* If the time is less than 1200 hours, show good morning */
                        if ($time < '12') {
                            echo 'Good morning';
                        } /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */ elseif ($time >= '12' && $time < '17') {
                            echo 'Good afternoon';
                        } /* Should the time be between or equal to 1700 and 1900 hours, show good evening */ elseif ($time >= '17' && $time < '19') {
                            echo 'Good evening';
                        } /* Finally, show good night if the time is greater than or equal to 1900 hours */ elseif ($time >= '19') {
                            echo 'Good night';
                        }
                        ?>
                    </span>
                    <strong class="username">{{ $user_karyawan->name }}</strong>
                </div>

                <!-- RIGHT -->
                <div class="header-right">
                    <button class="icon-btn theme-btn">
                        <i class="fa-regular fa-moon dark"></i>
                        <i class="fa-regular fa-sun light"></i>
                    </button>

                    <div class="avatar">
                        @if ($user_karyawan->foto_karyawan == '' || $user_karyawan->foto_karyawan == null)
                            <a href="{{ url('profile/32') }}"><img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                    class="rounded-circle" alt="User"></a>
                        @else
                            <a href="{{ url('profile/32') }}"><img
                                    src="{{ asset('storage/foto_karyawan/' . $user_karyawan->foto_karyawan) }}"
                                    class="rounded-circle" alt="User"></a>
                        @endif
                    </div>
                </div>

            </div>
        </header>

        <div class="page-content">

            <div class="content-inner pt-0">

                <!-- FLOATING INFO CARD -->
                <div class="floating-wrapper">
                    <div class="container">
                        <div class="fixed-content">
                            @yield('content_top')
                            @yield('alert')
                            @yield('absensi')
                        </div>
                    </div>
                </div>

                <!-- DASHBOARD CONTENT -->
                <div class="dashboard-area">
                    <div class="container">
                        @yield('content')
                    </div>
                </div>

            </div>

        </div>

        <!-- Page Content End-->

        <!-- Menubar -->
        @include('users.layouts.menubar')
        <!-- Menubar -->


    </div>
    <!--********************************** Scripts ***********************************-->
    @include('users.layouts.js')
    @yield('js')

</body>

</html>
