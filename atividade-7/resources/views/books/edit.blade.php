@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Editar Livro</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Título -->
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text"
                   class="form-control @error('title') is-invalid @enderror"
                   id="title"
                   name="title"
                   value="{{ old('title', $book->title) }}"
                   required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Editora -->
        <div class="mb-3">
            <label for="publisher_id" class="form-label">Editora</label>
            <select class="form-select @error('publisher_id') is-invalid @enderror"
                    id="publisher_id"
                    name="publisher_id"
                    required>
                <option value="">Selecione uma editora</option>
                @foreach($publishers as $publisher)
                    <option value="{{ $publisher->id }}"
                        {{ (int) old('publisher_id', $book->publisher_id) === $publisher->id ? 'selected' : '' }}>
                        {{ $publisher->name }}
                    </option>
                @endforeach
            </select>
            @error('publisher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Autor -->
        <div class="mb-3">
            <label for="author_id" class="form-label">Autor</label>
            <select class="form-select @error('author_id') is-invalid @enderror"
                    id="author_id"
                    name="author_id"
                    required>
                <option value="">Selecione um autor</option>
                @foreach($authors as $author)
                    <option value="{{ $author->id }}"
                        {{ (int) old('author_id', $book->author_id) === $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>
            @error('author_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Categoria -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Categoria</label>
            <select class="form-select @error('category_id') is-invalid @enderror"
                    id="category_id"
                    name="category_id"
                    required>
                <option value="">Selecione uma categoria</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (int) old('category_id', $book->category_id) === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Capa atual e preview -->
        <div class="mb-3">
            <label class="form-label">Capa atual</label>
            @php
                $coverPath = $book->cover ?? null;
                $hasCover = $coverPath && file_exists(storage_path('app/public/' . $coverPath));
                $previewSrc = $hasCover ? asset('storage/' . $coverPath) : asset('images/default-cover.png');
            @endphp

            <div class="mb-2">
                <img id="cover-preview" src="{{ $previewSrc }}" alt="Capa" style="max-width:180px; max-height:240px; object-fit:cover; border:1px solid #ddd; padding:4px;">
            </div>

            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="remove_cover" name="remove_cover" value="1">
                <label class="form-check-label" for="remove_cover">Remover capa atual</label>
            </div>
        </div>

        <!-- Substituir capa -->
        <div class="mb-3">
            <label for="cover" class="form-label">Substituir Capa (opcional)</label>
            <input type="file" name="cover" id="cover" accept="image/*" class="form-control @error('cover') is-invalid @enderror">
            @error('cover') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Salvar mudanças</button>
        <a href="{{ route('books.show', $book) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('cover');
    const preview = document.getElementById('cover-preview');
    const removeCheckbox = document.getElementById('remove_cover');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Selecione um arquivo de imagem.');
            this.value = '';
            return;
        }

        // desmarca remoção se selecionar nova imagem
        if (removeCheckbox.checked) removeCheckbox.checked = false;

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // se marcar 'remover capa' mostra a imagem default
    removeCheckbox.addEventListener('change', function () {
        if (this.checked) {
            preview.src = "{{ asset('images/default-cover.png') }}";
        } else {
            // se não houver cover no DB, mantém default; caso contrário, recarrega a original
            preview.src = "{{ $previewSrc }}";
        }
    });
});
</script>
@endpush

@endsection
