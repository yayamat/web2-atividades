<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Formulário com input de ID
    public function createWithId()
    {
        return view('books.create-id');
    }

    // Salvar livro com input de ID (campo de upload chamado 'cover')
    public function storeWithId(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $fileName = uniqid('book_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('books', $fileName, 'public'); // storage/app/public/books/...
            $data['cover'] = $path;
        } else {
            $data['cover'] = null;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Formulário com input select
    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select (campo 'cover')
    public function storeWithSelect(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $fileName = uniqid('book_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('books', $fileName, 'public');
            $data['cover'] = $path;
        } else {
            $data['cover'] = null;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'remove_cover' => 'nullable|in:1'
        ]);

        // Atualiza os campos normais
        $book->title = $request->title;
        $book->publisher_id = $request->publisher_id;
        $book->author_id = $request->author_id;
        $book->category_id = $request->category_id;

        // Se o usuário marcou para remover a capa atual
        if ($request->has('remove_cover') && $request->remove_cover == '1') {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = null;
        }

        // Se veio um novo arquivo, deletar a antiga (se existir) e salvar a nova com nome único
        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $file = $request->file('cover');
            $fileName = uniqid('book_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('books', $fileName, 'public');
            $book->cover = $path;
        }

        $book->save();

        return redirect()->route('books.show', $book)->with('success', 'Livro atualizado com sucesso.');
    }

    public function show(Book $book)
    {
        // Carregando autor, editora e categoria do livro com eager loading
        $book->load(['author', 'publisher', 'category']);

        // Carregar todos os usuários para o formulário de empréstimo
        $users = User::all();

        return view('books.show', compact('book', 'users'));
    }

    public function index()
    {
        // Carregar os livros com autores usando eager loading e paginação
        $books = Book::with('author')->paginate(20);

        return view('books.index', compact('books'));
    }

    public function destroy(Book $book)
    {
        // Deleta capa do storage se existir
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso.');
    }
}
