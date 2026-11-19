<!DOCTYPE html>

@php
$unreadNotifCount = 0;
$latestNotifs = collect();
@endphp

@if (auth()->check())
@php
$unreadNotifCount = auth()->user()->unreadNotifications()->count();
$latestNotifs = auth()->user()->unreadNotifications()->latest()->take(10)->get();
@endphp
@endif

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HRD-APP | ADMIN</title>

    {{-- Favicon --}}
    @php
    $favicon = 'holding/assets/img/logosipbaru.png';
    if ($holding == 'sp') {
    $favicon = 'holding/assets/img/logosp.png';
    } elseif ($holding == 'sps') {
    $favicon = 'holding/assets/img/logosps.png';
    }
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" /> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />

    {{-- Icons --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'" />

    {{-- Core CSS --}}
    <link rel="stylesheet"
        href="{{ asset('admin/assets/vendor/css/core.css') }}?v={{ filemtime(public_path('admin/assets/vendor/css/core.css')) }}" />
    <link rel="preload" href="{{ asset('admin/assets/vendor/css/theme-default.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'" />
    <link rel="preload" href="{{ asset('admin/assets/css/demo.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'" />
    <link rel="stylesheet"
        href="{{ asset('admin/assets/css/master-table-layout.css') }}?v={{ filemtime(public_path('admin/assets/css/master-table-layout.css')) }}" />

    {{-- Vendors CSS --}}
    <link rel="preload" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"
        as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <link rel="preload" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'" />

    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.min.css') }}">

    @yield('css')

    <style>
        /* ================= BADGE ================= */
        .badge-notifications {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            z-index: 9999;
            /* 🔥 PALING PENTING */
            pointer-events: none;
        }

        /* unread */
        .badge-unread {
            background: linear-gradient(135deg, #7C3AED, #A78BFA);
            color: #fff;
        }

        /* zero */
        .badge-zero {
            background: #e5e7eb;
            color: #9ca3af;
        }

        .layout-menu .menu-item,
        .layout-menu .menu-link {
            overflow: visible;
        }

        .layout-menu .menu-link {
            position: relative;
        }

        .layout-menu .sidebar-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            font-size: 11px;
            line-height: 1;
            flex: 0 0 auto;
        }

        html.layout-menu-collapsed .layout-menu .sidebar-count-badge {
            position: absolute;
            top: 7px;
            right: 6px;
            z-index: 5;
            margin-left: 0 !important;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(255, 62, 29, .25);
        }

        /* ================= NOTIFICATION ITEM ================= */
        /* wrapper */
        .notif-item {
            cursor: pointer;
            border-radius: 12px;
            margin: 8px 10px;
            padding: 12px 14px;
            transition: all .15s ease;
            display: flex;
            align-items: flex-start;
        }

        /* text truncation */
        .notif-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4em;
            max-height: calc(1.4em * 3);
        }

        /* unread */
        .notif-warning {
            background: #fff8e6;
            /* pastel kuning */
        }

        .notif-urgent {
            background: #fdeaea;
            /* pastel merah */
        }

        /* read */
        .notif-read {
            background: #f9f9fb;
            opacity: 0.7;
        }

        /* accent bar */
        .notif-accent {
            width: 4px;
            border-radius: 4px;
            min-height: 100%;
        }

        .notif-accent-warning {
            background: #f0ad4e;
        }

        .notif-accent-urgent {
            background: #dc3545;
        }

        .notif-accent-read {
            background: #bdbdbd;
        }

        /* hover */
        .notif-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        /* empty */
        .notif-empty {
            padding: 24px;
        }

        /* dropdown notifikasi */
        .notif-dropdown {
            width: 380px;
            max-height: 420px;
            overflow-y: auto;
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

        .spinner-modern {
            width: 46px;
            height: 46px;
            margin: 0 auto 14px;
            border-radius: 50%;
            border: 4px solid rgba(99, 102, 241, .2);
            border-top-color: #6366f1;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        .loading-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite steps(4);
        }

        @keyframes dots {
            0% {
                content: '';
            }

            25% {
                content: '.';
            }

            50% {
                content: '..';
            }

            75% {
                content: '...';
            }

            100% {
                content: '';
            }
        }


        /* Menghilangkan spinner default Swal */
        .swal2-loader {
            display: none !important;
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

        @media (min-width: 1200px) {
            html.layout-menu-collapsed .layout-page {
                padding-left: 4.25rem !important;
            }

            html.layout-menu-collapsed .content-wrapper>.container-xxl {
                width: 100%;
                max-width: none;
            }

            html.layout-menu-collapsed #layout-navbar.container-xxl {
                max-width: none;
            }

            #layout-navbar .navbar-content-row {
                position: relative;
                align-items: center;
                min-height: 44px;
                margin-right: 0;
                margin-left: 0;
            }

            #layout-navbar .navbar-clock {
                position: absolute;
                top: 50%;
                left: 50%;
                display: flex;
                justify-content: center;
                width: auto;
                transform: translate(-50%, -50%);
            }

            #layout-navbar .navbar-actions {
                position: absolute;
                top: 50%;
                right: 0;
                width: auto;
                padding-right: 0;
                transform: translateY(-50%);
            }

            #layout-navbar .navbar-actions .navbar-nav-right {
                width: auto;
            }

        }
    </style>

    <script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
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

    {{-- Layout --}}
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin.layouts.sidebar')
            <div class="layout-page">
                @include('admin.layouts.navbar')
                <div class="content-wrapper">
                    @yield('isi')
                    @include('admin.layouts.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    {{-- jQuery --}}
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.min.js') }}"></script>

    {{-- Bootstrap & lainnya --}}
    <script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('admin/assets/js/main.js') }}"></script>
    <script
        src="{{ asset('admin/assets/js/master-table-layout.js') }}?v={{ filemtime(public_path('admin/assets/js/master-table-layout.js')) }}"></script>

    {{-- GLOBAL LOADING LOGIC --}}
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
                if (!form || form.hasAttribute('data-no-loading') || form.classList?.contains('no-loading') || form.closest('.modal')) {
                    return;
                }

                const btn = document.activeElement;
                if (btn?.hasAttribute('data-no-loading') || btn?.dataset?.noLoading) {
                    return;
                }

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

                /* Skip DataTables controls */

                if (
                    el.closest('.dt-container') ||
                    el.closest('.dataTables_wrapper') ||
                    el.closest('.dt-paging') ||
                    el.closest('.dataTables_paginate')
                ) {
                    return;
                }

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

    @yield('js')
    @stack('js')

</body>

</html>
