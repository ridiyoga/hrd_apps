<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseKategori extends Model
{
    protected $table = 'knowledge_base_kategori';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'kb_parent_id',
        'kb_name',
        'kb_slug',
        'kb_deskripsi',
        'kb_sort_order',
        'kb_icon',
        'kb_is_active',
        'kb_access_type',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function parent()
    {
        return $this->belongsTo(KnowledgeBaseKategori::class, 'kb_parent_id');
    }

    public function children()
    {
        return $this->hasMany(KnowledgeBaseKategori::class, 'kb_parent_id')->orderBy('kb_sort_order', 'asc')->with('children');
    }

    public function child_kategori()
    {
        return $this->hasMany(KnowledgeBaseKategori::class, 'kb_parent_id');
    }

    public function artikel()
    {
        return $this->hasMany(KnowledgeBaseArtikel::class, 'kba_kategori_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allowedDepartments()
    {
        return $this->belongsToMany(
            Departemen::class,
            'knowledge_base_kategori_access',
            'kb_kategori_id',
            'departemen_id'
        );
    }

    public function ancestorChain(): array
    {
        $chain = [];
        $current = $this;
        while ($current) {
            $chain[] = $current;
            $current = $current->kb_parent_id
                ? self::find($current->kb_parent_id)
                : null;
        }
        return array_reverse($chain);   
    }

    public function isAccessibleByDepartment($departmentIds)
    {
        $departmentIds = array_filter(
            is_array($departmentIds) ? $departmentIds : [$departmentIds]
        );

        $chain = $this->ancestorChain();
        foreach (array_reverse($chain) as $kategori) {
            if ($kategori->kb_access_type === 'restricted') {
                if (empty($departmentIds)) {
                    return false;
                }
                return $kategori->allowedDepartments()
                    ->whereIn('departemen_id', $departmentIds)
                    ->exists();
            }
        }
        return true;
    }
}
