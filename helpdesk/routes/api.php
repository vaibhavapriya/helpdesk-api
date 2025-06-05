<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\ProfileController;
// use App\Http\Controllers\Api\TicketController;
// use App\Http\Controllers\Api\ReplyController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login'])->name('login-p');
Route::post('/register',[AuthController::class,'register'])->name('register-p');
Route::post('/forgot-password', [AuthController::class,'forgotP']);
Route::post('/reset-password/{token}', [AuthController::class,'resetP']) ;

Route::group(['middleware'=>'auth:sanctum'],function () {
    Route::get('/profile',[ProfileController::class,'show'])->name('profile');//'auth.login'
    Route::post('/profile/{id}',[ProfileController::class,'store'])->name('profile-p');
    Route::get('/tickets',[TicketController::class,'index']);
    Route::get('/tickets/{id}',[TicketController::class,'show']);
    Route::put('/tickets/{id}/update',[TicketController::class,'update']);
    Route::delete('/tickets/{id}/delete',[TicketController::class,'delete']);
    Route::post('/ticket/{ticket}/comment',[ReplyController::class,'store'])->name('comment');

});



//clienthome,kb,auth(login register forgotpassword resetpassword ),tickets(store,update, delete),profile(update)
