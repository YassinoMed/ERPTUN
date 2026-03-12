<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseCategory extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'created_by',
    ];

    public function articles()
    {
        return $this->hasMany(KnowledgeBaseArticle::class, 'knowledge_base_category_id');
    }
}
