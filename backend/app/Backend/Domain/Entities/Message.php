<?php

namespace Backend\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['user'];

    // Relationship: The conversation this message belongs to
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Relationship: The user who sent the message
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUserMessages($query)
    {
        return $query->where('type', 'user');
    }

    public function scopeBotMessages($query)
    {
        return $query->where('type', 'bot');
    }
}
