<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_repository_id',
        'version_label',
        'file_name',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function documentRepository()
    {
        return $this->belongsTo(DocumentRepository::class);
    }
}
