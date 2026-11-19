<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Pengingat Dokumen Legal</title>
</head>

<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="680" cellspacing="0" cellpadding="0"
                    style="width:680px;max-width:94%;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #e5e7eb;">
                            <h2 style="margin:0;font-size:18px;color:#111827;">Pengingat Dokumen Legal</h2>
                            <p style="margin:8px 0 0;font-size:14px;color:#6b7280;">
                                Halo {{ $recipientName }}, berikut dokumen legal yang perlu dicek.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="border-collapse:collapse;font-size:13px;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;">Nomor</th>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;">Nama</th>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;">Kategori</th>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;">Tanggal Akhir</th>
                                        <th align="left" style="padding:10px;border-bottom:1px solid #e5e7eb;">Reminder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                                                {{ $document['nomer_dukumen'] }}
                                            </td>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                                                {{ $document['nama'] }}
                                            </td>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                                                {{ $document['kategori'] }}
                                            </td>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                                                {{ $document['tanggal_akhir'] }}
                                            </td>
                                            <td style="padding:10px;border-bottom:1px solid #f3f4f6;">
                                                {{ $document['reminder_label'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="margin-top:20px;">
                                <a href="{{ $actionUrl }}"
                                    style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:6px;font-size:14px;">
                                    Buka Dokumen Legal
                                </a>
                            </div>

                            <p style="margin:20px 0 0;font-size:12px;color:#6b7280;">
                                Email ini dibuat otomatis oleh HRD APP. Abaikan email ini jika dokumen sudah diproses.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
