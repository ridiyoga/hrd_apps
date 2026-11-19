<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Menu;
use App\Models\ProjectManagementMeeting;
use App\Models\ProjectManagementMeetingActionItem;
use App\Models\ProjectManagementMeetingAttachment;
use App\Models\ProjectManagementMeetingParticipant;
use App\Models\ProjectManagementMeetingTask;
use App\Models\ProjectManagementProject;
use App\Models\ProjectManagementTask;
use App\Models\ProjectManagementTaskActivity;
use App\Models\RoleUsers;
use App\Models\User;
use App\Policies\ProjectManagementProjectPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectManagementMeetingController extends Controller
{
    public function index(string $holding, string $project)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'viewMeetings');
        $meetings = $project->meetings()
            ->with(['milestone', 'creator.KaryawanNIK'])
            ->withCount(['participants', 'tasks', 'actionItems'])
            ->orderByDesc('meeting_at')
            ->paginate(20);
        $menus = $this->sidebarMenus();

        return view('admin.project_management.meetings.index', compact('holding', 'project', 'meetings', 'menus'));
    }

    public function create(string $holding, string $project)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'createMeeting');
        $project->load(['milestones', 'tasks.primaryAssignee', 'tasks.communicatedMeetings', 'members.user']);
        $users = $this->participantUsers($project);
        $menus = $this->sidebarMenus();

        return view('admin.project_management.meetings.create', compact('holding', 'project', 'users', 'menus'));
    }

    public function store(Request $request, string $holding, string $project)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'createMeeting');
        $data = $this->validateMeetingHeader($request, $project);

        $meeting = DB::transaction(function () use ($request, $project, $data) {
            ProjectManagementProject::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $meeting = ProjectManagementMeeting::create([
                ...collect($data)->except(['participant_ids', 'external_participants', 'task_ids', 'attachments'])->all(),
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'meeting_code' => $this->nextMeetingCode($project),
                'status' => 'draft',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $this->syncParticipants($meeting, $data);
            $this->storeAttachments($meeting, $request->file('attachments', []));
            return $meeting;
        });

        return redirect()->route('project_management.meetings.show', [$holding->holding_code, $project->id, $meeting->id])
            ->with('success', 'Minutes of Meeting berhasil dibuat.');
    }

    public function show(string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'viewMeetings');
        $meeting = $project->meetings()->with([
            'milestone', 'creator.KaryawanNIK', 'participants.user.KaryawanNIK',
            'meetingTasks.task.primaryAssignee.KaryawanNIK', 'meetingTasks.task.reviewer.KaryawanNIK',
            'actionItems.pic.KaryawanNIK', 'actionItems.linkedTask', 'actionItems.generatedTask', 'attachments',
        ])->findOrFail($meeting);
        $users = $this->participantUsers($project->loadMissing(['members', 'tasks.communicatedMeetings']));
        $availableTasks = $project->tasks
            ->filter(fn($task) => $task->status === 'done' && !$task->isCommunicated()
                && !ProjectManagementMeetingTask::where('task_id', $task->id)
                    ->where('decision', 'accepted')
                    ->whereHas('meeting', fn($query) => $query->where('status', 'draft'))
                    ->exists()
                && !$meeting->meetingTasks->contains('task_id', $task->id))
            ->sortBy('due_date')
            ->values();
        $canEdit = $this->canEdit($project, $meeting);
        $canManageTasks = $project->project_manager_id === Auth::id();
        $menus = $this->sidebarMenus();

        return view('admin.project_management.meetings.show', compact('holding', 'project', 'meeting', 'users', 'availableTasks', 'canEdit', 'canManageTasks', 'menus'));
    }

    public function pdf(Request $request, string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'viewMeetings');
        $meeting = $project->meetings()->with([
            'milestone', 'creator.KaryawanNIK', 'participants.user.KaryawanNIK',
            'meetingTasks.task.primaryAssignee.KaryawanNIK', 'meetingTasks.task.reviewer.KaryawanNIK',
            'actionItems.pic.KaryawanNIK', 'actionItems.linkedTask', 'actionItems.generatedTask', 'attachments',
        ])->findOrFail($meeting);

        $pdf = Pdf::loadView('admin.project_management.meetings.pdf', compact('holding', 'project', 'meeting'))
            ->setPaper('a4', 'portrait');
        $fileName = $meeting->meeting_code . '-minutes-of-meeting.pdf';

        return $request->boolean('preview') ? $pdf->stream($fileName) : $pdf->download($fileName);
    }

    public function edit(string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->with(['participants', 'tasks', 'meetingTasks'])->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'Hanya pembuat MoM atau Project Manager yang dapat mengubah MoM.');
        $project->load(['milestones', 'members']);
        $users = $this->participantUsers($project);
        $menus = $this->sidebarMenus();

        return view('admin.project_management.meetings.edit', compact('holding', 'project', 'meeting', 'users', 'menus'));
    }

    public function update(Request $request, string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'Hanya pembuat MoM atau Project Manager yang dapat mengubah MoM.');
        $data = $this->validateMeetingHeader($request, $project);

        DB::transaction(function () use ($request, $meeting, $data) {
            $meeting->update([
                ...collect($data)->except(['participant_ids', 'external_participants', 'task_ids', 'attachments'])->all(),
                'updated_by' => Auth::id(),
            ]);
            $this->syncParticipants($meeting, $data);
            $this->storeAttachments($meeting, $request->file('attachments', []));
        });

        return redirect()->route('project_management.meetings.show', [$holding->holding_code, $project->id, $meeting->id])
            ->with('success', 'Minutes of Meeting berhasil diperbarui.');
    }

    public function destroy(string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'Hanya pembuat MoM atau Project Manager yang dapat menghapus MoM.');
        $meeting->delete();

        return redirect()->route('project_management.meetings.index', [$holding->holding_code, $project->id])
            ->with('success', 'Minutes of Meeting berhasil dihapus.');
    }

    public function storeMeetingTask(Request $request, string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'MoM ini tidak dapat diubah.');
        $this->ensureDraft($meeting);
        $data = $request->validate([
            'task_id' => ['required', 'uuid'],
            'discussion_notes' => ['required', 'string', 'max:10000'],
            'decision' => ['required', Rule::in(['accepted', 'return_developer'])],
            'return_expectation' => ['nullable', 'required_if:decision,return_developer', 'string', 'max:10000'],
            'revision_due_date' => ['nullable', 'required_if:decision,return_developer', 'date'],
        ], [
            'discussion_notes.required' => 'Catatan pembahasan task wajib diisi.',
            'return_expectation.required_if' => 'Ekspektasi perbaikan wajib diisi jika task dikembalikan.',
            'revision_due_date.required_if' => 'Due revisi wajib diisi jika task dikembalikan.',
        ]);
        DB::transaction(function () use ($project, $meeting, $data) {
            $task = $project->tasks()->lockForUpdate()->findOrFail($data['task_id']);
            abort_unless($task->status === 'done', 422, 'Hanya task berstatus Done yang dapat dibahas.');
            abort_if($task->communicatedMeetings()->exists(), 422, 'Task sudah diterima pada MoM lain.');
            abort_if(ProjectManagementMeetingTask::where('task_id', $task->id)
                ->where('decision', 'accepted')
                ->whereHas('meeting', fn($query) => $query->where('status', 'draft'))
                ->exists(), 422, 'Task sudah diterima pada draft MoM lain.');
            abort_if($meeting->meetingTasks()->where('task_id', $task->id)->exists(), 422, 'Task sudah tercatat pada MoM ini.');

            ProjectManagementMeetingTask::create([
                'id' => (string) Str::uuid(), 'meeting_id' => $meeting->id, 'task_id' => $task->id,
                'discussion_notes' => $data['discussion_notes'], 'decision' => $data['decision'],
                'return_expectation' => $data['decision'] === 'return_developer' ? $data['return_expectation'] : null,
                'revision_due_date' => $data['decision'] === 'return_developer' ? $data['revision_due_date'] : null,
                'decided_by' => Auth::id(), 'decided_at' => now(),
            ]);

            if ($data['decision'] === 'return_developer') {
                $task->update([
                    'status' => 'in_progress', 'status_updated_at' => now(),
                    'due_revision_date' => $data['revision_due_date'],
                    'review_revision_notes' => $data['return_expectation'],
                    'reviewed_at' => now(), 'reviewed_by' => Auth::id(), 'updated_by' => Auth::id(),
                ]);
                ProjectManagementTaskActivity::create([
                    'id' => (string) Str::uuid(), 'task_id' => $task->id, 'user_id' => Auth::id(),
                    'activity_type' => 'mom_returned', 'field_name' => 'status',
                    'old_value' => 'done', 'new_value' => 'in_progress',
                    'description' => 'Task dikembalikan ke developer melalui ' . $meeting->meeting_code
                        . '. Ekspektasi: ' . $data['return_expectation']
                        . '. Due revisi: ' . Carbon::parse($data['revision_due_date'])->format('d/m/Y') . '.',
                ]);
            }
            $this->recalculateProjectProgress($project);
        });

        return back()->with('success', 'Pembahasan task berhasil disimpan.');
    }

    public function destroyMeetingTask(string $holding, string $project, string $meeting, string $meetingTask)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'MoM ini tidak dapat diubah.');
        $this->ensureDraft($meeting);
        $meeting->meetingTasks()->findOrFail($meetingTask)->delete();

        return back()->with('success', 'Pembahasan task dihapus dari MoM. Status task tidak diubah otomatis.');
    }

    public function finalize(Request $request, string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        abort_unless($project->project_manager_id === Auth::id(), 403, 'Hanya Project Manager yang dapat memfinalisasi MoM.');
        $meeting = $project->meetings()->findOrFail($meeting);
        $this->ensureDraft($meeting);
        $data = $request->validate(['summary' => ['required', 'string', 'min:10', 'max:20000']], [
            'summary.required' => 'Summary MoM wajib diisi sebelum finalisasi.',
        ]);
        $meeting->update([
            'summary' => $data['summary'], 'status' => 'final',
            'finalized_at' => now(), 'finalized_by' => Auth::id(), 'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'MoM berhasil difinalisasi dan sekarang bersifat read-only.');
    }

    public function cancel(string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        abort_unless($project->project_manager_id === Auth::id(), 403, 'Hanya Project Manager yang dapat membatalkan MoM.');
        $meeting = $project->meetings()->findOrFail($meeting);
        $this->ensureDraft($meeting);
        $meeting->update(['status' => 'cancelled', 'updated_by' => Auth::id()]);

        return back()->with('success', 'MoM dibatalkan tanpa menghapus histori.');
    }

    public function storeActionItem(Request $request, string $holding, string $project, string $meeting)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        abort_unless($this->canEdit($project, $meeting), 403, 'Hanya pembuat MoM atau Project Manager yang dapat menambah task baru.');
        $this->ensureDraft($meeting);
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'description' => ['nullable', 'required_without:linked_task_id', 'string', 'max:5000'],
            'pic_id' => ['required', 'string', 'exists:users,id'],
            'estimate_value' => ['required', 'numeric', 'min:0.125', 'max:10000'],
            'estimate_unit' => ['required', Rule::in(['mandays', 'manhours'])],
            'linked_task_id' => ['nullable', 'uuid'],
        ]);
        if (!$this->participantUsers($project->loadMissing(['members', 'tasks']))->contains('id', $data['pic_id'])) {
            throw ValidationException::withMessages(['pic_id' => 'PIC harus merupakan participant project.']);
        }
        if (!empty($data['linked_task_id']) && !$project->tasks()->whereKey($data['linked_task_id'])->exists()) {
            throw ValidationException::withMessages(['linked_task_id' => 'Task terkait tidak berasal dari project ini.']);
        }
        $data['estimate_hours'] = round((float) $data['estimate_value'] * ($data['estimate_unit'] === 'mandays' ? 8 : 1), 2);
        $data['due_date'] = $this->calculateTaskDueDate($meeting->meeting_at, $data['estimate_hours'])->toDateString();
        unset($data['estimate_value'], $data['estimate_unit']);
        ProjectManagementMeetingActionItem::create($data + [
            'id' => (string) Str::uuid(), 'meeting_id' => $meeting->id, 'status' => 'open',
            'created_by' => Auth::id(), 'updated_by' => Auth::id(),
        ]);
        return back()->with('success', 'Task baru berhasil ditambahkan.');
    }

    public function updateActionItem(Request $request, string $holding, string $project, string $meeting, string $actionItem)
    {
        [$holding, $project] = $this->context($holding, $project);
        $meeting = $project->meetings()->findOrFail($meeting);
        $item = $meeting->actionItems()->findOrFail($actionItem);
        $this->ensureDraft($meeting);
        abort_unless($this->canEdit($project, $meeting) || $item->pic_id === Auth::id(), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['open', 'in_progress', 'done', 'cancelled'])]]);
        $item->update($data + ['updated_by' => Auth::id()]);
        return back()->with('success', 'Status task baru berhasil diperbarui.');
    }

    public function generateTask(string $holding, string $project, string $meeting, string $actionItem)
    {
        [$holding, $project] = $this->context($holding, $project);
        abort_unless($project->project_manager_id === Auth::id(), 403, 'Hanya Project Manager yang dapat menerbitkan task.');
        $meeting = $project->meetings()->findOrFail($meeting);
        $this->ensureDraft($meeting);
        $item = $meeting->actionItems()->findOrFail($actionItem);
        abort_if($item->generated_task_id, 422, 'Task baru ini sudah diterbitkan.');
        abort_unless($meeting->milestone_id, 422, 'Pilih milestone MoM sebelum menerbitkan task baru.');

        DB::transaction(function () use ($project, $meeting, $item) {
            ProjectManagementProject::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $task = ProjectManagementTask::create([
                'id' => (string) Str::uuid(), 'project_id' => $project->id,
                'milestone_id' => $meeting->milestone_id, 'task_code' => $this->nextTaskCode($project),
                'title' => $item->title, 'type' => 'task', 'priority' => 'medium', 'status' => 'todo',
                'reporter_id' => Auth::id(), 'primary_assignee_id' => $item->pic_id,
                'reviewer_id' => $project->project_manager_id, 'estimate_hours' => $item->estimate_hours,
                'actual_hours' => 0, 'start_date' => $this->nextWorkday(now())->toDateString(),
                'due_date' => $item->due_date, 'acceptance_criteria' => $item->description,
                'sort_order' => $project->tasks()->max('sort_order') + 1,
                'created_by' => Auth::id(), 'updated_by' => Auth::id(),
            ]);
            $item->update(['generated_task_id' => $task->id, 'linked_task_id' => $task->id, 'updated_by' => Auth::id()]);
            $this->recalculateProjectProgress($project);
        });
        return back()->with('success', 'Task baru berhasil diterbitkan dengan status Todo.');
    }

    public function attachment(string $holding, string $project, string $meeting, string $attachment)
    {
        [$holding, $project] = $this->context($holding, $project);
        $this->authorizeParticipant($project, 'viewMeetings');
        $meeting = $project->meetings()->findOrFail($meeting);
        $attachment = $meeting->attachments()->findOrFail($attachment);
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);
        return Storage::disk('public')->response($attachment->file_path, $attachment->original_name);
    }

    private function context(string $holdingCode, string $projectId): array
    {
        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();
        $project = ProjectManagementProject::where('holding_id', $holding->id)->findOrFail($projectId);
        return [$holding, $project];
    }

    private function authorizeParticipant(ProjectManagementProject $project, string $ability): void
    {
        $policy = app(ProjectManagementProjectPolicy::class);
        abort_unless($policy->{$ability}(Auth::user(), $project), 403, 'Anda bukan participant project ini.');
    }

    private function canEdit(ProjectManagementProject $project, ProjectManagementMeeting $meeting): bool
    {
        return $meeting->status === 'draft'
            && ($project->project_manager_id === Auth::id() || $meeting->created_by === Auth::id());
    }

    private function ensureDraft(ProjectManagementMeeting $meeting): void
    {
        abort_unless($meeting->status === 'draft', 422, 'Hanya MoM berstatus Draft yang dapat diubah.');
    }

    private function validateMeetingHeader(Request $request, ProjectManagementProject $project): array
    {
        $data = $request->validate([
            'meeting_type' => ['required', Rule::in(['kickoff', 'progress', 'review', 'uat', 'escalation', 'closing', 'other'])],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'meeting_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'milestone_id' => ['nullable', 'uuid'],
            'agenda' => ['required', 'string', 'max:10000'],
            'participant_ids' => ['nullable', 'array'],
            'participant_ids.*' => ['string', 'exists:users,id'],
            'external_participants' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240'],
        ]);
        $this->rejectSunday($data['meeting_at'], 'meeting_at');
        if (!empty($data['milestone_id']) && !$project->milestones()->whereKey($data['milestone_id'])->exists()) {
            throw ValidationException::withMessages(['milestone_id' => 'Milestone tidak berasal dari project ini.']);
        }

        return $data;
    }

    private function syncParticipants(ProjectManagementMeeting $meeting, array $data): void
    {
        $meeting->participants()->delete();
        foreach (User::with('KaryawanNIK')->whereIn('id', $data['participant_ids'] ?? [])->get() as $user) {
            ProjectManagementMeetingParticipant::create([
                'id' => (string) Str::uuid(), 'meeting_id' => $meeting->id, 'user_id' => $user->id,
                'participant_name' => data_get($user, 'KaryawanNIK.name') ?: $user->username,
                'participant_role' => 'Participant Project', 'attendance_status' => 'present',
            ]);
        }
        foreach (preg_split('/\r\n|\r|\n/', $data['external_participants'] ?? '') as $name) {
            if (blank(trim($name))) continue;
            ProjectManagementMeetingParticipant::create([
                'id' => (string) Str::uuid(), 'meeting_id' => $meeting->id,
                'participant_name' => trim($name), 'participant_role' => 'External / Stakeholder', 'attendance_status' => 'present',
            ]);
        }
    }

    private function storeAttachments(ProjectManagementMeeting $meeting, array $files): void
    {
        foreach ($files as $file) {
            if (!$file?->isValid()) continue;
            ProjectManagementMeetingAttachment::create([
                'id' => (string) Str::uuid(), 'meeting_id' => $meeting->id, 'uploaded_by' => Auth::id(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $file->store('project_management/meetings/' . $meeting->id, 'public'),
                'mime_type' => $file->getClientMimeType(), 'file_size' => $file->getSize(),
            ]);
        }
    }

    private function participantUsers(ProjectManagementProject $project)
    {
        $ids = collect([$project->requester_id, $project->business_owner_id, $project->project_manager_id, $project->technical_lead_id, $project->sponsor_id])
            ->merge($project->members()->where('is_active', true)->pluck('user_id'))
            ->merge($project->tasks()->pluck('primary_assignee_id'))->merge($project->tasks()->pluck('reviewer_id'))->filter()->unique();
        return User::with('KaryawanNIK')->whereIn('id', $ids)->orderBy('username')->get();
    }

    private function nextMeetingCode(ProjectManagementProject $project): string
    {
        return 'MoM-' . $project->project_code . '-' . str_pad((string) ($project->meetings()->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
    }

    private function nextTaskCode(ProjectManagementProject $project): string
    {
        return $project->project_code . '-T' . str_pad((string) ($project->tasks()->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
    }

    private function rejectSunday(string $date, string $field): void
    {
        if (Carbon::parse($date)->isSunday()) throw ValidationException::withMessages([$field => 'Hari Minggu tidak dapat dipilih.']);
    }

    private function nextWorkday(Carbon $date): Carbon
    {
        $date = $date->copy()->startOfDay();
        while ($date->isSunday()) $date->addDay();
        return $date;
    }

    private function calculateTaskDueDate(mixed $meetingAt, float $estimateHours): Carbon
    {
        $dueDate = Carbon::parse($meetingAt)->startOfDay();
        $workdays = max(1, (int) ceil($estimateHours / 8));

        for ($day = 1; $day < $workdays;) {
            $dueDate->addDay();
            if (!$dueDate->isSunday()) {
                $day++;
            }
        }

        return $dueDate;
    }

    private function sidebarMenus()
    {
        $roleIds = RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray();
        return Menu::whereIn('id', function ($query) use ($roleIds) {
            $query->select('menu_id')->from('role_menus')->whereIn('role_id', $roleIds);
        })->whereNull('parent_id')->where('kategori', 'web')->with('children')->orderBy('sort_order')->get();
    }

    private function recalculateProjectProgress(ProjectManagementProject $project): void
    {
        $tasks = $project->tasks()->where('status', '!=', 'cancelled')->get();
        $totalEstimate = (float) $tasks->sum('estimate_hours');
        $doneTasks = $tasks->where('status', 'done');
        $progress = $tasks->isEmpty() ? 0 : ($totalEstimate > 0
            ? ((float) $doneTasks->sum('estimate_hours') / $totalEstimate) * 100
            : ($doneTasks->count() / $tasks->count()) * 100);

        $project->update([
            'progress_percent' => round($progress, 2),
            'actual_hours' => $tasks->sum('actual_hours'),
            'updated_by' => Auth::id(),
        ]);
    }
}
