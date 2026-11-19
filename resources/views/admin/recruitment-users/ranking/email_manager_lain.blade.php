<!DOCTYPE html>
<html>

<head>
    <title>Email</title>
</head>

<body>
    <h2>Lolos Wawancara Manager Lain</h2>
    <br>
    <p>Halo {{ $nama }}</p>
    <p>Terima kasih telah mengikuti proses seleksi di {{ $e_holding }}. Setelah mempertimbangkan kualifikasi
        Anda,
        kami melihat potensi Anda lebih sesuai dengan posisi "{{ $jabatan }}".
        "{{ $divisi }}".</p>
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
