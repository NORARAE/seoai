<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatLog extends Model
{
    protected $fillable = [
        'session_id',
        'scan_id',
        'domain',
        'user_message',
        'ai_response',
        'intent',
        'context_page',
    ];

    public const UPDATED_AT = null;
}
