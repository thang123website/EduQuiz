<?php

namespace App\Modules\Chatbot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BotConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'bot_code',
        'system_instruction',
        'temperature',
        'response_schema',
    ];

    protected $casts = [
        'response_schema' => 'array',
        'temperature' => 'float',
    ];

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }
}
