<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Mapping Shift</title>
</head>
<body style="margin:0;background:#f4f5f7;font-family:Arial,sans-serif;color:#253044;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:760px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;background:#6f42c1;color:#ffffff;">
                            <h1 style="margin:0;font-size:22px;">Pengingat Mapping Shift</h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#eee7fb;">
                                Periode {{ $periodStart->format('d/m/Y') }}–{{ $periodEnd->format('d/m/Y') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;">Yth. {{ $supervisorName }},</p>
                            <p style="margin:0 0 20px;line-height:1.6;">
                                Berikut bawahan Anda yang memiliki minimal satu hari berstatus
                                <strong>TIDAK HADIR (Shift: SHIFT BELUM DITENTUKAN)</strong> dalam tujuh hari terakhir.
                                Mohon periksa dan lengkapi mapping shift yang sesuai.
                            </p>

                            <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;font-size:13px;">
                                <thead>
                                    <tr style="background:#f0eafa;text-align:left;">
                                        <th style="border:1px solid #ded7eb;">Karyawan</th>
                                        <th style="border:1px solid #ded7eb;">Jabatan/Unit</th>
                                        <th style="border:1px solid #ded7eb;">Tanggal belum mapping</th>
                                        <th style="border:1px solid #ded7eb;">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $item)
                                        <tr>
                                            <td style="border:1px solid #ded7eb;vertical-align:top;">
                                                <strong>{{ $item['employee']->name }}</strong><br>
                                                <span style="color:#687386;">{{ $item['employee']->nomor_identitas_karyawan }}</span>
                                            </td>
                                            <td style="border:1px solid #ded7eb;vertical-align:top;">
                                                {{ $item['position'] }}<br>
                                                <span style="color:#687386;">{{ $item['department'] }} / {{ $item['division'] }}</span>
                                            </td>
                                            <td style="border:1px solid #ded7eb;vertical-align:top;">
                                                {{ $item['missing_dates']->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d/m/Y'))->join(', ') }}
                                            </td>
                                            <td style="border:1px solid #ded7eb;vertical-align:top;">
                                                <a href="{{ $item['mapping_url'] }}" style="color:#6f42c1;font-weight:bold;">Buka mapping shift</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <p style="margin:20px 0 0;font-size:12px;line-height:1.5;color:#687386;">
                                Email ini dibuat otomatis oleh HRD APP. Hari libur perusahaan dan cuti yang telah disetujui tidak dimasukkan ke dalam daftar.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
