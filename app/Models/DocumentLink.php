<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentLink extends Model
{
    protected $fillable = [
        'document_repository_id',
        'linkable_type',
        'linkable_id',
        'relation_type',
        'linked_by',
        'created_by',
    ];

    public function documentRepository()
    {
        return $this->belongsTo(DocumentRepository::class);
    }

    public function linkable()
    {
        return $this->morphTo();
    }
}
