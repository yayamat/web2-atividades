<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Music;

class MusicController extends Controller
{
    public function index()
    {
        $musics = Music::all();
        return view('music.index', compact('musics'));
    }

    public function create()
    {
        return view('music.create');
    }

   public function store(Request $request)
{
    
    Music::create($request->all());

    return redirect()->route('music.index');
}


    public function edit(Music $music)
    {
        return view('music.edit', compact('music'));
    }

    public function update(Request $request, Music $music)
{
    $music->update($request->all());

    return redirect()->route('music.index');
}

    
		public function show(Music $music)
    {       
        return view('music.show', compact('music'));
    }



    public function destroy(Music $music)
    {
        $music->delete();
        return redirect()->route('music.index')->with('success','Música excluída com sucesso!');
    }
}
