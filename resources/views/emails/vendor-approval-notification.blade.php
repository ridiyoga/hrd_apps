<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Vendor</title>
</head>

<body style="margin:0;background:#f4f5f7;font-family:Arial,sans-serif;color:#253044;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px;background:#5b3db8;color:#ffffff;">
                            <h1 style="margin:0;font-size:21px;">Pengajuan Vendor Menunggu {{ $roleLabel }}</h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#eee7fb;">{{ $vendor->nomor_pengajuan_fis }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px;">Yth. {{ $recipientName }},</p>
                            <p style="margin:0 0 20px;line-height:1.6;">
                                Ada pengajuan vendor yang membutuhkan tindakan Anda sebagai <strong>{{ $roleLabel }}</strong>.
                                Silakan buka detail pengajuan melalui tombol di bawah ini.
                            </p>

                            <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;font-size:13px;margin:0 0 24px;">
                                <tr>
                                    <td colspan="2" style="border:1px solid #ded7eb;background:#f7f4fd;font-weight:bold;">
                                        {{ $vendor->nomor_pengajuan_fis }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:36%;border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Pengaju</td>
                                    <td style="border:1px solid #ded7eb;">{{ $vendor->creator_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Nama Usaha</td>
                                    <td style="border:1px solid #ded7eb;">{{ $vendor->data->nama_usaha ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Kategori Vendor</td>
                                    <td style="border:1px solid #ded7eb;">
                                        @php
                                            $cat = $vendor->vendor_category ?? '-';
                                            echo match($cat) {
                                                'V01' => 'Bahan Baku',
                                                'V02' => 'Non Bahan Baku',
                                                default => $cat,
                                            };
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Sistem</td>
                                    <td style="border:1px solid #ded7eb;">
                                        @php
                                            $sys = (int) ($vendor->system_type ?? 0);
                                            echo match($sys) {
                                                1 => 'Epicor',
                                                2 => 'Accurate',
                                                default => '-',
                                            };
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Status Saat Ini</td>
                                    <td style="border:1px solid #ded7eb;">{{ $vendor->current_step ?? '-' }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;">
                                <a href="{{ $actionUrl }}" style="display:inline-block;background:#5b3db8;color:#ffffff;text-decoration:none;font-weight:bold;padding:11px 18px;border-radius:6px;">
                                    Buka Pengajuan Vendor
                                </a>
                            </p>

                            <p style="margin:0;font-size:12px;line-height:1.5;color:#687386;">
                                Email ini dibuat otomatis oleh HRD APP. Abaikan email ini jika pengajuan sudah Anda proses.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
