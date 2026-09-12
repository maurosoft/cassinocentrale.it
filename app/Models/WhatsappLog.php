<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'provider',
        'to',
        'message',
        'status',
        'response',
    ];
}
