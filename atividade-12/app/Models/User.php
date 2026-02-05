<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_BIBLIOTECARIO = 'bibliotecario';
    public const ROLE_CLIENTE = 'cliente';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'debit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relação com livros via empréstimos
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'borrowings')
                    ->withPivot('id', 'borrowed_at', 'returned_at')
                    ->withTimestamps();
    }

    // ----------------------
    // Helpers de roles
    // ----------------------

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBibliotecario(): bool
    {
        return $this->role === self::ROLE_BIBLIOTECARIO;
    }

    public function isCliente(): bool
    {
        return $this->role === self::ROLE_CLIENTE;
    }

    // ----------------------
    // Permissões centralizadas
    // ----------------------

    /**
     * Pode gerenciar livros (criar/editar) - admin e bibliotecário
     */
    public function canManageBooks(): bool
    {
        return $this->isAdmin() || $this->isBibliotecario();
    }

    /**
     * Pode deletar livros - apenas admin
     */
    public function canDeleteBooks(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Pode gerenciar usuários (editar papéis) - apenas admin
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Pode visualizar qualquer dado - todos
     */
    public function canView(): bool
    {
        return true;
    }
}
