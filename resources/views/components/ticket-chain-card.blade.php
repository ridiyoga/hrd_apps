@isset($chainInfo)
@php
    $summary = $chainInfo['summary'] ?? [];
    $hasChain = $chainInfo['has_chain'] ?? false;
    $items = $chainInfo['items'] ?? [];
@endphp
<div class="mb-4">
    <!-- Banner Status Petugas yang Ditunggu -->
    @if(($summary['type'] ?? '') === 'warning')
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1.5px solid #fde68a !important;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-start gap-3">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706);
                            display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);">
                    <i class="fa-solid fa-clock-rotate-left text-white" style="font-size: 18px;"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                        <span style="font-size: 10.5px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-circle-info me-1"></i>Informasi Alur Proses
                        </span>
                        @if($hasChain)
                        <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; border-radius: 20px;">
                            <i class="fa-solid fa-layer-group me-1"></i>{{ $chainInfo['done_tickets'] }}/{{ $chainInfo['total_tickets'] }} Tiket Selesai
                        </span>
                        @endif
                    </div>
                    <h6 class="mb-1 fw-bold" style="color: #92400e; font-size: 13.5px;">
                        {{ $summary['title'] ?? 'Pengajuan Sedang Menunggu Proses' }}
                    </h6>
                    {{-- <p class="mb-2 text-dark" style="font-size: 12px; line-height: 1.5;">
                        {{ $summary['description'] ?? '-' }}
                    </p> --}}

                    @if(!empty($summary['waiting_on_list']) && count($summary['waiting_on_list']) > 0)
                    <div class="d-flex align-items-start gap-2 px-3 py-2 rounded-3 mt-2" style="background: #ffffff; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-user-clock text-warning mt-1"></i>
                        <div style="font-size: 11.5px; color: #78350f;" class="w-100">
                            <span class="d-block fw-bold mb-1">Saat ini menunggu tindakan dari:</span>
                            <div class="d-flex flex-column gap-1 ms-1">
                                @foreach($summary['waiting_on_list'] as $checkerItem)
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:10.5px; font-weight:700;">
                                        <i class="fa-solid fa-user me-1"></i>{{ $checkerItem['name'] }}
                                    </span>
                                    @if(!empty($checkerItem['sub_menu']))
                                    <span class="text-muted" style="font-size:10.5px;">({{ $checkerItem['sub_menu'] }})</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @elseif(!empty($summary['waiting_on']))
                    <div class="d-flex align-items-start gap-2 px-3 py-2 rounded-3 mt-2" style="background: #ffffff; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-user-clock text-warning mt-1"></i>
                        <div style="font-size: 11.5px; color: #78350f;">
                            <span class="d-block mb-1">Saat ini menunggu tindakan dari:</span>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach(explode(' / ', $summary['waiting_on']) as $name)
                                <span class="badge bg-warning text-dark border border-warning" style="font-size:10.5px; font-weight:600;">
                                    <i class="fa-solid fa-user me-1"></i>{{ $name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @elseif(($summary['type'] ?? '') === 'success')
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px solid #bbf7d0 !important;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #16a34a, #15803d);
                            display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                    <i class="fa-solid fa-circle-check text-white" style="font-size: 20px;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold" style="color: #166534; font-size: 13.5px;">
                        {{ $summary['title'] ?? 'Seluruh Rangkaian Pengajuan Telah Selesai' }}
                    </h6>
                    <p class="mb-0 text-muted" style="font-size: 12px;">
                        {{ $summary['description'] ?? 'Semua proses pada pengajuan ini telah selesai dikerjakan.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Daftar Rangkaian Pengajuan Terkait (Jika ada lebih dari 1 tiket serombongan) -->
    @if($hasChain && count($items) > 0)
    <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff; border: 1.5px solid #ede9fe !important; overflow: hidden;">
        <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-sitemap" style="font-size: 14px;"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">Rangkaian Pengajuan Terkait</h6>
                    <small class="text-muted" style="font-size: 10.5px;">Seluruh tiket dalam satu group pengajuan ini</small>
                </div>
            </div>
            <span class="badge" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; font-size: 11px; padding: 5px 10px; border-radius: 20px;">
                Total {{ count($items) }} Pengajuan
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 12px;">
                    <thead style="background: #faf8ff; color: #4c1d95; font-size: 11px; text-transform: uppercase;">
                        <tr>
                            <th class="ps-3 py-3" style="width: 40px;">No</th>
                            <th class="py-3">Nomor &amp; Jenis Tiket</th>
                            <th class="py-3">Layanan / Sub Modul</th>
                            <th class="py-3">Status Saat Ini</th>
                            <th class="py-3">Petugas yang Sedang Ditunggu</th>
                            <th class="pe-3 py-3 text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $item)
                        <tr style="{{ $item['is_current'] ? 'background: #fdfaff;' : '' }}">
                            <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <div class="fw-bold {{ $item['is_current'] ? 'text-primary' : 'text-dark' }}" style="font-size: 12.5px;">
                                            {{ $item['ticket_number'] }}
                                        </div>
                                        <span class="badge {{ $item['is_root'] ? 'bg-primary' : 'bg-label-info' }}" style="font-size: 9.5px; font-weight: 700;">
                                            {{ $item['type_label'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item['sub_menu_name'] }}</div>
                                <small class="text-muted" style="font-size: 10.5px;">{{ $item['menu_name'] }}</small>
                            </td>
                            <td>
                                @if($item['is_done'] && !$item['is_rejected'])
                                    <span class="status-badge status-approved" style="font-size: 10px;">
                                        <i class="fa-solid fa-circle-check me-1"></i>Selesai
                                    </span>
                                @elseif($item['is_rejected'])
                                    <span class="status-badge status-rejected" style="font-size: 10px;">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>Dibatalkan
                                    </span>
                                @elseif($item['status_code'] === 'T00' || $item['status_badge'] === 'status-draft')
                                    <span class="status-badge status-draft" style="font-size: 10px;">
                                        <i class="fa-solid fa-pencil me-1"></i>Belum Dilengkapi
                                    </span>
                                @else
                                    <span class="status-badge status-pending" style="font-size: 10px;">
                                        <i class="fa-solid fa-clock me-1"></i>{{ $item['status_label'] }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($item['waiting_on']))
                                    @php
                                        $isPrimaryBottleneck = empty($item['waiting_dependency_ticket_id']) && !$item['is_done'] && !$item['is_rejected'];
                                    @endphp
                                    <div style="font-size: 11.5px; font-weight: 700; color: #374151;">
                                        {{ $item['waiting_on'] }}
                                        @if($isPrimaryBottleneck)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 8.5px; padding: 2px 6px; border-radius: 4px;" title="Titik bottleneck utama yang sedang ditunggu tindakan oleh petugas">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>Ticket tertahan
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">
                                        {{ $item['waiting_role'] }} ({{ $item['waiting_stage'] }})
                                    </small>
                                @elseif($item['is_done'] && !$item['is_rejected'])
                                    <span class="text-success" style="font-size: 11px;">
                                        <i class="fa-solid fa-check me-1"></i>Proses Lengkap
                                    </span>
                                @elseif($item['is_rejected'])
                                    <span class="text-danger" style="font-size: 11px;">
                                        <i class="fa-solid fa-ban me-1"></i>Tidak Diproses
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                @endif
                            </td>
                            <td class="pe-3 text-center">
                                @if($item['is_current'])
                                    <span class="badge bg-light text-primary border" style="font-size: 10px; font-weight: 700; padding: 5px 8px;">
                                        Sedang Dibuka
                                    </span>
                                @else
                                    <a href="{{ route('ticket.lihat', [$item['menu_id'] ?? ($menu_id ?? '1'), $item['id']]) }}"
                                       class="btn btn-sm btn-outline-primary px-2 py-1"
                                       style="font-size: 11px; border-radius: 8px;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Buka
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endisset
