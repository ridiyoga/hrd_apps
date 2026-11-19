@isset($ticket)
@php
$parentTicket = $ticket->parentTicket 
    ?? ($ticket->parent_ticket_id ? \App\Models\TicketHead::with(['ticketMenu', 'ticketSubMenu'])->find($ticket->parent_ticket_id) : null)
    ?? $ticket->generatedFromTicket
    ?? ($ticket->generated_from_ticket_id ? \App\Models\TicketHead::with(['ticketMenu', 'ticketSubMenu'])->find($ticket->generated_from_ticket_id) : null)
    ?? $ticket->originCrossTicket
    ?? ($ticket->origin_cross_ticket_id ? \App\Models\TicketHead::with(['ticketMenu', 'ticketSubMenu'])->find($ticket->origin_cross_ticket_id) : null);

$rootTicket = $ticket->rootTicket
    ?? ($ticket->root_ticket_id && $ticket->root_ticket_id !== $ticket->id ? \App\Models\TicketHead::with(['ticketMenu', 'ticketSubMenu'])->find($ticket->root_ticket_id) : null);

$hasParent = $parentTicket && $parentTicket->id !== $ticket->id;
$hasRoot = $rootTicket && $rootTicket->id !== $ticket->id && (!$parentTicket || $rootTicket->id !== $parentTicket->id);
$shouldDisplay = $hasParent || $hasRoot;

$getEnvMeta = function($t) {
    if (!$t) return ['name' => 'Epicor SPS', 'cls' => 'badge-env-epicor', 'icon' => 'fa-database', 'bg' => '#f5f3ff', 'color' => '#6d28d9', 'border' => '#ddd6fe'];
    $kat = (int) ($t->ticketMenu?->kategori ?? 0);
    $nomor = $t->nomor_ticket ?? '';
    if (str_contains($nomor, 'AOL-AJA')) $kat = 3;
    elseif (str_contains($nomor, 'AOL-SP') || str_contains($nomor, 'AOL-SPS')) $kat = 2;
    elseif (str_contains($nomor, 'EPIC')) $kat = 1;
    elseif ($kat === 0) $kat = 1;

    return match ($kat) {
        1 => ['name' => 'Epicor SPS', 'cls' => 'badge-env-epicor', 'icon' => 'fa-database', 'bg' => '#f5f3ff', 'color' => '#6d28d9', 'border' => '#ddd6fe'],
        2 => ['name' => 'AOL SP', 'cls' => 'badge-env-accurate-sp', 'icon' => 'fa-bolt', 'bg' => '#fff7ed', 'color' => '#c2410c', 'border' => '#fed7aa'],
        3 => ['name' => 'AOL AJA', 'cls' => 'badge-env-accurate-aja', 'icon' => 'fa-network-wired', 'bg' => '#f0fdfa', 'color' => '#0f766e', 'border' => '#99f6e4'],
        default => ['name' => 'Epicor SPS', 'cls' => 'badge-env-epicor', 'icon' => 'fa-database', 'bg' => '#f5f3ff', 'color' => '#6d28d9', 'border' => '#ddd6fe'],
    };
};
@endphp

@if($shouldDisplay)
<div class="card border-left-info mb-4" style="border-left: 4px solid #0284c7; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.08);">
    <div class="card-body p-3" style="background: linear-gradient(135deg, #f0f9ff, #ffffff);">
        <!-- Header -->
        <div class="d-flex align-items-center mb-3">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0284c7, #0ea5e9);
                        display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);">
                <i class="fa-solid fa-link" style="color: white; font-size: 15px;"></i>
            </div>
            <div class="ms-3 flex-grow-1">
                <h6 class="mb-0 fw-bold" style="color: #0369a1; font-size: 13px;">
                    <i class="fa-solid fa-history me-1"></i>Riwayat &amp; Asal Pengajuan
                </h6>
                <small class="text-muted" style="font-size: 11px;">Pengajuan ini merupakan kelanjutan dari pengajuan sebelumnya</small>
            </div>
        </div>

        <!-- Parent Ticket Info / Ticket Pemicu -->
        @if($hasParent)
        @php $pEnv = $getEnvMeta($parentTicket); @endphp
        <div style="background: white; border: 1px solid #bae6fd; border-radius: 10px; padding: 12px; margin-bottom: 10px;">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0f2fe;
                            display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fa-solid fa-arrow-up-from-bracket" style="color: #0284c7; font-size: 14px;"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                        <small style="font-size: 10px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px;">
                            Pengajuan Asal / Sebelumnya
                        </small>
                        <span class="badge {{ $pEnv['cls'] }}" style="font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: {{ $pEnv['bg'] }}; color: {{ $pEnv['color'] }}; border: 1px solid {{ $pEnv['border'] }}; display: inline-flex; align-items: center; gap: 3px;">
                            <i class="fa-solid {{ $pEnv['icon'] }}"></i>{{ $pEnv['name'] }}
                        </span>
                    </div>
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 10px 12px;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                            <div style="font-size: 13px; font-weight: 800; color: #0c4a6e;">
                                {{ $parentTicket->nomor_ticket ?? '-' }}
                            </div>
                            @if($parentTicket->requestor_name)
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fa-solid fa-user me-1"></i>{{ $parentTicket->requestor_name }}
                            </small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <small style="color: #475569; font-size: 11px; font-weight: 600;">
                                <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                                {{ collect([$parentTicket->ticketMenu?->nama_menu, $parentTicket->ticketSubMenu?->nama_sub_menu])->filter()->join(' - ') ?: ($parentTicket->ticketSubMenu?->nama_sub_menu ?? '-') }}
                            </small>
                            @if($parentTicket->created_at)
                            <small style="color: #64748b; font-size: 10px;">
                                <i class="fa-solid fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($parentTicket->created_at)->translatedFormat('d M Y H:i') }}
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <a href="{{ route('ticket.lihat', [$menu_id ?? ($parentTicket->ticket_menu_id ?? '1'), $parentTicket->id]) }}"
                            target="_blank"
                            class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 11px; color: #0284c7; font-weight: 600;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Asal
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Root Ticket Info (jika berbeda dari parent) -->
        @if($hasRoot)
        @php $rEnv = $getEnvMeta($rootTicket); @endphp
        <div style="background: white; border: 1px solid #dbeafe; border-radius: 10px; padding: 12px;">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #dbeafe;
                            display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fa-solid fa-crown" style="color: #0284c7; font-size: 14px;"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                        <small style="font-size: 10px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px;">
                            Pengajuan Pertama (Induk Utama)
                        </small>
                        <span class="badge {{ $rEnv['cls'] }}" style="font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: {{ $rEnv['bg'] }}; color: {{ $rEnv['color'] }}; border: 1px solid {{ $rEnv['border'] }}; display: inline-flex; align-items: center; gap: 3px;">
                            <i class="fa-solid {{ $rEnv['icon'] }}"></i>{{ $rEnv['name'] }}
                        </span>
                    </div>
                    <div style="background: #f0f9ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 10px 12px;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                            <div style="font-size: 13px; font-weight: 800; color: #0c4a6e;">
                                {{ $rootTicket->nomor_ticket ?? '-' }}
                            </div>
                            @if($rootTicket->requestor_name)
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fa-solid fa-user me-1"></i>{{ $rootTicket->requestor_name }}
                            </small>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <small style="color: #475569; font-size: 11px; font-weight: 600;">
                                <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                                {{ collect([$rootTicket->ticketMenu?->nama_menu, $rootTicket->ticketSubMenu?->nama_sub_menu])->filter()->join(' - ') ?: ($rootTicket->ticketSubMenu?->nama_sub_menu ?? '-') }}
                            </small>
                            @if($rootTicket->created_at)
                            <small style="color: #64748b; font-size: 10px;">
                                <i class="fa-solid fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($rootTicket->created_at)->translatedFormat('d M Y H:i') }}
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <a href="{{ route('ticket.lihat', [$menu_id ?? ($rootTicket->ticket_menu_id ?? '1'), $rootTicket->id]) }}"
                            target="_blank"
                            class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 11px; color: #0284c7; font-weight: 600;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Pertama
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Info Badge -->
        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px 10px; margin-top: 10px;">
            <small style="color: #0369a1; font-size: 10.5px; line-height: 1.4; display: block;">
                <i class="fa-solid fa-circle-info me-1"></i>
                Pengajuan ini dibuat sebagai kelanjutan dari pengajuan sebelumnya. Anda dapat melihat detail dan alasan perubahan pada pengajuan asal melalui tautan di atas.
            </small>
        </div>
    </div>
</div>
@endif
@endisset