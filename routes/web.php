<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
// Auth::routes(['verify' => true]);
// Route::get('/dashboard', function () {
//     // Only accessible by verified users
// })->middleware(['auth', 'verified']);
