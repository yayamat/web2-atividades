<?php

use App\Http\Controllers\MusicController;   
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('music', MusicController::class);   