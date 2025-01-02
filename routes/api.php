<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ChatController;
use App\Models\PushSubscription;
use Illuminate\Foundation\Auth\EmailVerificationNotifiable;


Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('resendOtp', [AuthController::class, 'resendOtp']);
    Route::post('logout', [AuthController::class, 'logout']);

});

Route::middleware(['auth:api','CheckAdmin'])->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
// Admins
Route::middleware(['auth:api', 'ADMIN'])->group(function () {
    Route::put('/approved/{id}', [ProductController::class, 'approve']);
    Route::put('/reject/{id}', [ProductController::class, 'reject']);
    Route::post('/customerCreate', [RoleController::class, 'customerCreate']);
    Route::delete('/deleteUser', [RoleController::class, 'deleteUser']);
    Route::get('/showSoftDeletedUsers', [RoleController::class, 'SoftDeletedUsers']);
});

// Customers
Route::middleware(['auth:api', 'customer'])->group(function () {
    Route::get('/view', [ProductController::class, 'view']);
    Route::post('/addProject', [CustomerController::class, 'addProject']);
    Route::put('/editProject/{id}', [CustomerController::class, 'editProject']);
    Route::delete('/deleteProject/{id}', [CustomerController::class, 'deleteProject']);
    Route::post('/{projectId}/addEvent', [CustomerController::class, 'addEvent']);
    Route::put('/{projectId}/editEvent/{id}', [CustomerController::class, 'editEvent']);
    Route::delete('/deleteEvent/{id}', [CustomerController::class, 'deleteEvent']);
});

// Users
Route::middleware(['auth:api', 'user'])->group(function () {
    Route::post('/create', [ProductController::class, 'create']);
    Route::get('/index', [ProductController::class, 'index']);
    Route::get('/projectView', [ProductController::class, 'project']);
    Route::get('/eventView', [ProductController::class, 'event']);

});
Route::middleware(['auth:api'])->group(function () {
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/show', [ChatController::class, 'show']);
    Route::get('/receive/{id}', [ChatController::class, 'receive']);
    Route::post('/connected_users', [ChatController::class, 'connect']);
    Route::delete('/connected_users/{socket_id}', [ChatController::class, 'disconnect']);

});

Route::post("push-subscribe", function (Request $request){
    PushSubscription::create([
        'data'=>$request->getContent()
    ]);
});


