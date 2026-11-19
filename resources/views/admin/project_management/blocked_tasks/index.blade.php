@extends('admin.layouts.dashboard')

@section('isi')
    @php
        $userName = fn($user) => $user ? (data_get($user, 'KaryawanNIK.name') ?: $user->username) : '-';
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">BLOCKED TASK</h5>
                    <small class="text-muted">Task blocked pada project yang Anda kelola sebagai PM.</small>
                </div>
                <a href="{{ route('project_management.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Data Project
                </a>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('project_management.blocked_tasks.index', $holding->holding_code) }}">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-lg-9 col-md-8">
                            <label class="form-label">Cari Blocked Task</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Kode task, judul task, blocker, kode project, atau nama project">
                            <small class="text-muted d-block mt-1">Gunakan untuk mencari blocker yang perlu keputusan PM.</small>
                        </div>
                        <div class="col-lg-3 col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                            <a href="{{ route('project_management.blocked_tasks.index', $holding->holding_code) }}"
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
                                <th>Assignee</th>
                                <th>Reviewer</th>
                                <th>Blocked Since</th>
                                <th>Due Date</th>
                                <th>Blocker Reason</th>
                                <th style="width:150px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                @php
                                    $effectiveDueDate = $task->due_revision_date ?: $task->due_date;
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
                                        <small class="text-muted">{{ ucfirst($task->type) }} - {{ ucfirst($task->priority) }}</small>
                                    </td>
                                    <td>{{ $userName($task->primaryAssignee) }}</td>
                                    <td>{{ $userName($task->reviewer) }}</td>
                                    <td>{{ $task->blocked_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        <div>{{ $effectiveDueDate?->format('d/m/Y') ?? '-' }}</div>
                                        @if ($task->due_revision_date)
                                            <small class="text-info">Due Revisi</small>
                                            <small class="text-muted d-block">Awal: {{ $task->due_date?->format('d/m/Y') ?? '-' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $task->blocker_reason ?: '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('project_management.tasks.show', [$holding->holding_code, $task->project_id, $task->id]) }}"
                                                class="btn btn-info btn-sm" title="Detail Task">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('project_management.tasks.edit', [$holding->holding_code, $task->project_id, $task->id]) }}"
                                                class="btn btn-warning btn-sm" title="Reopen Task">
                                                Reopen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Belum ada blocked task pada project Anda.</td>
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
