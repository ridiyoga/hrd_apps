@php
    $attachments = collect($attachments ?? []);
    $title = $title ?? 'Lampiran Review';
    $emptyText = $emptyText ?? 'Belum ada lampiran review.';
    $formatFileSize = function ($bytes) {
        if (!$bytes) {
            return '-';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        return number_format($bytes / 1024, 2) . ' KB';
    };
    $userName = $userName ?? function ($user) {
        if (!$user) {
            return '-';
        }

        return data_get($user, 'KaryawanNIK.name') ?: $user->username;
    };
    $taskForAttachment = $task ?? null;
@endphp

<div class="border rounded p-3 bg-white">
    <div class="mb-3">
        <h6 class="mb-1">{{ $title }}</h6>
        <small class="text-muted">File/foto pendukung dari proses review task.</small>
    </div>

    @forelse ($attachments as $attachment)
        @php
            $attachmentTask = $taskForAttachment ?: $attachment->task;
            $projectId = data_get($attachmentTask, 'project_id') ?: data_get($project ?? null, 'id');
            $fileUrl = route('project_management.tasks.attachments.show', [
                $holding->holding_code,
                $projectId,
                $attachment->task_id,
                $attachment->id,
            ]);
            $isImage = str_starts_with((string) $attachment->mime_type, 'image/');
        @endphp
        <div class="border rounded p-2 mb-2">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">{{ $attachment->original_name }}</div>
                    <div class="text-muted small">
                        {{ $userName($attachment->uploader) }}
                        - {{ $attachment->created_at?->format('d/m/Y H:i') ?? '-' }}
                        - {{ $formatFileSize($attachment->file_size) }}
                    </div>
                </div>
                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    Buka
                </a>
            </div>

            @if ($isImage)
                <a href="{{ $fileUrl }}" target="_blank" class="d-inline-block mt-2">
                    <img src="{{ $fileUrl }}" alt="{{ $attachment->original_name }}"
                        class="img-fluid rounded border" style="max-height: 120px;">
                </a>
            @endif
        </div>
    @empty
        <div class="text-center text-muted border rounded p-3">
            {{ $emptyText }}
        </div>
    @endforelse
</div>
