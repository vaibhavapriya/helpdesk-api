<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TicketController;
// use App\Http\Controllers\Api\ReplyController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login'])->name('login-p');//done
Route::post('/register',[AuthController::class,'register'])->name('register-p');//done
Route::post('/forgot-password', [AuthController::class,'forgotP']);
Route::post('/reset-password/{token}', [AuthController::class,'resetP']) ;

Route::group(['middleware'=>'auth:sanctum'],function () {
    Route::get('/profile/{id}',[ProfileController::class,'show'])->name('profile');//'auth.login'
    Route::put('/profile/{id}',[ProfileController::class,'update'])->name('profile-p');
    Route::get('/mytickets',[TicketController::class,'index']);//done
    Route::post('/mytickets',[TicketController::class,'store']);
    Route::get('/tickets/{id}',[TicketController::class,'show']);
    Route::put('/tickets/{id}/update',[TicketController::class,'update']);
    Route::delete('/tickets/{id}/delete',[TicketController::class,'delete']);
    Route::post('/ticket/{ticket}/comment',[ReplyController::class,'store'])->name('comment');
});

Route::group(['prefix'=>'admin','middleware'=>['auth:sanctum','role:admin']],function () {//role middleware
    Route::post('/tickets',[TicketController::class,'storeAdmin']);
    Route::get('/tickets',[TicketController::class,'indexAdmin']);
    Route::get('/useridemail',[UserController::class,'storeAdmin']);//show,index
    Route::get('/profiles',[ProfileController::class,'index']);
});




//clienthome,kb,auth(login register forgotpassword resetpassword ),tickets(store,update, delete),profile(update)
