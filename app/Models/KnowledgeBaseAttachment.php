<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseAttachment extends Model
{
    protected $table = 'knowledge_base_attachment';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kba_artikel_id',
        'kba_file_name',
        'kba_original_name',
        'kba_file_path',
        'kba_file_extension',
        'kba_file_size',
        'kba_mime_type',
        'kba_description',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at'
    ];

    public function artikel()
    {
        return $this->belongsTo(KnowledgeBaseArtikel::class, 'kba_artikel_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
