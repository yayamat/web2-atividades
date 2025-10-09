<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Música</title>
</head>
<body>
    <h1>Detalhes da Música</h1>        

    <div>
        <p><strong>ID:</strong> {{ $music->id }}</p>
        <p><strong>Título:</strong> {{ $music->title }}</p>
        <p><strong>Artista:</strong> {{ $music->artist }}</p>
        <p><strong>Álbum:</strong> {{ $music->album }}</p>
        <p><strong>Ano:</strong> {{ $music->year }}</p>
        <p><strong>Gênero:</strong> {{ $music->genre }}</p>
        <p><strong>Duração:</strong> {{ $music->duration }}</p>
    </div>

    <br>
    <a href="{{ route('music.index') }}">Voltar para a lista</a>
    <a href="{{ route('music.edit', $music) }}">Editar</a>
</body>
</html>
