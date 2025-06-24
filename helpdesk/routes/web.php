<?php

use Illuminate\Support\Facades\Route;

// clienthome,kb,auth(login register forgotpassword resetpassword ),tickets(index,create,show,edit),profile(show,edit),errorlogs(view)

Route::view('/','clienthome')->name('home');
Route::view('/knowledgebase','guest.knowledge')->name('kb');

Route::view('/register','guest.register')->name('register');
Route::view('/login','guest.login')->name('login');
Route::view('/forgotpassword','guest.forgotPassword')->name('fp');
Route::view('/resetpassword','guest.resetPassword')->name('rp');
//Route::get('/resetpassword',[ViewController::resetPassword])->name('rp');

Route::group(['prefix'=>'myProfile'],function () {
    Route::view('/','user.editprofile');
    Route::view('/edit','user.editprofile');
    // Route::get('/{id}',function($id){
    //     return view('user.editprofile',compact('id'));
    // });
});

Route::group(['prefix'=>'tickets'],function () {
    Route::view('/','ticket.index')->name('tickets');
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

Route::group(['prefix'=>'admin'],function () {
    Route::view('/','adminhome')->name('adminhome');
    Route::group(['prefix'=>'tickets'],function () {
        Route::view('/','admin.tickets')->name('atickets');
        Route::view('/create','admin.newtickets')->name('aticket');
        Route::get('/{id}',function($id){
            return view('admin.ticketshow',compact('id'));
        })->name('aticketshow');
        Route::get('/{id}/edit',function($id){
            return view('admin.ticketedit',compact('id'));
        })->name('aticketedit');
    });
    Route::view('/profiles','admin.profiles')->name('profiles');
    Route::view('/profile','admin.profile')->name('profile');
    Route::view('/newuser','admin.newUsers')->name('newuser');
    Route::view('/errorlogs','admin.errorlog')->name('errorlog');
    Route::view('/mailconfig','admin.mailconfig')->name('mail');
    Route::view('/cconfig','admin.cache')->name('cconfig');
    Route::view('/qconfig','admin.queue')->name('qconfig');
    Route::get('/profile/{id}', function ($id) {
        return view('admin.usershow', compact('id'));
    })->name('profile.show');
    //newUsers, deleteUsers(button), querys(index)
});