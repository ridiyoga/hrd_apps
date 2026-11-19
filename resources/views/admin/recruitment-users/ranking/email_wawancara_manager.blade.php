<!DOCTYPE html>
<html>

<head>
    <title>Email</title>
</head>

<body>
    <h2>Lolos Tahap Wawancara Awal</h2>
    <br>
    <p>Halo {{ $nama }}</p>
    <p>Selamat, Anda lolos tahap wawancara awal. Kami mengundang Anda untuk melanjutkan ke wawancara bersama Manager
        pada:</p>
    <p>📅 Hari/Tanggal : {{ $hari }}</p>
    <p>🕒 Pukul : {{ $pukul }}</p>
    <p>📍{{ $tempat }},{{ $maps }}</p>
    <p>Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):</p>
    <p>https://karir.sumberpangan.store/cpanel/recruitment_detail/{{ $user_id }}</p>
    <br>
    <p><b>Best regards, </b></h2>
    <p>Karir Beras Kediri</p>
    <p>🏢 CV Sumber Pangan & PT Surya Pangan Semesta</p>
    <p>📞 +62 878-1388-8885</p>
    <p>✉️ karir@beraskediri.com</p>
</body>

</html>
