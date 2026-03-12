<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRepositoryCategory extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'created_by',
    ];

    public function documents()
    {
        return $this->hasMany(DocumentRepository::class, 'document_repository_category_id');
    }
}
