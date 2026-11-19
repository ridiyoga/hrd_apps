<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error - HRD-APPS</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f5f3ff;
            color: #2f2a4a;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .card {
            background: #ffffff;
            padding: 50px 45px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(111, 90, 230, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            max-width: 450px;
            width: 90%;
            border: 1px solid rgba(111, 90, 230, 0.1);
        }

        .error-code {
            font-size: 84px;
            font-weight: 800;
            /* Efek teks gradien untuk kesan mewah */
            background: linear-gradient(135deg, #6F5AE6 0%, #9b89f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
            line-height: 1;
        }

        h2 {
            margin: 15px 0 12px;
            font-weight: 700;
            font-size: 22px;
            color: #1a162e;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            color: #6b6685;
            margin-bottom: 30px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        /* Tombol Utama (Kembali) */
        .btn-primary {
            background: #6F5AE6;
            color: #fff;
            box-shadow: 0 8px 20px rgba(111, 90, 230, 0.25);
        }

        .btn-primary:hover {
            background: #5846c5;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(111, 90, 230, 0.35);
        }

        /* Tombol Sekunder (Refresh) */
        .btn-outline {
            background: transparent;
            color: #6F5AE6;
            border: 2px solid #eae6ff;
        }

        .btn-outline:hover {
            background: #f8f7ff;
            border-color: #6F5AE6;
            transform: translateY(-2px);
        }

        /* Ikon SVG */
        .icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="error-code">500</div>
        <h2>Internal Server Error</h2>
        <p>
            Maaf, sistem kami sedang mengalami gangguan teknis.<br>
            Tim IT sedang berusaha memperbaikinya agar aplikasi dapat segera digunakan kembali.
        </p>

        <div class="btn-group">
            <button onclick="window.location.reload()" class="btn btn-outline">
                <svg class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.75a9.25 9.25 0 1 0 4.737 17.197l-1.057-1.122A7.75 7.75 0 1 1 19.75 12h-1.5l2.625-3.375L23.5 12h-2.25a9.25 9.25 0 0 0-9.25-9.25z" />
                </svg>
                Muat Ulang
            </button>

            <a href="/" class="btn btn-primary">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>