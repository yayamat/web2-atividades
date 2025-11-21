<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'category_id',
        'publisher_id',
        'published_year',
        'cover', // nome alinhado com o controller recomendado
    ];

    protected $casts = [
        'published_year' => 'integer',
    ];

    // RELACIONAMENTOS
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * Relacionamento many-to-many com pivot 'borrowings'.
     * Ajuste os nomes de coluna se sua migration for diferente.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'borrowings')
                    ->withPivot('id', 'borrowed_at', 'returned_at')
                    ->withTimestamps();
    }

    /**
     * Retorna a URL pública da capa (ou imagem padrão caso não haja)
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover && Storage::disk('public')->exists($this->cover)) {
            return asset('storage/' . $this->cover);
        }

        return asset('images/default-cover.png'); // ajuste o caminho da imagem padrão
    }
}
