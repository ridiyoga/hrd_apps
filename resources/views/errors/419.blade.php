<!DOCTYPE html>
<html lang="id" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 | Session Expired</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

        .error-card {
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

        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: #6f42c1;
        }
    </style>
</head>

<body class="position-relative d-flex align-items-center justify-content-center">

    <!-- Abstract Background -->
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <!-- Error Card -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-5 col-lg-4">

                <div class="card error-card p-4 text-center">

                    <div class="error-code">419</div>

                    <h5 class="fw-bold mt-2">Session Expired</h5>

                    <p class="text-muted small mb-4">
                        Sesi Anda telah berakhir atau halaman terlalu lama tidak digunakan.
                        Silakan login kembali.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ url()->previous() }}"
                            class="btn btn-outline-secondary">
                            Kembali
                        </a>

                        <a href="{{ route('login') }}"
                            class="btn btn-gradient text-white fw-semibold">
                            Login Ulang
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Floating Footer Logos -->
    <div class="footer-floating">
        <div class="d-flex align-items-center gap-3 justify-content-center">
            <img src="{{ asset('holding/assets/img/logosps.png') }}">
            <img src="{{ asset('holding/assets/img/logosipbaru.png') }}">
            <img src="{{ asset('holding/assets/img/logosp.png') }}">
        </div>
        <div class="version-text">
            HRD-APP v1.0
        </div>
    </div>

</body>

</html>