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
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

         if ($validator->fails()){
            return response()->json(['status'=>false,'message'=>$validator->errors()],404);     }
        $customer = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'CUSTOMER',
            'password' => bcrypt($request->password),
        ]);
        $customer->save();

        return response()->json([
            'status'=>'success',
            'message' => 'Customer create successfully', 'customer' => $customer], 201);
    }

    public function deleteUser(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
        ]);

        if($validated->fails()){
            return response()->json(['status'=>false,'message'=>$validated->errors(),401]);
        }
        $user = User::findOrFail('user_id');
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    public function SoftDeletedUsers()
    {
        $deletedUsers = User::onlyTrashed()->get();

        return response()->json(['message' => $deletedUsers], 200);
    }

}
