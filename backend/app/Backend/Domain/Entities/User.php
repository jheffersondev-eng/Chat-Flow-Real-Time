<?php

namespace Backend\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'oauth_provider',
        'oauth_provider_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship: Conversations this user participates in
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps()
            ->orderBy('updated_at', 'desc');
    }

    // Relationship: Messages sent by this user
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relationship: Friendships where this user sent the request
    public function friendRequestsSent()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    // Relationship: Friendships where this user received the request
    public function friendRequestsReceived()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    // Get all friends (accepted)
    public function friends()
    {
        $sentFriends = $this->friendRequestsSent()
            ->where('status', 'accepted')
            ->with('friend')
            ->get()
            ->pluck('friend');

        $receivedFriends = $this->friendRequestsReceived()
            ->where('status', 'accepted')
            ->with('user')
            ->get()
            ->pluck('user');

        return $sentFriends->merge($receivedFriends);
    }

    // Check if users are friends
    public function isFriendsWith($userId)
    {
        return Friendship::where(function ($query) use ($userId) {
            $query->where('user_id', $this->id)
                  ->where('friend_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('friend_id', $this->id);
        })->where('status', 'accepted')->exists();
    }

    // Check if a friend request is pending
    public function hasPendingRequestWith($userId)
    {
        return Friendship::where(function ($query) use ($userId) {
            $query->where('user_id', $this->id)
                  ->where('friend_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('friend_id', $this->id);
        })->where('status', 'pending')->exists();
    }

    // Search users
    public static function search($term)
    {
        return static::where('name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();
    }
}
