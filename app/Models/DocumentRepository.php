<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRepository extends Model
{
    protected $fillable = [
        'document_repository_category_id',
        'title',
        'reference',
        'version',
        'status',
        'document',
        'description',
        'effective_date',
        'expires_at',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'approved' => 'Approved',
        'archived' => 'Archived',
    ];

    public function category()
    {
        return $this->hasOne(DocumentRepositoryCategory::class, 'id', 'document_repository_category_id');
    }

    public function links()
    {
        return $this->hasMany(DocumentLink::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->latest('id');
    }
}
