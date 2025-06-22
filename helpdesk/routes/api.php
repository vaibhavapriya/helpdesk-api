<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\MailconfigController;
use App\Http\Controllers\Api\ErrorlogsController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\LangController;

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

Route::post('/oauth/token', function (ServerRequestInterface $request) {
    return app(AccessTokenController::class)->issueToken($request);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::group(['middleware'=>'guest.api'],function () {
    Route::post('/login',[AuthController::class,'login'])->name('login-p');//done
    Route::post('/register',[AuthController::class,'register'])->name('register-p');//done
    Route::post('/forgot-password', [AuthController::class,'forgotP']);
    Route::post('/reset-password', [AuthController::class,'resetP']) ;
});
//accessToken
Route::group(['middleware'=>'auth:api'],function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile/{id}',[ProfileController::class,'show'])->name('profile');//done
    Route::put('/profile/{id}/update',[ProfileController::class,'update'])->name('profile-p');//d without image
    Route::put('/profile/{id}/updatePassword',[AuthController::class,'updatePassword'])->name('profile-p');
    Route::get('/mytickets',[TicketController::class,'index']);//done
    Route::post('/mytickets',[TicketController::class,'store']);//done
    Route::get('/tickets/{id}',[TicketController::class,'show']);//done//with replier name
    Route::put('/tickets/{id}/update',[TicketController::class,'update']);//done
    Route::delete('/tickets/{id}/delete',[TicketController::class,'destroy']);//done
    Route::post('/tickets/{ticket}/comment',[ReplyController::class,'store'])->name('comment');//done
});

Route::group(['prefix'=>'admin','middleware'=>['auth:api','role:admin']],function () {//role middleware
    Route::post('/tickets',[TicketController::class,'storeAdmin']);//done
    Route::get('/tickets',[TicketController::class,'indexAdmin']);//done add querytring
    Route::get('/useridemail',[ProfileController::class,'getUsersIdAndEmail']);//done
    Route::get('/profiles',[ProfileController::class,'index']);//done
    Route::prefix('profiles')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('/post', [ProfileController::class, 'store']);
        Route::delete('/delete/{id}', [ProfileController::class, 'destroy']);
    });
    Route::prefix('mails')->group(function () {
        Route::get('/', [MailconfigController::class, 'index']);
        Route::post('/post', [MailconfigController::class, 'store']);
        Route::delete('/delete/{id}', [MailconfigController::class, 'destroy']);
        Route::patch('/{id}', [MailconfigController::class, 'update']);
    });
    Route::get('/errorlogs',[ErrorlogsController::class,'index']);//done
    Route::get('/queue-driver', [QueueController::class, 'getDriver']);
    Route::post('/queue-driver', [QueueController::class, 'setDriver']);
        // Cache driver retrieval and update
    Route::get('cache-driver', [CacheController::class, 'getDriver']);
    Route::post('cache-driver', [CacheController::class, 'setDriver']);
});
Route::group(['middleware'=>['web','lang']],function () {
    Route::get('/faq', [LangController::class, 'getFaq']);
    Route::post('/locale', [LangController::class, 'setLocale']);
    Route::get('/newticket', [LangController::class, 'getNewticket']);
    Route::get('/tickets', [LangController::class, 'getTickets']);
    Route::get('/editticket', [LangController::class, 'getEditTranslations']);
    Route::get('/showticket', [LangController::class, 'getViewTranslations']);
    Route::get('/profiletranslation', [LangController::class, 'getProfileTranslations']);

});


