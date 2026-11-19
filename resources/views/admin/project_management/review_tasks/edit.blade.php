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
        $reviewedAtValue = old(
            'reviewed_at',
            optional($task->reviewed_at)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i'),
        );
        $dueRevisionValue = old('due_revision_date', optional($task->due_revision_date)->format('Y-m-d'));
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Review QA / UAT Task</h5>
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
                    <a href="{{ route('project_management.review_tasks.index', $holding->holding_code) }}"
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
                                    <th>Developer</th>
                                    <td>{{ $userName($task->primaryAssignee) }}</td>
                                </tr>
                                <tr>
                                    <th>Reviewer / QA</th>
                                    <td>{{ $userName($task->reviewer) }}</td>
                                </tr>
                                <tr>
                                    <th>Submit Review</th>
                                    <td>{{ $task->status_updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <td>
                                        {{ $task->due_date?->format('d/m/Y') ?? '-' }}
                                        @if ($task->due_revision_date)
                                            <span class="badge bg-info ms-2">Due Revisi {{ $task->due_revision_date->format('d/m/Y') }}</span>
                                        @endif
                                        @if ($task->review_due_date)
                                            <span class="badge bg-warning ms-2">Review Due {{ $task->review_due_date->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Acceptance Criteria</th>
                                    <td>{{ $task->acceptance_criteria ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan Hasil Developer</th>
                                    <td>{{ $task->developer_update_notes ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status Saat Ini</th>
                                    <td>
                                        <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <form method="POST"
                            action="{{ route('project_management.review_tasks.update', [$holding->holding_code, $task->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Keputusan Review <span class="text-danger">*</span></label>
                            <select name="status" id="review_status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'done') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Done menghitung progress project; Return mengembalikan ke developer.</small>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal & Jam Review <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="reviewed_at"
                                class="form-control @error('reviewed_at') is-invalid @enderror"
                                value="{{ $reviewedAtValue }}" required>
                            <small class="text-muted d-block mt-1">Isi waktu aktual saat QA/UAT melakukan review.</small>
                            @error('reviewed_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Reviewer</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->username }}" readonly>
                            <small class="text-muted d-block mt-1">Akun reviewer login yang menyimpan keputusan.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Due Revisi</label>
                            <input type="date" name="due_revision_date"
                                id="due_revision_date"
                                class="form-control @error('due_revision_date') is-invalid @enderror"
                                value="{{ $dueRevisionValue }}">
                            <small class="text-muted d-block mt-1">Wajib jika Return/Blocked; dipakai sebagai target perbaikan dan overdue aktif.</small>
                            @error('due_revision_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Catatan QA / UAT</label>
                            <textarea name="review_notes" rows="3" class="form-control @error('review_notes') is-invalid @enderror"
                                placeholder="Tulis catatan review terbaru...">{{ old('review_notes') }}</textarea>
                            <small class="text-muted d-block mt-1">Isi catatan review baru untuk keputusan ini; catatan sebelumnya tetap tersimpan di Riwayat Pekerjaan.</small>
                            @error('review_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Catatan Perbaikan / Ekspektasi Reviewer</label>
                            <textarea name="review_revision_notes" rows="3"
                                class="form-control @error('review_revision_notes') is-invalid @enderror"
                                placeholder="Wajib diisi jika task dikembalikan ke developer atau diblokir reviewer">{{ old('review_revision_notes') }}</textarea>
                            <small class="text-muted d-block mt-1">Tuliskan ekspektasi/perbaikan baru untuk siklus review ini; ekspektasi sebelumnya tetap ada di Riwayat Pekerjaan.</small>
                            @error('review_revision_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Upload Foto / File Review</label>
                            <input type="file" name="review_files[]" multiple
                                class="form-control @error('review_files') is-invalid @enderror @error('review_files.*') is-invalid @enderror">
                            <small class="text-muted d-block mt-1">Lampirkan screenshot, foto, dokumen UAT, atau bukti perbaikan yang diharapkan. Maksimal 10 MB per file.</small>
                            @error('review_files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('review_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Blocker / Alasan Return</label>
                            <textarea name="blocker_reason" rows="3"
                                class="form-control @error('blocker_reason') is-invalid @enderror"
                                placeholder="Wajib diisi jika status Blocked">{{ old('blocker_reason', $task->blocker_reason) }}</textarea>
                            <small class="text-muted d-block mt-1">Isi jika task perlu dikembalikan atau ada blocker setelah review.</small>
                            @error('blocker_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('project_management.review_tasks.index', $holding->holding_code) }}"
                                    class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Review</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusInput = document.getElementById('review_status');
            const dueRevisionInput = document.getElementById('due_revision_date');
            const reviewedAtInput = document.querySelector('input[name="reviewed_at"]');

            if (!statusInput || !dueRevisionInput) {
                return;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const formatDate = function (date) {
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${date.getFullYear()}-${month}-${day}`;
            };
            const selectedReviewDate = function () {
                if (!reviewedAtInput || !reviewedAtInput.value) {
                    return today;
                }

                const date = new Date(reviewedAtInput.value);
                date.setHours(0, 0, 0, 0);

                return Number.isNaN(date.getTime()) ? today : date;
            };

            const syncDueRevisionRequired = function () {
                dueRevisionInput.required = ['in_progress', 'blocked'].includes(statusInput.value);
                const minimumDate = selectedReviewDate() > today ? selectedReviewDate() : today;
                dueRevisionInput.min = formatDate(minimumDate);
                validateDueRevisionDate();
            };

            const validateDueRevisionDate = function () {
                dueRevisionInput.setCustomValidity('');

                if (!dueRevisionInput.value) {
                    return;
                }

                const selectedDate = new Date(dueRevisionInput.value);
                selectedDate.setHours(0, 0, 0, 0);

                if (selectedDate < today || selectedDate < selectedReviewDate()) {
                    dueRevisionInput.setCustomValidity('Due Revisi tidak boleh sebelum hari ini atau sebelum tanggal review.');
                    return;
                }

                if (selectedDate.getDay() === 0) {
                    dueRevisionInput.setCustomValidity('Due Revisi tidak boleh jatuh pada hari Minggu.');
                }
            };

            statusInput.addEventListener('change', syncDueRevisionRequired);
            dueRevisionInput.addEventListener('change', validateDueRevisionDate);
            reviewedAtInput?.addEventListener('change', syncDueRevisionRequired);
            syncDueRevisionRequired();
        });
    </script>
@endsection
