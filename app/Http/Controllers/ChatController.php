<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'recipient' => 'required|string|exists:users,email', // ensure recipient exists
            'message' => 'required|string',
        ]);

        $recipient = $validated['recipient'];
        $message = $validated['message'];

        return response()->json(['message' => 'Message sent successfully']);
    }

    public function receiveMessage(Request $request)
    {
        return response()->json(['message' => 'Message received']);
    }
}
