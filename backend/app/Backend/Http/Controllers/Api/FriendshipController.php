<?php

namespace Backend\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Backend\Application\UseCases\AcceptFriendRequestUseCase;
use Backend\Domain\Repositories\FriendshipRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    public function __construct(
        private FriendshipRepositoryInterface $friendshipRepository,
        private AcceptFriendRequestUseCase $acceptFriendRequest
    ) {}

    // Send friend request
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        // Check if already friends or request exists
        if ($this->friendshipRepository->exists($user->id, $request->friend_id)) {
            return response()->json(['message' => 'Friendship already exists'], 400);
        }

        $friendship = $this->friendshipRepository->create($user->id, $request->friend_id);

        return response()->json([
            'message' => 'Friend request sent',
            'friendship' => $friendship->load('friend'),
        ]);
    }

    // Accept friend request
    public function acceptRequest($id)
    {
        $user = Auth::user();
        
        try {
            $result = $this->acceptFriendRequest->execute($id, $user->id);

            return response()->json([
                'message' => 'Friend request accepted',
                'friendship' => $result['friendship'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // Reject friend request
    public function rejectRequest($id)
    {
        $user = Auth::user();
        $friendship = $this->friendshipRepository->findById($id);

        if (!$friendship || $friendship->friend_id !== $user->id || $friendship->status !== 'pending') {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $this->friendshipRepository->delete($id);

        return response()->json(['message' => 'Friend request rejected']);
    }

    // Remove friend
    public function removeFriend($friendId)
    {
        $user = Auth::user();
        $this->friendshipRepository->deleteBetween($user->id, $friendId);

        return response()->json(['message' => 'Friend removed']);
    }

    // Get pending requests
    public function pending()
    {
        $user = Auth::user();
        $requests = $this->friendshipRepository->getPendingRequests($user->id);

        return response()->json(['requests' => $requests]);
    }

    // Get friends
    public function friends()
    {
        $user = Auth::user();
        $friends = $this->friendshipRepository->getFriends($user->id);

        return response()->json(['friends' => $friends]);
    }
}
