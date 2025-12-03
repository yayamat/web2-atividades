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

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class)
        ->middleware('can:manage-categories');
});

Route::middleware(['auth'])->group(function () {
    // rotas de criação: usam diretamente a BookPolicy::create
    Route::get('/books/create-id-number', [BookController::class, 'createWithId'])
        ->name('books.create.id')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])
        ->name('books.store.id')
        ->middleware('can:create,App\Models\Book');

    Route::get('/books/create-select', [BookController::class, 'createWithSelect'])
        ->name('books.create.select')
        ->middleware('can:create,App\Models\Book');

    Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])
        ->name('books.store.select')
        ->middleware('can:create,App\Models\Book');

    // Rotas RESTful para index, show, edit, update, delete
    // authorizeResource no controller vai checar view/update/delete etc.
    Route::resource('books', BookController::class)
        ->except(['create', 'store']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('authors', AuthorController::class)
        ->middleware('can:manage-authors');

    Route::resource('publishers', PublisherController::class)
        ->middleware('can:manage-publishers');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:manage-users');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:manage-users');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('can:manage-users');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])
        ->name('books.borrow');
       // ->middleware('can:borrow-books');

    Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])
        ->name('users.borrowings');

    Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])
        ->name('borrowings.return')
        ->middleware('can:return-books');
});
