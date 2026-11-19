<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArtikel extends Model
{
    protected $table = 'knowledge_base_artikel';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'kba_kategori_id',
        'kba_title',
        'kba_slug',
        'kba_konten',
        'kba_status',
        'kba_tags',
        'kba_view_count',
        'kba_access_override',
        'created_by',
        'created_at',
        'published_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'kba_tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(KnowledgeBaseKategori::class, 'kba_kategori_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(KnowledgeBaseAttachment::class, 'kba_artikel_id');
    }

    public function allowedDepartments()
    {
        return $this->belongsToMany(
            Departemen::class,
            'knowledge_base_artikel_access',
            'kba_artikel_id',
            'departemen_id'
        );
    }

    public function isAccessibleByDepartment($departmentIds): bool
    {
        if ($this->kba_access_override === 'public') {
            return true;
        }
        if ($this->kba_access_override === 'restricted') {
            $departmentIds = array_filter(
                is_array($departmentIds) ? $departmentIds : [$departmentIds]
            );
            if (empty($departmentIds)) {
                return false;
            }
            return $this->allowedDepartments()
                ->whereIn('departemen_id', $departmentIds)
                ->exists();
        }
        return $this->category?->isAccessibleByDepartment($departmentIds) ?? true;
    }
}
