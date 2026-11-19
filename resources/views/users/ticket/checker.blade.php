@extends('users.layouts.main')
@section('title') APPS | APPROVAL - Checker Ticket @endsection
@section('css')
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #0ea5e9, #38bdf8);
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(14, 165, 233, .7);
    }

    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #e0f2fe;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0284c7;
        font-size: 14px;
        text-decoration: none;
        z-index: 10;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .status-pending, .status-waiting {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-approved, .status-done {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }

    .status-process, .status-ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .status-draft {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .status-feedback {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    .kondisi-check-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #f0f9ff, #fff);
        border: 1.5px solid #bae6fd;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 10px;
        gap: 12px;
        transition: box-shadow .2s;
    }

    .kondisi-check-card.is-active {
        border-color: #0ea5e9;
        box-shadow: 0 4px 16px rgba(14, 165, 233, .2);
    }

    .kcc-info {
        flex: 1;
        overflow: hidden;
    }

    .kcc-label {
        font-size: 15px;
        font-weight: 700;
        color: #0c4a6e;
        margin-bottom: 4px;
    }

    .kcc-desc {
        font-size: 11px;
        color: #6b7280;
    }

    .kcc-generate {
        font-size: 10px;
        font-weight: 700;
        color: #0284c7;
        background: #e0f2fe;
        border-radius: 6px;
        padding: 3px 8px;
        margin-top: 5px;
        display: inline-block;
    }

    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #e2e8f0;
        border-radius: 50px;
        transition: background .3s;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, .1);
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform .3s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .15);
    }

    .toggle-switch input:checked+.toggle-slider {
        background: linear-gradient(135deg, #0ea5e9, #38bdf8);
    }

    .toggle-switch input:checked+.toggle-slider::before {
        transform: translateX(24px);
    }

    .sig-checker-card {
        border: 2px dashed #38bdf8;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 16px rgba(14, 165, 233, .1);
    }

    .sig-checker-header {
        background: linear-gradient(135deg, #f0f9ff, #fff);
        border-bottom: 1px solid #bae6fd;
        padding: 10px 14px;
        text-align: center;
    }

    #checker-signature-pad {
        width: 100%;
        height: 180px;
        touch-action: none;
        display: block;
        cursor: crosshair;
    }

    .btn-checker {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border: none;
        border-radius: 16px;
        padding: 14px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .4px;
        box-shadow: 0 8px 24px rgba(2, 132, 199, .3);
        transition: transform .15s;
    }

    .btn-checker:active {
        transform: scale(.98);
    }

    .field-readonly {
        background: #f0f9ff;
        border: 1.5px solid #bae6fd;
        border-radius: 12px;
        padding: 11px 14px 11px 42px;
        font-size: 13px;
        color: #374151;
        min-height: 44px;
        display: flex;
        align-items: center;
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #94a3b8 !important;
        opacity: 1;
    }

    .form-view-group {
        position: relative;
        margin-bottom: 14px;
    }

    .form-view-group .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0284c7;
        font-size: 13px;
        z-index: 6;
        pointer-events: none;
    }

    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #0369a1;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        margin-top: 4px;
    }

    .alert-inline {
        font-size: 12px;
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: none;
    }

    .sig-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 14px;
        border-top: 1px solid #e0f2fe;
    }

    .change-card-item {
        background: linear-gradient(135deg, #f0f9ff, #ffffff);
        border: 1.5px solid #bae6fd;
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: box-shadow .2s, transform .15s;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .change-card-item:hover {
        box-shadow: 0 6px 20px rgba(14, 165, 233, .15);
        transform: translateY(-1px);
    }

    .change-card-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0ea5e9, #38bdf8);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .change-card-info .nomor {
        font-size: 12px;
        font-weight: 700;
        color: #0c4a6e;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .change-card-info .alasan {
        font-size: 11px;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lampiran-card {
        border: 1.5px solid #bae6fd;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        width: 140px;
    }

    .lampiran-card img {
        width: 100%;
        height: 90px;
        object-fit: cover;
    }

    .lampiran-card .lc-footer {
        padding: 6px 8px;
    }

    .lampiran-card .lc-name {
        font-size: 11px;
        color: #555;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }

    .subdetail-accordion-item {
        border: 1.5px solid #bae6fd;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .subdetail-accordion-item:hover {
        border-color: #7dd3fc;
    }

    .subdetail-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        cursor: pointer;
        user-select: none;
        background: #f0f9ff;
        gap: 8px;
        transition: background 0.15s ease;
    }

    .subdetail-accordion-header:hover {
        background: #e0f2fe;
    }

    .subdetail-accordion-header[aria-expanded="true"] {
        background: #e0f2fe;
        border-bottom: 1px solid #bae6fd;
    }

    .subdetail-chevron {
        color: #0369a1;
        font-size: 10px;
        transition: transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #bae6fd;
        flex-shrink: 0;
    }

    .subdetail-accordion-header[aria-expanded="true"] .subdetail-chevron {
        transform: rotate(180deg);
        background: #0369a1;
        color: #ffffff;
    }

    .subdetail-accordion-body {
        padding: 12px;
        background: #f8fafc;
    }
</style>
@endsection
@section('content_top')
<div class="card" style="position:relative;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon"><i class="fa-solid fa-magnifying-glass-check"></i></div>
        <h5 class="mt-3 mb-1" style="color:#0369a1; font-size:18px;">{{ $ticket->nomor_ticket }}</h5>
        <h5 class="mt-1 mb-1" style="font-size:14px; font-weight:700;">PEMERIKSAAN TICKET</h5>
        <small class="text-muted">Periksa kondisi lalu selesaikan pengecekan</small>
        <div class="mt-2 mb-1">
            @include('components.ticket-status-badge', ['ticket' => $ticket])
        </div>
    </div>
    <div id="alert_checker" class="mx-3 mt-2"></div>
    <div class="card-body px-3 pb-4">
        @if(!empty($ticket->origin_cross_ticket_id) && $ticket->originCrossTicket)
        <div class="alert alert-primary d-flex align-items-center gap-2 mb-3" style="font-size:12px; border-radius:10px; background:#eff6ff; border:1.5px solid #bfdbfe; color:#1e40af;">
            <i class="fa-solid fa-link fa-lg"></i>
            <div>
                <strong>Rujukan dari Sistem Terkait:</strong> Tiket ini merupakan tindak lanjut dari 
                <strong>{{ $ticket->originCrossTicket->nomor_ticket }}</strong> ({{ $ticket->originCrossTicket->ticketMenu?->nama_menu }} - {{ $ticket->originCrossTicket->ticketSubMenu?->nama_sub_menu }}).
            </div>
        </div>
        @endif

        @if($ticket->spawnedCrossTickets && $ticket->spawnedCrossTickets->isNotEmpty())
        <div class="alert alert-info d-flex align-items-center gap-2 mb-3" style="font-size:12px; border-radius:10px; background:#f0fdf4; border:1.5px solid #bbf7d0; color:#166534;">
            <i class="fa-solid fa-share-nodes fa-lg"></i>
            <div>
                <strong>Tiket Terkait pada Sistem / Cabang Lain:</strong>
                @foreach($ticket->spawnedCrossTickets as $sTicket)
                    <span class="badge bg-success ms-1">{{ $sTicket->nomor_ticket }} ({{ $sTicket->ticketMenu?->nama_menu }})</span>
                @endforeach
            </div>
        </div>
        @endif

        @include('components.parent-ticket-card')

        <form id="form_checker_ticket">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <input type="hidden" name="signature" id="checker_signature_input">
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-info me-2">1</span>
                    <h6 class="mb-0 fw-bold">Informasi Ticket</h6>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-folder-open"></i></span>
                    <div class="field-readonly">{{ $ticket->ticketMenu?->nama_menu ?? '-' }}</div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-list-ul"></i></span>
                    <div class="field-readonly">{{ $ticket->ticketSubMenu?->nama_sub_menu ?? '-' }}</div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                    <div class="field-readonly"><strong>{{ $ticket->requestor_name ?? '-' }} <br> Diajukan pada: {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y H:i:s') : '-' }}</div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-table-list"></i></span>
                    <div class="field-readonly">{{ $ticket->ticketDetails->count() }} detail perubahan</div>
                </div>
            </div>
            <div class="section-label" style="color:#0369a1;">
                <i class="fa-solid fa-table-list"></i> Detail Perubahan Data
            </div>
            @if($ticket->ticketDetails && $ticket->ticketDetails->count() > 0)
            @foreach($ticket->ticketDetails as $i => $detail)
            @php
            $jumlahBaris = $detail->subDetails->count();
            $tanggalLabel = $detail->tanggal_transaksi
            ? \Carbon\Carbon::parse($detail->tanggal_transaksi)->translatedFormat('d M Y')
            : null;
            $refTanggalLabel = $detail->referenceTicketDetail?->tanggal_transaksi
            ? \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->translatedFormat('d M Y')
            : null;
            @endphp
            <div class="change-card-item"
                data-detail-id="{{ $detail->id }}"
                data-bs-toggle="offcanvas"
                data-bs-target="#off-canvas-change-detail"
                data-nomor="{{ $detail->nomor_detail }}"
                data-tanggal="{{ $tanggalLabel }}"
                data-ref-nomor="{{ $detail->referenceTicketDetail?->nomor_detail ?? '' }}"
                data-ref-tanggal="{{ $refTanggalLabel ?? '' }}"
                data-subdetails="{{ $detail->subDetails->map(fn($sd) => [
                        'id'                => $sd->id,
                        'is_cancelled'      => (bool) $sd->is_cancelled,
                        'cancel_reason'     => $sd->cancel_reason,
                        'cancelled_by_role' => $sd->cancelled_by_role,
                        'cancelled_by_name' => $sd->cancelled_by_name,
                        'line_number' => $sd->line_number,
                        'alasan_ubah' => $sd->alasan_ubah,
                        'before_ubah' => $sd->before_ubah,
                        'after_ubah'  => $sd->after_ubah,
                    ])->toJson() }}"
                data-idx="{{ $i + 1 }}">
                <div class="change-card-num" style="background:linear-gradient(135deg, #0284c7, #38bdf8);">{{ $i + 1 }}</div>
                <div class="change-card-info">
                    <div class="nomor" style="color:#0369a1;">Transaksi: {{ $detail->nomor_detail ?: '-' }}</div>
                    <div class="alasan">{{ $jumlahBaris }} baris perubahan{{ $tanggalLabel ? ' · ' . $tanggalLabel : '' }}</div>
                    <div class="line-cancel-badge fw-semibold mt-1" data-detail-id="{{ $detail->id }}" style="display:none; font-size:10.5px; color:#dc2626;"></div>
                    @if($detail->referenceTicketDetail)
                    <div class="text-truncate mt-1" style="font-size:10.5px; color:#0284c7;">
                        <i class="fa-solid fa-link me-1"></i>Ref: {{ $detail->referenceTicketDetail->nomor_detail ?? '-' }}{{ $refTanggalLabel ? ' (' . $refTanggalLabel . ')' : '' }}
                    </div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-muted" style="font-size:11px;"></i>
            </div>
            @endforeach
            @else
            <div class="text-center text-muted py-3 rounded-3"
                style="font-size:12px; border:1.5px dashed #bae6fd; background:#f0f9ff;">
                <i class="fa-solid fa-table fa-lg mb-1 d-block text-info"></i>
                Tidak ada detail perubahan
            </div>
            @endif
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-info me-2"><i class="fa-solid fa-paperclip"></i></span>
                    <h6 class="mb-0 fw-bold">Lampiran</h6>
                </div>
                @php
                $attachmentsList = collect();
                if ($ticket->attachments && $ticket->attachments->isNotEmpty()) {
                $attachmentsList = $ticket->attachments;
                } elseif ($ticket->lampiran) {
                $attachmentsList = collect([(object)[
                'original_name' => basename($ticket->lampiran),
                'file_path' => $ticket->lampiran,
                'file_size' => null,
                ]]);
                }
                @endphp
                @if($attachmentsList->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($attachmentsList as $att)
                    @php
                    $filePath = is_array($att) ? ($att['file_path'] ?? '') : ($att->file_path ?? '');
                    $origName = is_array($att) ? ($att['original_name'] ?? basename($filePath)) : ($att->original_name ?? basename($filePath));
                    $fileSize = is_array($att) ? ($att['file_size'] ?? null) : ($att->file_size ?? null);
                    $fileUrl = asset('storage/' . $filePath);
                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    $isPdf = $ext === 'pdf';
                    $isExcel = in_array($ext, ['xls','xlsx','csv']);
                    $isDoc = in_array($ext, ['doc','docx']);
                    $isZip = in_array($ext, ['zip','rar','7z']);
                    @endphp
                    <div class="lampiran-card">
                        @if($isImg)
                        <div style="height:90px; background:#f8fafc; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                            <img src="{{ $fileUrl }}" alt="{{ $origName }}" style="width:100%; height:90px; object-fit:cover;">
                        </div>
                        @else
                        <div class="text-center py-3 d-flex align-items-center justify-content-center" style="height:90px; background:#f8fafc;">
                            @if($isPdf)
                            <i class="fa-solid fa-file-pdf text-danger" style="font-size:36px;"></i>
                            @elseif($isExcel)
                            <i class="fa-solid fa-file-excel text-success" style="font-size:36px;"></i>
                            @elseif($isDoc)
                            <i class="fa-solid fa-file-word text-primary" style="font-size:36px;"></i>
                            @elseif($isZip)
                            <i class="fa-solid fa-file-zipper text-warning" style="font-size:36px;"></i>
                            @else
                            <i class="fa-solid fa-file text-secondary" style="font-size:36px;"></i>
                            @endif
                        </div>
                        @endif
                        <div class="lc-footer">
                            <div class="lc-name" title="{{ $origName }}">{{ $origName }}</div>
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="btn btn-sm btn-info w-100 text-white" style="font-size:11px;">
                                <i class="fa-solid fa-eye me-1"></i> Lihat
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="field-readonly" style="color:#94a3b8; font-style:italic; font-size:12px; padding-left:14px;">
                    <i class="fa-solid fa-paperclip me-2" style="color:#bae6fd;"></i>Tidak ada lampiran
                </div>
                @endif
            </div>
            <div class="row g-2 d-flex flex-wrap justify-content-end align-items-stretch mb-4">
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                        style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIAJUKAN OLEH:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if(!empty($ticket->requestor_signature))
                            <img src="{{ asset('storage/' . $ticket->requestor_signature) }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important; display:none;"></i>
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $ticket->requestor_name ?? '-' }}
                            </small>
                            @if($ticket->created_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i:s') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                        style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-1:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if(!empty($ticket->approve1_signature))
                            <img src="{{ asset('storage/' . $ticket->approve1_signature) }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important; display:none;"></i>
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $ticket->approve1_name ?? '-' }}
                            </small>
                            @if($ticket->approve1_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve1_at)->translatedFormat('d M Y, H:i:s') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                        style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-2:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if(!empty($ticket->approve2_signature))
                            <img src="{{ asset('storage/' . $ticket->approve2_signature) }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important; display:none;"></i>
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $ticket->approve2_name ?? '-' }}
                            </small>
                            @if($ticket->approve2_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve2_at)->translatedFormat('d M Y, H:i:s') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($ticket->approvals && $ticket->approvals->where('role', 'checker')->count() > 0)
                @foreach($ticket->approvals->where('role', 'checker') as $chk)
                @php
                    $isCross = (bool) ($chk->dependencyRule?->is_cross_environment ?? false);
                    $targetEnvName = $chk->branchSubMenu?->ticketMenu?->kategori_display;
                    $targetMenuName = $chk->branchSubMenu?->ticketMenu?->nama_menu;
                    $targetSubMenuName = $chk->branchSubMenu?->nama_sub_menu;
                    $ruleLabelParts = [];
                    if (!empty($targetMenuName)) {
                        $ruleLabelParts[] = $targetMenuName;
                    }
                    if (!empty($targetSubMenuName) && $targetSubMenuName !== $targetMenuName) {
                        $ruleLabelParts[] = $targetSubMenuName;
                    }
                    $ruleDisplay = implode(' - ', $ruleLabelParts);
                    if (!empty($targetEnvName)) {
                        $ruleDisplay = ($ruleDisplay !== '' ? $ruleDisplay . ' · ' : '') . $targetEnvName;
                    }

                    $isDone = in_array($chk->status, ['completed', 'approved', 'skipped', 'done'])
                        || !empty($chk->signature)
                        || !empty($chk->approved_at)
                        || !empty($chk->generated_ticket_id)
                        || !empty($chk->generated_at)
                        || in_array($chk->generate_status, ['SUCCESS', 'SKIPPED', 'completed'])
                        || ((bool) $ticket->checked && ($ticket->checker_id == $chk->karyawan_id || $ticket->checker_name == $chk->karyawan_name));
                @endphp
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between position-relative"
                        style="background: {{ $isCross ? '#f0fdfa' : '#ffffff' }} !important; border: 1.5px solid {{ $isCross ? '#99f6e4' : '#e2e8f0' }} !important;">
                        <div>
                            <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">
                                DIPERIKSA OLEH{{ $ticket->approvals->where('role', 'checker')->count() > 1 ? ('-' . ($loop->index + 1)) : '' }}:
                            </small>
                            @if($isCross)
                            <span class="badge" style="background:#ccfbf1; color:#0f766e; font-size:7.5px; font-weight:700; border:1px solid #99f6e4; padding:1px 5px; border-radius:4px; margin-top:2px;" title="Pemeriksaan lintas environment (bersifat independen / non-blocking)">
                                <i class="fa-solid fa-arrows-split-up-and-left me-1"></i>Lintas Sistem
                            </span>
                            @endif
                        </div>
                        <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                            @if(!empty($chk->signature))
                            <img src="{{ asset('storage/' . $chk->signature) }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            <span class="badge bg-success mt-1" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                            @elseif($isDone)
                            <span class="badge bg-success" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                            @if(!empty($chk->generated_ticket_id) || $chk->generate_status === 'SUCCESS')
                            <small class="text-success d-block mt-1" style="font-size:8px; font-weight:600;"><i class="fa-solid fa-ticket me-1"></i>Generate Tiket Lanjutan</small>
                            @elseif($chk->generate_status === 'SKIPPED')
                            <small class="text-muted d-block mt-1" style="font-size:8px;"><i class="fa-solid fa-minus me-1"></i>Tidak Generate Tiket</small>
                            @endif
                            @else
                            <span class="badge bg-warning text-dark" style="font-size:9px;"><i class="fa-solid fa-clock me-1"></i>Menunggu Pemeriksaan</span>
                            @if($isCross)
                            <small style="font-size:7.5px; color:#0f766e; font-weight:600; line-height:1.1; margin-top:3px;">
                                Belum diperiksa<br><span style="color:#64748b; font-weight:400;">(Tidak Mempengaruhi Proses Approval)</span>
                            </small>
                            @endif
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $chk->karyawan_name ?? ($chk->karyawan?->name ?? '-') }}
                            </small>
                            @if(!empty($ruleDisplay))
                            <span style="font-size:8px; color:#4338ca !important; font-weight:600; line-height:1.2; display:block; margin-top:2px;">({{ $ruleDisplay }})</span>
                            @endif
                            @php
                            $chkDate = $chk->approved_at ?? ($chk->status === 'completed' || $chk->status === 'approved' ? $chk->updated_at : null);
                            @endphp
                            @if($chkDate)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($chkDate)->translatedFormat('d M Y, H:i:s') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                @php
                    $singleDone = (bool) $ticket->checked || !empty($ticket->checker_signature) || !empty($ticket->checked_at);
                @endphp
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                        style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIPERIKSA OLEH:</small>
                        <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                            @if(!empty($ticket->checker_signature))
                            <img src="{{ asset('storage/' . $ticket->checker_signature) }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            <span class="badge bg-success mt-1" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                            @elseif($singleDone)
                            <span class="badge bg-success" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                            @else
                            <span class="badge bg-warning text-dark" style="font-size:9px;"><i class="fa-solid fa-clock me-1"></i>Menunggu Pemeriksaan</span>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $ticket->checker_name ?? '-' }}
                            </small>
                            @if($ticket->checked_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->checked_at)->translatedFormat('d M Y, H:i:s') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-info me-2">2</span>
                    <h6 class="mb-0 fw-bold">Pemeriksaan Koreksi Modul Terkait</h6>
                </div>
                <small class="text-muted d-block mb-3" style="font-size:11px;">
                    <i class="fa-solid fa-circle-info me-1 text-info"></i>
                    Tentukan apakah perubahan data ini memerlukan pengajuan koreksi lanjutan pada modul terkait berikut:
                </small>
                @if($rules->count() > 0)
                @foreach($rules as $rule)
                @php
                $approval = $myApprovals->get($rule->target_sub_menu_id);
                $genStatus = $approval?->generate_status;
                $genTicket = $approval && $approval->generated_ticket_id
                ? \App\Models\TicketHead::find($approval->generated_ticket_id)
                : null;
                $isCrossEnv = !empty($rule->is_cross_environment);
                $envColors = [1 => '#2563eb', 2 => '#dc2626', 3 => '#16a34a'];
                $envBg     = [1 => '#eff6ff', 2 => '#fef2f2', 3 => '#f0fdf4'];
                $envBorder = [1 => '#bfdbfe', 2 => '#fecaca', 3 => '#bbf7d0'];
                $srcKat    = (int)($rule->source_kategori ?? 1);
                $tgtKat    = (int)($rule->target_kategori ?? 1);
                $srcName   = $rule->source_kategori_name ?? 'Epicor ERP SPS';
                $tgtName   = $rule->target_kategori_name ?? 'Epicor ERP SPS';
                $tgtColor  = $envColors[$tgtKat] ?? '#2563eb';
                $tgtBg     = $envBg[$tgtKat] ?? '#eff6ff';
                $tgtBdr    = $envBorder[$tgtKat] ?? '#bfdbfe';
                @endphp
                <div class="kondisi-rule-card-wrapper p-3 mb-3 border rounded-3" id="kcc_wrap_{{ $rule->rule_id }}"
                    style="background: linear-gradient(135deg, #f0f9ff, #fff); border-color: {{ $isCrossEnv ? $tgtBdr : '#bae6fd' }} !important; @if($isCrossEnv) border-left: 4px solid {{ $tgtColor }} !important; @endif transition: all 0.2s ease;">
                    <div class="d-flex align-items-center justify-content-between gap-3" id="kcc_{{ $rule->rule_id }}">
                        <input type="hidden"
                            name="conditions[{{ $rule->rule_id }}]"
                            id="val_{{ $rule->rule_id }}"
                            value="0">
                        <div class="kcc-info flex-grow-1">
                            {{-- Badge Satu Sistem vs Lintas Sistem --}}
                            @if($isCrossEnv)
                            <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                <span style="font-size:9px; font-weight:700; color:#fff; background:{{ $tgtColor }}; padding:2px 8px; border-radius:50px; letter-spacing:.3px;">
                                    <i class="fa-solid fa-earth-americas me-1"></i>Lintas Sistem
                                </span>
                                <span style="font-size:9px; font-weight:600; color:{{ $envColors[$srcKat] ?? '#2563eb' }}; background:{{ $envBg[$srcKat] ?? '#eff6ff' }}; border:1px solid {{ $envBorder[$srcKat] ?? '#bfdbfe' }}; padding:2px 7px; border-radius:50px;">
                                    {{ $srcName }}
                                </span>
                                <i class="fa-solid fa-arrow-right" style="font-size:8px; color:#9ca3af;"></i>
                                <span style="font-size:9px; font-weight:600; color:{{ $tgtColor }}; background:{{ $tgtBg }}; border:1px solid {{ $tgtBdr }}; padding:2px 7px; border-radius:50px;">
                                    {{ $tgtName }}
                                </span>
                            </div>
                            @else
                            <div class="mb-2">
                                <span style="font-size:9px; font-weight:700; color:#0284c7; background:#e0f2fe; border:1px solid #bae6fd; padding:2px 8px; border-radius:50px; letter-spacing:.3px;">
                                    <i class="fa-solid fa-link me-1"></i>Satu Sistem
                                </span>
                            </div>
                            @endif

                            <div class="kcc-label fw-bold" style="font-size:13px; color:#0c4a6e;">
                                <strong> Apakah transaksi ini memerlukan revisi pada </strong>{{ $rule->target_menu_name }} › {{ $rule->target_submenu_name }} ?
                            </div>

                            @if($isCrossEnv && !empty($rule->rule_description))
                            <div style="font-size:11px; color:#6b7280; margin-bottom:4px;">
                                <i class="fa-solid fa-circle-info me-1" style="color:{{ $tgtColor }};"></i>{{ $rule->rule_description }}
                            </div>
                            @endif

                            <div class="switch-state-desc mt-2 p-2 rounded-2 border" id="state_desc_{{ $rule->rule_id }}" style="font-size:11px; background:#ffffff; border-color:#e0f2fe !important;">
                                <span class="text-muted"><i class="fa-solid fa-circle-minus me-1 text-muted"></i><strong>Tombol Mati:</strong> Modul ini <u>tidak akan</u> dibuatkan tiket pengajuan lanjutan.</span>
                            </div>

                            @if($isCrossEnv)
                            <div style="display:inline-block; font-size:10px; color:#b45309; background:#fffbeb; border:1px solid #fde68a; border-radius:4px; padding:4px 7px; margin-top:6px;">
                                <i class="fa-solid fa-shield me-1"></i>Dapat Berjalan Bersamaan
                            </div>
                            @endif

                            @if($genStatus === 'SUCCESS' && $genTicket)
                            <span class="badge bg-success ms-1 mt-1" style="font-size:10px;">
                                <i class="fa-solid fa-check-circle me-1"></i>Dibuat: {{ $genTicket->nomor_ticket }}
                            </span>
                            @elseif($genStatus === 'FAILED')
                            <span class="badge bg-danger ms-1 mt-1" style="font-size:10px;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>Gagal Dibuat Otomatis
                            </span>
                            @elseif($genStatus === 'SKIPPED')
                            <span class="badge bg-secondary ms-1 mt-1" style="font-size:10px;">
                                <i class="fa-solid fa-ban me-1"></i>Dilewati (Tidak Aktif)
                            </span>
                            @elseif($genStatus === 'PROCESSING')
                            <span class="badge bg-warning text-dark ms-1 mt-1" style="font-size:10px;">
                                <i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...
                            </span>
                            @endif
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                            <span class="badge bg-light text-muted border py-1.5 px-2.5 switch-state-label" id="state_label_{{ $rule->rule_id }}" style="font-size:10px;">
                                <i class="fa-solid fa-circle-minus me-1 text-muted"></i>Mati
                            </span>
                            <label class="toggle-switch m-0">
                                <input type="checkbox"
                                    class="kondisi-toggle"
                                    data-rule="{{ $rule->rule_id }}">
                                <span class="toggle-slider"></span>
                            </label>
                            @if($genStatus === 'FAILED')
                            <button type="button"
                                class="btn btn-sm btn-outline-danger generate-btn mt-1"
                                data-approval-id="{{ $approval?->id ?? '' }}"
                                data-rule-id="{{ $rule->rule_id }}"
                                style="font-size:9.5px; border-radius:6px;"
                                @if(!$approval) disabled @endif>
                                <i class="fa-solid fa-rotate-right me-1"></i>Coba Buat Ulang
                            </button>
                            @endif
                            @if($genStatus === 'SUCCESS' && $genTicket)
                            <a href="{{ route('ticket.lihat', [$menu_id, $genTicket->id]) }}"
                                class="btn btn-sm btn-success mt-1" style="font-size:10px; border-radius:8px;">
                                <i class="fa-solid fa-eye me-1"></i>Lihat Pengajuan
                            </a>
                            @endif
                        </div>
                    </div>

                    @if($ticket->ticketDetails && $ticket->ticketDetails->isNotEmpty())
                    <div class="rule-details-container mt-3 pt-3 border-top" id="rule_details_wrap_{{ $rule->rule_id }}" style="display:none; border-color:#e0f2fe !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold" style="font-size:11px; color:#0369a1;">
                                <i class="fa-solid fa-list-check me-1"></i>Pilih Detail yang Diteruskan (Opsional):
                            </span>
                            <button type="button" class="btn btn-xs btn-link p-0 text-decoration-none btn-select-all-details" data-rule="{{ $rule->rule_id }}" style="font-size:10.5px; color:#0284c7; font-weight:600;">
                                <i class="fa-solid fa-check-double me-1"></i>Pilih Semua
                            </button>
                        </div>
                        <label class="d-flex align-items-center justify-content-between gap-2 p-2 rounded-2 mb-2 copy-full-data-card" style="font-size:11px; background:#fefce8; border:1px dashed #fde68a; cursor:pointer;">
                            <span class="flex-grow-1">
                                <i class="fa-solid fa-clone me-1 text-warning"></i>
                                <strong>Salin Data Lengkap</strong> 
                                <small class="d-block text-muted mt-0.5" style="font-size:9.5px; font-style:italic;">
                                    Jika dicentang ini, Data transaksi dan alasan perubahan akan otomatis terisi pada ticket baru yang tergenerate.
                                </small>
                            </span>
                            <input type="checkbox"
                                   class="form-check-input flex-shrink-0 copy-full-data-toggle"
                                   name="copy_full_data[{{ $rule->rule_id }}]"
                                   value="1"
                                   data-rule="{{ $rule->rule_id }}">
                        </label>
                        <div class="d-flex flex-column gap-1">
                            @foreach($ticket->ticketDetails as $pIdx => $pDet)
                            @php
                                $firstReason = $pDet->subDetails->first()?->alasan_ubah ?? '';
                                $subCount = $pDet->subDetails->count();
                                $tglStr = $pDet->tanggal_transaksi ? \Carbon\Carbon::parse($pDet->tanggal_transaksi)->format('d/m/Y') : '';
                            @endphp
                            <label class="d-flex align-items-start gap-2 p-2 rounded-2 border bg-white cursor-pointer mb-1 rule-detail-item" style="font-size:11px; border-color:#bae6fd !important; cursor:pointer;">
                                <input type="checkbox" 
                                       class="form-check-input flex-shrink-0 mt-0.5 chk-rule-detail" 
                                       name="selected_details[{{ $rule->rule_id }}][]" 
                                       value="{{ $pDet->id }}"
                                       data-rule="{{ $rule->rule_id }}">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-semibold text-truncate text-dark" style="font-size:11.5px;">
                                            Transaksi: {{ $pDet->nomor_detail ?: ('Detail #' . ($pIdx + 1)) }}
                                        </span>
                                        <span class="badge bg-light text-muted border ms-1" style="font-size:9.5px;">
                                            {{ $subCount }} baris{{ $tglStr ? ' · ' . $tglStr : '' }}
                                        </span>
                                    </div>
                                    @if($firstReason)
                                    <div class="text-truncate text-muted mt-0.5" style="font-size:10px;">
                                        <i class="fa-solid fa-comment-dots me-1 text-info"></i>{{ \Illuminate\Support\Str::limit($firstReason, 50) }}
                                    </div>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size:9.5px; font-style:italic;">
                            <i class="fa-solid fa-circle-info me-1 text-info"></i>
                            Jika tidak ada detail dipilih, tiket turunan dibuat kosong (diisi manual sepenuhnya).
                            Jika detail dipilih tanpa "Salin Data Lengkap", tiket turunan hanya membawa referensi ke detail induk (nomor baris & alasan tetap kosong).
                            Jika "Salin Data Lengkap" diaktifkan, nomor baris & alasan perubahan ikut disalin &mdash; namun kondisi Sebelum/Setelah Perubahan tetap wajib diisi ulang.
                        </small>
                    </div>
                    @endif
                </div>
                @endforeach
                @else
                <div class="text-center text-muted py-4 rounded-3"
                    style="font-size:12px; border:1.5px dashed #bae6fd; background:#f0f9ff;">
                    <i class="fa-solid fa-circle-check fa-lg mb-2 d-block text-info"></i>
                    Tidak ada pilihan pemeriksaan lanjutan yang ditugaskan kepada Anda
                </div>
                @endif
            </div>
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-info me-2">3</span>
                    <h6 class="mb-0 fw-bold">Tanda Tangan Pemeriksa</h6>
                </div>
                <div class="sig-checker-card">
                    <div class="sig-checker-header">
                        <small style="font-size:10px; font-weight:700; color:#0284c7; text-transform:uppercase; letter-spacing:.6px;">
                            <i class="fa-solid fa-signature me-1"></i>Tanda Tangan
                        </small>
                        <button type="button" id="clear_checker_sig"
                            class="btn btn-link text-danger p-0 text-decoration-none"
                            style="font-size:10px;">
                            <i class="fa-solid fa-rotate-left me-1"></i>Reset TTD
                        </button>
                    </div>
                    <div class="sig-checker-wrapper">
                        <canvas id="checker-signature-pad"></canvas>
                    </div>
                    <div class="sig-checker-footer text-center">
                        <small class="text-muted" style="font-size:10px;">
                            <i class="fa-solid fa-hand-pointer me-1"></i>Tanda tangan di atas
                        </small>
                    </div>
                </div>
                <div class="alert alert-danger mt-2 py-2" id="alert_checker_sig" style="display:none; font-size:12px;"></div>
                <div style="position:relative; margin-top:10px;">
                    <span style="position:absolute;left:14px;top:14px;color:#0284c7;font-size:13px;z-index:6;pointer-events:none;">
                        <i class="fa-solid fa-comment-dots"></i>
                    </span>
                    <input name="comment_text" class="form-control"
                        placeholder="Catatan dari pemeriksa"
                        type="text"
                        style="border-radius:12px;border:1.5px solid #bae6fd;font-size:13px;
                               padding:12px 14px 12px 42px;resize:none;min-height:70px;
                               background:#f0f9ff;width:100%;">
                    </input>
                </div>
            </div>
            <button type="button" id="btn_checker_selesai" class="btn-checker w-100 mb-2">
                <i class="fa-solid fa-check-double me-2"></i>Review Complete (Selesai Periksa)
            </button>
        </form>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="oc-konfirmasi-checker"
    style="height: auto; min-height: 200px; z-index: 1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon me-3"
                style="width:45px;height:45px;font-size:18px;background:linear-gradient(145deg,#0284c7,#0ea5e9);box-shadow:0 0 20px rgba(2,132,199,.5);">
                <i class="fa-solid fa-check-double fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi Selesai Dicek</h6>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 pb-5">
        <div class="mt-2 text-center d-flex gap-3 justify-content-center">
            <button type="button" id="btn_confirm_checker"
                class="btn btn-info btn-sm px-4 py-2 rounded-pill fw-bold text-white">
                <i class="fa-solid fa-check me-1"></i> Ya, Selesaikan
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill"
                data-bs-dismiss="offcanvas">Batal</button>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-change-detail"
    style="height: 85vh; z-index: 1090 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width:45px; height:45px; font-size:18px;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Detail Transaksi</h6>
                <small class="text-muted" id="change-detail-subtitle">Transaksi #1</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <div class="mb-3" id="oc-wrap-reference" style="display:none;">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-link me-1"></i> Referensi Detail Sebelumnya
            </label>
            <div class="field-readonly" id="oc-reference" style="padding-left:14px; background:#f0f9ff; border-color:#bae6fd; color:#0369a1;">
                <span class="field-empty">-</span>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-hashtag me-1"></i> Nomor Transaksi
            </label>
            <div class="field-readonly" id="oc-nomor" style="padding-left:14px;">
                <span class="field-empty">-</span>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-calendar-days me-1"></i> Tanggal Transaksi
            </label>
            <div class="field-readonly" id="oc-tanggal" style="padding-left:14px;">
                <span class="field-empty">-</span>
            </div>
        </div>

        <label class="form-label fw-semibold mt-2" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
            <i class="fa-solid fa-list-ol me-1"></i> Baris Perubahan
        </label>
        <div id="oc-sub-detail-list" class="d-flex flex-column gap-2 mb-2"></div>
        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill mt-2 mb-4"
            data-bs-dismiss="offcanvas">
            <i class="fa-solid fa-xmark me-1"></i> Tutup
        </button>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', e => {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        const canvas = document.getElementById('checker-signature-pad');
        const sigPad = new SignaturePad(canvas);

        function resizeCanvas() {
            const r = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * r;
            canvas.height = canvas.offsetHeight * r;
            canvas.getContext('2d').scale(r, r);
            sigPad.clear();
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        $('#clear_checker_sig').on('click', () => sigPad.clear());

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }


        /* ── Pembatalan per baris: state di sisi browser, dikirim saat "Selesai Periksa" ── */
        const lineCancelState = {};
        document.querySelectorAll('.change-card-item[data-subdetails]').forEach(card => {
            let list = [];
            try { list = JSON.parse(card.dataset.subdetails || '[]'); } catch (e) { list = []; }
            list.forEach(sd => {
                if (!sd.id) return;
                lineCancelState[String(sd.id)] = {
                    cancelled: !!sd.is_cancelled,
                    reason: sd.cancel_reason || '',
                    detailId: card.dataset.detailId || '',
                    lockedByValidator: sd.cancelled_by_role === 'validator',
                    by: sd.cancelled_by_name || ''
                };
            });
        });
        function refreshCancelBadges() {
            $('.line-cancel-badge').each(function() {
                const did = String($(this).attr('data-detail-id'));
                let n = 0;
                Object.values(lineCancelState).forEach(st => { if (st.detailId === did && st.cancelled) n++; });
                if (n > 0) {
                    $(this).html('<i class="fa-solid fa-ban me-1"></i>' + n + ' baris ditandai tidak perlu diproses').show();
                } else {
                    $(this).hide().empty();
                }
            });
        }
        refreshCancelBadges();
        $(document).on('change', '.chk-line-cancel', function() {
            const st = lineCancelState[String($(this).attr('data-sub-id'))];
            if (!st) return;
            st.cancelled = this.checked;
            const ta = $(this).closest('label').siblings('.txt-line-cancel-reason');
            if (this.checked) { ta.show().trigger('focus'); } else { ta.hide(); }
            $(this).closest('.line-cancel-box').css({
                background: this.checked ? '#fef2f2' : '#f8fafc',
                borderColor: this.checked ? '#fca5a5' : '#cbd5e1'
            });
            refreshCancelBadges();
        });
        $(document).on('input', '.txt-line-cancel-reason', function() {
            const st = lineCancelState[String($(this).attr('data-sub-id'))];
            if (st) st.reason = this.value;
        });

        document.getElementById('off-canvas-change-detail')
            .addEventListener('show.bs.offcanvas', function(e) {
                const trigger = e.relatedTarget;
                if (!trigger) return;
                const idx = trigger.dataset.idx || '?';
                const nomor = trigger.dataset.nomor || '';
                const tanggal = trigger.dataset.tanggal || '';
                const refNomor = trigger.dataset.refNomor || '';
                const refTanggal = trigger.dataset.refTanggal || '';
                let subDetails = [];
                try {
                    subDetails = JSON.parse(trigger.dataset.subdetails || '[]');
                } catch (e) {
                    subDetails = [];
                }
                $('#change-detail-subtitle').text('Transaksi #' + idx);

                function fillField(id, val) {
                    const el = $(id);
                    if (val && val.trim() !== '') {
                        el.html(val.replace(/\n/g, '<br>'));
                    } else {
                        el.html('<span class="field-empty">Tidak diisi</span>');
                    }
                }
                if (refNomor || refTanggal) {
                    const refText = [refNomor, refTanggal ? '(' + refTanggal + ')' : ''].filter(Boolean).join(' ');
                    $('#oc-reference').html(`<i class="fa-solid fa-link me-1 text-primary"></i>` + escHtml(refText));
                    $('#oc-wrap-reference').show();
                } else {
                    $('#oc-wrap-reference').hide();
                }
                fillField('#oc-nomor', nomor);
                fillField('#oc-tanggal', tanggal);
                const wrap = $('#oc-sub-detail-list');
                wrap.empty();
                if (subDetails.length === 0) {
                    wrap.append(`
                <div class="text-center text-muted py-3 rounded-3 border border-dashed"
                    style="font-size:11px; border-color:#bae6fd !important;">
                    Tidak ada baris perubahan
                </div>`);
                    return;
                }
                subDetails.forEach((sd, j) => {
                    const nomor_urut = j + 1;
                    const collapseId = 'collapse_sub_' + j;
                    const line_number = sd.line_number && sd.line_number.trim() !== '' ?
                        sd.line_number.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const alasan = sd.alasan_ubah && sd.alasan_ubah.trim() !== '' ?
                        sd.alasan_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const before = sd.before_ubah && sd.before_ubah.trim() !== '' ?
                        sd.before_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const after = sd.after_ubah && sd.after_ubah.trim() !== '' ?
                        sd.after_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const isFirst = j === 0;
                    const st = sd.id ? lineCancelState[String(sd.id)] : null;
                    const cancelBlock = st ? `
                                <div class="line-cancel-box mt-2 p-2 rounded-2" style="background:${st.cancelled ? '#fef2f2' : '#f8fafc'}; border:1px dashed ${st.cancelled ? '#fca5a5' : '#cbd5e1'};">
                                    <label class="d-flex align-items-center gap-2 mb-0" style="font-size:11.5px; cursor:${st.lockedByValidator ? 'not-allowed' : 'pointer'};">
                                        <input type="checkbox" class="form-check-input mt-0 flex-shrink-0 chk-line-cancel" data-sub-id="${escHtml(sd.id)}" ${st.cancelled ? 'checked' : ''} ${st.lockedByValidator ? 'disabled' : ''}>
                                        <span class="fw-semibold" style="color:#b91c1c;"><i class="fa-solid fa-ban me-1"></i>Tidak perlu diproses (batalkan baris ini)</span>
                                    </label>
                                    <textarea class="form-control mt-2 txt-line-cancel-reason" rows="2" maxlength="500" data-sub-id="${escHtml(sd.id)}"
                                        placeholder="Keterangan wajib diisi, mis. sudah benar / tidak jadi diajukan"
                                        style="font-size:12px; display:${st.cancelled ? 'block' : 'none'};" ${st.lockedByValidator ? 'disabled' : ''}>${escHtml(st.reason)}</textarea>
                                    ${st.lockedByValidator ? '<small class="text-muted d-block mt-1" style="font-size:10px;">Dibatalkan oleh validator' + (st.by ? ' (' + escHtml(st.by) + ')' : '') + ', tidak dapat diubah oleh checker.</small>' : ''}
                                </div>` : '';

                    wrap.append(`
                    <div class="subdetail-accordion-item">
                        <div class="subdetail-accordion-header ${isFirst ? '' : 'collapsed'}"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${isFirst ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                                <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:10px;">
                                    <i class="fa-solid fa-list-ol me-1"></i> Line #${nomor_urut}
                                </span>
                                <span class="text-truncate fw-semibold" style="font-size:11px; color:#4b5563; ${(st && st.cancelled) ? 'text-decoration:line-through; opacity:.6;' : ''}">
                                    ${escHtml(sd.alasan_ubah || (sd.line_number ? 'Line: ' + sd.line_number : 'Perubahan Data'))}
                                </span>
                                ${(st && st.cancelled) ? '<span class="badge bg-danger flex-shrink-0" style="font-size:9.5px;"><i class="fa-solid fa-ban me-1"></i>Dibatalkan</span>' : ''}
                            </div>
                            <div class="subdetail-chevron">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>
                        <div id="${collapseId}" class="collapse ${isFirst ? 'show' : ''}">
                            <div class="subdetail-accordion-body">
                                <div class="mb-2">
                                    <small class="d-block text-muted mb-1" style="font-size:10px;"><i class="fa-solid fa-circle-info me-1"></i>Line Detail / Keterangan</small>
                                    <div class="field-readonly textarea-view no-icon" style="font-size:12px;">${line_number}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="d-block text-muted mb-1" style="font-size:10px;"><i class="fa-solid fa-comment-dots me-1"></i>Alasan Perubahan</small>
                                    <div class="field-readonly textarea-view no-icon" style="font-size:12px;">${alasan}</div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <small class="d-block text-danger fw-bold mb-1" style="font-size:10px;"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum Perubahan</small>
                                        <div class="field-readonly textarea-view no-icon" style="font-size:12px; background:#fef2f2; border-color:#fee2e2;">${before}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="d-block text-success fw-bold mb-1" style="font-size:10px;"><i class="fa-solid fa-circle-check me-1"></i>Setelah Perubahan</small>
                                        <div class="field-readonly textarea-view no-icon" style="font-size:12px; background:#f0fdf4; border-color:#dcfce7;">${after}</div>
                                    </div>
                                </div>
                                ${cancelBlock}
                            </div>
                        </div>
                    </div>`);
                });
            });
        $(document).on('change', '.kondisi-toggle', function() {
            const ruleId = $(this).data('rule');
            const checked = this.checked;
            $(`#val_${ruleId}`).val(checked ? 1 : 0);
            $(`#kcc_wrap_${ruleId}`).toggleClass('is-active', checked);
            $(`#kcc_${ruleId}`).toggleClass('is-active', checked);
            if (checked) {
                $(`#state_label_${ruleId}`)
                    .removeClass('bg-light text-muted border')
                    .addClass('bg-success text-white')
                    .html('<i class="fa-solid fa-circle-check me-1"></i>Aktif');
                $(`#state_desc_${ruleId}`)
                    .html('<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><strong>Tombol Nyala:</strong> Tiket koreksi untuk modul ini <strong>akan otomatis dibuat</strong> saat Anda menyelesaikan pemeriksaan.</span>');
                $(`#rule_details_wrap_${ruleId}`).slideDown(200);
            } else {
                $(`#state_label_${ruleId}`)
                    .removeClass('bg-success text-white')
                    .addClass('bg-light text-muted border')
                    .html('<i class="fa-solid fa-circle-minus me-1 text-muted"></i>Mati');
                $(`#state_desc_${ruleId}`)
                    .html('<span class="text-muted"><i class="fa-solid fa-circle-minus me-1 text-muted"></i><strong>Tombol Mati:</strong> Modul ini <u>tidak akan</u> dibuatkan tiket pengajuan lanjutan.</span>');
                $(`#rule_details_wrap_${ruleId}`).slideUp(200);
            }
        });

        $(document).on('change', '.copy-full-data-toggle', function() {
            const ruleId = $(this).data('rule');
            const checkboxes = $(`.chk-rule-detail[data-rule="${ruleId}"]`);
            const anyChecked = checkboxes.filter(':checked').length > 0;
            // "Salin Data Lengkap" hanya berlaku pada detail yang dipilih. Kalau
            // toggle dinyalakan tapi belum ada detail dicentang, pilih semua
            // supaya toggle tidak "nyala" tanpa efek apa pun.
            if (this.checked && !anyChecked) {
                checkboxes.prop('checked', true);
                $(`.btn-select-all-details[data-rule="${ruleId}"]`)
                    .html('<i class="fa-solid fa-xmark me-1"></i>Batal Pilih');
            }
        });

        $(document).on('click', '.btn-select-all-details', function(e) {
            e.preventDefault();
            const ruleId = $(this).data('rule');
            const checkboxes = $(`.chk-rule-detail[data-rule="${ruleId}"]`);
            const allChecked = checkboxes.length > 0 && checkboxes.length === checkboxes.filter(':checked').length;
            checkboxes.prop('checked', !allChecked);
            $(this).html(allChecked 
                ? '<i class="fa-solid fa-check-double me-1"></i>Pilih Semua' 
                : '<i class="fa-solid fa-xmark me-1"></i>Batal Pilih');
        });

        // Manual per-rule generation (retry). Delegates to the same service as
        // automatic generation; idempotency + PROCESSING lock prevent duplicates.
        $(document).on('click', '.generate-btn', function() {
            const btn = $(this);
            const approvalId = btn.data('approval-id');
            if (!approvalId) {
                showAlert('#alert_checker', 'danger', 'Data approval tidak ditemukan untuk rule ini.');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data approval tidak ditemukan untuk rule ini.'
                });
                return;
            }
            const origHtml = btn.html();
            btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Generate...');
            $.ajax({
                url: "{{ route('approval.ticket.checker.generate', '__APPROVAL_ID__') }}".replace('__APPROVAL_ID__', approvalId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    menu_id: "{{ $menu_id }}"
                },
                success(data) {
                    btn.prop('disabled', false).html(origHtml);
                    if (data.code == 200) {
                        showAlert('#alert_checker', 'success', data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Ticket berhasil digenerate.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal generate ticket.');
                        showAlert('#alert_checker', 'danger', failMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: failMsg
                        });
                    }
                },
                error(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    const errMsg = window.getFriendlyErrorMessage(xhr, 'Terjadi kesalahan saat generate ticket.');
                    showAlert('#alert_checker', 'danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                }
            });
        });

        function showAlert(selector, type, msg) {
            $(selector).stop(true, true)
                .html(`<div class="alert alert-${type} py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`)
                .show();
            setTimeout(() => {
                $(selector).animate({
                    opacity: 0,
                    marginTop: '-10px'
                }, 800, function() {
                    $(this).slideUp('fast', function() {
                        $(this).html('').css({
                            opacity: '',
                            marginTop: ''
                        });
                    });
                });
            }, 6000);
        }
        $('#btn_checker_selesai').on('click', function() {
            const lcStates = Object.values(lineCancelState);
            const lcMissing = lcStates.filter(st => st.cancelled && !st.lockedByValidator && !String(st.reason || '').trim());
            if (lcMissing.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan belum diisi',
                    text: 'Isi keterangan pada setiap baris yang ditandai "Tidak perlu diproses".'
                });
                return;
            }
            const allCancelled = lcStates.length > 0 && lcStates.every(st => st.cancelled);
            if (allCancelled && !window.__confirmAllCancelled) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Semua baris dibatalkan',
                    text: 'Seluruh baris ditandai tidak perlu diproses. Ticket tetap diteruskan ke validator dengan keterangan "dibatalkan semua", dan tidak ada ticket turunan yang dibuat. Lanjutkan?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) {
                        window.__confirmAllCancelled = true;
                        $('#btn_checker_selesai').trigger('click');
                        window.__confirmAllCancelled = false;
                    }
                });
                return;
            }
            if (sigPad.isEmpty()) {
                $('#alert_checker_sig')
                    .html('<i class="fa-solid fa-triangle-exclamation me-1"></i>Tanda tangan wajib diisi.')
                    .show();
                canvas.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }
            $('#alert_checker_sig').hide();
            new bootstrap.Offcanvas(document.getElementById('oc-konfirmasi-checker')).show();
        });
        $(document).on('click', '#btn_confirm_checker', function() {
            const btn = $(this);
            const origHtml = btn.html();
            $('#checker_signature_input').val(sigPad.isEmpty() ? '' : sigPad.toDataURL());
            const formData = new FormData($('#form_checker_ticket')[0]);
            Object.entries(lineCancelState).forEach(([id, st]) => {
                formData.append(`line_cancel[${id}][cancelled]`, st.cancelled ? 1 : 0);
                formData.append(`line_cancel[${id}][reason]`, st.reason || '');
            });
            $.ajax({
                url: "{{ route('approval.ticket.checker.save', $ticket->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend() {
                    btn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
                },
                error(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    bootstrap.Offcanvas.getInstance(document.getElementById('oc-konfirmasi-checker'))?.hide();
                    const errMsg = window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat memproses pemeriksaan.');
                    showAlert('#alert_checker', 'danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                },
                success(data) {
                    btn.prop('disabled', false).html(origHtml);
                    bootstrap.Offcanvas.getInstance(document.getElementById('oc-konfirmasi-checker'))?.hide();

                    if (data.code == 200) {
                        showAlert('#alert_checker', 'success', data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Pemeriksaan ticket berhasil disimpan.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else if (data.code == 422) {
                        const errors = data.errors || {};
                        const errList = [];
                        $.each(errors, function(key, val) {
                            if (Array.isArray(val)) {
                                errList.push(val.join(', '));
                            } else if (typeof val === 'string') {
                                errList.push(val);
                            }
                        });
                        const valErrMsg = errList.length > 0 ? errList.join('<br>') : (data.message ?? 'Data yang dikirim tidak valid.');
                        showAlert('#alert_checker', 'danger', valErrMsg);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            html: valErrMsg
                        });
                    } else {
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal memproses pemeriksaan.');
                        showAlert('#alert_checker', 'danger', failMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: failMsg
                        });
                    }
                }
            });
        });

        window.onbeforeunload = () => {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-info spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };
    });
</script>
@endsection