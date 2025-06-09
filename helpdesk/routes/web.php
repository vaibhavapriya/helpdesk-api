<?php

use Illuminate\Support\Facades\Route;

// clienthome,kb,auth(login register forgotpassword resetpassword ),tickets(index,create,show,edit),profile(show,edit),errorlogs(view)

Route::view('/','clienthome')->name('home');
Route::view('/admin','adminhome')->name('adminhome');
Route::view('/knowledgebase','guest.knowledge')->name('kb');


Route::view('/register','guest.register')->name('register');
Route::view('/login','guest.login')->name('login');
Route::view('/forgotpassword','guest.forgotPassword')->name('fp');
Route::view('/resetpassword','guest.resetPassword')->name('rp');
//Route::get('/resetpassword',[ViewController::resetPassword])->name('rp');

Route::group(['prefix'=>'myProfile'],function () {
    Route::view('/','user.editprofile');
    Route::view('/edit','user.editprofile');
});

Route::group(['prefix'=>'tickets'],function () {
    Route::view('/','ticket.index')->name('tickets');;
    Route::view('/create','ticket.create')->name('ticket');
    Route::get('/{id}',function($id){
        return view('ticket.show',compact('id'));
    });
    Route::get('/{id}/edit',function($id){
        return view('ticket.edit',compact('id'));
    });
});

Route::get('/errortest',function(){
    throw new \Exception("Something went wrong!");
});