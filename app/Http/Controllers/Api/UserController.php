<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Search users by name or email
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $user = Auth::user();
        $searchQuery = $request->input('query');

        $users = User::where('id', '!=', $user->id)
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('email', 'LIKE', "%{$searchQuery}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($searchUser) use ($user) {
                return [
                    'id' => $searchUser->id,
                    'name' => $searchUser->name,
                    'email' => $searchUser->email,
                    'is_friend' => $user->isFriendsWith($searchUser->id),
                ];
            });

        return response()->json(['users' => $users]);
    }

    // Get current user profile
    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}
