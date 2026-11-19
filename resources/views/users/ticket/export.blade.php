<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>FORM UBAH HAPUS | {{ $ticket->nomor_ticket }}</title>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Dekorasi Abstrak */
        .abstract-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);
            clip-path: circle(50% at 70% 20%);
            z-index: -1;
            opacity: 0.1;
        }

        .container-fluid {
            padding: 30px 40px;
            position: relative;
        }

        /* =====================
           HEADER
        ===================== */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .no-doc {
            background: #6f42c1;
            color: white;
            padding: 5px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
            display: inline-block;
            text-align: center;
        }

        .title {
            text-align: center;
        }

        .title h2 {
            margin: 0;
            margin-right: 3px;
            font-size: 19px;
            /* font-weight: 800; */
            color: #3b1d75;
            line-height: 1.1;
            white-space: nowrap;
            /* letter-spacing: 0.3px; */
        }

        .title small {
            color: #666;
            font-size: 12px;
            font-weight: 500;
            display: block;
            margin-top: 5px;
        }

        /* =====================
           SECTION TITLES
        ===================== */
        .section-title {
            background: #f1edff;
            color: #5a32a3;
            padding: 2px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 10px;
            /* margin-top: 10px; */
            border-left: 5px solid #6f42c1;
        }

        .section-title-center {
            background: #f1edff;
            color: #5a32a3;
            padding: 4px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            margin-bottom: 8px;
            margin-top: 15px;
            text-align: center;
        }

        /* =====================
           INFO TABLE
        ===================== */
        .info-table {
            width: 100%;
        }

        .info-table th {
            width: 35%;
            text-align: left;
            color: #777;
            font-weight: 400;
            padding: 2px 0;
            font-size: 10px;
        }

        .info-table td {
            font-weight: 600;
            color: #222;
            padding: 1px 0;
            font-size: 10px;
        }

        /* =====================
           HIGHLIGHT
        ===================== */
        .highlight-purple {
            color: #6f42c1;
            font-weight: 700;
            font-size: 13px;
        }

        .badge-menu {
            display: inline-block;
            background: #e6e0f8;
            color: #6f42c1;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-progress {
            background: #dbeafe;
            color: #1e40af;
        }

        /* =====================
           DETAIL TABLE
        ===================== */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 4px;
        }

        .detail-table th {
            background: #f1edff;
            color: #5a32a3;
            font-size: 9px;
            font-weight: 700;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #d4c8f0;
        }

        .detail-table td {
            border: 1px solid #d4c8f0;
            padding: 5px 4px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            font-size: 9px;
            color: #333;
        }

        .detail-table tr:nth-child(even) {
            background: #faf8ff;
        }

        /* =====================
           APPROVAL / SIGNATURE
        ===================== */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 4px;
        }
        .approval-table th {
            background-color: #f1edff;
            color: #6f42c1;
            text-align: center;
            font-weight: 600;
            font-size: 9px;
            padding: 6px 4px;
            border: 1px solid #d4c8f0;
        }
        .approval-table td {
            border: 1px solid #d4c8f0;
            text-align: center;
            padding: 8px 4px;
            vertical-align: top;
        }
        .signature-area {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
        }
        .signature-area img {
            max-height: 65px;
            max-width: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .name-label {
            font-weight: 600;
            text-decoration: underline;
            margin: 0;
            font-size: 9px;
            color: #222;
        }

        .role-label {
            font-size: 8px;
            color: #6f42c1;
            font-weight: 600;
            margin: 0;
        }

        .tgl-label {
            margin: 2px 0 0;
            font-size: 8px;
            color: #777;
        }

        /* =====================
           WATERMARK
        ===================== */
        .watermark {
            position: fixed;
            top: 380px;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 40px;
            border-radius: 15px;
            text-align: center;
            font-size: 52px;
            font-weight: bold;
            letter-spacing: 1px;
            background: transparent;
            border: 8px solid currentColor;
            z-index: -1;
            opacity: 0.12;
            box-sizing: border-box;
            white-space: nowrap;
        }

        /* =====================
           FOOTER
        ===================== */
        footer {
            margin-top: 15px;
            text-align: center;
        }

        footer small {
            font-size: 8px;
            color: #9c9c9c;
        }

        .reprint-info {
            font-size: 8px;
            color: #aaa;
            text-align: left;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="abstract-bg"></div>

    @php
        $watermarkMap = [
            \App\Models\TicketHead::STATUS_DRAFT                 => ['text' => 'DRAFT', 'color' => 'rgba(156, 163, 175, 0.85)'],
            \App\Models\TicketHead::STATUS_SUBMITTED             => ['text' => 'DIAJUKAN', 'color' => 'rgba(255, 126, 6, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_APPROVAL1     => ['text' => 'DISETUJUI ATASAN 1', 'color' => 'rgba(2, 132, 199, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_APPROVAL2     => ['text' => 'DISETUJUI ATASAN 2', 'color' => 'rgba(2, 132, 199, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_CHECKER       => ['text' => 'MENUNGGU DIPERIKSA', 'color' => 'rgba(255, 165, 0, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_VALIDATOR     => ['text' => 'MENUNGGU DIVALIDASI', 'color' => 'rgba(22, 163, 74, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_ASSIGNER      => ['text' => 'DIVALIDASI', 'color' => 'rgba(2, 132, 199, 0.85)'],
            \App\Models\TicketHead::STATUS_APPROVED              => ['text' => 'DISETUJUI', 'color' => 'rgba(0, 127, 44, 0.85)'],
            \App\Models\TicketHead::STATUS_ASSIGNED              => ['text' => 'DITUGASKAN', 'color' => 'rgba(2, 132, 199, 0.85)'],
            \App\Models\TicketHead::STATUS_IN_PROGRESS           => ['text' => 'DIKERJAKAN', 'color' => 'rgba(29, 78, 216, 0.85)'],
            \App\Models\TicketHead::STATUS_WAITING_REQUESTOR     => ['text' => 'MENUNGGU RESPON PEMINTA', 'color' => 'rgba(255, 165, 0, 0.85)'],
            \App\Models\TicketHead::STATUS_FEEDBACK_REQUESTOR    => ['text' => 'RESPON BALASAN PEMINTA', 'color' => 'rgba(220, 38, 38, 0.85)'],
            \App\Models\TicketHead::STATUS_CLOSED                => ['text' => 'SELESAI', 'color' => 'rgba(0, 127, 44, 0.85)'],
            \App\Models\TicketHead::STATUS_CANCELLED             => ['text' => 'DIBATALKAN', 'color' => 'rgba(220, 38, 38, 0.85)'],
            \App\Models\TicketHead::STATUS_PENDING_EXTERNAL      => ['text' => 'PENDING', 'color' => 'rgba(255, 165, 0, 0.85)'],    
        ];
        if ($ticket->approval_status === \App\Models\TicketHead::APPROVAL_REJECTED) {
            $wm = ['text' => 'DITOLAK', 'color' => 'rgba(220, 38, 38, 0.85)'];
        } else {
            $wm = $watermarkMap[$ticket->ticket_status] ?? ['text' => strtoupper($ticket->ticket_status ?? 'UNKNOWN'), 'color' => 'rgba(0,0,0,0.5)'];
        }
    @endphp

    @php
        // Resolve holding info strictly based on ticket environment / kategori
        $kategori = (int) ($ticket->ticketMenu?->kategori ?? 1);
        
        $envCompanyMap = [
            1 => [
                'name'        => 'PT SURYA PANGAN SEMESTA',
                'code'        => 'SPS',
                'local_logo'  => public_path('holding/assets/img/logosps.png'),
                'remote_logo' => 'https://hrd.sumberpangan.store/storage/holding_image/logosps.png',
            ],
            2 => [
                'name'        => 'CV SUMBER PANGAN',
                'code'        => 'SP',
                'local_logo'  => public_path('holding/assets/img/logosp.png'),
                'remote_logo' => 'https://hrd.sumberpangan.store/storage/holding_image/logosp.png',
            ],
            3 => [
                'name'        => 'CV AGILE JAYA ABADI',
                'code'        => 'AJA',
                'local_logo'  => public_path('holding/assets/img/logoaja.png'),
                'remote_logo' => 'https://hrd.sumberpangan.store/storage/holding_image/logoaja.png',
            ],
        ];

        $envInfo     = $envCompanyMap[$kategori] ?? $envCompanyMap[1];
        $holdingName = $envInfo['name'];
        $holdingLogo = '';

        $localFile = null;
        if (isset($holding) && $holding && $holding->holding_image) {
            $holdingName = $holding->holding_name ?? $envInfo['name'];
            $storagePath = public_path('storage/holding_image/' . $holding->holding_image);
            if (file_exists($storagePath)) {
                $localFile = $storagePath;
            }
        }

        if (!$localFile && file_exists($envInfo['local_logo'])) {
            $localFile = $envInfo['local_logo'];
        }

        if ($localFile && file_exists($localFile)) {
            $imgData = base64_encode(file_get_contents($localFile));
            $ext = pathinfo($localFile, PATHINFO_EXTENSION) ?: 'png';
            $holdingLogo = 'data:image/' . $ext . ';base64,' . $imgData;
        } else {
            $holdingLogo = $envInfo['remote_logo'];
        }
    @endphp

    <div class="watermark" style="color: {{ $wm['color'] }};">{{ $wm['text'] }}</div>

    <div class="container-fluid">
        {{-- ==================== HEADER ==================== --}}
        <table class="header-table">
            <tr>
                <td width="17%" align="left">
                    @if($holdingLogo)
                        <img src="{{ $holdingLogo }}" style="max-width:85px; max-height:65px; height:auto;">
                    @endif
                </td>
                <td width="55%" class="title">
                    <h2>FORM UBAH HAPUS DATABASE</h2>
                    <small>Tanggal Pengajuan: {{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y') }}</small>
                </td>
                <td width="28%" align="right">
                    <div class="no-doc">
                        {{ $ticket->nomor_ticket }}
                    </div>
                </td>
            </tr>
        </table>
        {{-- ==================== INFO KARYAWAN & DETAIL TICKET ==================== --}}
        <table style="width: 100%;">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div class="section-title">Informasi Pemohon</div>
                    <table class="info-table">
                        <tr>
                            <th>NIK</th>
                            <td>: {{ $requestorKaryawan->nomor_identitas_karyawan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>: {{ $ticket->requestor_name ?? $requestorKaryawan->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Departemen / Divisi</th>
                            <td>: {{ $karyawanJabatan?->Departemen?->nama_departemen ?? '-' }} / {{ $karyawanJabatan?->Divisi?->nama_divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>: {{ $karyawanJabatan?->Jabatan?->nama_jabatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Kerja</th>
                            <td>: {{ $requestorKaryawan?->PenempatanKerja?->site_name ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; vertical-align: top;">
                    <div class="section-title">Detail Ticket</div>
                    <table class="info-table">
                        <tr>
                            <td colspan="2">
                                <small style="color:#777;">Perusahaan:</small><br>
                                <strong style="color:#222; font-size:10px;">{{ $holdingName }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <small style="color:#777;">Menu:</small><br>
                                <span class="badge-menu">{{ $ticket->ticketMenu?->nama_menu ?? '-' }}</span>
                            </td>
                            <td align="right">
                                <small style="color:#777;">Jumlah Detail:</small><br>
                                <span class="highlight-purple">{{ $ticket->ticketDetails->count() }} Item</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <small style="color:#777;">Jenis Transaksi:</small><br>
                                <span class="badge-menu">{{ $ticket->ticketSubMenu?->nama_sub_menu ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <small style="color:#777;">Status:</small><br>
                                @php
                                    $meta = \App\Models\TicketHead::getStatusMeta($ticket->ticket_status, $ticket->approval_status);
                                    $statusLabel = strtoupper($meta['label']);
                                    $statusClass = match($meta['class']) {
                                        'status-approved', 'status-done' => 'badge-approved',
                                        'status-rejected' => 'badge-rejected',
                                        'status-process', 'status-ready' => 'badge-progress',
                                        default => 'badge-pending'
                                    };
                                @endphp
                                <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        {{-- ==================== DATA DETAIL TICKET ==================== --}}
        <div class="section-title" style="margin-top:15px;">Data Perubahan</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:18%;">Nomor Transaksi</th>
                    <th style="width:10%;">Line</th>
                    <th style="width:23%;">Alasan</th>
                    <th style="width:22%;">Sebelum</th>
                    <th style="width:23%;">Sesudah</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($ticket->ticketDetails as $detail)
                    @php
                        $grouped = $detail->subDetails->groupBy('line_number');
                        $rowspan = max($grouped->count(), 1);
                    @endphp
                    @if($grouped->isEmpty())
                        <tr>
                            <td style="text-align:center;">{{ $no++ }}</td>
                            <td>
                                {{ $detail->nomor_detail }}
                                @if($detail->referenceTicketDetail)
                                    <br><span style="font-size:7.5px; color:#5b3db8; font-weight:normal;">
                                        Ref: {{ $detail->referenceTicketDetail->nomor_detail }}
                                        @if($detail->referenceTicketDetail->tanggal_transaksi)
                                            ({{ \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->format('d/m/Y') }})
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @else
                        @foreach($grouped as $lineNumber => $subs)
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">
                                        {{ $no++ }}
                                    </td>
                                    <td rowspan="{{ $rowspan }}" style="vertical-align:middle;">
                                        {{ $detail->nomor_detail }}
                                        @if($detail->referenceTicketDetail)
                                            <br><span style="font-size:7.5px; color:#5b3db8; font-weight:normal;">
                                                Ref: {{ $detail->referenceTicketDetail->nomor_detail }}
                                                @if($detail->referenceTicketDetail->tanggal_transaksi)
                                                    ({{ \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->format('d/m/Y') }})
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                <td style="text-align:center;">{{ $lineNumber }}</td>
                                <td>{!! implode('<br>', $subs->pluck('alasan_ubah')->toArray()) !!}</td>
                                <td>{!! implode('<br>', $subs->pluck('before_ubah')->toArray()) !!}</td>
                                <td>{!! implode('<br>', $subs->pluck('after_ubah')->toArray()) !!}</td>
                            </tr>
                        @endforeach
                    @endif
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:#999; padding:15px;">Tidak ada data detail.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ==================== LEMBAR PENGESAHAN ==================== --}}
        <div class="section-title-center">Lembar Pengesahan</div>

        @php
            $signers = [];

            // 1. Dibuat Oleh (Requestor)
            $signers[] = [
                'role_label' => 'Dibuat Oleh',
                'label'      => 'Pemohon',
                'name'       => $ticket->requestor_name,
                'sign'       => $ticket->requestor_signature,
                'date'       => $ticket->created_at,
            ];

            // 2. Approver 1 (Diketahui / Disetujui)
            if (!empty($ticket->approve1_name)) {
                $signers[] = [
                    'role_label' => !empty($ticket->approve2_name) ? 'Diketahui Oleh' : 'Disetujui Oleh',
                    'label'      => 'Atasan 1',
                    'name'       => $ticket->approve1_name,
                    'sign'       => $ticket->approve1_signature,
                    'date'       => $ticket->approve1_at,
                ];
            }

            // 3. Approver 2 (Disetujui)
            if (!empty($ticket->approve2_name)) {
                $signers[] = [
                    'role_label' => 'Disetujui Oleh',
                    'label'      => 'Atasan 2',
                    'name'       => $ticket->approve2_name,
                    'sign'       => $ticket->approve2_signature,
                    'date'       => $ticket->approve2_at,
                ];
            }

            // 4. Checker (from TicketApproval entries with role = 'checker')
            foreach ($ticket->approvals->where('role', 'checker') as $approval) {
                $checkerName = $approval->karyawan?->name ?? $approval->karyawan_name ?? '-';
                $isCross = (bool) ($approval->dependencyRule?->is_cross_environment ?? false);
                $signers[] = [
                    'role_label' => 'Diperiksa Oleh' . ($isCross ? ' (Lintas Sistem)' : ''),
                    'label'      => 'Checker' . ($isCross ? ' (Lintas Sistem)' : ''),
                    'name'       => $checkerName,
                    'sign'       => $approval->signature,
                    'date'       => $approval->approved_at,
                ];
            }

            // 5. Validator
            $signers[] = [
                'role_label' => 'Divalidasi Oleh',
                'label'      => 'Validator',
                'name'       => $ticket->validator_name ?: 'JM Finance & Accounting',
                'sign'       => $ticket->validator_signature,
                'date'       => $ticket->validator_at,
            ];

            $signerWidth = count($signers) > 0 ? round(100 / count($signers), 2) : 100;
        @endphp

        <table class="approval-table">
            <thead>
                <tr>
                    @foreach($signers as $signer)
                        <th style="width:{{ $signerWidth }}%;">{{ $signer['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($signers as $signer)
                        <td>
                            <p class="role-label">{{ $signer['role_label'] }}</p>

                            <div class="signature-area">
                                @if(!empty($signer['sign']))
                                    <img src="{{ storage_path('app/public/signatures/' . basename((string)$signer['sign'])) }}"
                                        style="max-height:65px; max-width:120px; width:auto;">
                                @endif
                            </div>

                            <p class="name-label">{{ $signer['name'] ?? '-' }}</p>
                            <p class="tgl-label">
                                {{ $signer['date'] ? \Carbon\Carbon::parse($signer['date'])->translatedFormat('d M Y, H:i') : '-' }}
                            </p>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        {{-- ==================== FOOTER ==================== --}}
        <footer>
            <small>Dokumen ini dibuat secara otomatis oleh Sistem Ticketing HRD-APPS &copy; {{ date('Y') }}</small>
        </footer>

        <div class="reprint-info">
            Dicetak pada {{ date('d M Y, H:i') }} oleh
            {{ auth()->user()->KaryawanNIK?->name ?? auth()->user()->name }}
        </div>
    </div>
</body>

</html>