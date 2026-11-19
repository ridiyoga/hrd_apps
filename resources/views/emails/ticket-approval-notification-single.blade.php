<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Ticket</title>
</head>

<body style="margin:0;background:#f4f5f7;font-family:Arial,sans-serif;color:#253044;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px;background:#5b3db8;color:#ffffff;">
                            <h1 style="margin:0;font-size:21px;">
                                @if(($trigger ?? '') === 'feedback')
                                Pemberitahuan Umpan Balik / Revisi Ticket
                                @elseif(($trigger ?? '') === 'waiting_requestor')
                                Konfirmasi Hasil Pengerjaan Ticket
                                @elseif(($trigger ?? '') === 'assigned')
                                Pemberitahuan Penugasan PIC Ticket
                                @elseif(($trigger ?? '') === 'reassigned')
                                Pemberitahuan Pengalihan Penugasan PIC Ticket
                                @else
                                Ticket Menunggu Persetujuan {{ $roleLabel }}
                                @endif
                            </h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#eee7fb;">Nomor Referensi: {{ $ticket->nomor_ticket }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 14px;">Yth. {{ $recipientName }},</p>
                            <p style="margin:0 0 20px;line-height:1.6;">
                                @if(($trigger ?? '') === 'feedback')
                                Pengaju ticket telah memberikan catatan feedback/revisi untuk ticket <strong>{{ $ticket->nomor_ticket }}</strong>. Silakan periksa rincian catatannya pada bagian di bawah ini.
                                @elseif(($trigger ?? '') === 'waiting_requestor')
                                PIC telah menyelesaikan pengerjaan ticket <strong>{{ $ticket->nomor_ticket }}</strong>. Mohon dilakukan pengecekan dan berikan konfirmasi selesai (Close) atau feedback revisi apabila masih diperlukan.
                                @elseif(($trigger ?? '') === 'assigned')
                                Anda ditugaskan sebagai PIC untuk menindaklanjuti ticket <strong>{{ $ticket->nomor_ticket }}</strong>. Mohon segera diproses.
                                @elseif(($trigger ?? '') === 'reassigned')
                                Penugasan ticket <strong>{{ $ticket->nomor_ticket }}</strong> telah dialihkan/diperbarui kepada Anda sebagai PIC baru. Mohon ditindaklanjuti.
                                @else
                                Terdapat ticket yang membutuhkan tindak lanjut Anda selaku <strong>{{ $roleLabel }}</strong>. Rincian ticket dapat dilihat pada tabel di bawah ini.
                                @endif
                            </p>
                            <h5 style="margin:0 0 10px;">
                                Ringkasan Ticket {{ $ticket->nomor_ticket }}
                            </h5>
                            <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;font-size:13px;margin:0 0 18px;">
                                <tr>
                                    <td colspan="2" style="border:1px solid #ded7eb;background:#f7f4fd;font-weight:bold;">{{ $ticket->nomor_ticket }}</td>
                                </tr>
                                <tr>
                                    <td style="width:32%;border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Pemohon</td>
                                    <td style="border:1px solid #ded7eb;">{{ $ticket->requestor_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Modul</td>
                                    <td style="border:1px solid #ded7eb;">{{ $ticket->ticketMenu?->nama_menu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Jenis Transaksi</td>
                                    <td style="border:1px solid #ded7eb;">{{ $ticket->ticketSubMenu?->nama_sub_menu ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #ded7eb;background:#fbfaff;font-weight:bold;">Status</td>
                                    <td style="border:1px solid #ded7eb;">{{ $ticket->approval_status ?? '-' }}</td>
                                </tr>
                            </table>
                            <table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse;font-size:12px;margin:0 0 24px;">
                                <tr>
                                    <td colspan="6" style="border:1px solid #ded7eb;background:#eef4ff;font-weight:bold;">Rincian Ticket: {{ $ticket->nomor_ticket }}</td>
                                </tr>
                                <tr>
                                    <th align="center" style="border:1px solid #ded7eb;background:#f7f4fd;width:5%;">No.</th>
                                    <th align="left" style="border:1px solid #ded7eb;background:#f7f4fd;width:20%;">Nomor Transaksi</th>
                                    <th align="left" style="border:1px solid #ded7eb;background:#f7f4fd;width:12%;">Nomor Line</th>
                                    <th align="left" style="border:1px solid #ded7eb;background:#f7f4fd;width:21%;">Alasan Perubahan</th>
                                    <th align="left" style="border:1px solid #ded7eb;background:#f7f4fd;width:21%;">Data Sebelumnya</th>
                                    <th align="left" style="border:1px solid #ded7eb;background:#f7f4fd;width:21%;">Data Sesudah Perubahan</th>
                                </tr>
                                @php $detailCount = 0; @endphp
                                @forelse($ticket->ticketDetails ?? [] as $detail)
                                @php
                                $grouped = $detail->subDetails->groupBy('line_number');
                                $rowspan = $grouped->count();
                                $detailCount++;
                                @endphp
                                @foreach($grouped as $lineNumber => $subs)
                                <tr>
                                    @if($loop->first)
                                    <td rowspan="{{ $rowspan }}" align="center" style="border:1px solid #ded7eb;vertical-align:top;">{{ $detailCount }}</td>
                                    <td rowspan="{{ $rowspan }}" style="border:1px solid #ded7eb;vertical-align:top;">
                                        {{ $detail->nomor_detail ?? '-' }}
                                        @if($detail->referenceTicketDetail)
                                            <br><span style="font-size:10px; color:#5b3db8; font-weight:normal;">Ref: {{ $detail->referenceTicketDetail->nomor_detail ?? '-' }}{{ $detail->referenceTicketDetail->tanggal_transaksi ? ' (' . \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->format('d/m/Y') . ')' : '' }}</span>
                                        @endif
                                    </td>
                                    @endif
                                    <td style="border:1px solid #ded7eb;vertical-align:top;">{{ $lineNumber }}</td>
                                    <td style="border:1px solid #ded7eb;vertical-align:top;white-space:pre-wrap;">{!! implode('<br>', $subs->pluck('alasan_ubah')->toArray()) !!}</td>
                                    <td style="border:1px solid #ded7eb;vertical-align:top;white-space:pre-wrap;">{!! implode('<br>', $subs->pluck('before_ubah')->toArray()) !!}</td>
                                    <td style="border:1px solid #ded7eb;vertical-align:top;white-space:pre-wrap;">{!! implode('<br>', $subs->pluck('after_ubah')->toArray()) !!}</td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan="6" style="border:1px solid #ded7eb;text-align:center;">Tidak terdapat rincian pada ticket ini</td>
                                </tr>
                                @endforelse
                            </table>

                            <p style="margin:0 0 24px;">
                                <a href="{{ $actionUrl }}" style="display:inline-block;background:#5b3db8;color:#ffffff;text-decoration:none;font-weight:bold;padding:11px 18px;border-radius:6px;">
                                    Tinjau Ticket
                                </a>
                            </p>
                            <p style="margin:0 0 18px;line-height:1.6;">
                                Terima kasih atas perhatian dan kerja sama Anda.
                            </p>
                            <!-- <p style="margin:0 0 4px;">Salam,</p>
                            <p style="margin:0;font-weight:bold;">Ticket Ubah/Hapus</p> -->
                            <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
                            <p style="margin:0;font-size:12px;line-height:1.5;color:#687386;">
                                Email ini dikirim secara otomatis dari menu Ticket Ubah/Hapus. Abaikan email ini jika ticket sudah Anda proses.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>