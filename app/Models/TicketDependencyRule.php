<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketDependencyRule extends Model
{
    protected $table = 'ticket_dependency_rule';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'source_sub_menu_id',
        'target_sub_menu_id',
        'condition_key',
        'is_conditional',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'is_bidirectional',
        'description',
        'is_mandatory_forward',
        'is_cross_environment',
        'forward_checker_condition_id',
        'reverse_checker_condition_id',
        'forward_requestor_condition_id',
        'reverse_requestor_condition_id',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'is_bidirectional'     => 'boolean',
        'is_conditional'       => 'boolean',
        'is_mandatory_forward' => 'boolean',
        'is_cross_environment' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function rulesForSource(int $sourceSubMenuId): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->where('source_sub_menu_id', $sourceSubMenuId)
            ->get();
    }

    public static function rulesForSubmenu(string $submenuId): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->where(function ($q) use ($submenuId) {
                $q->where('source_sub_menu_id', $submenuId)
                    ->orWhere(function ($q2) use ($submenuId) {
                        $q2->where('target_sub_menu_id', $submenuId)
                            ->where('is_bidirectional', true);
                    });
            })
            ->get()
            ->map(function ($rule) use ($submenuId) {
                if ($rule->target_sub_menu_id == $submenuId) {
                    // ── Clone rule, jangan mutasi objek asli ──
                    // In-place swap ($rule->source = $rule->target, dst.) akan
                    // merusak properti Eloquent model jika instance yang sama
                    // dipakai di DependencyEngine::determineDirection() atau
                    // tempat lain dalam request yang sama, sehingga direction
                    // menjadi keliru. Clone memastikan objek asli tidak berubah.
                    $reversed                    = clone $rule;
                    $reversed->source_sub_menu_id = $rule->target_sub_menu_id;
                    $reversed->target_sub_menu_id = $rule->source_sub_menu_id;
                    return $reversed;
                }
                return $rule;
            });
    }


}
