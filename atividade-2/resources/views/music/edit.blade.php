<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Música</title>
</head>
<body>
    <h1>Editar Música</h1>

    <form action="{{ route('music.update', $music) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="title">Título:</label>
            <input type="text" id="title" name="title" value="{{ $music->title }}" required>
        </div>

        <div>
            <label for="artist">Artista:</label>
            <input type="text" id="artist" name="artist" value="{{ $music->artist }}" required>
        </div>

        <div>
            <label for="album">Álbum:</label>
            <input type="text" id="album" name="album" value="{{ $music->album }}">
        </div>

        <div>
            <label for="year">Ano:</label>
            <input type="number" id="year" name="year" value="{{ $music->year }}">
        </div>

        <div>
            <label for="genre">Gênero:</label>
            <select name="genre" id="genre">
                <option value="">Selecione o Gênero</option>
                <option value="Pop" @if ($music->genre == 'Pop') selected @endif>Pop</option>
                <option value="Rock" @if ($music->genre == 'Rock') selected @endif>Rock</option>
                <option value="Sertanejo" @if ($music->genre == 'Sertanejo') selected @endif>Sertanejo</option>
                <option value="MPB" @if ($music->genre == 'MPB') selected @endif>MPB</option>
                <option value="Forro" @if ($music->genre == 'Forro') selected @endif>Forro</option>
                <option value="Brega" @if ($music->genre == 'Brega') selected @endif>Brega</option> 
                <option value="Outro" @if ($music->genre == 'Outro') selected @endif>Outro</option>
            </select>
        </div>

        <div>
            <label for="duration">Duração:</label>
            <input type="time" id="duration" name="duration" value="{{ $music->duration }}">
        </div>

        <button type="submit">Salvar</button>
    </form>

    <br>
    <a href="{{ route('music.index') }}"> Voltar para a lista</a>
</body>
</html>
