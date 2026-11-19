@props(['ticket' => null, 'status' => null, 'approvalStatus' => null])

@php
    $st = $status ?? ($ticket?->ticket_status ?? null);
    $appSt = $approvalStatus ?? ($ticket?->approval_status ?? null);
    $meta = \App\Models\TicketHead::getStatusMeta($st, $appSt);
    if ($ticket && ($ticket->is_draft || $ticket->ticket_status === \App\Models\TicketHead::STATUS_DRAFT)) {
        $meta['label'] = $ticket->is_generated ? 'Draft (Lengkapi Detail)' : 'Draft';
        $meta['class'] = 'status-draft';
        $meta['icon']  = 'fa-pen-to-square';
    }
@endphp

<span class="status-badge {{ $meta['class'] }}" {{ $attributes }}>
    <i class="fa-solid {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
</span>
