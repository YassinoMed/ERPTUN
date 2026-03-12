<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends ChatGptModel
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'prompt',
        'field_json',
        'is_tone',
    ];

}
