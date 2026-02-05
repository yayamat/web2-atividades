<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BooksControllerApi extends Controller
{
    public function index()
    {
        return Book::all();
    }  

    public function show($id)
    {
        return Book::findOrFail($id);
    }   

    public function store(Request $request)
    {
        $path = null;

        // trata a imagem
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('books', 'public');
        }

        // cria o livro
        $book = Book::create([
            'title'        => $request->title,
            'publisher_id' => $request->publisher_id,
            'author_id'    => $request->author_id,
            'category_id'  => $request->category_id,
            'cover'        => $path
        ]);

        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // se veio nova imagem
        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('books', 'public');
            $book->cover = $path;
        }

        // atualiza os outros campos
        $book->update($request->except('cover'));

        return response()->json($book, 200);
    }   

    public function destroy($id)
    {
        Book::destroy($id);
        return response()->json(['message' => 'Livro deletado com sucesso'], 204);
    }
}
