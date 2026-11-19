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
        $commentTypeColor = [
            'comment' => 'secondary',
            'update' => 'primary',
            'blocker' => 'danger',
            'review' => 'warning',
        ];
        $canManageProject = $project->project_manager_id === auth()->id();
        $effectiveDueDate = $task->status === 'review'
            ? ($task->review_due_date ?: $task->due_revision_date ?: $task->due_date)
            : ($task->due_revision_date ?: $task->due_date);
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
                    <h5 class="mb-1">Detail Task</h5>
                    <small class="text-muted">{{ $task->task_code }} - {{ $task->title }} - {{ $project->project_name }}</small>
                </div>
                <div class="d-flex gap-2">
                    @if ($canManageProject)
                        @if ($task->status === 'done')
                            @include('admin.project_management.tasks._reopen_modal', ['task' => $task, 'showLabel' => true])
                        @endif
                        <a href="{{ route('project_management.tasks.edit', [$holding->holding_code, $project->id, $task->id]) }}"
                            class="btn btn-warning btn-sm">Edit</a>
                    @endif
                    <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                        class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                @if ($task->status === 'done')
                    <div class="mb-3">
                        <span class="badge bg-{{ $task->isCommunicated() ? 'success' : 'warning' }}">{{ $task->isCommunicated() ? 'Sudah Disampaikan via MoM' : 'Belum Disampaikan' }}</span>
                        @if($task->isCommunicated())<small class="ms-2 text-muted">{{ $task->communicatedAt()?->format('d/m/Y H:i') }}</small>@endif
                        @if($task->presented_at)<small class="d-block text-muted mt-1">Riwayat legacy presentasi: {{ $task->presented_at->format('d/m/Y H:i') }}</small>@endif
                    </div>
                @endif
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge bg-{{ $statusColor[$task->status] ?? 'secondary' }}">
                                    {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Priority</small>
                            <div class="fw-semibold">{{ ucfirst($task->priority) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Estimate / Actual</small>
                            <div class="fw-semibold">{{ $task->estimate_hours ?? 0 }} / {{ $task->actual_hours ?? 0 }} jam</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Due Date</small>
                            <div class="fw-semibold">{{ $effectiveDueDate?->format('d/m/Y') ?? '-' }}</div>
                            @if ($task->status === 'review' && $task->review_due_date)
                                <small class="text-warning">Review Due</small>
                            @elseif ($task->due_revision_date)
                                <small class="text-info">Due Revisi</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <table class="table table-bordered align-middle">
                            <tr>
                                <th style="width:240px;">Project</th>
                                <td>{{ $project->project_code }} - {{ $project->project_name }}</td>
                            </tr>
                            <tr>
                                <th>Judul Task</th>
                                <td>
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    <small class="text-muted">{{ $task->task_code }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Parent Task</th>
                                <td>{{ $task->parentTask?->task_code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ ucfirst($task->type) }}</td>
                            </tr>
                            <tr>
                                <th>Reporter</th>
                                <td>{{ $userName($task->reporter) }}</td>
                            </tr>
                            <tr>
                                <th>Assignee</th>
                                <td>{{ $userName($task->primaryAssignee) }}</td>
                            </tr>
                            <tr>
                                <th>Reviewer / QA</th>
                                <td>{{ $userName($task->reviewer) }}</td>
                            </tr>
                            <tr>
                                <th>Start Date</th>
                                <td>{{ $task->start_date?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Due Date Awal</th>
                                <td>{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Due Revisi</th>
                                <td>{{ $task->due_revision_date?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Review Due Date</th>
                                <td>{{ $task->review_due_date?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Update Status Terakhir</th>
                                <td>{{ $task->status_updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Acceptance Criteria</th>
                                <td>{{ $task->acceptance_criteria ?: '-' }}</td>
                            </tr>
                        </table>

                        <div class="border rounded p-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">Lampiran Task</h6>
                                    <small class="text-muted">Dokumen awal task, hasil review, dan dokumen pendukung lainnya.</small>
                                </div>
                                <span class="badge bg-secondary">{{ $task->attachments->count() }} file</span>
                            </div>

                            @php
                                $attachmentSourceLabels = [
                                    'task' => ['label' => 'Task Awal', 'color' => 'warning'],
                                    'review' => ['label' => 'Review / QA', 'color' => 'info'],
                                    'presentation' => ['label' => 'Penyampaian', 'color' => 'success'],
                                ];
                            @endphp
                            @forelse ($task->attachments->sortByDesc('created_at') as $attachment)
                                @php
                                    $sourceMeta = $attachmentSourceLabels[$attachment->source]
                                        ?? ['label' => ucfirst($attachment->source ?: 'Lainnya'), 'color' => 'secondary'];
                                @endphp
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-top py-2 {{ $loop->first ? 'border-top-0 pt-0' : '' }}">
                                    <div class="min-w-0">
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="badge bg-{{ $sourceMeta['color'] }}">{{ $sourceMeta['label'] }}</span>
                                            <span class="fw-semibold text-break">{{ $attachment->original_name }}</span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $userName($attachment->uploader) }}
                                            - {{ $attachment->created_at?->format('d/m/Y H:i') ?? '-' }}
                                            @if ($attachment->file_size)
                                                - {{ number_format($attachment->file_size / 1024, 0, ',', '.') }} KB
                                            @endif
                                        </small>
                                    </div>
                                    <a href="{{ route('project_management.tasks.attachments.show', [
                                        $holding->holding_code,
                                        $project->id,
                                        $task->id,
                                        $attachment->id,
                                    ]) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        Buka
                                    </a>
                                </div>
                            @empty
                                <div class="text-center text-muted py-2">Belum ada lampiran untuk task ini.</div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">Komentar Task</h6>
                            <small class="text-muted">
                                Diskusi, arahan PM, catatan developer, dan review QA/UAT.
                                @if (($pendingCommentReplyCount ?? 0) > 0)
                                    <span class="badge bg-danger ms-1">{{ $pendingCommentReplyCount }} perlu dibalas</span>
                                @endif
                            </small>
                        </div>
                    </div>

                    @if ($canCommentTask)
                        <form method="POST"
                            action="{{ route('project_management.tasks.comments.store', [$holding->holding_code, $project->id, $task->id]) }}"
                            class="border rounded p-3 mb-3 bg-white">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-4">
                                    <label class="form-label fw-semibold">Tipe Komentar</label>
                                    <select name="comment_type" class="form-select @error('comment_type') is-invalid @enderror" required>
                                        <option value="comment" {{ old('comment_type', 'comment') === 'comment' ? 'selected' : '' }}>Comment</option>
                                        <option value="update" {{ old('comment_type') === 'update' ? 'selected' : '' }}>Update</option>
                                        <option value="blocker" {{ old('comment_type') === 'blocker' ? 'selected' : '' }}>Blocker</option>
                                        <option value="review" {{ old('comment_type') === 'review' ? 'selected' : '' }}>Review</option>
                                    </select>
                                    @error('comment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-7 col-md-8">
                                    <label class="form-label fw-semibold">Komentar</label>
                                    <textarea name="comment" rows="2" class="form-control @error('comment') is-invalid @enderror"
                                        placeholder="Tulis komentar, arahan, klarifikasi, atau catatan task..." required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-2 col-md-12 d-grid">
                                    <button type="submit" class="btn btn-primary">Tambah Komentar</button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <div id="komentar-task">
                        @php
                            $rootComments = $task->comments
                                ->filter(fn($comment) => blank($comment->parent_id) && $comment->comment_type !== 'system')
                                ->sortByDesc('created_at');
                        @endphp

                        @forelse ($rootComments as $comment)
                            @php
                                $replyCount = $comment->replies->count();
                                $repliesCollapseId = 'comment-replies-' . $comment->id;
                                $replyFormCollapseId = 'comment-reply-form-' . $comment->id;
                            @endphp
                            <div class="border rounded p-2 mb-2 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2 small">
                                            <strong class="text-body">{{ $userName($comment->user) }}</strong>
                                            <span class="badge bg-{{ $commentTypeColor[$comment->comment_type] ?? 'secondary' }}">
                                                {{ ucfirst($comment->comment_type) }}
                                            </span>
                                            <small class="text-muted">{{ $comment->created_at?->format('d/m/Y H:i') ?? '-' }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        @if ($canCommentTask)
                                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2"
                                                data-bs-toggle="collapse" data-bs-target="#{{ $replyFormCollapseId }}"
                                                aria-expanded="false" aria-controls="{{ $replyFormCollapseId }}">
                                                Balas
                                            </button>
                                        @endif
                                        @if ($comment->user_id === auth()->id() || $canManageProject)
                                            <form method="POST"
                                                action="{{ route('project_management.tasks.comments.destroy', [$holding->holding_code, $project->id, $task->id, $comment->id]) }}"
                                                onsubmit="return confirm('Hapus komentar ini beserta balasannya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm py-0 px-2">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="small mt-2">{!! nl2br(e($comment->comment)) !!}</div>

                                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                    @if ($replyCount > 0)
                                        <button type="button" class="btn btn-link btn-sm p-0"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $repliesCollapseId }}"
                                            aria-expanded="false" aria-controls="{{ $repliesCollapseId }}">
                                            Lihat {{ $replyCount }} balasan
                                        </button>
                                    @endif
                                </div>

                                @if ($replyCount > 0)
                                    <div class="collapse mt-2 ps-3 border-start" id="{{ $repliesCollapseId }}">
                                        @foreach ($comment->replies->sortBy('created_at') as $reply)
                                            <div class="border rounded p-2 mb-2 bg-light">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex flex-wrap align-items-center gap-2 small">
                                                            <strong class="text-body">{{ $userName($reply->user) }}</strong>
                                                            <span class="badge bg-{{ $commentTypeColor[$reply->comment_type] ?? 'secondary' }}">
                                                                {{ ucfirst($reply->comment_type) }}
                                                            </span>
                                                            <small class="text-muted">{{ $reply->created_at?->format('d/m/Y H:i') ?? '-' }}</small>
                                                        </div>
                                                    </div>
                                                    @if ($reply->user_id === auth()->id() || $canManageProject)
                                                        <form method="POST"
                                                            action="{{ route('project_management.tasks.comments.destroy', [$holding->holding_code, $project->id, $task->id, $reply->id]) }}"
                                                            onsubmit="return confirm('Hapus balasan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm py-0 px-2">
                                                                <i class="mdi mdi-delete-outline"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                                <div class="small mt-2">{!! nl2br(e($reply->comment)) !!}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($canCommentTask)
                                    <form method="POST"
                                        action="{{ route('project_management.tasks.comments.store', [$holding->holding_code, $project->id, $task->id]) }}"
                                        class="collapse mt-2 ps-3 border-start" id="{{ $replyFormCollapseId }}">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <div class="rounded bg-light p-2">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-lg-3 col-md-4">
                                                    <label class="form-label small fw-semibold">Tipe Balasan</label>
                                                    <select name="comment_type" class="form-select" required>
                                                        <option value="comment">Comment</option>
                                                        <option value="update">Update</option>
                                                        <option value="blocker">Blocker</option>
                                                        <option value="review">Review</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-7 col-md-8">
                                                    <label class="form-label small fw-semibold">Balas Komentar</label>
                                                    <textarea name="comment" rows="2" class="form-control"
                                                        placeholder="Tulis balasan untuk komentar ini..." required></textarea>
                                                </div>
                                                <div class="col-lg-2 col-md-12 d-grid">
                                                    <button type="submit" class="btn btn-outline-primary">Balas</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="border rounded p-3 text-center text-muted">Belum ada komentar task.</div>
                        @endforelse
                    </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        @include('admin.project_management.tasks._work_history_card', ['task' => $task, 'maxHeight' => '620px'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
