<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = [
        'knowledge_base_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'status',
        'is_featured',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ];

    public function category()
    {
        return $this->hasOne(KnowledgeBaseCategory::class, 'id', 'knowledge_base_category_id');
    }
}
