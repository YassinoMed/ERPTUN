<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ChatGptModel extends Model
{
    protected $connection = 'chatgpt';
}
