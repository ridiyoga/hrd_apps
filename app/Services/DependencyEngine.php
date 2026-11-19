<?php

namespace App\Services;

use App\Models\TicketDependencyRule;
use App\Models\TicketHead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DependencyEngine
{
    public const DIRECTION_FORWARD = 'FORWARD';
    public const DIRECTION_REVERSE = 'REVERSE';

    /**
     * Build a stable traversal identity for a dependency edge in a single chain.
     * This intentionally uses existing graph data only and does not require
     * schema changes or persisted traversal state.
     */
    public function buildTraversalEdgeKey($rootTicketId, TicketDependencyRule $rule, string $direction): string
    {
        $rootId = (string) ($rootTicketId ?? '');
        $ruleId = (string) ($rule->id ?? '');
        $source = (string) ($rule->source_sub_menu_id ?? '');
        $target = (string) ($rule->target_sub_menu_id ?? '');
        $direction = strtoupper((string) $direction);

        return implode('|', [$rootId, $ruleId, $source, $target, $direction]);
    }

    /**
     * Returns the current chain traversal history derived from existing lineage.
     */
    public function buildTraversalHistory(TicketHead $ticket): array
    {
        $history = [];
        $rootId = (string) ($ticket->root_ticket_id ?? $ticket->id ?? '');
        $current = $ticket;
        $seen = [];

        while ($current) {
            $currentId = (string) ($current->id ?? '');
            if ($currentId !== '' && isset($seen[$currentId])) {
                break;
            }
            if ($currentId !== '') {
                $seen[$currentId] = true;
            }

            if ($current->dependency_rule_id && $current->ticket_sub_menu_id) {
                $rule = TicketDependencyRule::find($current->dependency_rule_id);
                if ($rule) {
                    $direction = $this->determineDirection($rule, $current->ticket_sub_menu_id);
                    if ($direction) {
                        $history[] = $this->buildTraversalEdgeKey($rootId, $rule, $direction);
                    }
                }
            }

            $parentId = $current->parent_ticket_id;
            if (!$parentId) {
                break;
            }

            $current = TicketHead::find($parentId);
        }

        return array_values(array_unique($history));
    }

    public function isDuplicateEdgeTraversal(TicketHead $ticket, TicketDependencyRule $rule, string $direction, array $traversalHistory = []): bool
    {
        if (!$rule || !$direction) {
            return false;
        }

        $rootId = (string) ($ticket->root_ticket_id ?? $ticket->id ?? '');
        $edgeKey = $this->buildTraversalEdgeKey($rootId, $rule, $direction);

        foreach ($traversalHistory as $existingKey) {
            if ((string) $existingKey === $edgeKey) {
                Log::warning('[DEPENDENCY_TRACE] Duplicate edge traversal prevented.', [
                    'ticket_id' => $ticket->id,
                    'root_ticket_id' => $rootId,
                    'dependency_rule_id' => $rule->id,
                    'direction' => $direction,
                    'current_sub_menu_id' => $ticket->ticket_sub_menu_id,
                    'edge_key' => $edgeKey,
                    'traversal_history' => $traversalHistory,
                    'ACTION' => 'SKIP_DUPLICATE_EDGE',
                ]);
                return true;
            }
        }

        return false;
    }

    public function shouldBlockTraversal(TicketHead $ticket, TicketDependencyRule $rule, string $direction, array $traversalHistory = []): bool
    {
        if (!$rule || !$direction) {
            return false;
        }

        $targetSubMenuId = $this->getTargetSubMenuId($rule, $ticket->ticket_sub_menu_id);
        if (!$targetSubMenuId) {
            return true;
        }

        if ($this->isIncomingEdge($ticket, $rule)) {
            Log::warning('[DEPENDENCY_TRACE] Incoming edge prevented.', [
                'ticket_id' => $ticket->id,
                'root_ticket_id' => $ticket->root_ticket_id ?? $ticket->id,
                'current_sub_menu_id' => $ticket->ticket_sub_menu_id,
                'target_sub_menu_id' => $targetSubMenuId,
                'dependency_rule_id' => $rule->id,
                'direction' => $direction,
                'ACTION' => 'SKIP_INCOMING_EDGE',
            ]);
            return true;
        }

        if ($this->detectLoop($ticket, $targetSubMenuId)) {
            Log::warning('[DEPENDENCY_TRACE] Ancestor loop prevented.', [
                'ticket_id' => $ticket->id,
                'root_ticket_id' => $ticket->root_ticket_id ?? $ticket->id,
                'current_sub_menu_id' => $ticket->ticket_sub_menu_id,
                'target_sub_menu_id' => $targetSubMenuId,
                'dependency_rule_id' => $rule->id,
                'direction' => $direction,
                'ancestor_submenus' => $ticket->getAncestorSubMenuIds(),
                'ACTION' => 'SKIP_CYCLE',
            ]);
            return true;
        }

        if ($this->isDuplicateEdgeTraversal($ticket, $rule, $direction, $traversalHistory)) {
            return true;
        }

        return false;
    }

    /**
     * Validates combination of flags.
     * Semua kombinasi is_bidirectional / is_conditional diizinkan:
     * - is_bidirectional = true  → alur bisa forward + reverse (bolak-balik)
     * - is_conditional   = true  → opsional, tergantung toggle user
     * - is_bidirectional = true AND is_conditional = true → opsional + bolak-balik (VALID)
     * - is_conditional   = false → mandatory / wajib eksekusi
     */
    public function isValidRuleConfig(TicketDependencyRule $rule): bool
    {
        return true;
    }

    /**
     * Determines edge direction relative to $currentSubMenuId:
     * 
     * LOGIKA PENENTUAN ARAH (FORWARD vs REVERSE):
     * 1. FORWARD (Maju)   : $currentSubMenuId sama dengan $rule->source_sub_menu_id.
     * 2. REVERSE (Mundur) : $currentSubMenuId sama dengan $rule->target_sub_menu_id DAN is_bidirectional = true.
     *    (Berlaku untuk semua kombinasi is_conditional).
     * 
     * @param TicketDependencyRule $rule
     * @param string|int $currentSubMenuId
     * @return string|null 'FORWARD', 'REVERSE', atau null jika tidak cocok
     */
    public function determineDirection(TicketDependencyRule $rule, $currentSubMenuId): ?string
    {
        if (!$this->isValidRuleConfig($rule)) {
            return null;
        }

        $currentSubMenuId = (string) $currentSubMenuId;
        $sourceId         = (string) $rule->source_sub_menu_id;
        $targetId         = (string) $rule->target_sub_menu_id;

        // [FORWARD] Posisi saat ini adalah Source -> Arah Maju ke Target
        if ($currentSubMenuId === $sourceId) {
            return self::DIRECTION_FORWARD;
        }

        // [REVERSE] Posisi saat ini adalah Target DAN bidirectional aktif -> Arah Mundur ke Source
        if ($rule->is_bidirectional && $currentSubMenuId === $targetId) {
            return self::DIRECTION_REVERSE;
        }

        return null;
    }

    /**
     * Determines target sub menu ID relative to $currentSubMenuId and rule direction.
     *
     * LOGIKA PENENTUAN SUBMENU TUJUAN:
     * - Jika FORWARD -> target adalah $rule->target_sub_menu_id
     * - Jika REVERSE -> target dibalik menjadi $rule->source_sub_menu_id
     */
    public function getTargetSubMenuId(TicketDependencyRule $rule, $currentSubMenuId): ?string
    {
        $direction = $this->determineDirection($rule, $currentSubMenuId);

        if ($direction === self::DIRECTION_FORWARD) {
            return (string) $rule->target_sub_menu_id;
        }

        if ($direction === self::DIRECTION_REVERSE) {
            return (string) $rule->source_sub_menu_id;
        }

        return null;
    }

    /**
     * Level 1 Loop Prevention — Incoming Edge Exclusion:
     * 
     * LOGIKA ANTI-LOOP LEVEL 1:
     * Mengecek apakah rule ini merupakan dependency_rule_id yang baru saja melahirkan tiket ini.
     * Jika ya, traversal diblokir agar tiket tidak memicu kembali ke edge/rule pembuatnya.
     */
    public function isIncomingEdge(TicketHead $ticket, TicketDependencyRule $rule): bool
    {
        if (!$ticket->dependency_rule_id) {
            return false;
        }

        $isIncoming = ((string) $rule->id === (string) $ticket->dependency_rule_id);

        if ($isIncoming) {
            Log::info('[DependencyEngine] Level 1 exclusion: rule is the incoming edge that spawned this ticket.', [
                'ticket_id'          => $ticket->id,
                'ticket_submenu'     => $ticket->ticket_sub_menu_id,
                'dependency_rule_id' => $rule->id,
            ]);
        }

        return $isIncoming;
    }

    /**
     * Level 2 Loop Prevention — Ancestor Lineage Check:
     * 
     * LOGIKA ANTI-LOOP LEVEL 2 (Pencegahan Siklus Silsilah Tiket):
     * Mengecek apakah $targetSubMenuId sudah pernah dilalui di dalam seluruh pohon silsilah
     * transaksi sebelumnya (ancestor lineage: parent -> grandparent -> root).
     * Jika submenu sudah pernah ada dalam lineage, traversal diblokir untuk mencegah circular loop.
     */
    public function detectLoop(TicketHead $parentTicket, $targetSubMenuId): bool
    {
        $isLoop = $parentTicket->isInLineage($targetSubMenuId);

        if ($isLoop) {
            Log::warning('[DependencyEngine] Level 2 exclusion: target submenu is already in parent lineage.', [
                'parent_ticket_id' => $parentTicket->id,
                'parent_submenu'   => $parentTicket->ticket_sub_menu_id,
                'target_submenu'   => $targetSubMenuId,
                'lineage'          => $parentTicket->getAncestorSubMenuIds(),
            ]);
        }

        return $isLoop;
    }

    /**
     * Gets all valid dependency rules applicable for $ticket.
     * Applies Level 1 (Incoming Edge Exclusion) and Level 2 (Ancestor Lineage Check).
     *
     * @param TicketHead $ticket
     * @param bool $includeBackward
     * @return Collection<int, TicketDependencyRule>
     */
    public function getValidRulesForTicket(TicketHead $ticket, bool $includeBackward = true): Collection
    {
        $currentSubMenuId = (string) $ticket->ticket_sub_menu_id;

        $rules = TicketDependencyRule::active()
            ->where(function ($q) use ($currentSubMenuId, $includeBackward) {
                $q->where('source_sub_menu_id', $currentSubMenuId);
                if ($includeBackward) {
                    $q->orWhere(function ($q2) use ($currentSubMenuId) {
                        // Rule bidirectional (termasuk yang conditional) dapat ditempuh balik
                        $q2->where('target_sub_menu_id', $currentSubMenuId)
                            ->where('is_bidirectional', true);
                    });
                }
            })
            ->get();

        $traversalHistory = $this->buildTraversalHistory($ticket);

        return $rules->filter(function ($rule) use ($ticket, $currentSubMenuId, $traversalHistory) {
            if (!$this->isValidRuleConfig($rule)) {
                return false;
            }

            $direction = $this->determineDirection($rule, $currentSubMenuId);
            if (!$direction) {
                return false;
            }

            if ($this->shouldBlockTraversal($ticket, $rule, $direction, $traversalHistory)) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Resolves valid candidate submenus for a reverse transaction starting from a given ticket.
     * Candidate must:
     * 1. Have relationship.is_bidirectional = true (berlaku untuk semua kombinasi is_conditional)
     * 2. Not be the incoming edge rule
     * 3. Target must NOT already be an ancestor in the lineage
     */
    public function resolveCandidatesForReverse(TicketHead $ticket): Collection
    {
        $currentSubMenuId = (string) $ticket->ticket_sub_menu_id;

        $rules = TicketDependencyRule::active()
            ->where('is_bidirectional', true)
            ->where(function ($q) use ($currentSubMenuId) {
                $q->where('target_sub_menu_id', $currentSubMenuId)
                    ->orWhere('source_sub_menu_id', $currentSubMenuId);
            })
            ->get();

        $candidateSubMenuIds = collect();
        $traversalHistory = $this->buildTraversalHistory($ticket);

        foreach ($rules as $rule) {
            if (!$this->isValidRuleConfig($rule)) {
                continue;
            }

            $direction = $this->determineDirection($rule, $currentSubMenuId);
            if (!$direction) {
                continue;
            }

            if ($this->shouldBlockTraversal($ticket, $rule, $direction, $traversalHistory)) {
                continue;
            }

            $candidateId = $this->getTargetSubMenuId($rule, $currentSubMenuId);
            if (!$candidateId) {
                continue;
            }

            $candidateSubMenuIds->push($candidateId);
        }

        return $candidateSubMenuIds->unique()->values();
    }

    /**
     * Determines whether a dependency rule should fire based on condition answer.
     */
    public function shouldRuleFire(TicketDependencyRule $rule, bool $conditionAnswer): bool
    {
        return $conditionAnswer;
    }

    // =========================================================================
    // DIRECTION INHERITANCE — Lineage-based traversal direction detection
    // =========================================================================

    /**
     * Determines whether a ticket was generated via BACKWARD/REVERSE traversal
     * of its incoming dependency rule — using only existing schema fields.
     *
     * A ticket is "reverse-generated" when its own sub_menu equals the SOURCE
     * of its dependency_rule, meaning the rule was walked in reverse (target→source).
     *
     * Examples (Rule R2: CS→ARI, is_bidirectional=true):
     *   - Ticket sub_menu=CS,  dep_rule=R2  →  R2.source=CS == CS  →  TRUE  (ARI generated CS backward)
     *   - Ticket sub_menu=ARI, dep_rule=R2  →  R2.source=CS != ARI →  FALSE (CS generated ARI forward)
     *
     * Business implication:
     *   - REVERSE-generated ticket  → continues going backward  (includeBackward = true)
     *   - FORWARD-generated ticket  → continues going forward   (includeBackward = false)
     *
     * @param  TicketHead $ticket  The generated ticket to inspect.
     * @return bool  True if this ticket was created via a backward/reverse traversal.
     */
    public function wasGeneratedInReverse(TicketHead $ticket): bool
    {
        if (!$ticket->dependency_rule_id || !$ticket->ticket_sub_menu_id) {
            // Root ticket — has no incoming dependency edge.
            return false;
        }

        $rule = TicketDependencyRule::find($ticket->dependency_rule_id);
        if (!$rule) {
            return false;
        }

        $isReverse = (string) $rule->source_sub_menu_id === (string) $ticket->ticket_sub_menu_id;

        Log::debug('[DependencyEngine] wasGeneratedInReverse check', [
            'ticket_id'          => $ticket->id,
            'ticket_sub_menu_id' => $ticket->ticket_sub_menu_id,
            'dependency_rule_id' => $ticket->dependency_rule_id,
            'rule_source'        => $rule->source_sub_menu_id,
            'rule_target'        => $rule->target_sub_menu_id,
            'is_reverse'         => $isReverse,
        ]);

        return $isReverse;
    }

    /**
     * Derives the correct `includeBackward` flag for resolving checkers of a
     * generated ticket — purely from lineage data, without relying on user input.
     *
     * Rules:
     *   - Root ticket (dependency_rule_id = NULL): user decides via $userChoice.
     *   - BACKWARD-generated ticket: must continue backward  → true.
     *   - FORWARD-generated ticket:  must continue forward   → false.
     *
     * This prevents the situation where a user submitting a backward-generated
     * draft ticket incorrectly includes or excludes backward checkers because
     * the UI toggle was left in an unrelated state.
     *
     * @param  TicketHead $ticket     The generated (or root) ticket being submitted.
     * @param  bool       $userChoice The value the user sent (only respected for root tickets).
     * @return bool
     */
    public function deriveIncludeBackward(TicketHead $ticket, bool $userChoice = false): bool
    {
        if (!$ticket->dependency_rule_id) {
            // Root ticket — no incoming edge, respect the user's explicit selection.
            Log::debug('[DependencyEngine] deriveIncludeBackward: root ticket, using user choice', [
                'ticket_id'   => $ticket->id,
                'user_choice' => $userChoice,
            ]);
            return $userChoice;
        }

        $derived = $this->wasGeneratedInReverse($ticket);

        Log::debug('[DependencyEngine] deriveIncludeBackward: generated ticket, derived from lineage', [
            'ticket_id'          => $ticket->id,
            'dependency_rule_id' => $ticket->dependency_rule_id,
            'derived_backward'   => $derived,
            'user_choice_ignored' => $userChoice,
        ]);

        return $derived;
    }
}

