<?php

namespace Backend\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: The user who sent the request
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: The user who received the request
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }
}
