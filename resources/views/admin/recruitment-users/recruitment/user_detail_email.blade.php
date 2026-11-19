<!DOCTYPE html>
<html>

<head>
    <title>Email</title>
</head>

<body>
    <h2>Lolos Tahap Wawancara</h2>
    <br>
    <p>Halo {{ $nama }}</p>
    <p>Terima kasih telah melamar di {{ $e_holding }}. Setelah proses seleksi administrasi, kami mengundang Anda
        untuk mengikuti wawancara tahap pertama pada:</p>
    <p>📅 Hari/Tanggal : {{ $hari }}</p>
    <p>🕒 Pukul : {{ $pukul }}</p>
    <p>📍{{ $tempat_wawancara }},{{ $maps }}</p>
    <p>Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):</p>
    <p>https://karir.sumberpangan.store/cpanel/recruitment_detail/{{ $user_id }}</p>
</body>

</html>
