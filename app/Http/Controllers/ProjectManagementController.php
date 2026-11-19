<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\ProjectManagementProject;
use App\Models\ProjectManagementMember;
use App\Models\ProjectManagementMilestone;
use App\Models\ProjectManagementTask;
use App\Models\ProjectManagementTaskActivity;
use App\Models\ProjectManagementTaskAttachment;
use App\Models\ProjectManagementTaskComment;
use App\Models\RoleUsers;
use App\Models\User;
use App\Policies\ProjectManagementProjectPolicy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectManagementController extends Controller
{
    private const WORK_HOURS_PER_DAY = 8;
    private const WORK_DAYS_PER_MONTH = 26;

    public function index(Request $request, string $holding)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $userId = Auth::id();

        $projects = ProjectManagementProject::with(['projectManager.KaryawanNIK', 'requester.KaryawanNIK'])
            ->where('holding_id', $holding->id)
            ->visibleTo($userId)
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = strtolower($request->q);

                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(project_code) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(project_name) LIKE ?', ["%{$keyword}%"]);
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->when($request->filled('priority'), fn($query) => $query->where('priority', $request->priority))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.project_management.index', compact('holding', 'menus', 'projects'));
    }

    public function report(Request $request, ?string $holding = null)
    {
        $holding = $holding ? $this->holding($holding) : Holding::orderBy('holding_code')->firstOrFail();
        $menus = $this->sidebarMenus();
        $statusOptions = $this->taskStatusOptions();
        $filters = $request->validate([
            'project_id' => ['nullable', 'uuid'],
            'assignee_id' => ['nullable', 'string'],
            'reviewer_id' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(array_keys($statusOptions))],
            'presentation' => ['nullable', Rule::in(['pending', 'presented'])],
            'due_from' => ['nullable', 'date_format:Y-m-d'],
            'due_to' => array_merge(['nullable', 'date_format:Y-m-d'], $request->filled('due_from') ? ['after_or_equal:due_from'] : []),
            'per_page' => ['nullable', Rule::in([25, 50, 100])],
            'export' => ['nullable', Rule::in(['csv', 'pdf'])],
        ]);
        $userId = Auth::id();
        $projects = ProjectManagementProject::with('holding')
            ->visibleTo($userId)
            ->orderBy('project_name')
            ->get();
        $query = ProjectManagementTask::with(['project.holding', 'primaryAssignee', 'reviewer', 'communicatedMeetings'])
            ->whereIn('project_id', $projects->pluck('id'));
        foreach (['project_id' => 'project_id', 'assignee_id' => 'primary_assignee_id', 'reviewer_id' => 'reviewer_id', 'status' => 'status'] as $filter => $column) {
            if (!empty($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }
        if (!empty($filters['presentation'])) {
            $query->where('status', 'done');
            $filters['presentation'] === 'presented'
                ? $query->whereHas('communicatedMeetings')
                : $query->whereDoesntHave('communicatedMeetings');
        }
        $dueSql = 'DATE(CASE WHEN status = \'review\' THEN COALESCE(review_due_date, due_revision_date, due_date) ELSE COALESCE(due_revision_date, due_date) END)';
        foreach (['due_from' => '>=', 'due_to' => '<='] as $filter => $operator) {
            if (!empty($filters[$filter])) {
                $query->whereRaw($dueSql . ' ' . $operator . ' ?', [$filters[$filter]]);
            }
        }
        $reportUsers = ProjectManagementTask::whereIn('project_id', $projects->pluck('id'))->get(['primary_assignee_id', 'reviewer_id']);
        $users = User::whereIn('id', $reportUsers->pluck('primary_assignee_id')->merge($reportUsers->pluck('reviewer_id'))->filter()->unique())->orderBy('username')->get();
        $totals = (clone $query)->selectRaw('COUNT(*) as total, COALESCE(SUM(estimate_hours), 0) as estimate, COALESCE(SUM(actual_hours), 0) as actual')->first();
        $statusCounts = (clone $query)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $columns = ['Project', 'Progress Project (%)', 'Kode Task', 'Task', 'Assignee', 'Reviewer', 'Status', 'Estimasi (MD)', 'Aktual (MD)', 'Start Date', 'Due Task', 'Due Revisi', 'Review Due', 'Due Aktif', 'Penyampaian', 'Tanggal MoM Terakhir'];
        $row = function ($task) use ($statusOptions) {
            return [$task->project->project_name, $task->project->progress_percent, $task->task_code, $task->title,
                $task->primaryAssignee?->username ?? '-', $task->reviewer?->username ?? '-', $statusOptions[$task->status] ?? $task->status,
                number_format((float) $task->estimate_hours / self::WORK_HOURS_PER_DAY, 2, '.', ''),
                number_format((float) $task->actual_hours / self::WORK_HOURS_PER_DAY, 2, '.', ''),
                $task->start_date?->format('d/m/Y') ?? '-', $task->due_date?->format('d/m/Y') ?? '-',
                $task->due_revision_date?->format('d/m/Y') ?? '-', $task->review_due_date?->format('d/m/Y') ?? '-',
                $this->effectiveTaskDueDate($task)?->format('d/m/Y') ?? '-',
                $task->status !== 'done' ? '-' : ($task->isCommunicated() ? 'Sudah Disampaikan' : 'Belum Disampaikan'),
                $task->communicatedAt()?->format('d/m/Y H:i') ?? '-'];
        };
        if (($filters['export'] ?? null) === 'pdf') {
            if (empty($filters['project_id'])) {
                throw ValidationException::withMessages([
                    'project_id' => 'Pilih satu project terlebih dahulu untuk membuat dokumen laporan penyelesaian project.',
                ]);
            }
            $reportTasks = (clone $query)->with(['activities.user', 'attachments'])->orderBy('project_id')->orderBy('sort_order')->orderBy('id')->get();
            $reportProjects = $projects->filter(fn($project) => empty($filters['project_id']) || $project->id === $filters['project_id']);
            if (collect($filters)->except(['project_id', 'per_page', 'export'])->filter(fn($value) => filled($value))->isNotEmpty()) {
                $reportProjects = $reportProjects->whereIn('id', $reportTasks->pluck('project_id'));
            }
            $reportProjects->load([
                'projectManager', 'requester', 'businessOwner', 'technicalLead', 'sponsor', 'milestones.owner',
                'meetings' => fn($query) => $query->where('status', 'final')->with(['milestone', 'meetingTasks.task', 'participants'])->orderBy('meeting_at'),
            ]);
            $effectiveDueDate = fn($task) => $this->effectiveTaskDueDate($task);
            $generatedAt = now();
            $generatedBy = Auth::user()->username;
            $pdf = Pdf::loadView('admin.project_management.report_pdf', compact(
                'reportTasks', 'reportProjects', 'totals', 'statusCounts', 'statusOptions', 'filters',
                'effectiveDueDate', 'generatedAt', 'generatedBy'
            ))->setPaper('a4', 'portrait');
            $pdf->render();
            $pdf->getDomPDF()->getCanvas()->page_text(480, 815, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 8, [0.4, 0.45, 0.48]);
            return $pdf->download('report-project-it-' . $generatedAt->format('Ymd-His') . '.pdf');
        }
        if (($filters['export'] ?? null) === 'csv') {
            return response()->streamDownload(function () use ($query, $columns, $row) {
                $output = fopen('php://output', 'w');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, $columns);
                foreach ($query->orderBy('id')->lazy(200) as $task) {
                    fputcsv($output, array_map(function ($value) {
                        $value = (string) $value;
                        return preg_match('/^[\s]*[=+@-]/u', $value) ? "'" . $value : $value;
                    }, $row($task)));
                }
                fclose($output);
            }, 'project-management-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
        $tasks = $query->orderByDesc('updated_at')->orderBy('id')->paginate((int) ($filters['per_page'] ?? 25))->withQueryString();
        return view('admin.project_management.report', compact('holding', 'menus', 'projects', 'users', 'tasks', 'totals', 'statusCounts', 'statusOptions', 'columns', 'row'));
    }

    public function workloadDashboard(Request $request, ?string $holding = null)
    {
        $holding = $holding ? $this->holding($holding) : Holding::orderBy('holding_code')->first();
        $menus = $this->sidebarMenus();
        $statusOptions = $this->taskStatusOptions();
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $users = $this->users();
        $activeStatuses = ['todo', 'in_progress', 'blocked', 'review'];
        $today = now()->startOfDay();
        $effectiveDueDate = fn(ProjectManagementTask $task) => $this->effectiveTaskDueDate($task);
        $estimateMandays = fn(ProjectManagementTask $task) => $this->taskEstimateMandays($task);
        $effectiveDueDateSql = "DATE(CASE WHEN status = 'review' THEN COALESCE(review_due_date, due_revision_date, due_date) ELSE COALESCE(due_revision_date, due_date) END)";
        $riskPerPage = in_array((int) $request->input('per_page', 25), [25, 50, 100], true)
            ? (int) $request->input('per_page', 25)
            : 25;

        $tasks = ProjectManagementTask::with([
            'project.holding',
            'primaryAssignee.KaryawanNIK',
            'reviewer.KaryawanNIK',
            'communicatedMeetings',
        ])
            ->whereHas('project', fn($projectQuery) => $projectQuery->visibleTo(Auth::id()))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->when(!$request->filled('status'), fn($query) => $query->where('status', '!=', 'cancelled'))
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->whereHas('primaryAssignee.KaryawanNIK', function ($employeeQuery) use ($request) {
                    $employeeQuery->where('dept_id', $request->department_id);
                });
            })
            ->when($request->filled('assignee_id'), fn($query) => $query->where('primary_assignee_id', $request->assignee_id))
            ->when($request->filled('reviewer_id'), fn($query) => $query->where('reviewer_id', $request->reviewer_id))
            ->when($request->filled('due_from'), fn($query) => $query->whereRaw($effectiveDueDateSql . ' >= ?', [$request->due_from]))
            ->when($request->filled('due_to'), fn($query) => $query->whereRaw($effectiveDueDateSql . ' <= ?', [$request->due_to]))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = strtolower($request->q);

                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(task_code) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereHas('project', function ($projectQuery) use ($keyword) {
                            $projectQuery->whereRaw('LOWER(project_code) LIKE ?', ["%{$keyword}%"])
                                ->orWhereRaw('LOWER(project_name) LIKE ?', ["%{$keyword}%"]);
                        })
                        ->orWhereHas('primaryAssignee', function ($userQuery) use ($keyword) {
                            $userQuery->whereRaw('LOWER(username) LIKE ?', ["%{$keyword}%"])
                                ->orWhereHas('KaryawanNIK', function ($employeeQuery) use ($keyword) {
                                    $employeeQuery->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                                });
                        });
                });
            })
            ->get();
        $tasks->each(function (ProjectManagementTask $task) use ($estimateMandays) {
            $task->setAttribute('estimate_mandays', $estimateMandays($task));
        });

        $activeTasks = $tasks->whereIn('status', $activeStatuses);
        $summary = [
            'projects' => $tasks->pluck('project_id')->unique()->count(),
            'tasks' => $tasks->count(),
            'active_tasks' => $activeTasks->count(),
            'active_estimate' => round($activeTasks->sum(fn($task) => $estimateMandays($task)), 2),
            'overdue' => $activeTasks
                ->filter(fn($task) => ($effectiveDueDate($task))?->lt($today))
                ->count(),
            'blocked' => $tasks->where('status', 'blocked')->count(),
            'review' => $tasks->where('status', 'review')->count(),
            'review_overdue' => $tasks
                ->where('status', 'review')
                ->filter(fn($task) => ($task->review_due_date ?: $effectiveDueDate($task))?->lt($today))
                ->count(),
            'unassigned' => $tasks->whereNull('primary_assignee_id')->count(),
        ];

        $statusCounts = collect(array_keys($statusOptions))
            ->mapWithKeys(fn($status) => [$status => $tasks->where('status', $status)->count()]);

        $developerWorkloads = $tasks
            ->groupBy(fn($task) => $task->primary_assignee_id ?: 'unassigned')
            ->map(function ($userTasks, $assigneeId) use ($today, $effectiveDueDate, $estimateMandays) {
                $activeUserTasks = $userTasks->whereIn('status', ['todo', 'in_progress', 'blocked', 'review']);
                $overdueCount = $activeUserTasks
                    ->filter(fn($task) => ($effectiveDueDate($task))?->lt($today))
                    ->count();
                $firstTask = $userTasks->first();
                $assignee = $firstTask?->primaryAssignee;

                return [
                    'assignee_id' => $assigneeId === 'unassigned' ? null : $assigneeId,
                    'assignee' => $assignee,
                    'open_count' => $activeUserTasks->count(),
                    'todo_count' => $userTasks->where('status', 'todo')->count(),
                    'in_progress_count' => $userTasks->where('status', 'in_progress')->count(),
                    'review_count' => $userTasks->where('status', 'review')->count(),
                    'blocked_count' => $userTasks->where('status', 'blocked')->count(),
                    'done_count' => $userTasks->where('status', 'done')->count(),
                    'overdue_count' => $overdueCount,
                    'active_estimate' => round($activeUserTasks->sum(fn($task) => $estimateMandays($task)), 2),
                    'latest_due_date' => $activeUserTasks
                        ->filter(fn($task) => filled($effectiveDueDate($task)))
                        ->sortBy(fn($task) => $effectiveDueDate($task))
                        ->map(fn($task) => $effectiveDueDate($task))
                        ->first(),
                ];
            })
            ->sortByDesc('active_estimate')
            ->values();

        $reviewerQueues = $activeTasks
            ->groupBy(fn($task) => $task->reviewer_id ?: 'unassigned')
            ->map(function ($reviewTasks, $reviewerId) use ($today, $effectiveDueDate) {
                $reviewerQueueTasks = $reviewTasks->where('status', 'review');
                $developerQueueTasks = $reviewTasks->whereIn('status', ['todo', 'in_progress', 'blocked']);
                $reviewer = $reviewTasks->first()?->reviewer;

                return [
                    'reviewer_id' => $reviewerId === 'unassigned' ? null : $reviewerId,
                    'reviewer' => $reviewer,
                    'task_count' => $reviewTasks->count(),
                    'developer_count' => $developerQueueTasks->count(),
                    'review_count' => $reviewerQueueTasks->count(),
                    'overdue_count' => $reviewTasks
                        ->filter(fn($task) => ($effectiveDueDate($task))?->lt($today))
                        ->count(),
                    'review_overdue_count' => $reviewerQueueTasks
                        ->filter(fn($task) => ($task->review_due_date ?: $effectiveDueDate($task))?->lt($today))
                        ->count(),
                    'oldest_submit' => $reviewerQueueTasks
                        ->filter(fn($task) => filled($task->status_updated_at))
                        ->sortBy('status_updated_at')
                        ->first()?->status_updated_at,
                    'nearest_review_due_date' => $reviewerQueueTasks
                        ->filter(fn($task) => filled($task->review_due_date))
                        ->sortBy('review_due_date')
                        ->first()?->review_due_date,
                ];
            })
            ->sortByDesc('task_count')
            ->values();

        $usersById = $users->keyBy('id');
        $combinedWorkloads = $activeTasks
            ->pluck('primary_assignee_id')
            ->merge($activeTasks->pluck('reviewer_id'))
            ->filter()
            ->unique()
            ->map(function ($userId) use ($activeTasks, $usersById, $estimateMandays) {
                $user = $usersById[$userId] ?? null;
                $developmentTasks = $activeTasks->where('primary_assignee_id', $userId);
                $reviewTasks = $activeTasks->where('reviewer_id', $userId);
                $reviewerQueueTasks = $reviewTasks->where('status', 'review');
                $developerQueueTasks = $reviewTasks->whereIn('status', ['todo', 'in_progress', 'blocked']);

                return [
                    'user' => $user,
                    'development_count' => $developmentTasks->count(),
                    'development_estimate' => round($developmentTasks->sum(fn($task) => $estimateMandays($task)), 2),
                    'review_total_count' => $reviewTasks->count(),
                    'review_in_developer_count' => $developerQueueTasks->count(),
                    'review_in_reviewer_count' => $reviewerQueueTasks->count(),
                    'nearest_review_due_date' => $reviewerQueueTasks
                        ->filter(fn($task) => filled($task->review_due_date))
                        ->sortBy('review_due_date')
                        ->first()?->review_due_date,
                    'total_responsibility_count' => $developmentTasks->count() + $reviewTasks->count(),
                ];
            })
            ->sortByDesc('total_responsibility_count')
            ->values();

        $riskTaskCollection = $activeTasks
            ->sortBy(function ($task) use ($effectiveDueDate) {
                $dueDate = $effectiveDueDate($task);

                if ($task->status === 'blocked') {
                    return '0-' . ($dueDate?->format('Ymd') ?? '99999999');
                }

                return ($dueDate && $dueDate->isPast() ? '1-' : '2-')
                    . ($dueDate?->format('Ymd') ?? '99999999');
            })
            ->values();
        $riskCurrentPage = LengthAwarePaginator::resolveCurrentPage();
        $riskTasks = new LengthAwarePaginator(
            $riskTaskCollection->forPage($riskCurrentPage, $riskPerPage)->values(),
            $riskTaskCollection->count(),
            $riskPerPage,
            $riskCurrentPage,
            ['path' => $request->url()]
        );
        $riskTasks->appends($request->except('page'))->fragment('task-risiko');

        return view('admin.project_management.workload_dashboard', compact(
            'menus',
            'holding',
            'statusOptions',
            'departemens',
            'users',
            'summary',
            'statusCounts',
            'developerWorkloads',
            'reviewerQueues',
            'combinedWorkloads',
            'riskTasks',
            'riskPerPage',
            'tasks'
        ));
    }

    public function create(string $holding)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $users = $this->users();
        $departemens = $this->departemens($holding->id);

        return view('admin.project_management.create', compact('holding', 'menus', 'users', 'departemens'));
    }

    public function store(Request $request, string $holding)
    {
        $holding = $this->holding($holding);
        $data = $this->validatedProject($request);

        $project = DB::transaction(function () use ($data, $holding) {
            $data['id'] = (string) Str::uuid();
            $data['holding_id'] = $holding->id;
            $data['project_code'] = $this->generateProjectCode($holding);
            $data['status'] = $data['status'] ?? 'draft';
            $data['health'] = $data['health'] ?? 'gray';
            $data['requested_at'] = now();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            return ProjectManagementProject::create($data);
        });

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id])
            ->with('success', 'Project berhasil ditambahkan. Silakan lengkapi detail task project.');
    }

    public function show(string $holding, string $id)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $project = ProjectManagementProject::with([
            'requester',
            'businessOwner',
            'requester.KaryawanNIK',
            'businessOwner.KaryawanNIK',
            'projectManager.KaryawanNIK',
            'technicalLead.KaryawanNIK',
            'sponsor.KaryawanNIK',
            'requesterDepartment',
            'members.user.KaryawanNIK',
            'milestones.owner.KaryawanNIK',
            'milestones.tasks',
            'tasks.primaryAssignee.KaryawanNIK',
            'tasks.reviewer.KaryawanNIK',
            'tasks.communicatedMeetings',
        ])
            ->withCount('meetings')
            ->where('holding_id', $holding->id)
            ->findOrFail($id);
        $this->authorizeProject('view', $project);
        $pendingCommentReplyCounts = $this->pendingCommentReplyCounts($project->tasks);
        $activeProjectTasks = $project->tasks->where('status', '!=', 'cancelled');
        $projectEstimateMandays = round($activeProjectTasks->sum(fn($task) => $this->taskEstimateMandays($task)), 2);
        $projectActualMandays = round(((float) $activeProjectTasks->sum('actual_hours')) / self::WORK_HOURS_PER_DAY, 2);
        $users = $this->users();

        return view('admin.project_management.show', compact(
            'holding',
            'menus',
            'project',
            'pendingCommentReplyCounts',
            'projectEstimateMandays',
            'projectActualMandays',
            'users'
        ));
    }

    public function storeMilestone(Request $request, string $holding, string $project)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $data = $this->validatedMilestone($request, $project);
        $data['id'] = (string) Str::uuid();
        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        ProjectManagementMilestone::create($data);

        return $this->redirectToTeamTab($holding, $project, 'Milestone berhasil ditambahkan.');
    }

    public function updateMilestone(Request $request, string $holding, string $project, string $milestone)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $milestone = $project->milestones()->findOrFail($milestone);
        $data = $this->validatedMilestone($request, $project, $milestone);
        $data['updated_by'] = Auth::id();
        $milestone->update($data);

        return $this->redirectToTeamTab($holding, $project, 'Milestone berhasil diperbarui.');
    }

    public function destroyMilestone(string $holding, string $project, string $milestone)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $milestone = $project->milestones()->withCount('tasks')->findOrFail($milestone);

        if ($milestone->tasks_count > 0) {
            return $this->redirectToTeamTab($holding, $project)
                ->withErrors(['milestone' => 'Milestone masih digunakan oleh task. Pindahkan task ke milestone lain sebelum menghapusnya.']);
        }

        $milestone->delete();

        return $this->redirectToTeamTab($holding, $project, 'Milestone berhasil dihapus.');
    }

    public function storeMember(Request $request, string $holding, string $project)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $data = $this->validatedMember($request);

        $member = ProjectManagementMember::withTrashed()
            ->where('project_id', $project->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if ($member && !$member->trashed()) {
            return $this->redirectToTeamTab($holding, $project)
                ->withErrors(['user_id' => 'User tersebut sudah terdaftar sebagai anggota project.']);
        }

        if ($member) {
            $member->restore();
            $member->update($data + ['is_active' => true, 'updated_by' => Auth::id()]);
        } else {
            ProjectManagementMember::create($data + [
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return $this->redirectToTeamTab($holding, $project, 'Anggota project berhasil ditambahkan.');
    }

    public function updateMember(Request $request, string $holding, string $project, string $member)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $member = $project->members()->findOrFail($member);
        $data = $this->validatedMember($request, $member);
        $data['updated_by'] = Auth::id();
        $member->update($data);

        return $this->redirectToTeamTab($holding, $project, 'Anggota project berhasil diperbarui.');
    }

    public function destroyMember(string $holding, string $project, string $member)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $member = $project->members()->findOrFail($member);
        $member->delete();

        return $this->redirectToTeamTab($holding, $project, 'Anggota project berhasil dihapus.');
    }

    public function edit(string $holding, string $id)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $users = $this->users();
        $departemens = $this->departemens($holding->id);
        $project = $this->project($holding, $id);
        $this->authorizeProject('update', $project);

        return view('admin.project_management.edit', compact('holding', 'menus', 'users', 'departemens', 'project'));
    }

    public function update(Request $request, string $holding, string $id)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $id);
        $this->authorizeProject('update', $project);
        $data = $this->validatedProject($request, true);
        $data['updated_by'] = Auth::id();

        if ($project->status !== 'in_progress' && ($data['status'] ?? null) === 'in_progress' && !$project->actual_start_at) {
            $data['actual_start_at'] = now();
        }

        if (($data['status'] ?? null) === 'completed' && !$project->actual_end_at) {
            $data['actual_end_at'] = now();
        }

        $project->update($data);

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id])
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(string $holding, string $id)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $id);
        $this->authorizeProject('delete', $project);
        $project->delete();

        return redirect()
            ->route('project_management.index', $holding->holding_code)
            ->with('success', 'Project berhasil diarsipkan.');
    }

    public function createTask(string $holding, string $project)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $users = $this->users();
        $departemens = $this->departemens($holding->id);
        $tasks = $project->tasks()->orderBy('sort_order')->orderBy('title')->get();

        return view('admin.project_management.tasks.create', compact('holding', 'menus', 'project', 'users', 'departemens', 'tasks'));
    }

    public function storeTask(Request $request, string $holding, string $project)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $request->validate([
            'task_attachments' => ['nullable', 'array', 'max:10'],
            'task_attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip'],
        ], [
            'task_attachments.array' => 'Lampiran task tidak valid.',
            'task_attachments.max' => 'Maksimal 10 file dapat diunggah untuk satu task.',
            'task_attachments.*.file' => 'Lampiran task harus berupa file.',
            'task_attachments.*.max' => 'Ukuran setiap lampiran task maksimal 10 MB.',
            'task_attachments.*.mimes' => 'Format lampiran task tidak didukung.',
        ]);
        $data = $this->validatedTask($request, $project);
        $attachments = $request->file('task_attachments', []);

        DB::transaction(function () use ($data, $project, $holding, $attachments) {
            $data['id'] = (string) Str::uuid();
            $data['project_id'] = $project->id;
            $data['task_code'] = $this->generateTaskCode($project);
            $data['status'] = 'todo';
            $data['reporter_id'] = $data['reporter_id'] ?? Auth::id();
            $data['actual_hours'] = 0;
            $data['sort_order'] = $project->tasks()->max('sort_order') + 1;
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            $data['blocked_at'] = null;
            $data['blocker_reason'] = null;

            $task = ProjectManagementTask::create($data);
            $this->recordTaskActivity($task, 'created', 'Task dibuat dengan status Todo.', 'status', null, 'Todo');
            $this->storeInitialTaskAttachments($task, $attachments);
            $this->recalculateProjectProgress($project->fresh());
        });

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id])
            ->with('success', 'Task berhasil ditambahkan.');
    }

    public function showTask(string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $project = $this->project($holding, $project);
        $task = $project->tasks()
            ->with([
                'parentTask',
                'reporter.KaryawanNIK',
                'primaryAssignee.KaryawanNIK',
                'reviewer.KaryawanNIK',
                'reviewedBy.KaryawanNIK',
                'comments.user.KaryawanNIK',
                'comments.replies.user.KaryawanNIK',
                'activities.user.KaryawanNIK',
                'attachments.uploader.KaryawanNIK',
                'communicatedMeetings',
            ])
            ->findOrFail($task);
        $canCommentTask = $this->canCommentTask($task);
        $pendingCommentReplyCount = $this->pendingCommentReplyCount($task->id);

        return view('admin.project_management.tasks.show', compact('holding', 'menus', 'project', 'task', 'canCommentTask', 'pendingCommentReplyCount'));
    }

    public function storeTaskComment(Request $request, string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $task = $project->tasks()->with('project.members')->findOrFail($task);
        $this->ensureCanCommentTask($task);

        $data = $request->validate([
            'comment_type' => ['required', Rule::in(['comment', 'update', 'blocker', 'review'])],
            'comment' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'uuid'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());
        $parentId = null;

        if (!empty($data['parent_id'])) {
            $parent = $task->comments()
                ->whereNull('parent_id')
                ->findOrFail($data['parent_id']);
            $parentId = $parent->id;
        }

        ProjectManagementTaskComment::create([
            'id' => (string) Str::uuid(),
            'task_id' => $task->id,
            'parent_id' => $parentId,
            'user_id' => Auth::id(),
            'comment_type' => $data['comment_type'],
            'comment' => $data['comment'],
        ]);

        return redirect()
            ->route('project_management.tasks.show', [$holding->holding_code, $project->id, $task->id])
            ->withFragment('komentar-task')
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroyTaskComment(string $holding, string $project, string $task, string $comment)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $task = $project->tasks()->with('project')->findOrFail($task);
        $comment = $task->comments()->findOrFail($comment);

        abort_unless(
            $comment->user_id === Auth::id() || $project->project_manager_id === Auth::id(),
            403,
            'Hanya pembuat komentar atau Project Manager yang dapat menghapus komentar.'
        );

        if (!$comment->parent_id) {
            $comment->replies()->delete();
        }

        $comment->delete();

        return redirect()
            ->route('project_management.tasks.show', [$holding->holding_code, $project->id, $task->id])
            ->withFragment('komentar-task')
            ->with('success', 'Komentar berhasil dihapus.');
    }

    public function showTaskAttachment(string $holding, string $project, string $task, string $attachment)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $task = $project->tasks()->with('project.members')->findOrFail($task);
        $this->ensureCanCommentTask($task);
        $attachment = $task->attachments()->findOrFail($attachment);

        abort_unless(
            Storage::disk('public')->exists($attachment->file_path),
            404,
            'File lampiran tidak ditemukan.'
        );

        return Storage::disk('public')->response($attachment->file_path, $attachment->original_name);
    }

    public function editTask(string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $task = $project->tasks()
            ->with([
                'primaryAssignee.Karyawan.Departemen',
                'primaryAssignee.Karyawan.KaryawanJabatan.Departemen',
                'primaryAssignee.KaryawanNIK.Departemen',
                'primaryAssignee.KaryawanNIK.KaryawanJabatan.Departemen',
            ])
            ->findOrFail($task);
        $users = $this->users();
        $departemens = $this->departemens($holding->id);

        if ($task->primaryAssignee && !$users->contains('id', $task->primaryAssignee->id)) {
            $users->push($task->primaryAssignee);
            $users = $users->sortBy('username')->values();
        }

        $assigneeDepartments = collect([
            data_get($task, 'primaryAssignee.Karyawan.Departemen'),
            data_get($task, 'primaryAssignee.KaryawanNIK.Departemen'),
        ])->merge(collect(data_get($task, 'primaryAssignee.Karyawan.KaryawanJabatan', []))->pluck('Departemen'))
            ->merge(collect(data_get($task, 'primaryAssignee.KaryawanNIK.KaryawanJabatan', []))->pluck('Departemen'))
            ->filter()
            ->unique('id');

        foreach ($assigneeDepartments as $department) {
            if (!$departemens->contains('id', $department->id)) {
                $departemens->push($department);
            }
        }
        $departemens = $departemens->sortBy('nama_departemen')->values();
        $tasks = $project->tasks()
            ->where('id', '!=', $task->id)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.project_management.tasks.edit', compact('holding', 'menus', 'project', 'task', 'users', 'departemens', 'tasks'));
    }

    public function updateTask(Request $request, string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $task = $project->tasks()->findOrFail($task);
        $data = $this->validatedTask($request, $project, $task);
        $usesStatusOverride = (bool) ($data['status_override'] ?? false);
        $overrideReason = $data['status_override_reason'] ?? null;
        unset($data['status_override'], $data['status_override_reason']);

        if ($data['status'] === 'done' && $task->status !== 'done' && !$usesStatusOverride) {
            throw ValidationException::withMessages([
                'status_override' => 'Status Done normal hanya dapat diberikan oleh Reviewer / QA. Centang override dan isi alasan jika PM perlu menetapkan Done secara khusus.',
            ]);
        }

        $data['updated_by'] = Auth::id();
        $data['blocked_at'] = $data['status'] === 'blocked'
            ? ($task->blocked_at ?: now())
            : null;
        $data['blocker_reason'] = $data['status'] === 'blocked'
            ? ($data['blocker_reason'] ?? null)
            : null;
        $data['review_due_date'] = $data['status'] === 'review'
            ? ($data['review_due_date'] ?? $this->calculateReviewDueDate($task->status_updated_at ?: now()))
            : null;
        $oldStatus = $task->status;
        $original = $task->getOriginal();

        DB::transaction(function () use ($task, $data, $project, $oldStatus, $original, $usesStatusOverride, $overrideReason) {
            $task->update($data);
            $this->recordStatusSystemComment($task, $oldStatus, $data['status'], 'PM update task');
            $this->recordTaskChanges($task, $original, $data, 'PM update task');
            if ($usesStatusOverride && $oldStatus !== $data['status']) {
                $this->recordTaskActivity(
                    $task,
                    'status_override',
                    'PM melakukan override status dari ' . $this->activityValueForField('status', $oldStatus)
                        . ' ke ' . $this->activityValueForField('status', $data['status']) . '. Alasan: ' . $overrideReason,
                    'status_override_reason',
                    null,
                    $overrideReason
                );
            }
            $this->recalculateProjectProgress($project->fresh());
        });

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id])
            ->with('success', 'Task berhasil diperbarui.');
    }

    public function reopenTask(Request $request, string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $task = $project->tasks()->findOrFail($task);

        abort_unless($task->status === 'done', 422, 'Hanya task dengan status Done yang dapat di-reopen.');

        $data = $request->validate([
            'reopen_status' => ['required', Rule::in(['in_progress', 'todo'])],
            'reopen_reason' => ['required', 'string', 'max:5000'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());

        $oldStatus = $task->status;

        DB::transaction(function () use ($task, $project, $data, $oldStatus) {
            $task->update([
                'status' => $data['reopen_status'],
                'status_updated_at' => now(),
                'blocked_at' => null,
                'blocker_reason' => null,
                'updated_by' => Auth::id(),
            ]);

            $this->recordTaskActivity(
                $task,
                'status_changed',
                'PM reopen task: Status berubah dari ' . $this->activityValueForField('status', $oldStatus)
                    . ' ke ' . $this->activityValueForField('status', $data['reopen_status']) . '.',
                'status',
                $this->activityValueForField('status', $oldStatus),
                $this->activityValueForField('status', $data['reopen_status'])
            );
            $this->recordTaskActivity(
                $task,
                'field_changed',
                'PM reopen task: Alasan Reopen / Catatan Revisi ditambahkan.',
                'reopen_reason',
                null,
                $data['reopen_reason']
            );
            $this->recalculateProjectProgress($project->fresh());
        });

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'detail'])
            ->withFragment('detail-panel')
            ->with('success', 'Task berhasil di-reopen.');
    }

    public function destroyTask(string $holding, string $project, string $task)
    {
        $holding = $this->holding($holding);
        $project = $this->project($holding, $project);
        $this->ensureProjectManager($project);
        $task = $project->tasks()->findOrFail($task);

        DB::transaction(function () use ($task, $project) {
            $task->delete();
            $this->recalculateProjectProgress($project->fresh());
        });

        return redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id])
            ->with('success', 'Task berhasil dihapus.');
    }

    public function myTasks(Request $request, string $holding)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $statusOptions = $this->taskStatusOptions();
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys($statusOptions))],
            'q' => ['nullable', 'string', 'max:200'],
        ]);

        $baseQuery = ProjectManagementTask::query()
            ->where('primary_assignee_id', Auth::id())
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->when(filled($filters['q'] ?? null), function ($query) use ($filters) {
                $keyword = strtolower(trim($filters['q']));

                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(task_code) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(type) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(priority) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(COALESCE(acceptance_criteria, \'\')) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(COALESCE(developer_update_notes, \'\')) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(COALESCE(blocker_reason, \'\')) LIKE ?', ["%{$keyword}%"])
                        ->orWhereHas('project', function ($projectQuery) use ($keyword) {
                            $projectQuery->whereRaw('LOWER(project_code) LIKE ?', ["%{$keyword}%"])
                                ->orWhereRaw('LOWER(project_name) LIKE ?', ["%{$keyword}%"]);
                        });
                });
            });

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $tasks = (clone $baseQuery)
            ->with(['project', 'primaryAssignee.KaryawanNIK'])
            ->withCount([
                'comments' => fn($query) => $query->where('comment_type', '!=', 'system'),
            ])
            ->when(filled($filters['status'] ?? null), fn($query) => $query->where('status', $filters['status']))
            ->orderByRaw("case when (case when status = 'review' then COALESCE(review_due_date, due_revision_date, due_date) else COALESCE(due_revision_date, due_date) end) is null then 1 else 0 end")
            ->orderByRaw("case when status = 'review' then COALESCE(review_due_date, due_revision_date, due_date) else COALESCE(due_revision_date, due_date) end")
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();
        $pendingCommentReplyCounts = $this->pendingCommentReplyCounts($tasks->getCollection());

        return view('admin.project_management.my_tasks.index', compact('holding', 'menus', 'tasks', 'statusOptions', 'statusCounts', 'pendingCommentReplyCounts'));
    }

    public function editMyTask(string $holding, string $task)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $statusOptions = $this->assigneeTaskStatusOptions();
        $task = ProjectManagementTask::with([
            'project',
            'primaryAssignee.KaryawanNIK',
            'reviewer.KaryawanNIK',
            'attachments.uploader.KaryawanNIK',
            'activities.user.KaryawanNIK',
        ])
            ->withCount([
                'comments' => fn($query) => $query->where('comment_type', '!=', 'system'),
            ])
            ->where('primary_assignee_id', Auth::id())
            ->whereNotIn('status', ['blocked', 'review', 'done', 'cancelled'])
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->findOrFail($task);
        $pendingCommentReplyCount = $this->pendingCommentReplyCount($task->id);

        return view('admin.project_management.my_tasks.edit', compact('holding', 'menus', 'task', 'statusOptions', 'pendingCommentReplyCount'));
    }

    public function updateMyTask(Request $request, string $holding, string $task)
    {
        $holding = $this->holding($holding);
        $task = ProjectManagementTask::with('project')
            ->where('primary_assignee_id', Auth::id())
            ->whereNotIn('status', ['blocked', 'review', 'done', 'cancelled'])
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->findOrFail($task);
        $data = $this->validatedMyTask($request);

        if ($data['status'] === 'review' && !$task->reviewer_id) {
            return back()
                ->withErrors(['status' => 'Reviewer / QA harus diisi sebelum task bisa disubmit ke Review.'])
                ->withInput();
        }

        $data['updated_by'] = Auth::id();
        $data['blocked_at'] = $data['status'] === 'blocked'
            ? ($task->blocked_at ?: now())
            : null;
        $data['review_due_date'] = $data['status'] === 'review'
            ? $this->calculateReviewDueDate($data['status_updated_at'])
            : null;
        $oldStatus = $task->status;
        $original = $task->getOriginal();

        DB::transaction(function () use ($task, $data, $oldStatus, $original) {
            $task->update($data);
            $this->recordStatusSystemComment($task, $oldStatus, $data['status'], 'Developer update progress');
            $this->recordTaskChanges($task, $original, $data, 'Developer update progress');
            $this->recalculateProjectProgress($task->project->fresh());
        });

        return redirect()
            ->route('project_management.my_tasks.index', $holding->holding_code)
            ->with('success', 'Progress task berhasil diperbarui.');
    }

    public function reviewTasks(Request $request, string $holding)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $statusOptions = $this->reviewerTaskStatusOptions();

        $tasks = ProjectManagementTask::with(['project', 'primaryAssignee.KaryawanNIK', 'reviewer.KaryawanNIK'])
            ->where('reviewer_id', Auth::id())
            ->where('status', 'review')
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = strtolower($request->q);

                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(task_code) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereHas('project', function ($projectQuery) use ($keyword) {
                            $projectQuery->whereRaw('LOWER(project_code) LIKE ?', ["%{$keyword}%"])
                                ->orWhereRaw('LOWER(project_name) LIKE ?', ["%{$keyword}%"]);
                        });
                });
            })
            ->orderBy('status_updated_at')
            ->orderByRaw("case when COALESCE(review_due_date, due_revision_date, due_date) is null then 1 else 0 end")
            ->orderByRaw('COALESCE(review_due_date, due_revision_date, due_date)')
            ->paginate(15)
            ->withQueryString();
        $pendingCommentReplyCounts = $this->pendingCommentReplyCounts($tasks->getCollection());

        return view('admin.project_management.review_tasks.index', compact('holding', 'menus', 'tasks', 'statusOptions', 'pendingCommentReplyCounts'));
    }

    public function editReviewTask(string $holding, string $task)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();
        $statusOptions = $this->reviewerTaskStatusOptions();
        $task = ProjectManagementTask::with([
            'project',
            'primaryAssignee.KaryawanNIK',
            'reviewer.KaryawanNIK',
            'attachments.uploader.KaryawanNIK',
            'activities.user.KaryawanNIK',
        ])
            ->where('reviewer_id', Auth::id())
            ->where('status', 'review')
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->findOrFail($task);
        $pendingCommentReplyCount = $this->pendingCommentReplyCount($task->id);

        return view('admin.project_management.review_tasks.edit', compact('holding', 'menus', 'task', 'statusOptions', 'pendingCommentReplyCount'));
    }

    public function updateReviewTask(Request $request, string $holding, string $task)
    {
        $holding = $this->holding($holding);
        $task = ProjectManagementTask::with('project')
            ->where('reviewer_id', Auth::id())
            ->where('status', 'review')
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id))
            ->findOrFail($task);
        $data = $this->validatedReviewTask($request);
        $reviewFiles = array_filter((array) $request->file('review_files', []));
        unset($data['review_files']);
        $data['reviewed_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['status_updated_at'] = $data['reviewed_at'];
        $data['blocked_at'] = $data['status'] === 'blocked'
            ? ($task->blocked_at ?: $data['reviewed_at'])
            : null;
        $data['blocker_reason'] = $data['status'] === 'blocked'
            ? ($data['blocker_reason'] ?? null)
            : null;
        $data['due_revision_date'] = in_array($data['status'], ['in_progress', 'blocked'], true)
            ? ($data['due_revision_date'] ?? null)
            : null;
        $data['review_due_date'] = null;
        $oldStatus = $task->status;
        $original = $task->getOriginal();

        DB::transaction(function () use ($task, $data, $reviewFiles, $oldStatus, $original) {
            $task->update($data);
            $this->storeTaskReviewAttachments($task, $reviewFiles);
            $this->recordStatusSystemComment($task, $oldStatus, $data['status'], 'Reviewer / QA update review');
            $this->recordTaskChanges($task, $original, $data, 'Reviewer / QA update review');
            $this->recalculateProjectProgress($task->project->fresh());
        });

        return redirect()
            ->route('project_management.review_tasks.index', $holding->holding_code)
            ->with('success', 'Review task berhasil disimpan.');
    }

    public function blockedTasks(Request $request, string $holding)
    {
        $holding = $this->holding($holding);
        $menus = $this->sidebarMenus();

        $tasks = ProjectManagementTask::with([
            'project',
            'primaryAssignee.KaryawanNIK',
            'reviewer.KaryawanNIK',
        ])
            ->where('status', 'blocked')
            ->whereHas('project', function ($query) use ($holding) {
                $query->where('holding_id', $holding->id)
                    ->where('project_manager_id', Auth::id());
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = strtolower($request->q);

                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(task_code) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                        ->orWhereRaw('LOWER(blocker_reason) LIKE ?', ["%{$keyword}%"])
                        ->orWhereHas('project', function ($projectQuery) use ($keyword) {
                            $projectQuery->whereRaw('LOWER(project_code) LIKE ?', ["%{$keyword}%"])
                                ->orWhereRaw('LOWER(project_name) LIKE ?', ["%{$keyword}%"]);
                        });
                });
            })
            ->orderByRaw("case when blocked_at is null then 1 else 0 end")
            ->orderBy('blocked_at')
            ->orderByRaw("case when COALESCE(due_revision_date, due_date) is null then 1 else 0 end")
            ->orderByRaw('COALESCE(due_revision_date, due_date)')
            ->paginate(15)
            ->withQueryString();

        return view('admin.project_management.blocked_tasks.index', compact('holding', 'menus', 'tasks'));
    }

    private function holding(string $holdingCode): Holding
    {
        return Holding::where('holding_code', $holdingCode)->firstOrFail();
    }

    private function project(Holding $holding, string $projectId): ProjectManagementProject
    {
        $project = ProjectManagementProject::where('holding_id', $holding->id)->findOrFail($projectId);
        $this->authorizeProject('view', $project);

        return $project;
    }

    private function ensureProjectManager(ProjectManagementProject $project): void
    {
        $this->authorizeProject('manageTasks', $project);
    }

    private function ensureCanCommentTask(ProjectManagementTask $task): void
    {
        abort_unless(
            $this->canCommentTask($task),
            403,
            'Anda tidak memiliki akses komentar untuk task ini.'
        );
    }

    private function canCommentTask(ProjectManagementTask $task): bool
    {
        return app(ProjectManagementProjectPolicy::class)
            ->commentTask(Auth::user(), $task->project, $task);
    }

    private function authorizeProject(string $ability, ProjectManagementProject $project): void
    {
        $policy = app(ProjectManagementProjectPolicy::class);
        $allowed = match ($ability) {
            'view' => $policy->view(Auth::user(), $project),
            'update' => $policy->update(Auth::user(), $project),
            'delete' => $policy->delete(Auth::user(), $project),
            'manageTasks' => $policy->manageTasks(Auth::user(), $project),
            'viewMeetings' => $policy->viewMeetings(Auth::user(), $project),
            'createMeeting' => $policy->createMeeting(Auth::user(), $project),
            default => false,
        };

        abort_unless($allowed, 403, 'Anda tidak memiliki akses untuk melakukan tindakan ini pada project tersebut.');
    }

    private function pendingCommentReplyCount(string $taskId): int
    {
        return $this->pendingCommentReplyCounts(collect([(object) ['id' => $taskId]]))[$taskId] ?? 0;
    }

    private function pendingCommentReplyCounts(iterable $tasks): array
    {
        $taskIds = collect($tasks)
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();

        if ($taskIds->isEmpty()) {
            return [];
        }

        $counts = $taskIds
            ->mapWithKeys(fn($taskId) => [(string) $taskId => 0])
            ->all();
        $comments = ProjectManagementTaskComment::query()
            ->whereIn('task_id', $taskIds)
            ->where('comment_type', '!=', 'system')
            ->orderBy('created_at')
            ->get(['id', 'task_id', 'parent_id', 'user_id', 'created_at']);
        $rootComments = $comments
            ->whereNull('parent_id')
            ->keyBy('id');
        $latestCommentsByRoot = $rootComments->all();

        foreach ($comments->whereNotNull('parent_id') as $reply) {
            if (!$rootComments->has($reply->parent_id)) {
                continue;
            }

            $latest = $latestCommentsByRoot[$reply->parent_id] ?? null;

            if (!$latest || $reply->created_at->greaterThanOrEqualTo($latest->created_at)) {
                $latestCommentsByRoot[$reply->parent_id] = $reply;
            }
        }

        foreach ($rootComments as $rootComment) {
            $latest = $latestCommentsByRoot[$rootComment->id] ?? $rootComment;

            if ($latest->user_id !== Auth::id()) {
                $counts[$rootComment->task_id] = ($counts[$rootComment->task_id] ?? 0) + 1;
            }
        }

        return $counts;
    }

    private function storeTaskReviewAttachments(ProjectManagementTask $task, array $files): void
    {
        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $path = $file->store('project_management/review_attachments/' . $task->id, 'public');
            $attachment = ProjectManagementTaskAttachment::create([
                'id' => (string) Str::uuid(),
                'task_id' => $task->id,
                'uploaded_by' => Auth::id(),
                'source' => 'review',
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->recordTaskActivity(
                $task,
                'attachment_uploaded',
                'Reviewer / QA upload file review: ' . $attachment->original_name . '.',
                'review_attachment',
                null,
                $attachment->id
            );
        }
    }

    private function storeInitialTaskAttachments(ProjectManagementTask $task, array $files): void
    {
        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $path = $file->store('project_management/task_attachments/' . $task->id, 'public');
            $attachment = ProjectManagementTaskAttachment::create([
                'id' => (string) Str::uuid(),
                'task_id' => $task->id,
                'uploaded_by' => Auth::id(),
                'source' => 'task',
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->recordTaskActivity(
                $task,
                'attachment_uploaded',
                'PM menambahkan lampiran saat task dibuat: ' . $attachment->original_name . '.',
                'task_attachment',
                null,
                $attachment->id
            );
        }
    }

    private function recordStatusSystemComment(
        ProjectManagementTask $task,
        ?string $oldStatus,
        ?string $newStatus,
        string $source
    ): void {
        return;
    }

    private function recordTaskChanges(ProjectManagementTask $task, array $original, array $data, string $source): void
    {
        $labels = [
            'title' => 'Judul Task',
            'type' => 'Type',
            'priority' => 'Priority',
            'status' => 'Status',
            'reporter_id' => 'Reporter',
            'primary_assignee_id' => 'Assignee',
            'reviewer_id' => 'Reviewer / QA',
            'estimate_hours' => 'Estimate Jam',
            'actual_hours' => 'Actual Jam',
            'start_date' => 'Start Date',
            'due_date' => 'Due Date',
            'due_revision_date' => 'Due Revisi',
            'review_due_date' => 'Review Due Date',
            'acceptance_criteria' => 'Acceptance Criteria',
            'developer_update_notes' => 'Catatan Hasil Developer',
            'blocker_reason' => 'Blocker Reason',
            'review_notes' => 'Review QA / UAT',
            'review_revision_notes' => 'Catatan Perbaikan / Ekspektasi Reviewer',
            'reopen_reason' => 'Alasan Reopen / Catatan Revisi',
        ];

        foreach ($labels as $field => $label) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $oldValue = $this->normalizeActivityRawValue($original[$field] ?? null);
            $newValue = $this->normalizeActivityRawValue($data[$field] ?? null);

            if ($this->activityValuesEqual($field, $oldValue, $newValue)) {
                continue;
            }

            $oldDisplay = $this->activityValueForField($field, $oldValue);
            $newDisplay = $this->activityValueForField($field, $newValue);
            $this->recordTaskActivity(
                $task,
                $field === 'status' ? 'status_changed' : 'field_changed',
                "{$source}: {$label} berubah dari {$oldDisplay} ke {$newDisplay}.",
                $field,
                $oldDisplay,
                $newDisplay
            );
        }
    }

    private function recordTaskActivity(
        ProjectManagementTask $task,
        string $activityType,
        string $description,
        ?string $fieldName = null,
        mixed $oldValue = null,
        mixed $newValue = null
    ): void {
        ProjectManagementTaskActivity::create([
            'id' => (string) Str::uuid(),
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'activity_type' => $activityType,
            'field_name' => $fieldName,
            'old_value' => $this->stringifyActivityValue($oldValue),
            'new_value' => $this->stringifyActivityValue($newValue),
            'description' => $description,
        ]);
    }

    private function normalizeActivityRawValue(mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function activityValuesEqual(string $field, mixed $oldValue, mixed $newValue): bool
    {
        if ($oldValue === null && $newValue === null) {
            return true;
        }

        if (in_array($field, ['estimate_hours', 'actual_hours'], true)) {
            return (float) ($oldValue ?? 0) === (float) ($newValue ?? 0);
        }

        if (in_array($field, ['start_date', 'due_date', 'due_revision_date', 'review_due_date'], true)) {
            $oldDate = $oldValue ? Carbon::parse($oldValue)->toDateString() : null;
            $newDate = $newValue ? Carbon::parse($newValue)->toDateString() : null;

            return $oldDate === $newDate;
        }

        return $oldValue === $newValue;
    }

    private function activityValueForField(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (in_array($field, ['reporter_id', 'primary_assignee_id', 'reviewer_id'], true)) {
            return $this->activityUserName((string) $value);
        }

        if (in_array($field, ['start_date', 'due_date', 'due_revision_date', 'review_due_date'], true)) {
            return Carbon::parse($value)->format('d/m/Y');
        }

        if (in_array($field, ['estimate_hours', 'actual_hours'], true)) {
            return number_format((float) $value, 2) . ' jam';
        }

        if ($field === 'status') {
            return ucwords(str_replace('_', ' ', (string) $value));
        }

        return (string) $value;
    }

    private function activityUserName(string $userId): string
    {
        $user = User::with('KaryawanNIK')->find($userId);

        if (!$user) {
            return $userId;
        }

        $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

        return $employeeName && $employeeName !== $user->username
            ? $user->username . ' - ' . $employeeName
            : $user->username;
    }

    private function stringifyActivityValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_scalar($value) ? (string) $value : json_encode($value);
    }

    private function sidebarMenus()
    {
        $roleIds = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        return Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }

    private function users()
    {
        return User::with([
            'Karyawan.Departemen',
            'Karyawan.KaryawanJabatan.Departemen',
            'KaryawanNIK.Departemen',
            'KaryawanNIK.KaryawanJabatan.Departemen',
        ])
            ->where(function ($query) {
                $query->where('user_aktif', 1)
                    ->orWhere('user_aktif', '1')
                    ->orWhere('user_aktif', 'AKTIF')
                    ->orWhereNull('user_aktif');
            })
            ->orderBy('username')
            ->get();
    }

    private function departemens(string $holdingId)
    {
        return Departemen::where('holding', $holdingId)
            ->orderBy('nama_departemen')
            ->get();
    }

    private function validatedProject(Request $request, bool $isUpdate = false): array
    {
        $data = $request->validate([
            'project_name' => ['required', 'string', 'min:5', 'max:200'],
            'project_type' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'background' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'expected_benefit' => ['nullable', 'string'],
            'requester_id' => ['required', 'string', 'exists:users,id'],
            'requester_department_id' => ['nullable', 'string', 'exists:departemens,id'],
            'business_owner_id' => ['nullable', 'string', 'exists:users,id'],
            'project_manager_id' => ['nullable', 'string', 'exists:users,id'],
            'technical_lead_id' => ['nullable', 'string', 'exists:users,id'],
            'sponsor_id' => ['nullable', 'string', 'exists:users,id'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => ['required', Rule::in(['draft', 'proposed', 'under_review', 'approved', 'planning', 'in_progress', 'uat', 'ready_for_deployment', 'completed', 'on_hold', 'cancelled'])],
            'health' => ['required', Rule::in(['gray', 'green', 'yellow', 'red'])],
            'planned_start_date' => ['nullable', 'date'],
            'planned_end_date' => ['nullable', 'date', 'after_or_equal:planned_start_date'],
            'scope_summary' => ['nullable', 'string'],
            'in_scope' => ['nullable', 'string'],
            'out_of_scope' => ['nullable', 'string'],
            'success_criteria' => ['nullable', 'string'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'hold_reason' => ['nullable', 'string'],
            'pm_commentary' => ['nullable', 'string', 'max:10000'],
            'decisions_needed' => ['nullable', 'string', 'max:10000'],
            'cancel_reason' => ['nullable', 'string'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());

        $this->validateNoSundayDates($data, [
            'planned_start_date',
            'planned_end_date',
        ]);

        return $data;
    }

    private function validatedMilestone(
        Request $request,
        ProjectManagementProject $project,
        ?ProjectManagementMilestone $milestone = null
    ): array {
        $data = $request->validate([
            'milestone_name' => ['required', 'string', 'min:3', 'max:200'],
            'owner_id' => ['nullable', 'string', 'exists:users,id'],
            'target_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['planned', 'in_progress', 'completed', 'on_hold', 'cancelled'])],
            'success_condition' => ['required', 'string', 'max:5000'],
            'weight' => ['required', 'numeric', 'min:0.01', 'max:100'],
        ], $this->taskValidationMessages(), [
            'milestone_name' => 'Nama Milestone',
            'owner_id' => 'Owner Milestone',
            'target_date' => 'Target Milestone',
            'status' => 'Status Milestone',
            'success_condition' => 'Kondisi Keberhasilan',
            'weight' => 'Bobot Milestone',
        ]);

        $this->validateNoSundayDates($data, ['target_date']);
        $targetDate = Carbon::parse($data['target_date'])->startOfDay();

        if (!empty($data['owner_id']) && !ProjectManagementProject::query()
            ->whereKey($project->getKey())
            ->visibleTo($data['owner_id'])
            ->exists()) {
            throw ValidationException::withMessages([
                'owner_id' => 'Owner milestone harus merupakan participant project. Tambahkan user sebagai anggota project terlebih dahulu.',
            ]);
        }

        if ($project->planned_start_date && $targetDate->lt($project->planned_start_date->startOfDay())) {
            throw ValidationException::withMessages(['target_date' => 'Target milestone tidak boleh sebelum Planned Start project.']);
        }

        if ($project->planned_end_date && $targetDate->gt($project->planned_end_date->startOfDay())) {
            throw ValidationException::withMessages(['target_date' => 'Target milestone tidak boleh setelah Planned End project.']);
        }

        $otherWeight = (float) $project->milestones()
            ->when($milestone, fn($query) => $query->whereKeyNot($milestone->getKey()))
            ->sum('weight');

        if ($otherWeight + (float) $data['weight'] > 100.001) {
            throw ValidationException::withMessages([
                'weight' => 'Total bobot seluruh milestone tidak boleh melebihi 100%. Sisa bobot tersedia: '
                    . number_format(max(0, 100 - $otherWeight), 2) . '%.',
            ]);
        }

        return $data;
    }

    private function validatedMember(Request $request, ?ProjectManagementMember $member = null): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'string',
                'exists:users,id',
                ...($member ? [Rule::in([$member->user_id])] : []),
            ],
            'project_role' => ['required', Rule::in(['developer', 'reviewer_qa', 'business_analyst', 'technical_lead', 'stakeholder', 'observer'])],
            'allocation_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'allocation_hours' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ], $this->taskValidationMessages(), [
            'user_id' => 'User Anggota',
            'project_role' => 'Peran Project',
            'allocation_percent' => 'Alokasi',
            'allocation_hours' => 'Alokasi Jam',
            'is_active' => 'Status Anggota',
        ]);
    }

    private function redirectToTeamTab(Holding $holding, ProjectManagementProject $project, ?string $message = null)
    {
        $redirect = redirect()
            ->route('project_management.show', [$holding->holding_code, $project->id, 'tab' => 'team'])
            ->withFragment('team-panel');

        return $message ? $redirect->with('success', $message) : $redirect;
    }

    private function validatedTask(Request $request, ProjectManagementProject $project, ?ProjectManagementTask $task = null): array
    {
        $usesStructuredEstimate = $request->hasAny(['estimate_months', 'estimate_days', 'estimate_time_hours']);

        $data = $request->validate([
            'milestone_id' => ['nullable', 'string', 'exists:project_management_milestones,id'],
            'parent_task_id' => ['nullable', 'string', 'exists:project_management_tasks,id'],
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'type' => ['required', Rule::in(['task', 'bug', 'change', 'documentation', 'support'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'status' => [$task ? 'required' : 'nullable', Rule::in(['todo', 'in_progress', 'blocked', 'review', 'done', 'cancelled'])],
            'reporter_id' => ['nullable', 'string', 'exists:users,id'],
            'primary_assignee_id' => ['nullable', 'string', 'exists:users,id'],
            'reviewer_id' => ['nullable', 'string', 'exists:users,id'],
            'estimate_months' => ['nullable', 'integer', 'min:0'],
            'estimate_days' => ['nullable', 'integer', 'min:0'],
            'estimate_time_hours' => ['nullable', 'numeric', 'min:0'],
            'estimate_hours' => ['nullable', 'numeric', 'min:0'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'review_due_date' => ['nullable', 'date'],
            'acceptance_criteria' => ['nullable', 'string'],
            'blocker_reason' => ['nullable', 'required_if:status,blocked', 'string'],
            'status_override' => ['nullable', 'boolean'],
            'status_override_reason' => ['nullable', 'required_if:status_override,1', 'string', 'min:10', 'max:2000'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());

        if (!empty($data['parent_task_id'])) {
            if ($task && $data['parent_task_id'] === $task->id) {
                abort(422, 'Task tidak boleh menjadi parent untuk dirinya sendiri.');
            }

            $parentBelongsToProject = $project->tasks()
                ->where('id', $data['parent_task_id'])
                ->exists();

            if (!$parentBelongsToProject) {
                abort(422, 'Parent task tidak valid untuk project ini.');
            }
        }

        if (!empty($data['milestone_id'])) {
            $milestoneBelongsToProject = $project->milestones()
                ->where('id', $data['milestone_id'])
                ->exists();

            if (!$milestoneBelongsToProject) {
                abort(422, 'Milestone tidak valid untuk project ini.');
            }
        }

        foreach (['milestone_id', 'parent_task_id', 'reporter_id', 'primary_assignee_id', 'reviewer_id', 'start_date', 'due_date', 'review_due_date'] as $field) {
            if (($data[$field] ?? null) === '') {
                $data[$field] = null;
            }
        }

        if (($data['estimate_hours'] ?? null) === '') {
            $data['estimate_hours'] = null;
        }

        $structuredEstimateHours = $this->calculateTaskEstimateHoursFromParts(
            $data['estimate_months'] ?? null,
            $data['estimate_days'] ?? null,
            $data['estimate_time_hours'] ?? null
        );

        if ($usesStructuredEstimate || $structuredEstimateHours !== null) {
            $data['estimate_hours'] = $structuredEstimateHours;
        }

        unset($data['estimate_months'], $data['estimate_days'], $data['estimate_time_hours']);

        if (($data['actual_hours'] ?? null) === '') {
            $data['actual_hours'] = 0;
        }

        $this->validateNoSundayDates($data, [
            'start_date',
            'review_due_date',
        ]);

        $data['due_date'] = $this->calculateTaskDueDate(
            $data['start_date'] ?? null,
            $data['estimate_hours'] ?? null
        );

        return $data;
    }

    private function calculateTaskEstimateHoursFromParts(mixed $months, mixed $days, mixed $hours): ?float
    {
        if ($months === null && $days === null && $hours === null) {
            return null;
        }

        $totalHours = ((int) ($months ?: 0) * self::WORK_DAYS_PER_MONTH * self::WORK_HOURS_PER_DAY)
            + ((int) ($days ?: 0) * self::WORK_HOURS_PER_DAY)
            + (float) ($hours ?: 0);

        return $totalHours > 0 ? round($totalHours, 2) : null;
    }

    private function calculateTaskDueDate(?string $startDate, mixed $estimateHours): ?string
    {
        if (!$startDate || $estimateHours === null || $estimateHours === '') {
            return null;
        }

        $dueDate = Carbon::parse($startDate)->startOfDay();
        while ($dueDate->isSunday()) {
            $dueDate->addDay();
        }
        $workdays = max(1, (int) ceil(((float) $estimateHours) / self::WORK_HOURS_PER_DAY));

        for ($addedWorkdays = 1; $addedWorkdays < $workdays;) {
            $dueDate->addDay();

            if (!$dueDate->isSunday()) {
                $addedWorkdays++;
            }
        }

        return $dueDate->toDateString();
    }

    private function calculateReviewDueDate(mixed $submittedAt): ?string
    {
        if (!$submittedAt) {
            return null;
        }

        return $this->addWorkingDays($submittedAt, 3)->toDateString();
    }

    private function addWorkingDays(mixed $date, int $days): Carbon
    {
        $targetDate = Carbon::parse($date)->startOfDay();

        for ($addedDays = 0; $addedDays < $days;) {
            $targetDate->addDay();

            if (!$targetDate->isSunday()) {
                $addedDays++;
            }
        }

        return $targetDate;
    }

    private function effectiveTaskDueDate(ProjectManagementTask $task): ?Carbon
    {
        $dueDate = $task->status === 'review'
            ? ($task->review_due_date ?: $task->due_revision_date ?: $task->due_date)
            : ($task->due_revision_date ?: $task->due_date);

        return $dueDate ? Carbon::parse($dueDate)->startOfDay() : null;
    }

    private function taskEstimateMandays(ProjectManagementTask $task): float
    {
        $startDate = $task->start_date;
        $dueDate = $task->due_date;

        if (in_array($task->status, ['in_progress', 'blocked'], true) && $task->due_revision_date) {
            $startDate = $task->status_updated_at ?: $task->reviewed_at ?: now();
            $dueDate = $task->due_revision_date;
        }

        if ($startDate && $dueDate) {
            $workdays = $this->workingDayCount($startDate, $dueDate);

            if ($workdays > 0) {
                return (float) $workdays;
            }
        }

        return round(((float) ($task->estimate_hours ?? 0)) / self::WORK_HOURS_PER_DAY, 2);
    }

    private function workingDayCount(mixed $startDate, mixed $endDate, bool $includeStart = true): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lt($start)) {
            return 0;
        }

        if (!$includeStart) {
            $start->addDay();
        }

        $workdays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isSunday()) {
                $workdays++;
            }
        }

        return $workdays;
    }

    private function validateNoSundayDates(array $data, array $fields): void
    {
        $attributes = $this->taskValidationAttributes();
        $errors = [];

        foreach ($fields as $field) {
            if (blank($data[$field] ?? null)) {
                continue;
            }

            if (Carbon::parse($data[$field])->isSunday()) {
                $label = $attributes[$field] ?? ucwords(str_replace('_', ' ', $field));
                $errors[$field] = "{$label} tidak boleh jatuh pada hari Minggu.";
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function taskValidationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi jika :other dipilih :value.',
            'blocker_reason.required_if' => 'Blocker Reason wajib diisi jika status dipilih Blocked.',
            'status_override_reason.required_if' => 'Alasan Override wajib diisi ketika override alur status digunakan.',
            'review_revision_notes.required' => 'Catatan Perbaikan / Ekspektasi Reviewer wajib diisi jika task dikembalikan atau diblokir reviewer.',
            'due_revision_date.required' => 'Due Revisi wajib diisi jika task dikembalikan atau diblokir reviewer.',
            'due_revision_date.after_or_equal' => 'Due Revisi tidak boleh sebelum hari ini.',
            'review_files.*.file' => 'Lampiran review harus berupa file yang valid.',
            'review_files.*.max' => 'Ukuran setiap lampiran review maksimal 10 MB.',
            'reopen_reason.required' => 'Alasan Reopen / Catatan Revisi wajib diisi.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'numeric' => ':attribute harus berupa angka.',
            'string' => ':attribute harus berupa teks.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max karakter.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'uuid' => ':attribute tidak valid.',
        ];
    }

    private function taskValidationAttributes(): array
    {
        return [
            'project_name' => 'Nama Project',
            'project_type' => 'Tipe Project',
            'category' => 'Kategori',
            'description' => 'Deskripsi',
            'background' => 'Background',
            'objective' => 'Objective',
            'expected_benefit' => 'Expected Benefit',
            'requester_id' => 'Requester',
            'requester_department_id' => 'Departemen Requester',
            'business_owner_id' => 'Business Owner',
            'project_manager_id' => 'Project Manager',
            'technical_lead_id' => 'Technical Lead',
            'sponsor_id' => 'Sponsor',
            'health' => 'Health',
            'planned_start_date' => 'Planned Start',
            'planned_end_date' => 'Planned End',
            'scope_summary' => 'Scope Summary',
            'in_scope' => 'In Scope',
            'out_of_scope' => 'Out of Scope',
            'success_criteria' => 'Success Criteria',
            'estimated_hours' => 'Estimasi Jam',
            'hold_reason' => 'Hold Reason',
            'cancel_reason' => 'Cancel Reason',
            'milestone_id' => 'Milestone',
            'parent_task_id' => 'Parent Task',
            'title' => 'Judul Task',
            'type' => 'Tipe',
            'priority' => 'Priority',
            'status' => 'Status',
            'reporter_id' => 'Reporter',
            'primary_assignee_id' => 'Username Assignee',
            'reviewer_id' => 'Reviewer / QA',
            'estimate_months' => 'Estimasi Bulan',
            'estimate_days' => 'Estimasi Hari',
            'estimate_time_hours' => 'Estimasi Jam',
            'estimate_hours' => 'Estimate Jam',
            'actual_hours' => 'Actual Jam',
            'start_date' => 'Start Date',
            'due_date' => 'Due Date',
            'due_revision_date' => 'Due Revisi',
            'review_due_date' => 'Review Due Date',
            'status_updated_at' => 'Tanggal & Jam Update',
            'reviewed_at' => 'Tanggal & Jam Review',
            'acceptance_criteria' => 'Acceptance Criteria',
            'developer_update_notes' => 'Catatan Hasil Developer',
            'review_notes' => 'Review QA / UAT',
            'review_revision_notes' => 'Catatan Perbaikan / Ekspektasi Reviewer',
            'review_files' => 'Lampiran Review',
            'review_files.*' => 'Lampiran Review',
            'blocker_reason' => 'Blocker Reason',
            'status_override' => 'Override Alur Status',
            'status_override_reason' => 'Alasan Override',
            'reopen_status' => 'Status Tujuan',
            'reopen_reason' => 'Alasan Reopen / Catatan Revisi',
            'comment_type' => 'Tipe Komentar',
            'comment' => 'Komentar',
            'parent_id' => 'Komentar Induk',
        ];
    }

    private function validatedMyTask(Request $request): array
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->assigneeTaskStatusOptions()))],
            'status_updated_at' => ['required', 'date'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'developer_update_notes' => ['nullable', 'string'],
            'blocker_reason' => ['nullable', 'required_if:status,blocked', 'string'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());

        if (($data['actual_hours'] ?? null) === '') {
            $data['actual_hours'] = 0;
        }

        if (blank($data['developer_update_notes'] ?? null)) {
            unset($data['developer_update_notes']);
        }

        return $data;
    }

    private function validatedReviewTask(Request $request): array
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->reviewerTaskStatusOptions()))],
            'reviewed_at' => ['required', 'date'],
            'review_notes' => ['nullable', 'string'],
            'review_revision_notes' => [
                Rule::requiredIf(fn() => in_array($request->input('status'), ['in_progress', 'blocked'], true)),
                'nullable',
                'string',
            ],
            'due_revision_date' => [
                Rule::requiredIf(fn() => in_array($request->input('status'), ['in_progress', 'blocked'], true)),
                'nullable',
                'date',
            ],
            'review_files' => ['nullable', 'array'],
            'review_files.*' => ['file', 'max:10240'],
            'blocker_reason' => ['nullable', 'required_if:status,blocked', 'string'],
        ], $this->taskValidationMessages(), $this->taskValidationAttributes());

        if (blank($data['review_notes'] ?? null)) {
            unset($data['review_notes']);
        }

        if (blank($data['review_revision_notes'] ?? null)) {
            unset($data['review_revision_notes']);
        }

        if (($data['due_revision_date'] ?? null) === '') {
            $data['due_revision_date'] = null;
        }

        if (filled($data['due_revision_date'] ?? null)) {
            $dueRevisionDate = Carbon::parse($data['due_revision_date'])->startOfDay();
            $today = now()->startOfDay();
            $reviewedDate = Carbon::parse($data['reviewed_at'])->startOfDay();
            $minimumDate = $reviewedDate->greaterThan($today) ? $reviewedDate : $today;

            if ($dueRevisionDate->lt($minimumDate)) {
                throw ValidationException::withMessages([
                    'due_revision_date' => 'Due Revisi minimal tanggal ' . $minimumDate->format('d/m/Y') . '.',
                ]);
            }

            if ($dueRevisionDate->isSunday()) {
                throw ValidationException::withMessages([
                    'due_revision_date' => 'Due Revisi tidak boleh jatuh pada hari Minggu.',
                ]);
            }
        }

        if (($data['blocker_reason'] ?? null) === '') {
            $data['blocker_reason'] = null;
        }

        return $data;
    }

    private function generateProjectCode(Holding $holding): string
    {
        $holdingCode = strtoupper((string) Str::of($holding->holding_code)->before('_'));
        $holdingCode = preg_replace('/[^A-Z0-9]/', '', $holdingCode) ?: 'PRJ';
        $holdingCode = substr($holdingCode, 0, 8);

        $prefix = 'PM-' . $holdingCode . '-' . now()->format('Ym');
        $lastProject = ProjectManagementProject::where('project_code', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->orderByDesc('project_code')
            ->first();
        $lastNumber = $lastProject
            ? ((int) substr($lastProject->project_code, -4)) + 1
            : 1;

        return $prefix . '-' . str_pad((string) $lastNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateTaskCode(ProjectManagementProject $project): string
    {
        $lastTask = $project->tasks()
            ->lockForUpdate()
            ->orderByDesc('task_code')
            ->first();
        $lastNumber = $lastTask
            ? ((int) substr($lastTask->task_code, -3)) + 1
            : 1;

        return $project->project_code . '-T' . str_pad((string) $lastNumber, 3, '0', STR_PAD_LEFT);
    }

    private function taskStatusOptions(): array
    {
        return [
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'blocked' => 'Blocked',
            'review' => 'Review',
            'done' => 'Done',
            'cancelled' => 'Cancelled',
        ];
    }

    private function assigneeTaskStatusOptions(): array
    {
        return [
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'blocked' => 'Blocked',
            'review' => 'Submit Review',
        ];
    }

    private function reviewerTaskStatusOptions(): array
    {
        return [
            'done' => 'Done / Complete',
            'in_progress' => 'Return to Developer',
            'blocked' => 'Blocked',
        ];
    }

    private function recalculateProjectProgress(ProjectManagementProject $project): void
    {
        $tasks = $project->tasks()
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalTasks = $tasks->count();
        $totalEstimate = (float) $tasks->sum('estimate_hours');
        $doneTasks = $tasks->where('status', 'done');

        if ($totalTasks === 0) {
            $progress = 0;
        } elseif ($totalEstimate > 0) {
            $progress = ((float) $doneTasks->sum('estimate_hours') / $totalEstimate) * 100;
        } else {
            $progress = ($doneTasks->count() / $totalTasks) * 100;
        }

        $project->update([
            'progress_percent' => round($progress, 2),
            'actual_hours' => $tasks->sum('actual_hours'),
            'updated_by' => Auth::id(),
        ]);
    }
}
