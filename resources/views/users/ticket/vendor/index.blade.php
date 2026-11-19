@extends('users.layouts.main')
@section('title') APPS | Vendor Registration @endsection

@section('css')
<style>
    .vendor-page {
        padding-bottom: 100px;
    }

    .vendor-header {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 20px;
        padding: 20px;
        color: white;
        margin-bottom: 16px;
    }

    .vendor-title {
        font-size: 20px;
        font-weight: 700;
    }

    .vendor-subtitle {
        font-size: 12px;
        opacity: .85;
    }

    .btn-add-vendor {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: 14px;
        background: #7c3aed;
        color: white;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 16px;
    }

    .vendor-card {
        background: white;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid #ede9fe;
    }

    .vendor-name {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .vendor-business {
        font-size: 12px;
        font-weight: 600;
        color: #6d28d9;
        margin-top: 2px;
    }

    .vendor-date {
        font-size: 11px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    .vendor-status {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-draft {
        background: #f3f4f6;
        color: #374151;
    }

    .status-progress {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-approved {
        background: #dcfce7;
        color: #15803d;
    }

    .status-rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .vendor-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 10px 0;
    }

    .vbadge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .2px;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .vbadge i { font-size: 9px; }

    .vbadge-epicor   { background: #1e1b4b; color: #c4b5fd; }
    .vbadge-accurate { background: #1e3a8a; color: #bfdbfe; }
    .vbadge-bb       { background: #dcfce7; color: #15803d; }
    .vbadge-nbb      { background: #fef9c3; color: #b45309; }

    .progress {
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        background: #ede9fe;
        margin-top: 4px;
    }

    .progress-bar {
        background: linear-gradient(90deg, #7c3aed, #a855f7);
    }

    .vendor-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #ede9fe;
    }

    .vendor-pengaju {
        font-size: 11px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .vendor-pengaju i {
        color: #a855f7;
        font-size: 10px;
    }

    .btn-detail {
        background: #f5f3ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
        font-weight: 700;
        font-size: 11px;
        border-radius: 10px;
        padding: 6px 14px;
    }
</style>
@endsection

@section('content_top')

<div class="vendor-page">

    <div class="vendor-header">
        <div class="vendor-title">
            Pendaftaran Vendor
        </div>
        <div class="vendor-subtitle">
            Pantau seluruh proses pengajuan vendor
        </div>
    </div>

    <a href="{{ route('ticket.vendor.create',$menu_id) }}"
        class="btn-add-vendor">
        <i class="fa-solid fa-plus"></i>
        Pendaftaran Vendor Baru
    </a>

    @forelse($vendors as $vendor)

    <div class="vendor-card">

        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="vendor-name">
                    {{ $vendor->nomor_pengajuan_fis }}
                </div>

                <div class="vendor-business">
                    {{ $vendor->data?->nama_usaha ?? '-' }}
                </div>

                <div class="vendor-date">
                    <i class="fa-regular fa-clock"></i>
                    {{ $vendor->created_at->format('d M Y H:i') }}
                </div>
            </div>

            <div>
                @switch($vendor->status)

                @case('pending')
                <span class="vendor-status status-draft">
                    Pending
                </span>
                @break
                @case('draft')
                <span class="vendor-status status-draft">
                    Draft
                </span>
                @break

                @case('process')
                <span class="vendor-status status-progress">
                    Proses Approval
                </span>
                @break

                @case('approved')
                <span class="vendor-status status-approved">
                    Approved
                </span>
                @break

                @case('rejected')
                <span class="vendor-status status-rejected">
                    Rejected
                </span>
                @break

                @endswitch
            </div>
        </div>

        <div class="vendor-badges">
            @if($vendor->system_type == 1)
                <span class="vbadge vbadge-epicor"><i class="fa-solid fa-microchip"></i> Epicor</span>
            @elseif($vendor->system_type == 2)
                <span class="vbadge vbadge-accurate"><i class="fa-solid fa-chart-pie"></i> Accurate</span>
            @endif

            @if($vendor->vendor_category == 'V01')
                <span class="vbadge vbadge-bb"><i class="fa-solid fa-boxes-stacked"></i> Bahan Baku</span>
            @elseif($vendor->vendor_category == 'V02')
                <span class="vbadge vbadge-nbb"><i class="fa-solid fa-box-archive"></i> Non Bahan Baku</span>
            @endif
        </div>

        <div class="progress">
            <div class="progress-bar"
                style="width: {{ $vendor->progress }}%">
            </div>
        </div>
        <small class="text-muted" style="font-size:10px;">Progress {{ $vendor->progress }}%</small>

        <div class="vendor-footer">
            <div class="vendor-pengaju">
                <i class="fa-solid fa-user"></i>
                {{ $vendor->creator_name }}
            </div>

            <a href="{{ route('ticket.vendor.show', [$menu_id, $vendor->id]) }}"
                class="btn-detail">
                Detail <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
            </a>
        </div>

    </div>

    @empty

    <div class="card">
        <div class="card-body text-center">
            <i class="fa-solid fa-folder-open fa-2x text-muted mb-2"></i>
            <p class="mb-0">
                Belum ada pengajuan vendor.
            </p>
        </div>
    </div>

    @endforelse

</div>
@endsection