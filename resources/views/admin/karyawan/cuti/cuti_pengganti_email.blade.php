<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penunjukan Penanggung Jawab Sementara (Backup Cuti)</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e8ed;
        }

        .header {
            background-color: #2c3e50;
            padding: 30px 40px;
            text-align: center;
            border-bottom: 3px solid #18bc9c;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .content {
            padding: 40px;
            line-height: 1.6;
            font-size: 15px;
        }

        .greeting {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        p {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: justify;
        }

        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid #18bc9c;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 25px 40px;
            text-align: center;
            border-top: 1px solid #e1e8ed;
        }

        .footer p {
            margin: 0;
            font-size: 12px;
            color: #7f8c8d;
            line-height: 1.5;
            text-align: center;
        }

        .footer a {
            color: #18bc9c;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Pemberitahuan Penunjukan Backup Cuti</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Halo {{ $nama_pengganti }},</div>

            <p>Melalui email ini, kami menginformasikan bahwa Anda ditunjuk sebagai penanggung jawab sementara untuk
                menggantikan <strong>{{ $nama_cuti }}</strong> selama masa cutinya.</p>

            <div class="highlight-box">
                <strong>{{ $nama_cuti }}</strong> akan menjalani cuti mulai tanggal
                <strong>{{ $tanggal_mulai }}</strong> sampai dengan <strong>{{ $tanggal_selesai }}</strong>.
            </div>

            <p>Selama periode tersebut, Anda diharapkan dapat menjalankan fungsi sebagai <em>backup</em> serta membantu
                memastikan seluruh pekerjaan dan kegiatan operasional yang menjadi tanggung jawab
                <strong>{{ $nama_cuti }}</strong> tetap berjalan dengan baik.
            </p>

            <p>Apabila terdapat kendala, kondisi khusus, atau hal yang memerlukan keputusan di luar kewenangan Anda,
                silakan berkoordinasi dengan atasan langsung <strong>{{ $nama_cuti }}</strong>.</p>

            <p>Kami mengucapkan terima kasih atas kesediaan, kerja sama, dan komitmen Anda dalam mendukung kelancaran
                operasional perusahaan selama periode tersebut.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>*Email ini dibuat oleh sistem, mohon untuk tidak membalas email ini.*</p>
            <p style="margin-top: 5px;">Apabila terjadi kendala harap hubungi <a
                    href="mailto:hrd@beraskediri.com">hrd@beraskediri.com</a></p>
        </div>
    </div>

</body>

</html>
