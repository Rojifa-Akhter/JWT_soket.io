<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class roleController extends Controller
{
    //admin
    public function customerCreate(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $customer = User::create([
            'name' => $validator['name'],
            'email' => $validator['email'],
            'role' => 'customer',
            'password' => bcrypt($validator['password']),
        ]);
        $customer->save();

        return response()->json(['message' => 'Customer create successfully', 'customer' => $customer], 201);
    }

    public function deleteUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    public function SoftDeletedUsers()
    {
        $deletedUsers = User::onlyTrashed()->get();

        return response()->json(['message' => $deletedUsers], 200);
    }
    
}
