@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Adicionar Livro</h1>

    <form action="{{ route('books.store.select') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text"
                   class="form-control @error('title') is-invalid @enderror"
                   id="title"
                   name="title"
                   value="{{ old('title') }}"
                   required>
            @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="publisher_id" class="form-label">Editora</label>
            <select class="form-select @error('publisher_id') is-invalid @enderror"
                    id="publisher_id"
                    name="publisher_id"
                    required>
                <option value="" {{ old('publisher_id') ? '' : 'selected' }}>Selecione uma editora</option>
                @foreach($publishers as $publisher)
                    <option value="{{ $publisher->id }}" {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>
                        {{ $publisher->name }}
                    </option>
                @endforeach
            </select>
            @error('publisher_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="author_id" class="form-label">Autor</label>
            <select class="form-select @error('author_id') is-invalid @enderror"
                    id="author_id"
                    name="author_id"
                    required>
                <option value="" {{ old('author_id') ? '' : 'selected' }}>Selecione autor</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>
            @error('author_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Categoria</label>
            <select class="form-select @error('category_id') is-invalid @enderror"
                    id="category_id"
                    name="category_id"
                    required>
                <option value="" {{ old('category_id') ? '' : 'selected' }}>Selecione uma categoria</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

         <!-- Upload da Capa -->
        <div class="mb-3">
            <label for="cover" class="form-label">Capa do Livro (imagem)</label>
            <input type="file"
                   class="form-control @error('cover') is-invalid @enderror"
                   id="cover"
                   name="cover"
                   accept="image/*">
            @error('cover')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="mt-3">
                <img id="cover-preview"
                     src="{{ asset('images/default-cover.png') }}"
                     alt="Prévia da capa"
                     style="max-width: 180px; max-height: 240px; object-fit: cover; border:1px solid #ddd; padding:4px;">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <button type="button" class="btn btn-secondary" onclick="window.history.back(); return false;">Cancelar</button>
        <button type="button" class="btn btn-warning" onclick="window.history.back(); return false;">Voltar</button>
     
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('cover');
    const preview = document.getElementById('cover-preview');

    input.addEventListener('change', function (e) {
        const file = this.files[0];
        if (!file) {
            // volta para a imagem padrão se nada selecionado
            preview.src = "{{ asset('images/default-cover.png') }}";
            return;
        }

        if (!file.type.startsWith('image/')) {
            preview.src = "{{ asset('images/default-cover.png') }}";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            preview.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush

@endsection
