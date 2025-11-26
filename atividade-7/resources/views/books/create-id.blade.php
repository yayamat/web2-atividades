@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Adicionar Livro (Com ID)</h1>

    <form action="{{ route('books.store.id') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Título -->
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <!-- ID da Editora -->
        <div class="mb-3">
            <label for="publisher_id" class="form-label">ID da Editora</label>
            <input type="number" class="form-control" id="publisher_id" name="publisher_id" required>
        </div>

        <!-- ID do Autor -->
        <div class="mb-3">
            <label for="author_id" class="form-label">ID do Autor</label>
            <input type="number" class="form-control" id="author_id" name="author_id" required>
        </div>

        <!-- ID da Categoria -->
        <div class="mb-3">
            <label for="category_id" class="form-label">ID da Categoria</label>
            <input type="number" class="form-control" id="category_id" name="category_id" required>
        </div>

        <!-- Upload da CAPA -->
        <div class="mb-3">
            <label for="cover" class="form-label">Capa do Livro (opcional)</label>
            <input type="file" class="form-control" id="cover" name="cover">
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>
@endsection
