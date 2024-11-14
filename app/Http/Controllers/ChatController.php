<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\connectedUser;
use Illuminate\Support\Js;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message]);
    }

    public function show(Request $request)
    {
        $connectedUsers = ConnectedUser::select('user_id')
            ->with('user')
            ->where('user_id', '!=', auth()->user()->id)
            ->groupBy('user_id')
            ->get();

        $messages = Message::orderBy('created_at', 'asc')->get();

        return response()->json([
            'connected_users' => $connectedUsers,  
            'messages' => $messages,
        ]);
    }

    public function receive(Request $request, $id)
    {
        $currentUserId = auth()->id();

        $connectedUsers = ConnectedUser::with('user')
            ->where('user_id', '!=', auth()->user()->id)
            ->get()
            ->unique('user_id');

        $messages = Message::where(function ($query) use ($currentUserId, $id) {
            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $id);
        })
            ->orWhere(function ($query) use ($currentUserId, $id) {
                $query->where('sender_id', $id)
                    ->where('receiver_id', $currentUserId);
            })
            ->orderBy('created_at', 'asc')
            ->with('sender')
            ->get();

        // Return messages along with sender details
        return response()->json([
            'message' => 'Messages fetched successfully',
            'data' => $messages
        ]);
    }

    public function call(Request $request)
    {
        return view('views.call');
    }
    //for socket connection
    public function connect(Request $request)
    {
        ConnectedUser::create([
            'user_id' => Auth::id(),
            'socket_id' => $request->socket_id,
        ]);

        return response()->json(['message' => 'User connected'], 200);
    }
    public function disconnect($socket_id)
    {
        ConnectedUser::where('socket_id', $socket_id)->delete();

        return response()->json(['message' => 'User disconnected'], 204);
    }
}
