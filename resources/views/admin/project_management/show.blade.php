@extends('admin.layouts.dashboard')

@section('isi')
    @php
        $userName = function ($user) {
            if (!$user) {
                return '-';
            }

            $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

            return $employeeName && $employeeName !== $user->username
                ? $user->username . ' - ' . $employeeName
                : $user->username;
        };
        $statusColor = [
            'draft' => 'secondary',
            'proposed' => 'info',
            'under_review' => 'primary',
            'approved' => 'success',
            'planning' => 'warning',
            'in_progress' => 'primary',
            'uat' => 'info',
            'ready_for_deployment' => 'success',
            'completed' => 'success',
            'on_hold' => 'dark',
            'cancelled' => 'danger',
        ];
        $healthColor = [
            'gray' => 'secondary',
            'green' => 'success',
            'yellow' => 'warning',
            'red' => 'danger',
        ];
        $taskStatusColor = [
            'todo' => 'secondary',
            'in_progress' => 'primary',
            'blocked' => 'danger',
            'review' => 'warning',
            'done' => 'success',
            'cancelled' => 'dark',
        ];
        $activeTasks = $project->tasks->where('status', '!=', 'cancelled');
        $doneTasks = $activeTasks->where('status', 'done');
        $effectiveDueDate = fn($task) => $task->status === 'review'
            ? ($task->review_due_date ?: $task->due_revision_date ?: $task->due_date)
            : ($task->due_revision_date ?: $task->due_date);
        $involvedUserCount = $activeTasks
            ->pluck('primary_assignee_id')
            ->merge($activeTasks->pluck('reviewer_id'))
            ->filter()
            ->unique()
            ->count();
        $nearestDueDate = $activeTasks
            ->filter(fn($task) => filled($effectiveDueDate($task)) && $task->status !== 'done')
            ->sortBy(fn($task) => $effectiveDueDate($task))
            ->map(fn($task) => $effectiveDueDate($task))
            ->first();
        $taskStatusCounts = collect(array_keys($taskStatusColor))
            ->mapWithKeys(fn($status) => [$status => $project->tasks->where('status', $status)->count()]);
        $overdueTasks = $activeTasks
            ->filter(fn($task) => ($effectiveDueDate($task))?->isPast() && $task->status !== 'done')
            ->count();
        $unassignedTasks = $project->tasks->whereNull('primary_assignee_id')->count();
        $tasksById = $project->tasks->keyBy('id');
        $canManageProject = $project->project_manager_id === auth()->id();
        $selectedTaskStatus = request('task_status');
        $presentationFilter = request('presentation');
        $presentationFilter = in_array($presentationFilter, ['pending', 'presented'], true) ? $presentationFilter : null;
        if (request('tab') === 'presentation' && !$presentationFilter) {
            $presentationFilter = 'pending';
        }
        $selectedTaskStatus = array_key_exists($selectedTaskStatus, $taskStatusColor) ? $selectedTaskStatus : null;
        $activeProjectTab = in_array(request('tab'), ['header', 'team'], true)
            ? request('tab')
            : (($selectedTaskStatus || $presentationFilter || request('tab') === 'detail') ? 'detail' : 'summary');
        $detailTaskUrl = fn($status = null) => route('project_management.show', [$holding->holding_code, $project->id])
            . '?tab=detail'
            . ($status ? '&task_status=' . $status : '')
            . '#detail-panel';
        $filteredTasks = ($selectedTaskStatus && !$presentationFilter
            ? $project->tasks->where('status', $selectedTaskStatus)
            : $project->tasks)
            ->filter(fn($task) => !in_array($presentationFilter, ['pending', 'presented'], true)
                || ($task->status === 'done' && ($presentationFilter === 'presented' ? $task->isCommunicated() : !$task->isCommunicated())))
            ->sortBy('sort_order');
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $project->project_name }}</h5>
                    <small class="text-muted">{{ $project->project_code }}</small>
                </div>
                <div class="d-flex gap-2">
                    @if ($canManageProject)
                        <a href="{{ route('project_management.edit', [$holding->holding_code, $project->id]) }}"
                            class="btn btn-warning btn-sm">Edit</a>
                    @endif
                    <a href="{{ route('project_management.index', $holding->holding_code) }}"
                        class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeProjectTab === 'summary' ? 'active' : '' }}" id="summary-tab" data-bs-toggle="tab"
                            data-bs-target="#summary-panel" type="button" role="tab">
                            Summary
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeProjectTab === 'header' ? 'active' : '' }}" id="header-tab" data-bs-toggle="tab" data-bs-target="#header-panel"
                            type="button" role="tab">
                            Header
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activeProjectTab === 'team' ? 'active' : '' }}" id="team-tab"
                            href="{{ route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'team']) }}#team-panel">
                            Tim & Milestone
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $activeProjectTab === 'detail' && !$presentationFilter ? 'active' : '' }}" id="detail-tab"
                            href="{{ $detailTaskUrl() }}">
                            Detail Task
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ route('project_management.meetings.index', [$holding->holding_code, $project->id]) }}">
                            Minutes of Meeting <span class="badge bg-secondary ms-1">{{ $project->meetings_count }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $presentationFilter ? 'active' : '' }}"
                            href="{{ route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'presentation']) }}#detail-panel">
                            Penyampaian
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade {{ $activeProjectTab === 'summary' ? 'show active' : '' }}" id="summary-panel" role="tabpanel" aria-labelledby="summary-tab">
                        <div class="row g-3 mb-3">
                            @foreach (['pending' => 'Done Belum Disampaikan', 'presented' => 'Sudah Disampaikan via MoM'] as $key => $label)
                                <div class="col-md-6">
                                    <a class="d-block border rounded p-3" href="{{ route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'detail', 'presentation' => $key]) }}#detail-panel">
                                        <small>{{ $label }}</small>
                                        <div class="fw-semibold">{{ $doneTasks->filter(fn($item) => $key === 'presented' ? $item->isCommunicated() : !$item->isCommunicated())->count() }}</div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Status Project</small>
                                    <div>
                                        <span class="badge bg-{{ $statusColor[$project->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Health</small>
                                    <div>
                                        <span class="badge bg-{{ $healthColor[$project->health] ?? 'secondary' }}">
                                            {{ ucfirst($project->health) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Progress Project</small>
                                    <div class="fw-semibold">{{ $project->progress_percent }}%</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Estimasi / Aktual (MD)</small>
                                    <div class="fw-semibold">{{ number_format($projectEstimateMandays, 2) }} / {{ number_format($projectActualMandays, 2) }} MD</div>
                                </div>
                            </div>
                        </div>

                        <div class="progress mb-4" style="height: 10px;">
                            <div class="progress-bar" style="width: {{ $project->progress_percent }}%"></div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">PIC Terlibat</small>
                                    <div class="display-6">{{ $involvedUserCount }}</div>
                                    <small class="text-muted">Assignee dan reviewer aktif</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Due Terdekat</small>
                                    <div class="display-6">{{ $nearestDueDate?->format('d/m') ?? '-' }}</div>
                                    <small class="text-muted">{{ $nearestDueDate?->format('Y') ?? 'Tidak ada due aktif' }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted">Task Aktif</small>
                                    <div class="display-6">{{ $activeTasks->count() }}</div>
                                    <small class="text-muted">{{ $doneTasks->count() }} selesai</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach ($taskStatusCounts as $status => $count)
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                    <a href="{{ $detailTaskUrl($status) }}" class="text-decoration-none text-body d-block h-100">
                                    <div class="border rounded p-3 h-100">
                                        <small class="text-muted d-block mb-2">{{ ucwords(str_replace('_', ' ', $status)) }}</small>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h4 mb-0">{{ $count }}</span>
                                            <span class="badge bg-{{ $taskStatusColor[$status] ?? 'secondary' }}">Task</span>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            @endforeach

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block mb-2">Lewat Due Date</small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h4 mb-0">{{ $overdueTasks }}</span>
                                        <span class="badge bg-danger">Open</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block mb-2">Belum Assign</small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h4 mb-0">{{ $unassignedTasks }}</span>
                                        <span class="badge bg-secondary">Task</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeProjectTab === 'header' ? 'show active' : '' }}" id="header-panel" role="tabpanel" aria-labelledby="header-tab">
                        <table class="table table-bordered align-middle">
                            <tr>
                                <th style="width: 240px;">Tipe Project</th>
                                <td>{{ $project->project_type }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $project->category ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Requester</th>
                                <td>{{ $userName($project->requester) }}</td>
                            </tr>
                            <tr>
                                <th>Departemen Requester</th>
                                <td>{{ $project->requesterDepartment?->nama_departemen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Business Owner</th>
                                <td>{{ $userName($project->businessOwner) }}</td>
                            </tr>
                            <tr>
                                <th>Project Manager</th>
                                <td>{{ $userName($project->projectManager) }}</td>
                            </tr>
                            <tr>
                                <th>Technical Lead</th>
                                <td>{{ $userName($project->technicalLead) }}</td>
                            </tr>
                            <tr>
                                <th>Sponsor</th>
                                <td>{{ $userName($project->sponsor) }}</td>
                            </tr>
                            <tr>
                                <th>Periode Rencana</th>
                                <td>
                                    {{ $project->planned_start_date?->format('d/m/Y') ?? '-' }}
                                    -
                                    {{ $project->planned_end_date?->format('d/m/Y') ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $project->description }}</td>
                            </tr>
                            <tr>
                                <th>Background</th>
                                <td>{{ $project->background ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Objective</th>
                                <td>{{ $project->objective ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Scope Summary</th>
                                <td>{{ $project->scope_summary ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Success Criteria</th>
                                <td>{{ $project->success_criteria ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>In Scope</th>
                                <td>{{ $project->in_scope ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Out of Scope</th>
                                <td>{{ $project->out_of_scope ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Narasi PM</th>
                                <td>{!! nl2br(e($project->pm_commentary ?: '-')) !!}</td>
                            </tr>
                            <tr>
                                <th>Keputusan / Dukungan Dibutuhkan</th>
                                <td>{!! nl2br(e($project->decisions_needed ?: '-')) !!}</td>
                            </tr>
                            <tr>
                                <th>Expected Benefit</th>
                                <td>{{ $project->expected_benefit ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="tab-pane fade {{ $activeProjectTab === 'team' ? 'show active' : '' }}" id="team-panel" role="tabpanel" aria-labelledby="team-tab">
                        @include('admin.project_management.partials.team_milestones')
                    </div>

                    <div class="tab-pane fade {{ $activeProjectTab === 'detail' ? 'show active' : '' }}" id="detail-panel" role="tabpanel" aria-labelledby="detail-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">{{ $presentationFilter ? ($presentationFilter === 'pending' ? 'Task Belum Disampaikan' : 'Task Sudah Disampaikan via MoM') : 'Task Detail & Progress' }}</h6>
                                <small class="text-muted d-block">Progress berbobot: {{ $project->progress_percent }}%</small>
                                <small class="text-muted">{{ $doneTasks->count() }} dari {{ $activeTasks->count() }} task aktif selesai</small>
                            </div>
                            @if ($canManageProject && !$presentationFilter)
                                <a href="{{ route('project_management.tasks.create', [$holding->holding_code, $project->id]) }}"
                                    class="btn btn-primary btn-sm">
                                    + Tambah Task
                                </a>
                            @endif
                        </div>

                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" style="width: {{ $project->progress_percent }}%"></div>
                        </div>

                        @if ($presentationFilter)
                            <ul class="nav nav-pills mb-3">
                                @foreach (['pending' => 'Belum Disampaikan', 'presented' => 'Sudah Disampaikan'] as $key => $label)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $presentationFilter === $key ? 'active' : '' }}"
                                            href="{{ route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'presentation', 'presentation' => $key]) }}#detail-panel">
                                            {{ $label }}
                                            <span class="badge bg-secondary ms-1">{{ $doneTasks->filter(fn($item) => $key === 'presented' ? $item->isCommunicated() : !$item->isCommunicated())->count() }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                        <ul class="nav nav-pills mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ !$selectedTaskStatus && !$presentationFilter ? 'active' : '' }}"
                                    href="{{ $detailTaskUrl() }}">
                                    All Task
                                    <span class="badge bg-secondary ms-1">{{ $project->tasks->count() }}</span>
                                </a>
                            </li>
                            @foreach ($taskStatusCounts as $status => $count)
                                <li class="nav-item">
                                    <a class="nav-link {{ $selectedTaskStatus === $status ? 'active' : '' }}"
                                        href="{{ $detailTaskUrl($status) }}">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                        <span class="badge bg-{{ $taskStatusColor[$status] ?? 'secondary' }} ms-1">{{ $count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th>Kode</th>
                                        <th>Task</th>
                                        <th>Parent</th>
                                        <th>Assignee</th>
                                        <th>Reviewer</th>
                                        <th>Status</th>
                                        <th>Penyampaian</th>
                                        <th>Estimate</th>
                                        <th>Actual</th>
                                        <th>Due Aktif</th>
                                        <th style="width:250px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($filteredTasks as $task)
                                        @php
                                            $pendingCommentReplyCount = $pendingCommentReplyCounts[$task->id] ?? 0;
                                            $taskEffectiveDueDate = $effectiveDueDate($task);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $task->task_code }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $task->title }}</div>
                                                <small class="text-muted">{{ ucfirst($task->type) }} - {{ ucfirst($task->priority) }}</small>
                                                @if ($task->status === 'blocked' && $task->blocker_reason)
                                                    <div class="small text-danger mt-1">{{ $task->blocker_reason }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($task->parent_task_id && $tasksById->has($task->parent_task_id))
                                                    {{ $tasksById[$task->parent_task_id]->task_code }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $userName($task->primaryAssignee) }}</td>
                                            <td>{{ $userName($task->reviewer) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $taskStatusColor[$task->status] ?? 'secondary' }}">
                                                    {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($task->status === 'done')
                                                    <span class="badge bg-{{ $task->isCommunicated() ? 'success' : 'warning' }}">{{ $task->isCommunicated() ? 'Sudah Disampaikan' : 'Belum Disampaikan' }}</span>
                                                    @if ($task->isCommunicated())
                                                        <small class="d-block">via MoM, {{ $task->communicatedAt()?->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $task->estimate_hours ?? 0 }}</td>
                                            <td>{{ $task->actual_hours ?? 0 }}</td>
                                            <td>
                                                @if ($task->status === 'review' && $task->review_due_date)
                                                    <div><small class="text-warning">Review Due:</small> {{ $task->review_due_date->format('d/m/Y') }}</div>
                                                    <div><small class="text-muted">Due Task:</small> {{ $task->due_date?->format('d/m/Y') ?? '-' }}</div>
                                                @elseif ($task->due_revision_date)
                                                    <div><small class="text-info">Due Revisi:</small> {{ $task->due_revision_date->format('d/m/Y') }}</div>
                                                    <div><small class="text-muted">Due Task:</small> {{ $task->due_date?->format('d/m/Y') ?? '-' }}</div>
                                                @else
                                                    <div>{{ $taskEffectiveDueDate?->format('d/m/Y') ?? '-' }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('project_management.tasks.show', [$holding->holding_code, $project->id, $task->id]) }}"
                                                        class="btn btn-info btn-sm" title="Detail Task">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('project_management.tasks.show', [$holding->holding_code, $project->id, $task->id]) }}#komentar-task"
                                                        class="btn btn-primary btn-sm position-relative" title="Komentar Task">
                                                        <i class="mdi mdi-message-text-outline"></i>
                                                        @if ($pendingCommentReplyCount > 0)
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                                {{ $pendingCommentReplyCount }}
                                                            </span>
                                                        @endif
                                                    </a>
                                                    @if ($canManageProject)
                                                        <a href="{{ route('project_management.tasks.edit', [$holding->holding_code, $project->id, $task->id]) }}"
                                                            class="btn btn-warning btn-sm" title="Edit Task">
                                                            <i class="mdi mdi-pencil-outline"></i>
                                                        </a>
                                                        @if ($task->status === 'done')
                                                            @include('admin.project_management.tasks._reopen_modal', ['task' => $task])
                                                        @endif
                                                        <form
                                                            action="{{ route('project_management.tasks.destroy', [$holding->holding_code, $project->id, $task->id]) }}"
                                                            method="POST" onsubmit="return confirm('Hapus task ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="Hapus Task">
                                                                <i class="mdi mdi-delete-outline"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-muted">
                                                Belum ada task {{ $selectedTaskStatus ? 'status ' . ucwords(str_replace('_', ' ', $selectedTaskStatus)) : '' }} untuk project ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
