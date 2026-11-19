@extends('admin.layouts.dashboard')

@section('isi')
    @php
        $statusColor = [
            'todo' => 'secondary',
            'in_progress' => 'primary',
            'blocked' => 'danger',
            'review' => 'warning',
            'done' => 'success',
            'cancelled' => 'dark',
        ];
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">TASK SAYA</h5>
                <a href="{{ route('project_management.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Data Project
                </a>
            </div>

            <div class="card-body">
                <div class="overflow-auto mb-3">
                    <ul class="nav nav-pills flex-nowrap gap-1" style="min-width:max-content;">
                        <li class="nav-item">
                            <a class="nav-link {{ request('status') === null || request('status') === '' ? 'active' : '' }}"
                                href="{{ route('project_management.my_tasks.index', array_filter([$holding->holding_code, 'q' => request('q')])) }}">
                                Semua <span class="badge bg-label-secondary ms-1">{{ $statusCounts->sum() }}</span>
                            </a>
                        </li>
                        @foreach ($statusOptions as $value => $label)
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === $value ? 'active' : '' }}"
                                    href="{{ route('project_management.my_tasks.index', array_filter([$holding->holding_code, 'status' => $value, 'q' => request('q')])) }}">
                                    {{ $label }}
                                    <span class="badge bg-label-secondary ms-1">{{ $statusCounts[$value] ?? 0 }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form method="GET" action="{{ route('project_management.my_tasks.index', $holding->holding_code) }}">
                    @if (request()->filled('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="row g-2 align-items-end mb-4">
                        <div class="col-lg-8 col-md-9">
                            <label class="form-label">Cari di Tab {{ request()->filled('status') ? ($statusOptions[request('status')] ?? '') : 'Semua' }}</label>
                            <div class="input-group">
                                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                    placeholder="Cari kode, judul, project, tipe, prioritas, catatan, atau blocker">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                            {{-- <small class="text-muted d-block mt-1">Pencarian mencocokkan sebagian isi data pada tab aktif.</small> --}}
                        </div>
                        <div class="col-lg-4 col-md-3">
                            <a href="{{ route('project_management.my_tasks.index', array_filter([$holding->holding_code, 'status' => request('status')])) }}"
                                class="btn btn-outline-secondary w-100">Hapus Pencarian</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Project</th>
                                <th>Kode Task</th>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Estimate</th>
                                <th>Actual</th>
                                <th>Update Status</th>
                                <th>Due Aktif</th>
                                <th style="width:190px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                @php
                                    $pendingCommentReplyCount = $pendingCommentReplyCounts[$task->id] ?? 0;
                                    $effectiveDueDate = $task->status === 'review'
                                        ? ($task->review_due_date ?: $task->due_revision_date ?: $task->due_date)
                                        : ($task->due_revision_date ?: $task->due_date);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $tasks->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $task->project?->project_name ?? '-' }}</div>
                                        <small class="text-muted">{{ $task->project?->project_code ?? '-' }}</small>
                                    </td>
                                    <td>{{ $task->task_code }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $task->title }}</div>
                                        <small class="text-muted">{{ ucfirst($task->type) }}</small>
                                        @if ($task->status === 'blocked' && $task->blocker_reason)
                                            <div class="small text-danger mt-1">{{ $task->blocker_reason }}</div>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($task->priority) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                            {{ $statusOptions[$task->status] ?? $task->status }}
                                        </span>
                                    </td>
                                    <td>{{ $task->estimate_hours ?? 0 }}</td>
                                    <td>{{ $task->actual_hours ?? 0 }}</td>
                                    <td>{{ $task->status_updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if ($task->status === 'review' && $task->review_due_date)
                                            <div><small class="text-warning">Review Due:</small> {{ $task->review_due_date->format('d/m/Y') }}</div>
                                            <div><small class="text-muted">Due Task:</small> {{ $task->due_date?->format('d/m/Y') ?? '-' }}</div>
                                        @elseif ($task->due_revision_date)
                                            <div><small class="text-info">Due Revisi:</small> {{ $task->due_revision_date->format('d/m/Y') }}</div>
                                            <div><small class="text-muted">Due Task:</small> {{ $task->due_date?->format('d/m/Y') ?? '-' }}</div>
                                        @else
                                            <div>{{ $effectiveDueDate?->format('d/m/Y') ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('project_management.tasks.show', [$holding->holding_code, $task->project_id, $task->id]) }}#komentar-task"
                                                class="btn btn-info btn-sm position-relative">
                                                Komentar
                                                @if ($pendingCommentReplyCount > 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $pendingCommentReplyCount }}
                                                    </span>
                                                @endif
                                            </a>

                                            @if (in_array($task->status, ['blocked', 'review', 'done', 'cancelled'], true))
                                                <span class="badge bg-label-secondary">
                                                    @if ($task->status === 'blocked')
                                                        Menunggu PM
                                                    @elseif ($task->status === 'review')
                                                        Menunggu Review
                                                    @else
                                                        Locked
                                                    @endif
                                                </span>
                                            @else
                                                <a href="{{ route('project_management.my_tasks.edit', [$holding->holding_code, $task->id]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    Update
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Belum ada task yang assigned ke Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tasks->links('admin.project_management.my_tasks.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection
