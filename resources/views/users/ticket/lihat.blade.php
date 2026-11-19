@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Detail Ticket
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
    }

    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f3e8ff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 14px;
        text-decoration: none;
        transition: background .2s;
        z-index: 10;
    }

    .header-back-btn:hover {
        background: #ede9fe;
        color: #6d28d9;
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

    .download-pdf-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 9px 16px;
        border-radius: 999px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(124, 58, 237, .18);
        transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }

    .download-pdf-btn:hover {
        color: #fff;
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(124, 58, 237, .24);
    }

    .field-readonly {
        background: #faf9ff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 11px 14px 11px 42px;
        font-size: 13px;
        color: #374151;
        min-height: 44px;
        display: flex;
        align-items: center;
        position: relative;
        word-break: break-word;
    }

    .field-readonly.textarea-view {
        align-items: flex-start;
        min-height: 70px;
        padding-top: 12px;
        padding-bottom: 12px;
        line-height: 1.6;
    }

    .field-readonly.no-icon {
        padding-left: 14px;
    }

    .field-empty {
        color: #c4b5fd;
        font-style: italic;
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
        color: #7c3aed;
        font-size: 13px;
        z-index: 6;
        pointer-events: none;
    }

    .form-view-group .input-icon.top-align {
        top: 14px;
        transform: none;
    }

    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #6b21a8;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        margin-top: 4px;
    }

    .kondisi-view-section {
        background: linear-gradient(135deg, #faf7ff, #ffffff);
        border: 1px solid #e9d5ff;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .kondisi-view-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #ede9fe;
    }

    .kondisi-view-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .kondisi-view-item:first-child {
        padding-top: 0;
    }

    .kondisi-view-label {
        font-size: 12px;
        font-weight: 600;
        color: #4c1d95;
        flex-grow: 1;
        padding-right: 12px;
        line-height: 1.3;
    }

    .kondisi-pill-on {
        background: #ede9fe;
        color: #6d28d9;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 50px;
    }

    .kondisi-pill-off {
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 50px;
    }

    .change-card-item {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border: 1.5px solid #e9d5ff;
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
        box-shadow: 0 6px 20px rgba(124, 58, 237, .15);
        transform: translateY(-1px);
    }

    .change-card-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .change-card-info {
        flex: 1;
        overflow: hidden;
    }

    .change-card-info .nomor {
        font-size: 12px;
        font-weight: 700;
        color: #6b21a8;
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

    .border-dashed {
        border: 2px dashed #d8b4fe !important;
    }

    .sig-view-wrap {
        background: #fff;
        border: 1px solid #f3e8ff;
        border-radius: 8px;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sig-view-wrap img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
    }

    .sig-empty {
        color: #c4b5fd;
        font-size: 12px;
        font-style: italic;
        padding: 20px;
    }

    .lampiran-card {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        width: 140px;
        flex-shrink: 0;
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

    #off-canvas-change-detail {
        z-index: 1090 !important;
    }

    .subdetail-accordion-item {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .subdetail-accordion-item:hover {
        border-color: #c4b5fd;
    }
    .subdetail-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 8px;
        transition: background 0.15s ease;
    }
    .subdetail-accordion-header:hover {
        background: #f5f0ff;
    }
    .subdetail-accordion-header[aria-expanded="true"] {
        background: #f3e8ff;
        border-bottom: 1px solid #ede9fe;
    }
    .subdetail-chevron {
        color: #7c3aed;
        font-size: 10px;
        transition: transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ede9fe;
        flex-shrink: 0;
    }
    .subdetail-accordion-header[aria-expanded="true"] .subdetail-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .subdetail-accordion-body {
        padding: 12px;
        background: #faf9ff;
    }

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
    }

    .line-cancel-box{background:#fef2f2;border:1px dashed #fca5a5;border-radius:8px;padding:8px 10px;font-size:11.5px;margin-bottom:8px}
    .line-cancel-box .lcb-title{font-weight:700;color:#b91c1c}
    .line-cancel-box .lcb-reason{margin-top:3px;color:#7f1d1d;white-space:pre-wrap;word-break:break-word}
    .is-line-cancelled{opacity:.72}
    .is-line-cancelled .diff-content,
    .is-line-cancelled .subdetail-field-val,
    .is-line-cancelled .field-readonly{text-decoration:line-through}

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5
        }

        50% {
            opacity: 1
        }
    }
</style>
@endsection

@section('content_top')
<div class="card" style="position: relative;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-file-lines"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color: #623acf; font-size: 18px;">
            Nomor: {{ $ticket->nomor_ticket }}
        </h5>
        <h5 class="mt-1 mb-1" style="font-size: 14px; font-weight: 700;">DETAIL UBAH/HAPUS</h5>
        <small class="text-muted">Lihat detail pengajuan ubah/hapus data</small>
        <div class="mt-2 mb-1">
            @include('components.ticket-status-badge', ['ticket' => $ticket])
        </div>
        <a href="{{ route('ticket.export', $ticket->id) }}" target="_blank" class="download-pdf-btn">
            <i class="fa-solid fa-download"></i>
            Download PDF
        </a>
    </div>
    <div class="card-body px-3 px-md-4 pb-4">
        @include('components.ticket-chain-card', ['chainInfo' => $chainInfo ?? null, 'currentTicket' => $ticket])
        @include('components.parent-ticket-card')
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">1</span>
                <h6 class="mb-0 fw-bold">Pengajuan Ubah/Hapus</h6>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-tags"></i></span>
                <div class="field-readonly">
                    @php
                    $envMap = [1 => 'Epicor ERP', 2 => 'Accurate Online SP', 3 => 'Accurate Online AJA'];
                    $envLabel = $envMap[$ticket->ticketMenu?->kategori ?? 0] ?? null;
                    @endphp
                    @if($envLabel)
                    {{ $envLabel }}
                    @else
                    <span class="field-empty">Belum dipilih</span>
                    @endif
                </div>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-folder-open"></i></span>
                <div class="field-readonly">
                    @if($ticket->ticketMenu?->nama_menu)
                    {{ $ticket->ticketMenu->nama_menu }}
                    @else
                    <span class="field-empty">Belum dipilih</span>
                    @endif
                </div>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-list-ul"></i></span>
                <div class="field-readonly">
                    @if($ticket->ticketSubMenu?->nama_sub_menu)
                    {{ $ticket->ticketSubMenu->nama_sub_menu }}
                    @else
                    <span class="field-empty">Belum dipilih</span>
                    @endif
                </div>
            </div>
            <div class="section-label">
                <i class="fa-solid fa-table-list"></i> Detail Perubahan Data
            </div>
            @if($ticket->ticketDetails && $ticket->ticketDetails->count() > 0)
            @foreach($ticket->ticketDetails as $i => $detail)
            @php
            $jumlahBaris = $detail->subDetails->count();
            $jumlahBatal = $detail->subDetails->where('is_cancelled', true)->count();
            $tanggalLabel = $detail->tanggal_transaksi
                ? \Carbon\Carbon::parse($detail->tanggal_transaksi)->translatedFormat('d M Y')
                : null;
            $refTanggalLabel = $detail->referenceTicketDetail?->tanggal_transaksi
                ? \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->translatedFormat('d M Y')
                : null;
            @endphp
            <div class="change-card-item"
                data-bs-toggle="offcanvas"
                data-bs-target="#off-canvas-change-detail"
                data-nomor="{{ $detail->nomor_detail }}"
                data-tanggal="{{ $tanggalLabel }}"
                data-ref-nomor="{{ $detail->referenceTicketDetail?->nomor_detail ?? '' }}"
                data-ref-tanggal="{{ $refTanggalLabel ?? '' }}"
                data-subdetails="{{ $detail->subDetails->map(fn($sd) => [
                    'line_number'       => $sd->line_number,
                    'alasan_ubah'       => $sd->alasan_ubah,
                    'before_ubah'       => $sd->before_ubah,
                    'after_ubah'        => $sd->after_ubah,
                    'is_cancelled'      => (bool) $sd->is_cancelled,
                    'cancel_reason'     => $sd->cancel_reason,
                    'cancelled_by_role' => $sd->cancelled_by_role,
                    'cancelled_by_name' => $sd->cancelled_by_name,
                ])->toJson() }}"
                data-idx="{{ $i + 1 }}">
                <div class="change-card-num">{{ $i + 1 }}</div>
                <div class="change-card-info">
                    <div class="nomor">Transaksi: {{ $detail->nomor_detail ?: '-' }}</div>
                    <div class="alasan">{{ $jumlahBaris }} baris perubahan{{ $tanggalLabel ? ' · ' . $tanggalLabel : '' }}</div>
                    @if($jumlahBatal > 0)
                    <div class="fw-semibold mt-1" style="font-size:10.5px; color:#dc2626;">
                        <i class="fa-solid fa-ban me-1"></i>{{ $jumlahBatal }} baris dibatalkan
                    </div>
                    @endif
                    @if($detail->referenceTicketDetail)
                    <div class="text-truncate mt-1" style="font-size:10.5px; color:#6d28d9;">
                        <i class="fa-solid fa-link me-1"></i>Ref: {{ $detail->referenceTicketDetail->nomor_detail ?? '-' }}{{ $refTanggalLabel ? ' (' . $refTanggalLabel . ')' : '' }}
                    </div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-muted" style="font-size:11px;"></i>
            </div>
            @endforeach
            @else
            <div class="text-center text-muted py-3 rounded-3"
                style="font-size: 12px; border: 1.5px dashed #ede9fe; background: #faf9ff;">
                <i class="fa-solid fa-table fa-lg mb-1 d-block" style="color: #d8b4fe;"></i>
                Tidak ada detail perubahan
            </div>
            @endif
        </div>
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">2</span>
                <h6 class="mb-0 fw-bold">Lampiran</h6>
            </div>
            @php
                $attachmentsList = collect();
                if ($ticket->attachments && $ticket->attachments->isNotEmpty()) {
                    $attachmentsList = $ticket->attachments;
                } elseif ($ticket->lampiran) {
                    $attachmentsList = collect([(object)[
                        'original_name' => basename($ticket->lampiran),
                        'file_path'     => $ticket->lampiran,
                        'file_size'     => null,
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
                    $fileUrl  = asset('storage/' . $filePath);
                    $ext      = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $isImg    = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    $isPdf    = $ext === 'pdf';
                    $isExcel  = in_array($ext, ['xls','xlsx','csv']);
                    $isDoc    = in_array($ext, ['doc','docx']);
                    $isZip    = in_array($ext, ['zip','rar','7z']);
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
                        <a href="{{ $fileUrl }}"
                            target="_blank"
                            class="btn btn-sm btn-primary w-100" style="font-size:11px;">
                            <i class="fa-solid fa-eye me-1"></i> Lihat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="field-readonly no-icon" style="color:#c4b5fd; font-style:italic; font-size:12px;">
                <i class="fa-solid fa-paperclip me-2" style="color:#d8b4fe;"></i>Tidak ada lampiran
            </div>
            @endif
        </div>
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">3</span>
                <h6 class="mb-0 fw-bold">Otorisasi &amp; Pengesahan</h6>
            </div>
            <!-- <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                <div class="card-header bg-light border-0 py-2 text-center">
                    <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;">
                        Diajukan Oleh: {{ $ticket->requestor_name ?? $user_karyawan->name }}
                    </small>
                </div>
                <div class="card-body p-2">
                    <div class="sig-view-wrap">
                        @if($ticket->signature)
                        <img src="{{ $ticket->signature }}" alt="Tanda Tangan">
                        @else
                        <span class="sig-empty"><i class="fa-solid fa-pen-nib me-1"></i>Tanda tangan tidak tersedia</span>
                        @endif
                    </div>
                    <div class="text-end mt-1 px-1">
                        <small class="text-muted" style="font-size: 10px;">Tanda tangan pengaju</small>
                    </div>
                </div>
            </div> -->
            <div class="row g-2 d-flex flex-wrap justify-content-end align-items-stretch mb-3">
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIAJUKAN OLEH:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if($ticket->requestor_signature)
                            <img src="/storage/{{ $ticket->requestor_signature }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
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
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-1:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if($ticket->approve1_signature)
                            <img src="/storage/{{ $ticket->approve1_signature }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                @if($ticket->approve1_name)
                                {{ $ticket->approve1_name }}
                                @else
                                <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                @endif
                            </small>
                            @if($ticket->approve1_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve1_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-2:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if($ticket->approve2_signature)
                            <img src="/storage/{{ $ticket->approve2_signature }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                @if($ticket->approve2_name)
                                {{ $ticket->approve2_name }}
                                @else
                                <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                @endif
                            </small>
                            @if($ticket->approve2_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve2_at)->translatedFormat('d M Y, H:i') }}
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
                             style="{{ $isCross ? 'border-color:#99f6e4 !important; background:#f0fdfa !important;' : 'border-color:#e2e8f0 !important; background:#ffffff !important;' }}">
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
                                    Belum diperiksa<br><span style="color:#64748b; font-weight:400;">(Non-blocking)</span>
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
                                    <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($chkDate)->translatedFormat('d M Y, H:i') }}
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
                             style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                            <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIPERIKSA OLEH:</small>
                            <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                @if(!empty($ticket->checker_signature))
                                <img src="/storage/{{ $ticket->checker_signature }}"
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
                                    {{ $ticket->checker_name ?? 'JM FINANCE & ACCOUNTING' }}
                                </small>
                                @if($ticket->checked_at)
                                <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                    <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->checked_at)->translatedFormat('d M Y, H:i') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIVALIDASI OLEH:</small>
                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                            @if($ticket->validator_signature)
                            <img src="/storage/{{ $ticket->validator_signature }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            @else
                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                            @endif
                        </div>
                        <div>
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                {{ $ticket->validator_name ?? 'JM FINANCE & ACCOUNTING' }}
                            </small>
                            @if($ticket->validator_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->validator_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="field-readonly no-icon" style="font-size: 12px; gap: 8px;">
                <i class="fa-regular fa-calendar me-2" style="color:#a78bfa;"></i>
                Tanggal Pengajuan:&nbsp;
                <strong>{{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y H:i:s') : '-' }}</strong>
            </div>
        </div>
        @if(isset($generatedRules) && $generatedRules->count() > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-info me-2"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                <h6 class="mb-0 fw-bold">Tiket Lanjutan / Terkait (Otomatis dari Sistem)</h6>
            </div>
            <div id="alert_generate_lihat"></div>
            <div class="kondisi-view-section">
                @foreach($generatedRules as $gr)
                <div class="kondisi-view-item" style="align-items:flex-start;">
                    <div class="kondisi-view-label">
                        {{ $gr['label'] }}
                        <div style="font-size:10px; font-weight:500; color:#7c3aed; margin-top:2px;">
                            <i class="fa-solid fa-arrow-right me-1"></i>{{ $gr['target_menu_name'] }} &rsaquo; {{ $gr['target_submenu_name'] }}
                        </div>
                    </div>
                    <div class="text-end" style="flex-shrink:0;">
                        @if($gr['generate_status'] === 'SUCCESS' && $gr['generated_ticket'])
                        <span class="kondisi-pill-on d-block mb-1"><i class="fa-solid fa-check-circle me-1"></i>{{ $gr['generated_ticket']->nomor_ticket }}</span>
                        <a href="{{ route('ticket.lihat', [$menu_id, $gr['generated_ticket']->id]) }}"
                            class="btn btn-sm btn-outline-success" style="font-size:10px; border-radius:8px;">
                            <i class="fa-solid fa-eye me-1"></i>Lihat Tiket
                        </a>
                        @elseif($gr['generate_status'] === 'AWAITING_DETAILS' && $gr['generated_ticket'])
                        <span class="badge bg-warning text-dark d-block mb-1" style="font-size:10px;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>Perlu Dilengkapi: {{ $gr['generated_ticket']->nomor_ticket }}
                        </span>
                        <a href="{{ route('ticket.tambah.generated.draft', ['draftTicketId' => $gr['generated_ticket']->id, 'menuId' => $menu_id]) }}"
                            class="btn btn-sm btn-warning text-dark fw-bold" style="font-size:10px; border-radius:8px;">
                            <i class="fa-solid fa-pen-to-square me-1"></i>Lengkapi Detail Sekarang
                        </a>
                        @elseif($gr['generate_status'] === 'PROCESSING')
                        <span class="badge bg-warning text-dark" style="font-size:10px;">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...
                        </span>
                        @elseif($gr['generate_status'] === 'SKIPPED')
                        <span class="kondisi-pill-off"><i class="fa-solid fa-ban me-1"></i>Dilewati (Tidak Aktif)</span>
                        @else
                        @if($gr['generate_status'] === 'FAILED')
                        <span class="badge bg-danger d-block mb-1" style="font-size:10px;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>Gagal Dibuat Otomatis
                        </span>
                        @else
                        <span class="badge bg-secondary d-block mb-1" style="font-size:10px;">
                            <i class="fa-solid fa-clock me-1"></i>Belum Diproses
                        </span>
                        @endif
                        <button type="button"
                            class="btn btn-sm btn-outline-info retry-generate-btn"
                            data-approval-id="{{ $gr['approval_id'] }}"
                            style="font-size:10px; border-radius:8px;">
                            <i class="fa-solid fa-rotate-right me-1"></i>Buat Tiket Lanjutan Otomatis
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <small class="text-muted d-block mt-2" style="font-size:10.5px;">
                <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                Klik <strong>"Buat Tiket Lanjutan Otomatis"</strong> jika tiket lanjutan sempat gagal terbentuk atau koneksi sempat terputus.
            </small>
        </div>
        @endif
        @if($ticket->comments && $ticket->comments->count() > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">4</span>
                <h6 class="mb-0 fw-bold">Catatan & Riwayat Approval</h6>
            </div>
            @foreach($ticket->comments as $comment)
            @php
            $levelMap = [
            'approve1' => ['label' => 'Approver 1', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            'approve2' => ['label' => 'Approver 2', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            'checker' => ['label' => 'Checker', 'icon' => 'fa-magnifying-glass', 'color' => '#0369a1', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
            'validator' => ['label' => 'Validator', 'icon' => 'fa-shield', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
            'pic' => ['label' => 'PIC', 'icon' => 'fa-user-tie', 'color' => '#b45309', 'bg' => '#fffbeb', 'border' => '#fde68a'],
            'requestor' => ['label' => 'Requestor', 'icon' => 'fa-user', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
            'assign' => ['label' => 'Assign', 'icon' => 'fa-user-plus', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
            ];
            $actionMap = [
            'approve' => ['label' => 'Disetujui', 'icon' => 'fa-circle-check', 'color' => '#166534'],
            'reject' => ['label' => 'Ditolak', 'icon' => 'fa-circle-xmark', 'color' => '#9f1239'],
            'start' => ['label' => 'Mulai Kerjakan', 'icon' => 'fa-play', 'color' => '#1d4ed8'],
            'finish' => ['label' => 'Selesai', 'icon' => 'fa-flag-checkered','color' => '#166534'],
            'cancel' => ['label' => 'Dibatalkan', 'icon' => 'fa-ban', 'color' => '#9f1239'],
            'feedback' => ['label' => 'Feedback', 'icon' => 'fa-comment', 'color' => '#6b7280'],
            'assign' => ['label' => 'Ditugaskan', 'icon' => 'fa-user-plus', 'color' => '#1d4ed8'],
            ];
            $lv = $levelMap[$comment->approval_level] ?? ['label' => $comment->approval_level, 'icon' => 'fa-comment', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'];
            $ac = $actionMap[$comment->action_ticket] ?? ['label' => $comment->action_ticket, 'icon' => 'fa-circle', 'color' => '#6b7280'];
            @endphp
            <div style="background:{{ $lv['bg'] }}; border:1.5px solid {{ $lv['border'] }}; border-radius:14px; padding:12px 14px; margin-bottom:8px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:28px; height:28px; border-radius:50%; background:{{ $lv['color'] }}; color:#fff;
                             display:inline-flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;">
                            <i class="fa-solid {{ $lv['icon'] }}"></i>
                        </span>
                        <div>
                            <div style="font-size:11px; font-weight:700; color:{{ $lv['color'] }};">
                                {{ $lv['label'] }}
                            </div>
                            <div style="font-size:10px; color:#6b7280;">
                                {{ $comment->karyawan?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span style="font-size:10px; font-weight:700; color:{{ $ac['color'] }};">
                            <i class="fa-solid {{ $ac['icon'] }} me-1"></i>{{ $ac['label'] }}
                        </span>
                        <div style="font-size:9px; color:#9ca3af;">
                            {{ $comment->comment_at ? \Carbon\Carbon::parse($comment->comment_at)->translatedFormat('d M Y, H:i') : '-' }}
                        </div>
                    </div>
                </div>
                <div style="background:#fff; border:1px solid {{ $lv['border'] }}; border-radius:8px;
                    padding:8px 12px; font-size:12px; color:#374151; line-height:1.6;">
                    {{ $comment->comment_text }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(!empty($can_review_pic) && $can_review_pic)
        <div class="card border border-warning rounded-4 p-3 mb-4 shadow-sm" style="background: #fffbeb; border-color: #fde68a !important;">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="width:42px; height:42px; border-radius:12px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px;">Konfirmasi Hasil Pengerjaan Tiket</h6>
                    <small class="text-muted" style="font-size:11px;">Pengerjaan tiket telah diselesaikan oleh tim pelaksana. Silakan periksa hasil perubahan data Anda.</small>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 mt-2">
                <button type="button" class="btn btn-outline-warning rounded-pill flex-fill py-2.5 fw-semibold btn-ticket-feedback" data-id="{{ $ticket->id }}">
                    <i class="fa-solid fa-comment-dots me-1"></i> Beri Catatan / Minta Revisi
                </button>
                <button type="button" class="btn btn-success rounded-pill flex-fill py-2.5 fw-bold btn-ticket-close" data-id="{{ $ticket->id }}">
                    <i class="fa-solid fa-circle-check me-1"></i> Konfirmasi Selesai &amp; Tutup Tiket
                </button>
            </div>
        </div>
        @endif

        @if(!empty($can_action_approver) && $can_action_approver)
        <div class="card border border-primary rounded-4 p-3 mb-4 shadow-sm" style="background: #eff6ff; border-color: #bfdbfe !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px; height:42px; border-radius:12px; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px;">Menunggu Persetujuan Anda (Approver)</h6>
                        <small class="text-muted" style="font-size:11px;">Tiket ini memerlukan tanda tangan persetujuan / penolakan dari Anda.</small>
                    </div>
                </div>
                <a href="{{ route('approval.ticket.approve', [$menu_id, $ticket->id]) }}" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Halaman Persetujuan &rarr;
                </a>
            </div>
        </div>
        @endif

        @if(!empty($can_action_checker) && $can_action_checker)
        <div class="card border border-info rounded-4 p-3 mb-4 shadow-sm" style="background: #f0f9ff; border-color: #bae6fd !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px; height:42px; border-radius:12px; background:#e0f2fe; color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px;">Menunggu Pemeriksaan Anda (Checker)</h6>
                        <small class="text-muted" style="font-size:11px;">Tiket ini memerlukan verifikasi checklist kondisi &amp; otorisasi checker.</small>
                    </div>
                </div>
                <a href="{{ route('approval.ticket.checker', [$menu_id, $ticket->id]) }}" class="btn btn-info btn-sm text-white rounded-pill px-3 py-2 fw-bold">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Halaman Pemeriksaan &rarr;
                </a>
            </div>
        </div>
        @endif

        @if(!empty($can_action_validator) && $can_action_validator)
        <div class="card border border-success rounded-4 p-3 mb-4 shadow-sm" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px; height:42px; border-radius:12px; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size:13.5px;">Menunggu Validasi Akhir Anda (Validator)</h6>
                        <small class="text-muted" style="font-size:11px;">Seluruh tahap persetujuan telah selesai, tiket siap untuk divalidasi.</small>
                    </div>
                </div>
                <a href="{{ route('approval.ticket.validator.show', [$menu_id, $ticket->id]) }}" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-bold">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Halaman Validasi &rarr;
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@section('content')
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
            <div class="field-readonly" id="oc-reference" style="padding-left:14px; background:#f5f3ff; border-color:#ddd6fe; color:#5b21b6;">
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
        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill mt-2"
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
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2"
                data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        let menu_active = '{{ $menu_id }}';

        function showGenerateAlert(type, msg) {
            $('#alert_generate_lihat')
                .html(`<div class="alert alert-${type} py-2 px-3 rounded-3 mb-3" style="font-size:12px;">${msg}</div>`)
                .show();
            setTimeout(() => $('#alert_generate_lihat').fadeOut('fast', function() {
                $(this).html('').css('display', '');
            }), 6000);
        }

        // Retry generate untuk ticket turunan yang gagal/belum digenerate.
        // Memanggil endpoint yang sama dengan tombol Generate di halaman checker;
        // idempotency + PROCESSING lock di service mencegah duplikasi.
        $(document).on('click', '.retry-generate-btn', function() {
            const btn = $(this);
            const approvalId = btn.data('approval-id');
            if (!approvalId) {
                showGenerateAlert('danger', 'Data persetujuan tidak ditemukan untuk pengajuan ini.');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data persetujuan tidak ditemukan untuk pengajuan ini.'
                });
                return;
            }
            const origHtml = btn.html();
            btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
            $.ajax({
                url: "{{ route('approval.ticket.checker.generate', '__APPROVAL_ID__') }}".replace('__APPROVAL_ID__', approvalId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    menu_id: menu_active
                },
                success(data) {
                    if (data.code == 200) {
                        showGenerateAlert('success', data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Pengajuan lanjutan berhasil dibuat.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        btn.prop('disabled', false).html(origHtml);
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal membuat pengajuan lanjutan.');
                        showGenerateAlert('danger', failMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: failMsg
                        });
                    }
                },
                error(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    const errMsg = window.getFriendlyErrorMessage(xhr, 'Terjadi kesalahan saat membuat pengajuan lanjutan.');
                    showGenerateAlert('danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                }
            });
        });

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function lineCancelBox(sd) {
            if (!sd || !sd.is_cancelled) return '';
            const role = sd.cancelled_by_role === 'checker' ? 'oleh Checker'
                : (sd.cancelled_by_role === 'validator' ? 'oleh Validator' : 'otomatis (mengikuti transaksi induk)');
            return `<div class="line-cancel-box">
                <div class="lcb-title"><i class="fa-solid fa-ban me-1"></i>Baris dibatalkan ${role}${sd.cancelled_by_name ? ' (' + escHtml(sd.cancelled_by_name) + ')' : ''}</div>
                <div class="lcb-reason">${escHtml(sd.cancel_reason || '-')}</div>
            </div>`;
        }

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
                    style="font-size:11px; border-color:#d8b4fe !important;">
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
                    const cancelBox = lineCancelBox(sd);

                    wrap.append(`
                    <div class="subdetail-accordion-item">
                        <div class="subdetail-accordion-header ${isFirst ? '' : 'collapsed'}"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${isFirst ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                                <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px;">
                                    <i class="fa-solid fa-list-ol me-1"></i> Line #${nomor_urut}
                                </span>
                                <span class="text-truncate fw-semibold" style="font-size:11px; color:#4b5563; ${sd.is_cancelled ? 'text-decoration:line-through; opacity:.6;' : ''}">
                                    ${escHtml(sd.alasan_ubah || (sd.line_number ? 'Line: ' + sd.line_number : 'Perubahan Data'))}
                                </span>
                                ${sd.is_cancelled ? '<span class="badge bg-danger flex-shrink-0" style="font-size:9.5px;"><i class="fa-solid fa-ban me-1"></i>Dibatalkan</span>' : ''}
                            </div>
                            <div class="subdetail-chevron">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>
                        <div id="${collapseId}" class="collapse ${isFirst ? 'show' : ''}">
                            <div class="subdetail-accordion-body">
                                ${cancelBox}
                                <div class="${sd.is_cancelled ? 'is-line-cancelled' : ''}">
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
                                </div>
                            </div>
                        </div>
                    </div>`);
                });
            });

        /* ─── REQUESTOR ACTION (FEEDBACK & CLOSE) ────────────────────────── */
        const REQUESTOR_ACTION_URL = "{{ url('/ticket/requestor-action') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        $(document).on('click', '.btn-ticket-feedback', function() {
            submitRequestorAction($(this).data('id'), 'feedback');
        });
        $(document).on('click', '.btn-ticket-close', function() {
            submitRequestorAction($(this).data('id'), 'close');
        });

        function submitRequestorAction(ticketId, action) {
            const isFeedback = action === 'feedback';
            Swal.fire({
                icon: isFeedback ? 'warning' : 'question',
                title: isFeedback ? 'Kirim feedback ke PIC?' : 'Konfirmasi Selesai & Tutup Tiket?',
                text: isFeedback ?
                    'Tiket akan dikembalikan ke PIC untuk diperbaiki sesuai catatan Anda.' :
                    'Tiket akan ditutup permanen sebagai selesai dikerjakan.',
                input: 'textarea',
                inputPlaceholder: isFeedback ? 'Tuliskan bagian yang belum sesuai...' : 'Catatan opsional...',
                inputValidator: function(value) {
                    if (isFeedback && !String(value || '').trim()) return 'Feedback wajib diisi.';
                    return null;
                },
                showCancelButton: true,
                confirmButtonText: isFeedback ? '<i class="fa-solid fa-comment-dots me-1"></i> Kirim Feedback' : '<i class="fa-solid fa-circle-check me-1"></i> Tutup Tiket',
                cancelButtonText: 'Batal',
                confirmButtonColor: isFeedback ? '#d97706' : '#16a34a',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) return;
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: REQUESTOR_ACTION_URL + '/' + ticketId,
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        action: action,
                        comment_text: result.value || ''
                    },
                    success: function(res) {
                        if (res.code == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                            return;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan sistem'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                        });
                    }
                });
            });
        }

        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };
    });
</script>
@endsection