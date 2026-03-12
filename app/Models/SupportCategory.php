<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function supports()
    {
        return $this->hasMany(Support::class, 'support_category_id');
    }
}
