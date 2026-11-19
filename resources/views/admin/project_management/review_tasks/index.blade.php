@extends('admin.layouts.dashboard')

@section('isi')
    @php
        $statusColor = [
            'review' => 'warning',
            'done' => 'success',
            'in_progress' => 'primary',
            'blocked' => 'danger',
        ];
        $userName = fn($user) => $user ? (data_get($user, 'KaryawanNIK.name') ?: $user->username) : '-';
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">REVIEW TASK</h5>
                <a href="{{ route('project_management.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Data Project
                </a>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('project_management.review_tasks.index', $holding->holding_code) }}">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-lg-9 col-md-8">
                            <label class="form-label">Cari Task Review</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Kode task, judul task, kode project, atau nama project">
                            {{-- <small class="text-muted d-block mt-1">Hanya task yang sudah disubmit developer ke Review.</small> --}}
                        </div>
                        <div class="col-lg-3 col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                            <a href="{{ route('project_management.review_tasks.index', $holding->holding_code) }}"
                                class="btn btn-outline-secondary">Reset</a>
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
                                <th>Developer</th>
                                <th>Status</th>
                                <th>Submit Review</th>
                                <th>Due Aktif</th>
                                <th style="width:190px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                @php
                                    $pendingCommentReplyCount = $pendingCommentReplyCounts[$task->id] ?? 0;
                                    $effectiveDueDate = $task->review_due_date ?: $task->due_revision_date ?: $task->due_date;
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
                                        <small class="text-muted">{{ $task->acceptance_criteria ?: '-' }}</small>
                                    </td>
                                    <td>{{ $userName($task->primaryAssignee) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $task->status_updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if ($task->review_due_date)
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
                                            <a href="{{ route('project_management.review_tasks.edit', [$holding->holding_code, $task->id]) }}"
                                                class="btn btn-warning btn-sm">
                                                Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada task yang perlu direview.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
