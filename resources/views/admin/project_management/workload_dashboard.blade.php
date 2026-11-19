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
            'todo' => 'secondary',
            'in_progress' => 'primary',
            'blocked' => 'danger',
            'review' => 'warning',
            'done' => 'success',
            'cancelled' => 'dark',
        ];
        $number = fn($value) => number_format((float) $value, 2);
        $effectiveDueDate = fn($task) => $task->status === 'review'
            ? ($task->review_due_date ?: $task->due_revision_date ?: $task->due_date)
            : ($task->due_revision_date ?: $task->due_date);
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <a href="{{ route('project_management.report', $holding->holding_code) }}" class="btn btn-outline-primary btn-sm"><i class="mdi mdi-file-chart-outline"></i> Report</a>
                <div>
                    <h5 class="mb-1">Dashboard Workload Project Management</h5>
                    <small class="text-muted">Rangkuman semua holding untuk membantu keputusan assign task. Estimasi MD dihitung dari hari kerja target task.</small>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    @foreach (['pending' => 'Done Belum Disampaikan', 'presented' => 'Sudah Disampaikan via MoM'] as $key => $label)
                        <div class="col-md-6">
                            <a class="d-block border rounded p-3" href="#presentation-{{ $key }}" onclick="document.getElementById('presentation-{{ $key }}').open = true">
                                <small>{{ $label }}</small>
                                <div class="fw-semibold">{{ $tasks->where('status', 'done')->filter(fn($item) => $key === 'presented' ? $item->isCommunicated() : !$item->isCommunicated())->count() }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
                @foreach (['pending' => 'Done Belum Disampaikan', 'presented' => 'Sudah Disampaikan via MoM'] as $key => $label)
                    <details class="mb-3" id="presentation-{{ $key }}">
                        <summary>{{ $label }}</summary>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-striped">
                                <thead><tr><th>Project</th><th>Task</th><th>Penyampaian</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    @forelse ($tasks->where('status', 'done')->filter(fn($item) => $key === 'presented' ? $item->isCommunicated() : !$item->isCommunicated()) as $item)
                                        <tr><td>{{ $item->project->project_name }}</td><td>{{ $item->title }}</td><td>{{ $item->communicatedAt()?->format('d/m/Y H:i') ?? 'Belum Disampaikan' }}</td><td><a class="btn btn-info btn-sm" href="{{ route('project_management.tasks.show', [$item->project->holding->holding_code, $item->project_id, $item->id]) }}">Detail</a></td></tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted text-center">Tidak ada task.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
                <form method="GET" action="{{ route('project_management.workload_dashboard') }}" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Cari Project / Task / User</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Kode, nama project, task, username">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Departemen</label>
                            <select name="department_id" class="form-select">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id }}" {{ request('department_id') == $departemen->id ? 'selected' : '' }}>
                                        {{ $departemen->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Assignee</label>
                            <select name="assignee_id" class="form-select">
                                <option value="">Semua Assignee</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assignee_id') == $user->id ? 'selected' : '' }}>
                                        {{ $userName($user) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Reviewer</label>
                            <select name="reviewer_id" class="form-select">
                                <option value="">Semua Reviewer</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('reviewer_id') == $user->id ? 'selected' : '' }}>
                                        {{ $userName($user) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-4">
                            <label class="form-label">Due Aktif Dari</label>
                            <input type="date" name="due_from" value="{{ request('due_from') }}" class="form-control">
                        </div>
                        <div class="col-lg-1 col-md-4">
                            <label class="form-label">Due Aktif Sampai</label>
                            <input type="date" name="due_to" value="{{ request('due_to') }}" class="form-control">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('project_management.workload_dashboard') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Project Terlibat</small>
                            <div class="h3 mb-0">{{ $summary['projects'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Task Aktif</small>
                            <div class="h3 mb-0">{{ $summary['active_tasks'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Estimasi Aktif</small>
                            <div class="h3 mb-0">{{ $number($summary['active_estimate']) }}</div>
                            <small class="text-muted">MD</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Overdue</small>
                            <div class="h3 mb-0 text-danger">{{ $summary['overdue'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Blocked</small>
                            <div class="h3 mb-0 text-danger">{{ $summary['blocked'] }}</div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Menunggu Review</small>
                            <div class="h3 mb-0 text-warning">{{ $summary['review'] }}</div>
                            @if (($summary['review_overdue'] ?? 0) > 0)
                                <small class="text-danger">{{ $summary['review_overdue'] }} overdue review</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    @foreach ($statusCounts as $status => $count)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block mb-2">{{ $statusOptions[$status] ?? ucwords(str_replace('_', ' ', $status)) }}</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h4 mb-0">{{ $count }}</span>
                                    <span class="badge bg-{{ $statusColor[$status] ?? 'secondary' }}">Task</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row g-4">
                    <div class="col-xl-7">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">Workload Developer / Assignee</h6>
                                    <small class="text-muted">Estimasi aktif dihitung dari hari kerja task yang belum selesai.</small>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Assignee</th>
                                            <th class="text-center">Open</th>
                                            <th class="text-center">Todo</th>
                                            <th class="text-center">Progress</th>
                                            <th class="text-center">Review</th>
                                            <th class="text-center">Blocked</th>
                                            <th class="text-center">Overdue</th>
                                            <th class="text-end table-warning">Est. Aktif (MD)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($developerWorkloads as $workload)
                                            <tr>
                                                <td>{{ $userName($workload['assignee']) }}</td>
                                                <td class="text-center">{{ $workload['open_count'] }}</td>
                                                <td class="text-center">{{ $workload['todo_count'] }}</td>
                                                <td class="text-center">{{ $workload['in_progress_count'] }}</td>
                                                <td class="text-center">{{ $workload['review_count'] }}</td>
                                                <td class="text-center text-danger">{{ $workload['blocked_count'] }}</td>
                                                <td class="text-center text-danger">{{ $workload['overdue_count'] }}</td>
                                                <td class="text-end table-warning fw-semibold">{{ $number($workload['active_estimate']) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Belum ada data workload.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-1">Posisi Task Reviewer / QA</h6>
                            <small class="text-muted d-block mb-3">Menunjukkan task reviewer yang masih di developer dan yang sudah menunggu review.</small>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Reviewer</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Di Dev</th>
                                            <th class="text-center">Di Reviewer</th>
                                            <th class="table-warning">Review Due Terdekat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reviewerQueues as $queue)
                                            <tr>
                                                <td>{{ $userName($queue['reviewer']) }}</td>
                                                <td class="text-center">{{ $queue['task_count'] }}</td>
                                                <td class="text-center">{{ $queue['developer_count'] }}</td>
                                                <td class="text-center">
                                                    {{ $queue['review_count'] }}
                                                    @if (($queue['review_overdue_count'] ?? 0) > 0)
                                                        <small class="text-danger d-block">{{ $queue['review_overdue_count'] }} overdue</small>
                                                    @endif
                                                </td>
                                                <td class="table-warning fw-semibold">
                                                    @if ($queue['review_count'] > 0)
                                                        {{ $queue['nearest_review_due_date']?->format('d/m/Y') ?? '-' }}
                                                    @else
                                                        <small class="text-muted">Belum masuk reviewer</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Belum ada task dengan reviewer.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3 mt-4">
                    <h6 class="mb-1">Total Pekerjaan & Review</h6>
                    <small class="text-muted d-block mb-3">Rangkuman gabungan tanggung jawab user sebagai assignee/developer dan sebagai reviewer.</small>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>User</th>
                                    <th class="text-center">Pekerjaan</th>
                                    <th class="text-end table-warning">Est. Pekerjaan (MD)</th>
                                    <th class="text-center">Review Total</th>
                                    <th class="text-center">Review Masih di Dev</th>
                                    <th class="text-center">Review di Reviewer</th>
                                    <th class="table-warning">Review Due Terdekat</th>
                                    <th class="text-center table-success">Total Tanggung Jawab</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($combinedWorkloads as $workload)
                                    <tr>
                                        <td>{{ $userName($workload['user']) }}</td>
                                        <td class="text-center">{{ $workload['development_count'] }}</td>
                                        <td class="text-end table-warning fw-semibold">{{ $number($workload['development_estimate']) }}</td>
                                        <td class="text-center">{{ $workload['review_total_count'] }}</td>
                                        <td class="text-center">{{ $workload['review_in_developer_count'] }}</td>
                                        <td class="text-center">{{ $workload['review_in_reviewer_count'] }}</td>
                                        <td class="table-warning fw-semibold">
                                            @if ($workload['review_in_reviewer_count'] > 0)
                                                {{ $workload['nearest_review_due_date']?->format('d/m/Y') ?? '-' }}
                                            @else
                                                <small class="text-muted">Belum masuk reviewer</small>
                                            @endif
                                        </td>
                                        <td class="text-center table-success fw-semibold">{{ $workload['total_responsibility_count'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Belum ada pekerjaan aktif atau review aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border rounded p-3 mt-4" id="task-risiko">
                    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                        <div>
                            <h6 class="mb-1">Task Risiko / Perlu Perhatian</h6>
                            <small class="text-muted d-block">Prioritas tampil: blocked, overdue, lalu due date terdekat.</small>
                        </div>
                        <form method="GET" action="{{ route('project_management.workload_dashboard') }}" class="d-flex align-items-end gap-2">
                            @foreach (request()->except(['per_page', 'page']) as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @elseif ($value !== null && $value !== '')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <div>
                                <label class="form-label mb-1">Tampil</label>
                                <select name="per_page" class="form-select" onchange="this.form.submit()">
                                    @foreach ([25, 50, 100] as $option)
                                        <option value="{{ $option }}" {{ (int) $riskPerPage === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Project</th>
                                    <th>Task</th>
                                    <th>Assignee</th>
                                    <th>Status</th>
                                    <th class="text-end">Estimate (MD)</th>
                                    <th>Due Aktif</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riskTasks as $task)
                                    @php
                                        $taskEffectiveDueDate = $effectiveDueDate($task);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $task->project?->project_name ?? '-' }}</div>
                                            <small class="text-muted">{{ $task->project?->project_code ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $task->title }}</div>
                                            <small class="text-muted">{{ $task->task_code }}</small>
                                            @if ($task->status === 'blocked' && $task->blocker_reason)
                                                <div class="small text-danger mt-1">{{ $task->blocker_reason }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $userName($task->primaryAssignee) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                                {{ $statusOptions[$task->status] ?? ucwords(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ $number($task->estimate_mandays ?? 0) }}</td>
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
                                            @if ($taskEffectiveDueDate && $taskEffectiveDueDate->isPast())
                                                <span class="badge bg-danger ms-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($task->project?->holding?->holding_code)
                                                <a href="{{ route('project_management.tasks.show', [$task->project->holding->holding_code, $task->project_id, $task->id]) }}"
                                                    class="btn btn-info btn-sm">
                                                    Detail
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada task risiko dari filter saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-muted">
                                Menampilkan {{ $riskTasks->firstItem() ?? 0 }} - {{ $riskTasks->lastItem() ?? 0 }}
                                dari {{ $riskTasks->total() }} data
                            </small>
                            @if ($riskTasks->hasPages())
                                <nav aria-label="Pagination task risiko">
                                    <ul class="pagination mb-0">
                                        <li class="page-item {{ $riskTasks->onFirstPage() ? 'disabled' : '' }}">
                                            @if ($riskTasks->onFirstPage())
                                                <span class="page-link">Sebelumnya</span>
                                            @else
                                                <a class="page-link" href="{{ $riskTasks->previousPageUrl() }}">Sebelumnya</a>
                                            @endif
                                        </li>

                                        @foreach ($riskTasks->getUrlRange(1, $riskTasks->lastPage()) as $page => $url)
                                            <li class="page-item {{ $riskTasks->currentPage() === $page ? 'active' : '' }}">
                                                @if ($riskTasks->currentPage() === $page)
                                                    <span class="page-link">{{ $page }}</span>
                                                @else
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                @endif
                                            </li>
                                        @endforeach

                                        <li class="page-item {{ $riskTasks->hasMorePages() ? '' : 'disabled' }}">
                                            @if ($riskTasks->hasMorePages())
                                                <a class="page-link" href="{{ $riskTasks->nextPageUrl() }}">Berikutnya</a>
                                            @else
                                                <span class="page-link">Berikutnya</span>
                                            @endif
                                        </li>
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
