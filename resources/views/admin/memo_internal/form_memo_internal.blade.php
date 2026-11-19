<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Memo Internal</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
            margin: 40px;
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }


        .company-address {
            font-size: 11px;
        }

        /* TITLE */
        .title .title-memo {
            font-size: 20px;
            margin-top: 15px;
            text-decoration: underline;
        }

        .title .sub-title {
            font-size: 14px;
        }

        .title {
            text-align: center;
            font-weight: bold;
        }

        /* TABLE INFO */
        .table-info {
            width: 100%;
            margin-top: 10px;
        }

        .table-info td {
            padding: 3px;
            vertical-align: top;
        }

        #perihal {
            text-transform: uppercase;
            font-weight: bold;
        }

        /* CONTENT */
        .content {
            margin-top: 20px;
            line-height: 1.7;
            text-align: justify;
        }

        .content .isi {
            text-indent: 30px;
        }

        /* HOLIDAY BOX */
        .holiday {
            margin-top: 15px;
        }

        /* SIGNATURE */
        .signature {
            margin-top: 60px;
            width: 250px;
            float: right;
            text-align: center;
        }

        /* CLEAR */
        .clear {
            clear: both;
        }
    </style>

</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="company-name">
            @foreach($get_holding as $holding_memo)
            <img src="{{ asset('storage/holding_image/' . $holding_memo->holding_image) }}" width="50">
            @endforeach
            <!-- {{ $memo->holding->holding_name ?? 'NAMA PERUSAHAAN' }} -->
        </div>

        <!-- <div class="company-address">
            {{ $memo->holding->alamat ?? 'Alamat Perusahaan' }}
        </div> -->
    </div>

    <!-- TITLE -->
    <div class="title">
        <span class="title-memo">
            MEMO INTERNAL
        </span>
        <br>
        <span class="sub-title">
            {{ $memo->nomor_memo }}
        </span>
    </div>

    <!-- INFO -->
    <table class="table-info">
        <tr>
            <td width="10%">Perihal</td>
            <td id="perihal">: {{ $memo->perihal }}</td>
        <tr width="100%">
            <td>Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($memo->tanggal_memo)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr width="100%">
            <td>Kepada</td>
            <td>: @if($memo->penerima == 'all') Seluruh Karyawan @else {{ $memo->penerima }} @endif</td>
        </tr>
    </table>

    <!-- PEMBUKA -->
    <div class="content">
        Dengan hormat,<br><br>
        <div class="isi">
            {!! $memo->isi !!}
        </div>
    </div>

    <!-- HOLIDAY -->
    @if($memo->is_holiday)
    <div class="holiday">
        <br>
        Sehubungan dengan hal tersebut, diberitahukan bahwa hari libur berlaku pada:<br><br>

        - Tanggal Mulai : {{ \Carbon\Carbon::parse($memo->tanggal_mulai_libur)->translatedFormat('d F Y') }}<br>
        - Tanggal Selesai : {{ \Carbon\Carbon::parse($memo->tanggal_selesai_libur)->translatedFormat('d F Y') }}
    </div>
    @endif

    <!-- PENUTUP -->
    <div class="content">
        <br>
        Demikian memo ini disampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
    </div>

    <!-- TTD -->
    <div class="signature">
        Bekasi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br><br><br>

        <strong><u>{{ $memo->penandatangan }}</u></strong><br>
    </div>

    <div class="clear"></div>

</body>

</html>