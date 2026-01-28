<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use App\Jobs\ProcessBotResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    // Get all user conversations
    public function index()
    {
        $user = Auth::user();
        
        $conversations = $user->conversations()
            ->with(['participants', 'latestMessage.user'])
            ->get()
            ->map(function ($conversation) use ($user) {
                // Get other participant(s)
                $otherParticipants = $conversation->participants
                    ->where('id', '!=', $user->id);

                return [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'name' => $conversation->type === 'private' 
                        ? $otherParticipants->first()->name ?? 'Unknown'
                        : $conversation->name,
                    'participants' => $otherParticipants->values(),
                    'latest_message' => $conversation->latestMessage,
                    'unread_count' => $conversation->getUnreadCountFor($user->id),
                    'updated_at' => $conversation->updated_at,
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    // Get or create conversation with friend
    public function getOrCreate(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        // Check if conversation already exists
        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('participants', function ($query) use ($request) {
                $query->where('user_id', $request->friend_id);
            })
            ->first();

        if (!$conversation) {
            // Create new conversation
            $conversation = DB::transaction(function () use ($user, $request) {
                $conv = Conversation::create(['type' => 'private']);
                $conv->participants()->attach([$user->id, $request->friend_id]);
                return $conv;
            });
        }

        return response()->json([
            'conversation' => $conversation->load('participants', 'latestMessage')
        ]);
    }

    // Get single conversation
    public function show($id)
    {
        $user = Auth::user();
        
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['participants', 'latestMessage'])->findOrFail($id);

        // Get conversation name based on type
        if ($conversation->type === 'private') {
            $otherParticipant = $conversation->participants->where('id', '!=', $user->id)->first();
            $conversation->display_name = $otherParticipant ? $otherParticipant->name : 'Unknown';
        } else {
            $conversation->display_name = $conversation->name;
        }

        return response()->json(['conversation' => $conversation]);
    }

    // Get messages for a conversation
    public function getMessages($id)
    {
        $user = Auth::user();
        
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        $conversation->participants()
            ->updateExistingPivot($user->id, ['last_read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    // Send message
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => 'user',
        ]);

        // Update conversation timestamp
        $conversation->touch();

        // Broadcast message
        broadcast(new MessageSent($message->load('user'), $conversation->id));

        // If AI conversation, dispatch bot response
        if ($conversation->type === 'ai') {
            $conversationHistory = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->reverse()
                ->map(fn($m) => ['role' => $m->type === 'bot' ? 'assistant' : 'user', 'content' => $m->content])
                ->toArray();

            ProcessBotResponse::dispatch(
                $conversation->id,
                $request->content,
                $user,
                $conversationHistory
            );
        }

        return response()->json(['message' => $message]);
    }

    // Create AI conversation
    public function createAIChat()
    {
        $user = Auth::user();

        // Check if there's already an empty AI chat
        $existingEmptyChat = Conversation::where('type', 'ai')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('messages')
            ->first();

        if ($existingEmptyChat) {
            return response()->json(['conversation' => $existingEmptyChat]);
        }

        $conversation = DB::transaction(function () use ($user) {
            $conv = Conversation::create([
                'type' => 'ai',
                'name' => 'AI Assistant'
            ]);
            $conv->participants()->attach($user->id);
            return $conv;
        });

        return response()->json(['conversation' => $conversation]);
    }

    // Delete conversation
    public function destroy($id)
    {
        $user = Auth::user();
        
        $conversation = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        // Delete all messages
        $conversation->messages()->delete();
        
        // Detach participants
        $conversation->participants()->detach();
        
        // Delete conversation
        $conversation->delete();

        return response()->json(['message' => 'Conversa excluída com sucesso']);
    }
}
