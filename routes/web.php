<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
// Auth::routes(['verify' => true]);
// Route::get('/dashboard', function () {
//     // Only accessible by verified users
// })->middleware(['auth', 'verified']);
Route::get('/show', [ChatController::class, 'show']);