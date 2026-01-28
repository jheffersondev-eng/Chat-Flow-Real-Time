<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    // Send friend request
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        // Check if already friends or request exists
        $existing = Friendship::where(function ($query) use ($user, $request) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $request->friend_id);
        })->orWhere(function ($query) use ($user, $request) {
            $query->where('user_id', $request->friend_id)
                  ->where('friend_id', $user->id);
        })->first();

        if ($existing) {
            return response()->json(['message' => 'Friendship already exists'], 400);
        }

        $friendship = Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $request->friend_id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Friend request sent',
            'friendship' => $friendship->load('friend'),
        ]);
    }

    // Accept friend request
    public function acceptRequest($id)
    {
        $user = Auth::user();
        $friendship = Friendship::where('id', $id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($friendship, $user) {
            // Accept friendship
            $friendship->update(['status' => 'accepted']);

            // Create private conversation
            $conversation = Conversation::create(['type' => 'private']);
            $conversation->participants()->attach([$user->id, $friendship->user_id]);
        });

        return response()->json([
            'message' => 'Friend request accepted',
            'friendship' => $friendship->load('user'),
        ]);
    }

    // Reject friend request
    public function rejectRequest($id)
    {
        $user = Auth::user();
        $friendship = Friendship::where('id', $id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->delete();

        return response()->json(['message' => 'Friend request rejected']);
    }

    // Remove friend
    public function removeFriend($friendId)
    {
        $user = Auth::user();
        
        Friendship::where(function ($query) use ($user, $friendId) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($user, $friendId) {
            $query->where('user_id', $friendId)
                  ->where('friend_id', $user->id);
        })->delete();

        return response()->json(['message' => 'Friend removed']);
    }

    // Get all friends
    public function index()
    {
        $user = Auth::user();
        $friends = $user->friends();

        return response()->json(['friends' => $friends]);
    }

    // Get pending requests
    public function pending()
    {
        $user = Auth::user();
        $requests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return response()->json(['requests' => $requests]);
    }
}
