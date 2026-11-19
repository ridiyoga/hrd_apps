@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Approve Ticket
@endsection
@section('css')
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

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
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
        color: #7c3aed;
        font-size: 13px;
        z-index: 6;
        pointer-events: none;
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

    .sig-view-wrap {
        background: #fff;
        border: 1px solid #f3e8ff;
        border-radius: 8px;
        overflow: hidden;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sig-view-wrap img {
        max-height: 100px;
        max-width: 100%;
        object-fit: contain;
    }

    .sig-empty {
        color: #c4b5fd;
        font-size: 12px;
        font-style: italic;
        padding: 20px;
    }

    .sig-approver-card {
        border: 2px dashed #a855f7;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 4px;
        box-shadow: 0 4px 16px rgba(124, 58, 237, .1);
    }

    .sig-approver-header {
        background: linear-gradient(135deg, #faf7ff, #fff);
        border-bottom: 1px solid #f3e8ff;
        padding: 10px 14px;
        text-align: center;
    }

    .sig-approver-header small {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #7c3aed;
        letter-spacing: .6px;
    }

    #signature-pad {
        width: 100%;
        height: 180px;
        touch-action: none;
        display: block;
        cursor: crosshair;
    }

    .sig-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 14px;
        border-top: 1px solid #f3e8ff;
    }

    .sig-hint {
        font-size: 11px;
        color: #a78bfa;
        text-align: center;
        padding: 4px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .border-dashed {
        border: 2px dashed #d8b4fe !important;
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

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5
        }

        50% {
            opacity: 1
        }
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

    #off-canvas-konfirmasi-save,
    #off-canvas-konfirmasi-approve,
    #off-canvas-konfirmasi-reject {
        z-index: 1095 !important;
    }

    .btn-approve {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        border: none;
        border-radius: 16px;
        padding: 14px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .4px;
        width: 100%;
        box-shadow: 0 8px 24px rgba(22, 163, 74, .3);
        transition: transform .15s, box-shadow .15s;
    }

    .btn-approve:active {
        transform: scale(.98);
        box-shadow: 0 4px 12px rgba(22, 163, 74, .2);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border: none;
        border-radius: 16px;
        padding: 14px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .4px;
        width: 100%;
        box-shadow: 0 8px 24px rgba(220, 38, 38, .25);
        transition: transform .15s, box-shadow .15s;
    }

    .btn-reject:active {
        transform: scale(.98);
    }

    .alert-inline {
        font-size: 12px;
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: none;
    }

    #signature-pad {
        width: 100%;
        height: 180px;
        touch-action: none;
        display: block;
        cursor: crosshair;
    }

    .line-cancel-box{background:#fef2f2;border:1px dashed #fca5a5;border-radius:8px;padding:8px 10px;font-size:11.5px;margin-bottom:8px}
    .line-cancel-box .lcb-title{font-weight:700;color:#b91c1c}
    .line-cancel-box .lcb-reason{margin-top:3px;color:#7f1d1d;white-space:pre-wrap;word-break:break-word}
    .is-line-cancelled{opacity:.72}
    .is-line-cancelled .diff-content,
    .is-line-cancelled .subdetail-field-val,
    .is-line-cancelled .field-readonly{text-decoration:line-through}
</style>
@endsection

@section('content_top')
<div class="card" style="position: relative;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-shield"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color: #623acf; font-size: 18px;">
            {{ $ticket->nomor_ticket }}
        </h5>
        <h5 class="mt-1 mb-1" style="font-size: 14px; font-weight: 700;">APPROVAL UBAH/HAPUS</h5>
        <small class="text-muted">Periksa detail lalu berikan persetujuan</small>
        <div class="mt-2 mb-1">
            @include('components.ticket-status-badge', ['ticket' => $ticket])
        </div>
    </div>
    <div id="alert_approve" class="mx-3 mt-2"></div>
    <div class="card-body px-3 px-md-4 pb-4">
        @include('components.parent-ticket-card')
        <form id="form_approve_ticket" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_ticket" value="{{ $ticket->id }}">
            <input type="hidden" name="id_approver" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <input type="hidden" name="signature" id="signature_input">
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">1</span>
                    <h6 class="mb-0 fw-bold">Pengajuan Ubah/Hapus</h6>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-tags"></i></span>
                    <div class="field-readonly">
                        @php $envMap = [1 => 'Epicor ERP', 2 => 'Accurate Online SP', 3 => 'Accurate Online AJA']; @endphp
                        {{ $envMap[$ticket->ticketMenu?->kategori ?? 0] ?? '-' }}
                    </div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-folder-open"></i></span>
                    <div class="field-readonly">
                        {{ $ticket->ticketMenu?->nama_menu ?? '-' }}
                    </div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-list-ul"></i></span>
                    <div class="field-readonly">
                        {{ $ticket->ticketSubMenu?->nama_sub_menu ?? '-' }}
                    </div>
                </div>
                @if($ticket->ticketKondisi && $ticket->ticketKondisi->count() > 0)
                <div class="kondisi-view-section">
                    <div class="d-flex align-items-center mb-2 gap-2">
                        <i class="fa-solid fa-sliders" style="color:#7c3aed; font-size:13px;"></i>
                        <span style="font-size:12px; font-weight:700; color:#4c1d95; text-transform:uppercase; letter-spacing:.5px;">Kondisi Ubah Hapus</span>
                        @php $activeKondisi = $ticket->ticketKondisi->where('aktif', 1)->count(); @endphp
                        <span class="badge ms-auto kondisi-pill-on">{{ $activeKondisi }} aktif</span>
                    </div>
                    @foreach($ticket->ticketKondisi as $kond)
                    <div class="kondisi-view-item">
                        <div class="kondisi-view-label">{{ $kond->label_kondisi ?? $kond->code }}</div>
                        @if($kond->aktif)
                        <span class="kondisi-pill-on"><i class="fa-solid fa-check me-1"></i>Aktif</span>
                        @else
                        <span class="kondisi-pill-off"><i class="fa-solid fa-minus me-1"></i>Tidak</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
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
                            <a href="{{ $fileUrl }}" target="_blank"
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
                    <h6 class="mb-0 fw-bold">Data Pengaju &amp; Persetujuan</h6>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                    <div class="field-readonly">
                        {{ $ticket->requestor_name ?? $ticket->karyawan?->name ?? '-' }}
                    </div>
                </div>
                <div class="form-view-group">
                    <span class="input-icon"><i class="fa-regular fa-calendar"></i></span>
                    <div class="field-readonly">
                        {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y H:i:s') : '-' }}
                    </div>
                </div>
                <div class="section-label mt-1">
                    <i class="fa-solid fa-pen-nib"></i> Tanda Tangan Persetujuan
                </div>
                <div class="sig-approver-card">
                    <div class="sig-approver-header">
                        <small>Disetujui Oleh: {{ $user_karyawan->name }}</small>
                    </div>
                    <div class="alert-inline alert alert-danger mx-2 mt-2" id="alert_signature"></div>
                    <canvas id="signature-pad"></canvas>
                    <div class="sig-footer">
                        <button type="button" id="clear"
                            class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                            <i class="fa-solid fa-eraser me-1"></i> Hapus
                        </button>
                        <small class="text-muted" style="font-size:10px;">
                            <i class="fa-solid fa-hand-pointer me-1"></i>Tanda tangan di atas
                        </small>
                    </div>
                </div>
                <div class="mt-3 mb-3" style="position:relative;">
                    <span style="position:absolute; left:14px; top:14px; color:#7c3aed; font-size:13px; z-index:6; pointer-events:none;">
                        <i class="fa-solid fa-comment-dots"></i>
                    </span>
                    <input name="comment_text"
                        id="comment_text"
                        class="form-control"
                        type="text"
                        placeholder="Catatan dari pemberi otoritas persetujuan dan pengecekan"
                        style="border-radius:12px; border:1.5px solid #ede9fe; font-size:13px; padding:12px 14px 12px 42px; resize:none; min-height:70px; background:#fdfcff; width:100%;">
                    </input>
                </div>
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
            </div>
            <div class="d-flex gap-3 mt-2">
                <button type="button" id="btn_reject" class="btn-reject" style="flex:1;">
                    <i class="fa-solid fa-circle-xmark me-2"></i>Tolak
                </button>
                <button type="button" id="btn_approve" class="btn-approve" style="flex:2;">
                    <i class="fa-solid fa-circle-check me-2"></i>Setujui
                </button>
            </div>

        </form>
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
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-konfirmasi-approve"
    style="height: auto; min-height: 190px; z-index: 1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width:45px; height:45px; font-size:20px; background:linear-gradient(145deg,#16a34a,#22c55e); box-shadow:0 0 20px rgba(22,163,74,.5);">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi Setujui</h6>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 pb-5">
        <div class="mt-2 text-center d-flex gap-3 justify-content-center">
            <button type="button" id="btn_confirm_approve" class="btn btn-success btn-sm px-4 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-check me-1"></i> Ya, Setujui
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill"
                data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-xmark me-1"></i> Batal
            </button>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-konfirmasi-reject"
    style="height: auto; min-height: 250px; z-index: 1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3"
                style="width: 45px; height: 45px; font-size: 20px; background: linear-gradient(145deg, #ef4444, #f87171); box-shadow: 0 0 15px rgba(239, 68, 68, .5);">
                <i class="fa-solid fa-circle-xmark fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi Penolakan Pengajuan</h6>
                <small class="text-danger fw-semibold">Wajib memberikan alasan penolakan (min. 10 karakter)</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 pb-5">
        <div class="alert-inline alert alert-danger" id="alert_reject_reason"></div>
        <div style="position:relative; margin-bottom:12px;">
            <span style="position:absolute; left:14px; top:14px; color:#ef4444; font-size:13px; z-index:6; pointer-events:none;">
                <i class="fa-solid fa-comment-dots"></i>
            </span>
            <textarea id="alasan_reject"
                class="form-control"
                placeholder="Tuliskan alasan penolakan / koreksi yang harus diperbaiki oleh pengaju..."
                style="border-radius:12px; border:1.5px solid #fecaca; font-size:13px;
                       padding:12px 14px 12px 42px; resize:none; min-height:85px;
                       background:#fff5f5; width:100%;"></textarea>
        </div>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button" id="btn_confirm_reject" class="btn btn-danger btn-sm px-4 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-xmark me-1"></i> Tolak Pengajuan &amp; Minta Revisi
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill"
                data-bs-dismiss="offcanvas">
                Batal
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
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
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        let menu_active = '{{ $menu_id }}';
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            const ctx = canvas.getContext('2d');
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        $('#clear').on('click', () => signaturePad.clear());
        $.ajax({
            url: "{{ route('ticket.get_approval', [$menu_id, $ticket->id]) }}",
            type: 'GET',
            success: function(data) {
                let n1 = '-',
                    n2 = '-';
                if (data.code == 200) {
                    n1 = data.atasan1?.karyawan?.name ?? '-';
                    n2 = data.atasan2?.karyawan?.name ?? '-';
                }
                $('#atasan1').html(n1);
                $('#atasan2').html(n2);
            },
            error: function() {
                $('#atasan1').html('-');
                $('#atasan2').html('-');
            }
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

        function showAlert(selector, type, msg) {
            $(selector).stop(true, true)
                .html(`<div class="alert alert-${type} py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`)
                .show();
            setTimeout(function() {
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

        function validateSignature() {
            if (signaturePad.isEmpty()) {
                $('#alert_signature').html(
                    '<i class="fa-solid fa-triangle-exclamation me-1"></i>Tanda tangan wajib diisi.'
                ).show();
                canvas.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }
            $('#alert_signature').hide();
            return true;
        }
        $('#btn_approve').on('click', function() {
            if (!validateSignature()) return;
            const oc = new bootstrap.Offcanvas(document.getElementById('off-canvas-konfirmasi-approve'));
            oc.show();
        });
        $('#btn_reject').on('click', function() {
            const oc = new bootstrap.Offcanvas(document.getElementById('off-canvas-konfirmasi-reject'));
            oc.show();
        });
        $(document).on('click', '#btn_confirm_approve', function() {
            submitAction('approve');
        });
        $(document).on('click', '#btn_confirm_reject', function() {
            submitAction('reject');
        });

        function submitAction(action) {
            const btn = action === 'approve' ?
                $('#btn_confirm_approve') :
                $('#btn_confirm_reject');
            const origHtml = btn.html();

            if (action === 'reject') {
                const rejectReason = $('#alasan_reject').val().trim();
                if (!rejectReason || rejectReason.length < 10) {
                    $('#alert_reject_reason').html(
                        '<i class="fa-solid fa-triangle-exclamation me-1"></i>Wajib mengisi alasan penolakan minimal 10 karakter agar pengaju memahami revisi yang diminta.'
                    ).show();
                    $('#alasan_reject').focus();
                    return;
                }
                $('#alert_reject_reason').hide();
            }

            $('#signature_input').val(
                signaturePad.isEmpty() ? '' : signaturePad.toDataURL()
            );
            const formData = new FormData($('#form_approve_ticket')[0]);
            formData.append('action', action);
            if (action === 'reject') {
                formData.append('alasan_reject', $('#alasan_reject').val().trim());
            }
            $.ajax({
                url: "{{ route('approval.ticket.save', $ticket->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(origHtml);
                    ['off-canvas-konfirmasi-approve', 'off-canvas-konfirmasi-reject'].forEach(id => {
                        bootstrap.Offcanvas.getInstance(document.getElementById(id))?.hide();
                    });
                    const errMsg = window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat memproses persetujuan.');
                    showAlert('#alert_approve', 'danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                },
                success: function(data) {
                    btn.prop('disabled', false).html(origHtml);
                    ['off-canvas-konfirmasi-approve', 'off-canvas-konfirmasi-reject'].forEach(id => {
                        bootstrap.Offcanvas.getInstance(document.getElementById(id))?.hide();
                    });
                    if (data.code == 200) {
                        showAlert('#alert_approve', 'success', data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || (action === 'approve' ? 'Ticket berhasil disetujui.' : 'Ticket berhasil ditolak.'),
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
                        showAlert('#alert_approve', 'danger', valErrMsg);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            html: valErrMsg
                        });
                    } else {
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal memproses persetujuan.');
                        showAlert('#alert_approve', 'danger', failMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: failMsg
                        });
                    }
                }
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