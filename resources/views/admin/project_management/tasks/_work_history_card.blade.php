@php
    $historyMaxHeight = $maxHeight ?? '620px';
    $fieldLabels = [
        'title' => 'Judul Task',
        'type' => 'Type',
        'priority' => 'Priority',
        'status' => 'Status',
        'reporter_id' => 'Reporter',
        'primary_assignee_id' => 'Assignee',
        'reviewer_id' => 'Reviewer / QA',
        'estimate_hours' => 'Estimate Jam',
        'actual_hours' => 'Actual Jam',
        'status_updated_at' => 'Tanggal Update',
        'start_date' => 'Start Date',
        'due_date' => 'Due Date',
        'due_revision_date' => 'Due Revisi',
        'review_due_date' => 'Review Due Date',
        'acceptance_criteria' => 'Acceptance Criteria',
        'developer_update_notes' => 'Catatan Hasil Developer',
        'blocker_reason' => 'Blocker Reason',
        'review_notes' => 'Catatan QA / UAT',
        'review_revision_notes' => 'Catatan Perbaikan / Ekspektasi Reviewer',
        'review_attachment' => 'Lampiran Review',
        'task_attachment' => 'Lampiran Task',
        'reopen_reason' => 'Alasan Reopen / Catatan Revisi',
        'presentation_notes' => 'Hasil Presentasi',
        'presentation_attachment' => 'Lampiran Presentasi',
    ];
    $attachmentsByName = $task->attachments->keyBy('original_name');
    $historyUserName = function ($user) {
        if (!$user) {
            return '-';
        }

        $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

        return $employeeName && $employeeName !== $user->username
            ? $user->username . ' - ' . $employeeName
            : $user->username;
    };
    $activityMeta = function ($activity) {
        $description = (string) $activity->description;
        if ($activity->field_name === 'task_attachment') {
            return ['label' => 'Update PM', 'badge' => 'warning', 'border' => 'warning', 'bg' => 'bg-warning-subtle'];
        }

        if ($activity->activity_type === 'presentation_recorded') {
            return ['label' => 'Presentasi', 'badge' => 'success', 'border' => 'success', 'bg' => 'bg-success-subtle'];
        }

        if ($activity->activity_type === 'created') {
            return ['label' => 'Task Dibuat', 'badge' => 'success', 'border' => 'success', 'bg' => 'bg-light'];
        }

        if (
            $activity->activity_type === 'attachment_uploaded'
            || str_contains($description, 'Reviewer / QA')
            || in_array($activity->field_name, ['review_notes', 'review_revision_notes', 'review_attachment'], true)
        ) {
            return ['label' => 'Review / QA', 'badge' => 'info', 'border' => 'info', 'bg' => 'bg-info-subtle'];
        }

        if (
            str_contains($description, 'Developer update progress')
            || in_array($activity->field_name, ['developer_update_notes', 'actual_hours', 'status_updated_at'], true)
        ) {
            return ['label' => 'Hasil Task', 'badge' => 'primary', 'border' => 'primary', 'bg' => 'bg-primary-subtle'];
        }

        if (str_contains($description, 'PM update task') || str_contains($description, 'PM reopen task')) {
            return ['label' => 'Update PM', 'badge' => 'warning', 'border' => 'warning', 'bg' => 'bg-warning-subtle'];
        }

        return ['label' => 'Update Task', 'badge' => 'secondary', 'border' => 'secondary', 'bg' => 'bg-light'];
    };
    $noteFields = [
        'developer_update_notes',
        'blocker_reason',
        'review_notes',
        'review_revision_notes',
        'review_attachment',
        'task_attachment',
        'reopen_reason',
        'presentation_notes',
        'presentation_attachment',
    ];
    $isStatusActivity = function ($activity) {
        $description = strtolower((string) $activity->description);

        return $activity->field_name === 'status'
            || $activity->activity_type === 'status_changed'
            || str_contains($description, 'status berubah');
    };
    $isNoteActivity = function ($activity) use ($noteFields) {
        return $activity->activity_type === 'attachment_uploaded'
            || in_array($activity->field_name, $noteFields, true);
    };
    $sortedActivities = $task->activities->sortByDesc('created_at')->values();
    $historyTabs = [
        'all' => [
            'label' => 'Semua',
            'items' => $sortedActivities,
        ],
        'status' => [
            'label' => 'Status',
            'items' => $sortedActivities->filter($isStatusActivity)->values(),
        ],
        'notes' => [
            'label' => 'Catatan',
            'items' => $sortedActivities->filter($isNoteActivity)->values(),
        ],
        'other' => [
            'label' => 'Lainnya',
            'items' => $sortedActivities
                ->reject(fn($activity) => $isStatusActivity($activity) || $isNoteActivity($activity))
                ->values(),
        ],
    ];
    $historyTabPrefix = 'task-history-' . $task->id . '-' . ($historyId ?? 'main');
@endphp

<div class="border rounded p-3 bg-white mb-3">
    <div class="mb-3">
        <h6 class="mb-1">Riwayat Pekerjaan</h6>
        {{-- <small class="text-muted">Update terbaru di atas: PM, developer, reviewer, dan lampiran.</small> --}}
    </div>

    <ul class="nav nav-tabs flex-nowrap overflow-auto mb-3" role="tablist">
        @foreach ($historyTabs as $tabKey => $tab)
            @php
                $tabId = $historyTabPrefix . '-' . $tabKey;
            @endphp
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link px-2 py-2 {{ $loop->first ? 'active' : '' }}"
                    id="{{ $tabId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $tabId }}"
                    role="tab" aria-controls="{{ $tabId }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $tab['label'] }}
                    <span class="badge bg-secondary ms-1">{{ $tab['items']->count() }}</span>
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content p-0">
        @foreach ($historyTabs as $tabKey => $tab)
            @php
                $tabId = $historyTabPrefix . '-' . $tabKey;
            @endphp
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}"
                role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                <div style="max-height: {{ $historyMaxHeight }}; overflow-y: auto;">
                    @forelse ($tab['items'] as $activity)
                        @php
                            $meta = $activityMeta($activity);
                            $fieldLabel = $fieldLabels[$activity->field_name] ?? ucwords(str_replace('_', ' ', $activity->activity_type));
                            $attachment = $activity->field_name === 'review_attachment'
                                ? ($task->attachments->firstWhere('id', $activity->new_value) ?: $attachmentsByName->get($activity->new_value))
                                : (in_array($activity->field_name, ['task_attachment', 'presentation_attachment'], true)
                                    ? $task->attachments->firstWhere('id', $activity->new_value)
                                    : null);
                            $showDescription = !($isNoteActivity($activity) && filled($activity->new_value));
                        @endphp
                        <div class="border-start border-4 border-{{ $meta['border'] }} ps-3 pb-3 mb-3">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <span class="badge bg-{{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                <span class="badge bg-secondary">{{ $fieldLabel }}</span>
                                <small class="text-muted">{{ $activity->created_at?->format('d/m/Y H:i') ?? '-' }}</small>
                            </div>
                            <div class="fw-semibold small mb-1">{{ $historyUserName($activity->user) }}</div>
                            @if ($showDescription)
                                <div class="small">{{ $activity->description }}</div>
                            @endif
                            @if (filled($activity->new_value))
                                <div class="small mt-2 p-2 {{ $meta['bg'] }} rounded">
                                    {!! nl2br(e($attachment ? $attachment->original_name : $activity->new_value)) !!}
                                </div>
                            @endif
                            @if ($attachment)
                                <div class="mt-2">
                                    <a href="{{ route('project_management.tasks.attachments.show', [
                                        $holding->holding_code,
                                        $task->project_id,
                                        $task->id,
                                        $attachment->id,
                                    ]) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        Buka Lampiran
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted border rounded p-3">
                            Belum ada riwayat pekerjaan di tab ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
