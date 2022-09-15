<?php

use App\Http\Controllers\API\v1\Thread\SubscribeController;
use App\Http\Controllers\API\v1\Thread\ThreadController;
use Illuminate\Support\Facades\Route;


Route::resource('threads', 'App\Http\Controllers\API\v1\Thread\ThreadController');

Route::prefix('/threads')->group(function () {
    Route::resource('answers', 'App\Http\Controllers\API\v1\Thread\AnswerController');

    Route::post('/{thread}/subscribe', [SubscribeController::class, 'subscribe'])->name('subscribe');
    Route::post('/{thread}/unsubscribe', [SubscribeController::class, 'unSubscribe'])->name('unSubscribe');
});
