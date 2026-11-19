<!DOCTYPE html>
<html lang="id" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | HRD APP</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/assets_users/vendor/swiper/swiper-bundle.min.css') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
            background: #f4f6f9;
        }

        /* Abstract Background */
        .bg-shape-1,
        .bg-shape-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
        }

        .bg-shape-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #7b2ff7, #5f0fff);
            top: -150px;
            left: -150px;
        }

        .bg-shape-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #6610f2, #6f42c1);
            bottom: -150px;
            right: -150px;
        }

        .login-card {
            border-radius: 22px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.12);
            border: none;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #6f42c1, #5f0fff);
            border: none;
        }

        .btn-gradient:hover {
            opacity: 0.9;
        }

        /* Floating Footer */
        .footer-floating {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .footer-floating img {
            height: 28px;
            opacity: 0.7;
            transition: 0.3s ease;
        }

        .footer-floating img:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .version-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body class="position-relative d-flex align-items-center justify-content-center">
    <!-- Abstract Background -->
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <!-- Login Card -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-5 col-lg-4">

                <div class="card login-card p-4">

                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1">Welcome Back</h4>
                        <small class="text-muted">Login ke sistem HRD</small>
                    </div>

                    <div class="basic-form style-1" style="margin-top: 4%; min-height: 100%;">
                        <form method="POST" action="{{ route('login_user.proses') }}">
                            @csrf
                            <div class="form-input">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px"
                                        viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="6" r="4" stroke="#1C274C"
                                            stroke-width="1.5" />
                                        <path
                                            d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z"
                                            stroke="#1C274C" stroke-width="1.5" />
                                    </svg>
                                </span>
                                <input type="text" id="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username') }}" autocomplete="username"
                                    autofocus placeholder="Username">
                            </div>
                            @error('username')
                            <p class="alert alert-danger">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 form-input">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px"
                                        viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M21 8.5V6C21 4.89543 20.1046 4 19 4H5C3.89543 4 3 4.89543 3 6V11C3 12.1046 3.89543 13 5 13H10.875M19 14V12C19 10.8954 18.1046 10 17 10C15.8954 10 15 10.8954 15 12V14M14 20H20C20.5523 20 21 19.5523 21 19V15C21 14.4477 20.5523 14 20 14H14C13.4477 14 13 14.4477 13 15V19C13 19.5523 13.4477 20 14 20Z"
                                            stroke="#000000" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <circle cx="7.5" cy="8.5" r="1.5" fill="#000000" />
                                        <circle cx="12" cy="8.5" r="1.5" fill="#000000" />
                                    </svg>
                                </span>
                                <input type="password" name="password" id="dz-password"
                                    class="form-control be-0 @error('password') is-invalid @enderror"
                                    placeholder="***********">
                            </div>
                            @error('password')
                            <p class="alert alert-danger">{{ $message }}</p>
                            @enderror
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="show"
                                    id="password_show">
                                <label class="form-check-label" style="color: #222; font-size: 9pt;"
                                    for="flexCheckDefault">
                                    Password Show
                                </label>
                            </div>
                            <!-- <a href="forgot-password.html" class="btn-link d-block text-center">Forgot your password?</a> -->
                            <div class="d-grid">
                                <button type="submit"
                                    class="btn btn-gradient btn-md text-white fw-semibold">
                                    Login
                                </button>
                            </div>
                        </form>
                        {{-- @if (config('sso.enabled'))
                            <div class="text-center mt-3">
                                <span class="text-muted d-block mb-2">atau</span>
                                <a href="{{ route('sso.login') }}" class="btn btn-outline-dark btn-sm w-100">
                                    Masuk dengan akun perusahaan
                                </a>
                            </div>
                        @endif --}}
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom text-center" id="offcanvas_alert">
        <div class="container">
            <div class="offcanvas-body small">
                <div id="offcanvas_alert_icon"></div>
                <h5 class="title" id="offcanvas_alert_title"></h5>
            </div>
        </div>
    </div>

    <!-- Floating Footer Logos -->
    <div class="footer-floating">
        <div class="d-flex align-items-center gap-3 justify-content-center">
            <img src="{{ asset('holding/assets/img/logosp.png') }}">
            <img src="{{ asset('holding/assets/img/logosps.png') }}">
            <img src="{{ asset('holding/assets/img/logosipbaru.png') }}">
        </div>
        <div class="version-text">
            HRD-APP v1.0
        </div>
    </div>

</body>
<script src="{{ asset('assets/assets_users/js/jquery.js') }}"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    $("document").ready(function() {
        var logout_user_success = "{{ session('logout_user_success') }}";
        if (logout_user_success != '') {
            const el = document.getElementById('offcanvas_alert');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(el);
            offcanvas.show();
            $('#offcanvas_alert_title').html(logout_user_success);
            $('#offcanvas_alert_icon').html('<i class="fa-solid fa-check fa-bounce text-success fs-3"></i>');
            setTimeout(function() {
                offcanvas.hide();
            }, 10000);
        }
        $('#password_show').change(function() {
            var x = document.getElementById("dz-password");
            var ok = $(this).is(':checked');
            if (ok == true) {
                x.type = "text";
            } else {
                x.type = "password";

            }
        });
        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                heightAuto: false,
                scrollbarPadding: false,
                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false,

            });
        };
    });
</script>


</html>