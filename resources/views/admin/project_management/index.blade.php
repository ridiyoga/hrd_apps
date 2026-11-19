@extends('admin.layouts.dashboard')

@section('isi')
    @php
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
        $userName = function ($user) {
            if (!$user) {
                return '-';
            }

            $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

            return $employeeName && $employeeName !== $user->username
                ? $user->username . ' - ' . $employeeName
                : $user->username;
        };
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">PROJECT MANAGEMENT</h5>
                <a href="{{ route('project_management.report', $holding->holding_code) }}" class="btn btn-outline-primary btn-sm"><i class="mdi mdi-file-chart-outline"></i> Report</a>
                <a href="{{ route('project_management.create', $holding->holding_code) }}" class="btn btn-primary">
                    + Tambah Project
                </a>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('project_management.index', $holding->holding_code) }}">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Cari Project</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Kode atau nama project">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($statusColor as $status => $color)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">Semua Priority</option>
                                @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                                    <option value="{{ $priority }}"
                                        {{ request('priority') === $priority ? 'selected' : '' }}>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                            <a href="{{ route('project_management.index', $holding->holding_code) }}"
                                class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Kode</th>
                                <th>Nama Project</th>
                                <th>PM</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Health</th>
                                <th>Progress</th>
                                <th style="width:150px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                <tr>
                                    <td class="text-center">{{ $projects->firstItem() + $loop->index }}</td>
                                    <td>{{ $project->project_code }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $project->project_name }}</div>
                                        <small class="text-muted">{{ $project->project_type }}</small>
                                    </td>
                                    <td>
                                        {{ $userName($project->projectManager) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $project->priority === 'critical' ? 'danger' : ($project->priority === 'high' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($project->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusColor[$project->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $healthColor[$project->health] ?? 'secondary' }}">
                                            {{ ucfirst($project->health) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" style="width: {{ $project->progress_percent }}%">
                                            </div>
                                        </div>
                                        <small>{{ $project->progress_percent }}%</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            @if ($project->project_manager_id === auth()->id())
                                                <a href="{{ route('project_management.edit', [$holding->holding_code, $project->id]) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </a>
                                                <form
                                                    action="{{ route('project_management.destroy', [$holding->holding_code, $project->id]) }}"
                                                    method="POST" onsubmit="return confirm('Arsipkan project ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Arsipkan">
                                                        <i class="mdi mdi-archive-outline"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
