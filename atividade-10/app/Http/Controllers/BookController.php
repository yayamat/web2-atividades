<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // garante que apenas usuários logados acessam
        $this->authorizeResource(Book::class, 'book'); // aplica automaticamente a BookPolicy
    }
    //Logica de empréstimo de livros
    
    public function borrow(Request $request, Book $book)
{
    $this->authorize('borrow', $book); // <<< ESSA LINHA É ESSENCIAL

    
}


    // Formulário com input de ID
    public function createWithId()
    {
        $this->authorize('create', Book::class); // apenas admin/bibliotecario
        return view('books.create-id');
    }

    public function storeWithId(Request $request)
    {
        $this->authorize('create', Book::class); // apenas admin/bibliotecario

        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('books', 'public');
        } else {
            $data['cover'] = null;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Formulário com input select
    public function createWithSelect()
    {
        $this->authorize('create', Book::class); // apenas admin/bibliotecario

        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
    {
        $this->authorize('create', Book::class); // apenas admin/bibliotecario

        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('books', 'public');
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
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $book->title = $request->title;
        $book->publisher_id = $request->publisher_id;
        $book->author_id = $request->author_id;
        $book->category_id = $request->category_id;

        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }
            $book->cover = $request->file('cover')->store('books', 'public');
        }

        $book->save();

        return redirect()->route('books.show', $book)
                        ->with('success', 'Livro atualizado com sucesso.');
    }

    public function index()
    {
        $books = Book::with('author')->paginate(20);

        return view('books.index', compact('books'));
    }
  public function show(Book $book)
{
    // carrega relacionamentos principais do livro (autor, editora, categoria)
    $book->load(['author', 'publisher', 'category']);

    // lista de usuários para o select (mantém seu comportamento atual)
    $users = User::all();

    // carregamos os empréstimos desse livro (modelo Borrowing) com o usuário relacionado
    $borrowings = Borrowing::where('book_id', $book->id)
        ->with('user')
        ->orderByDesc('borrowed_at')
        ->get();

    // contagem de empréstimos ativos por usuário (user_id => total)
    // usamos DB::raw para contar apenas os returned_at IS NULL
    $activeCounts = Borrowing::whereNull('returned_at')
        ->groupBy('user_id')
        ->select('user_id', DB::raw('COUNT(*) as total'))
        ->pluck('total', 'user_id'); // retorna Collection [user_id => total]

    // envia tudo para a view books.show (conforme o template que combinamos)
    return view('books.show', compact('book', 'users', 'borrowings', 'activeCounts'));
}


    public function destroy(Book $book)
    {
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso.');
    }
}