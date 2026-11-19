<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketHead extends Model
{
    protected $table = 'ticket_head';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nomor_ticket',
        'ticket_menu_id',
        'ticket_sub_menu_id',
        'requestor_id',
        'ticket_status',
        'ticket_priority',
        'is_draft',
        'is_submit',
        'is_generated',
        'root_ticket_id',
        'parent_ticket_id',
        'generated_from_ticket_id',
        'dependency_rule_id',
        'is_cross_environment',
        'cross_env_status',
        'origin_cross_ticket_id',
        'closed_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'approve1',
        'approve1_id',
        'approve1_name',
        'approve1_at',
        'approve1_signature',
        'approve2',
        'approve2_id',
        'approve2_name',
        'approve2_at',
        'approve2_signature',
        'checked',
        'checker_id',
        'checker_name',
        'checked_at',
        'checker_signature',
        'requestor_name',
        'requestor_signature',
        'validator',
        'validator_id',
        'validator_name',
        'validator_at',
        'validator_signature',
        'approval_status',
        'execution_status',
        'assign',
        'assign_id',
        'assign_name',
        'assign_at',
        'pic_status', //boolean
        'pic_id',
        'pic_name',
        'finish_at',
        'lampiran',
        'pic_started_at',
        'actual_hours',
        'expected_hours',
        'variance_hours',
        'overrun_flag',
        'feedback_cycles',
    ];

    protected $casts = [
        'is_cross_environment' => 'boolean',
        'pic_started_at'  => 'datetime',
        'finish_at'       => 'datetime',
        'closed_at'       => 'datetime',
        'actual_hours'    => 'float',
        'expected_hours'  => 'float',
        'variance_hours'  => 'float',
        'overrun_flag'    => 'boolean',
        'feedback_cycles' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    public function difficulty(): BelongsTo
    {
        return $this->belongsTo(TicketDifficultyMaster::class, 'ticket_priority', 'code');
    }

    const CROSS_ENV_NONE            = 'NONE';
    const CROSS_ENV_PENDING_TRIGGER = 'PENDING_TRIGGER';
    const CROSS_ENV_TRIGGERED       = 'TRIGGERED';
    const CROSS_ENV_SKIPPED         = 'SKIPPED';

    const APPROVAL_DRAFT    = 'draft';
    const APPROVAL_PENDING  = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';
    const APPROVAL_CHECKED = 'checked';

    const EXEC_WAITING     = 'waiting';
    const EXEC_READY       = 'ready';
    const EXEC_IN_PROGRESS = 'in_progress';
    const EXEC_DONE        = 'done';

    const STATUS_SUBMITTED          = 'T01';
    const STATUS_APPROVED           = 'T02';
    const STATUS_ASSIGNED           = 'T03';
    const STATUS_IN_PROGRESS        = 'T04';
    const STATUS_WAITING_REQUESTOR  = 'T05';
    const STATUS_CLOSED             = 'T06';
    const STATUS_FEEDBACK_REQUESTOR = 'T07';
    const STATUS_CANCELLED          = 'T08';
    const STATUS_WAITING_APPROVAL1  = 'T09';
    const STATUS_WAITING_APPROVAL2  = 'T10';
    const STATUS_WAITING_VALIDATOR  = 'T11';
    const STATUS_WAITING_CHECKER    = 'T12';
    const STATUS_WAITING_ASSIGNER   = 'T13';
    const STATUS_PENDING_EXTERNAL   = 'T14';
    const STATUS_AUTOMATIC_CLOSE    = 'T15';
    const STATUS_DRAFT              = 'T00';

    const OVERALL_DRAFT             = 'draft';
    const OVERALL_APPROVAL          = 'approval';
    const OVERALL_WAITING_EXECUTION = 'waiting_execution';
    const OVERALL_EXECUTION         = 'execution';
    const OVERALL_COMPLETED         = 'completed';

    public const LEGACY_MAP = [
        'approve1' => [
            'status' => 'approve1',
            'id'     => 'approve1_id',
            'name'   => 'approve1_name',
            'at'     => 'approve1_at',
            'sig'    => 'approve1_signature',
        ],
        'approve2' => [
            'status' => 'approve2',
            'id'     => 'approve2_id',
            'name'   => 'approve2_name',
            'at'     => 'approve2_at',
            'sig'    => 'approve2_signature',
        ],
        'checker' => [
            'status' => 'checked',
            'id'     => 'checker_id',
            'name'   => 'checker_name',
            'at'     => 'checked_at',
            'sig'    => 'checker_signature',
        ],
        'final_approve' => [
            'status' => 'final_approve',
            'id'     => 'final_approve_id',
            'name'   => 'final_approve_name',
            'at'     => 'final_approve_at',
            'sig'    => 'final_approve_signature',
        ],
    ];
    public static function getStatusMeta(?string $status, ?string $approvalStatus = null): array
    {
        if ($approvalStatus === self::APPROVAL_REJECTED || $status === self::STATUS_CANCELLED) {
            return [
                'code'     => self::STATUS_CANCELLED,
                'label'    => 'Ditolak',
                'class'    => 'status-rejected',
                'bs_class' => 'bg-danger text-white',
                'icon'     => 'fa-ban',
            ];
        }

        $map = [
            self::STATUS_DRAFT => [
                'code'     => self::STATUS_DRAFT,
                'label'    => 'Draft',
                'class'    => 'status-draft',
                'bs_class' => 'bg-secondary text-white',
                'icon'     => 'fa-pen-to-square',
            ],
            self::STATUS_SUBMITTED => [
                'code'     => self::STATUS_SUBMITTED,
                'label'    => 'Menunggu Persetujuan',
                'class'    => 'status-pending',
                'bs_class' => 'bg-warning text-dark',
                'icon'     => 'fa-clock',
            ],
            self::STATUS_APPROVED => [
                'code'     => self::STATUS_APPROVED,
                'label'    => 'Disetujui',
                'class'    => 'status-approved',
                'bs_class' => 'bg-success text-white',
                'icon'     => 'fa-circle-check',
            ],
            self::STATUS_ASSIGNED => [
                'code'     => self::STATUS_ASSIGNED,
                'label'    => 'Ditugaskan',
                'class'    => 'status-process',
                'bs_class' => 'bg-primary text-white',
                'icon'     => 'fa-user-check',
            ],
            self::STATUS_IN_PROGRESS => [
                'code'     => self::STATUS_IN_PROGRESS,
                'label'    => 'Dikerjakan',
                'class'    => 'status-process',
                'bs_class' => 'bg-info text-white',
                'icon'     => 'fa-spinner',
            ],
            self::STATUS_WAITING_REQUESTOR => [
                'code'     => self::STATUS_WAITING_REQUESTOR,
                'label'    => 'Menunggu Respon Peminta',
                'class'    => 'status-waiting',
                'bs_class' => 'bg-warning text-dark',
                'icon'     => 'fa-hourglass-half',
            ],
            self::STATUS_CLOSED => [
                'code'     => self::STATUS_CLOSED,
                'label'    => 'Selesai',
                'class'    => 'status-approved',
                'bs_class' => 'bg-success text-white',
                'icon'     => 'fa-circle-check',
            ],
            self::STATUS_FEEDBACK_REQUESTOR => [
                'code'     => self::STATUS_FEEDBACK_REQUESTOR,
                'label'    => 'Respon Balasan Peminta',
                'class'    => 'status-feedback',
                'bs_class' => 'bg-danger text-white',
                'icon'     => 'fa-comment-dots',
            ],
            self::STATUS_CANCELLED => [
                'code'     => self::STATUS_CANCELLED,
                'label'    => 'Dibatalkan',
                'class'    => 'status-rejected',
                'bs_class' => 'bg-danger text-white',
                'icon'     => 'fa-ban',
            ],
            self::STATUS_WAITING_APPROVAL1 => [
                'code'     => self::STATUS_WAITING_APPROVAL1,
                'label'    => 'Disetujui Atasan 1',
                'class'    => 'status-approved',
                'bs_class' => 'bg-info text-white',
                'icon'     => 'fa-check-double',
            ],
            self::STATUS_WAITING_APPROVAL2 => [
                'code'     => self::STATUS_WAITING_APPROVAL2,
                'label'    => 'Disetujui Atasan 2',
                'class'    => 'status-approved',
                'bs_class' => 'bg-info text-white',
                'icon'     => 'fa-check-double',
            ],
            self::STATUS_WAITING_VALIDATOR => [
                'code'     => self::STATUS_WAITING_VALIDATOR,
                'label'    => 'Menunggu Divalidasi',
                'class'    => 'status-process',
                'bs_class' => 'bg-primary text-white',
                'icon'     => 'fa-clipboard-check',
            ],
            self::STATUS_WAITING_CHECKER => [
                'code'     => self::STATUS_WAITING_CHECKER,
                'label'    => 'Menunggu Pemeriksaan',
                'class'    => 'status-waiting',
                'bs_class' => 'bg-warning text-dark',
                'icon'     => 'fa-magnifying-glass',
            ],
            self::STATUS_WAITING_ASSIGNER => [
                'code'     => self::STATUS_WAITING_ASSIGNER,
                'label'    => 'Menunggu Penugasan',
                'class'    => 'status-waiting',
                'bs_class' => 'bg-warning text-white',
                'icon'     => 'fa-user-tag',
            ],
            self::STATUS_AUTOMATIC_CLOSE => [
                'code'     => self::STATUS_AUTOMATIC_CLOSE,
                'label'    => 'Selesai Otomatis oleh Sistem',
                'class'    => 'status-approved',
                'bs_class' => 'bg-success text-white',
                'icon'     => 'fa-robot',
            ],
            self::STATUS_PENDING_EXTERNAL => [
                'code'     => self::STATUS_PENDING_EXTERNAL,
                'label'    => 'PENDING',
                'class'    => 'status-pending',
                'bs_class' => 'bg-warning text-white',
                'icon'     => 'fa-clock',
            ],
        ];

        return $map[$status] ?? [
            'code'     => $status ?? '-',
            'label'    => $status ?? '-',
            'class'    => 'status-draft',
            'bs_class' => 'bg-secondary text-white',
            'icon'     => 'fa-circle',
        ];
    }

    public function getStatusMetaAttribute(): array
    {
        $meta = self::getStatusMeta($this->ticket_status, $this->approval_status);
        if ($this->is_draft || $this->ticket_status === self::STATUS_DRAFT) {
            $meta['label'] = $this->is_generated ? 'Draft (Lengkapi Detail)' : 'Draft';
            $meta['class'] = 'status-draft';
            $meta['icon']  = 'fa-pen-to-square';
        }
        return $meta;
    }

    public function Requestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function jmFinanceApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jm_finance_approved_by');
    }

    /** The root ticket of this chain (self if root). */
    public function rootTicket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'root_ticket_id');
    }

    /** The immediately preceding ticket. */
    public function parentTicket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'parent_ticket_id');
    }

    /** The ticket whose approval triggered the creation of this ticket. */
    public function generatedFromTicket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'generated_from_ticket_id');
    }

    /** All tickets that were directly spawned from this ticket. */
    public function generatedTickets(): HasMany
    {
        return $this->hasMany(TicketHead::class, 'generated_from_ticket_id');
    }

    /** All tickets in the same chain (same root). */
    public function chainTickets(): HasMany
    {
        return $this->hasMany(TicketHead::class, 'root_ticket_id');
    }

    /** All tickets that are children of this one (next in chain). */
    public function childTickets(): HasMany
    {
        return $this->hasMany(TicketHead::class, 'parent_ticket_id');
    }

    /** The cross-environment origin ticket that triggered this ticket. */
    public function originCrossTicket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'origin_cross_ticket_id');
    }

    /** All tickets spawned across other environments from this ticket. */
    public function spawnedCrossTickets(): HasMany
    {
        return $this->hasMany(TicketHead::class, 'origin_cross_ticket_id');
    }

    /** Berapa persen approval sudah selesai */
    public function approvalProgress(): int
    {
        $total   = $this->approvals()->count();
        $done    = $this->approvals()->where('status', 'approved')->count();
        return $total > 0 ? (int) round(($done / $total) * 100) : 0;
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(TicketExecutionLog::class, 'ticket_id')->latest();
    }

    public function workSessions(): HasMany
    {
        return $this->hasMany(TicketWorkSession::class, 'ticket_id')
            ->where('ticket_type', 'ticket')
            ->orderBy('cycle_number');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketApprovalComment::class, 'ticket_id')->orderBy('comment_at');
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_ticket_id);
    }

    public function getConditionValue(string $key): ?bool
    {
        $cv = $this->conditionValues->firstWhere('condition_key', $key);
        return $cv ? (bool) $cv->condition_value : null;
    }

    public function conditionValues(): HasMany
    {
        return $this->hasMany(TicketConditionValue::class, 'ticket_id');
    }
    public function ticketMenu()
    {
        return $this->belongsTo(TicketMenu::class, 'ticket_menu_id');
    }

    public function scopeForEnvironments($query, array $envs)
    {
        $envs = array_map('intval', $envs);

        return $query->where(function ($q) use ($envs) {
            $q->whereHas('ticketMenu', function ($m) use ($envs) {
                $m->whereIn('kategori', $envs);
            });
            // Tiket tanpa kategori dianggap Epicor,
            // sama seperti resolveEnvironmentKategori() di ApprovalController
            if (in_array(1, $envs, true)) {
                $q->orWhereDoesntHave('ticketMenu')
                    ->orWhereHas('ticketMenu', function ($m) {
                        $m->whereNull('kategori')->orWhere('kategori', 0);
                    });
            }
        });
    }

    public function ticketSubMenu()
    {
        return $this->belongsTo(TicketSubMenu::class, 'ticket_sub_menu_id');
    }

    public function ticketDetails()
    {
        return $this->hasMany(TicketDetail::class, 'ticket_id');
    }

    public function kondisis()
    {
        return $this->belongsToMany(
            TicketKondisiMenu::class,
            'ticket_attribute',   // pivot table
            'ticket_id',               // FK kolom ini di pivot
            'kondisi_id'             // FK model target di pivot
        );
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TicketApproval::class, 'ticket_id');
    }

    public function checkers(): HasMany
    {
        return $this->hasMany(TicketApproval::class, 'ticket_id')->where('role', 'checker');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketHeadAttachment::class, 'ticket_head_id')->whereNull('deleted_at');
    }

    /**
     * Traverses up through parent_ticket_id to collect all ancestor tickets.
     * Prevents infinite loops if circular reference somehow exists in DB.
     * 
     * Mengambil seluruh koleksi tiket pendahulu (ancestor) dalam satu rantai transaksi.
     */
    public function getAncestorTickets(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $visited = [$this->id];
        $current = $this->parentTicket;

        while ($current && !in_array($current->id, $visited, true)) {
            $ancestors->push($current);
            $visited[] = $current->id;
            $current = $current->parentTicket;
        }

        return $ancestors;
    }

    /**
     * Gets all sub_menu_ids in the lineage chain (self + all ancestors).
     * 
     * Mengumpulkan semua ID sub menu yang sudah pernah dilalui di sepanjang rantai transaksi
     * (dari tiket saat ini sampai root tiket).
     */
    public function getAncestorSubMenuIds(): array
    {
        $subMenuIds = [(string) $this->ticket_sub_menu_id];
        foreach ($this->getAncestorTickets() as $ancestor) {
            $subMenuIds[] = (string) $ancestor->ticket_sub_menu_id;
        }
        return array_values(array_unique($subMenuIds));
    }

    /**
     * Checks whether a target sub_menu_id is already present in the ancestor lineage.
     * 
     * LOGIKA ANTI-LOOP / PENCEGAHAN SIKLUS:
     * Mengecek apakah target sub_menu_id sudah pernah diproses di tiket-tiket sebelumnya.
     * Digunakan oleh DependencyEngine::detectLoop() agar transaksi reverse/forward
     * TIDAK looping kembali ke menu yang sudah pernah dilewati.
     */
    public function isInLineage($subMenuId): bool
    {
        return in_array((string) $subMenuId, $this->getAncestorSubMenuIds(), true);
    }

    public function getAllLinesCancelledAttribute(): bool
    {
        $this->loadMissing('ticketDetails.subDetails');
        $subs = $this->ticketDetails->flatMap(fn($d) => $d->subDetails);
        return $subs->isNotEmpty() && $subs->every(fn($s) => (bool) $s->is_cancelled);
    }
}
