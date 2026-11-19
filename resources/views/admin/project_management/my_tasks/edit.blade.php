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
        $userName = fn($user) => $user ? (data_get($user, 'KaryawanNIK.name') ?: $user->username) : '-';
        $statusUpdatedAtValue = old(
            'status_updated_at',
            optional($task->status_updated_at)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i'),
        );
        $actualHoursSuggestion = $task->actual_hours ?? 0;
        if ($task->start_date) {
            $actualSeconds = max(0, now()->timestamp - $task->start_date->copy()->startOfDay()->timestamp);
            $actualHoursSuggestion = round($actualSeconds / 3600, 2);
        }
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Update Progress Task</h5>
                    <small class="text-muted">{{ $task->task_code }} - {{ $task->project?->project_name }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('project_management.tasks.show', [$holding->holding_code, $task->project_id, $task->id]) }}#komentar-task"
                        class="btn btn-info btn-sm position-relative">
                        Komentar
                        @if (($pendingCommentReplyCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $pendingCommentReplyCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('project_management.my_tasks.index', $holding->holding_code) }}"
                        class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <tr>
                                    <th style="width:240px;">Project</th>
                                    <td>{{ $task->project?->project_code }} - {{ $task->project?->project_name }}</td>
                                </tr>
                                <tr>
                                    <th>Task</th>
                                    <td>{{ $task->title }}</td>
                                </tr>
                                <tr>
                                    <th>Assignee</th>
                                    <td>{{ $userName($task->primaryAssignee) }}</td>
                                </tr>
                                <tr>
                                    <th>Reviewer</th>
                                    <td>{{ $userName($task->reviewer) }}</td>
                                </tr>
                                <tr>
                                    <th>Estimate / Actual</th>
                                    <td>{{ $task->estimate_hours ?? 0 }} / {{ $task->actual_hours ?? 0 }} jam</td>
                                </tr>
                                <tr>
                                    <th>Update Status Terakhir</th>
                                    <td>{{ $task->status_updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Acceptance Criteria</th>
                                    <td>{{ $task->acceptance_criteria ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <td>
                                        {{ $task->due_date?->format('d/m/Y') ?? '-' }}
                                        @if ($task->due_revision_date)
                                            <span class="badge bg-info ms-2">Due Revisi {{ $task->due_revision_date->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <form method="POST"
                            action="{{ route('project_management.my_tasks.update', [$holding->holding_code, $task->id]) }}">
                            @csrf
                            @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('status', $task->status) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Pilih Review jika pekerjaan selesai dan perlu dicek reviewer/QA.</small>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal & Jam Update <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="status_updated_at"
                                class="form-control @error('status_updated_at') is-invalid @enderror"
                                value="{{ $statusUpdatedAtValue }}" required>
                            <small class="text-muted d-block mt-1">Isi waktu aktual saat status pekerjaan diupdate.</small>
                            @error('status_updated_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Actual Jam</label>
                            <input type="number" step="0.01" min="0" name="actual_hours"
                                class="form-control @error('actual_hours') is-invalid @enderror"
                                value="{{ old('actual_hours', $actualHoursSuggestion) }}">
                            <small class="text-muted d-block mt-1">Tersuggest dari Start Date sampai form dibuka; tetap bisa diubah manual.</small>
                            @error('actual_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status Saat Ini</label>
                            <div>
                                <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                    {{ $statusOptions[$task->status] ?? $task->status }}
                                </span>
                            </div>
                            <small class="text-muted d-block mt-2">Referensi status sebelum update.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Catatan Hasil Developer</label>
                            <textarea name="developer_update_notes" rows="3"
                                class="form-control @error('developer_update_notes') is-invalid @enderror"
                                placeholder="Tulis catatan hasil update terbaru...">{{ old('developer_update_notes') }}</textarea>
                            <small class="text-muted d-block mt-1">Tulis catatan baru untuk update ini; catatan sebelumnya tetap tersimpan di Riwayat Pekerjaan.</small>
                            @error('developer_update_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Blocker Reason</label>
                            <textarea name="blocker_reason" rows="3"
                                class="form-control @error('blocker_reason') is-invalid @enderror"
                                placeholder="Wajib diisi jika status Blocked">{{ old('blocker_reason', $task->blocker_reason) }}</textarea>
                            <small class="text-muted d-block mt-1">Isi hambatan, kebutuhan bantuan, atau next action jika task terblokir.</small>
                            @error('blocker_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('project_management.my_tasks.index', $holding->holding_code) }}"
                                    class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Update Progress</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-4">
                        @include('admin.project_management.tasks._work_history_card', ['task' => $task, 'maxHeight' => '620px'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
