<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BooksControllerApi;

Route::apiResource('books', BooksControllerApi::class);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('books', BooksControllerApi::class);


/*
|--------------------------------------------------------------------------
| Rotas da API - Books
|--------------------------------------------------------------------------
*/

Route::get('/books', [BooksControllerApi::class, 'index']);
Route::get('/books/{id}', [BooksControllerApi::class, 'show']);
Route::post('/books', [BooksControllerApi::class, 'store']);
Route::put('/books/{id}', [BooksControllerApi::class, 'update']);
Route::delete('/books/{id}', [BooksControllerApi::class, 'destroy']);
