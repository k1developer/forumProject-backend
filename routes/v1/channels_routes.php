<?php

use Illuminate\Support\Facades\Route;


Route::resource('channels', 'App\Http\Controllers\API\v1\Channel\ChannelController');
