<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// =======================
// CATEGORIES
// =======================
Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class)
        ->middleware('can:manage-categories');
});

// =======================
// BOOKS
// =======================
Route::middleware(['auth'])->group(function () {

    // Criar com ID
    Route::get('/books/create-id-number', [BookController::class, 'createWithId'])
        ->name('books.create.id')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])
        ->name('books.store.id')
        ->middleware('can:create,App\Models\Book');

    // Criar com SELECT
    Route::get('/books/create-select', [BookController::class, 'createWithSelect'])
        ->name('books.create.select')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])
        ->name('books.store.select')
        ->middleware('can:create,App\Models\Book');

    // Restante controlado pela BookPolicy
    Route::resource('books', BookController::class)
        ->except(['create', 'store']);
});

// =======================
// AUTHORS & PUBLISHERS
// =======================
Route::middleware(['auth'])->group(function () {
    Route::resource('authors', AuthorController::class)
        ->middleware('can:manage-authors');

    Route::resource('publishers', PublisherController::class)
        ->middleware('can:manage-publishers');
});

// =======================
// USERS (ADMIN / BIBLIOTECARIO)
// =======================
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});

// =======================
// BORROWING
// =======================
Route::middleware(['auth'])->group(function () {

    Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])
        ->name('books.borrow');

    Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])
        ->name('users.borrowings');

   Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])
    ->name('borrowings.return');

});
