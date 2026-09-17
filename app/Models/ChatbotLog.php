<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    protected $fillable = [
        'session_id',
        'question',
        'answer',
        'provider',
        'model',
        'ok',
        'error',
    ];

    protected function casts(): array
    {
        return ['ok' => 'boolean'];
    }
}
