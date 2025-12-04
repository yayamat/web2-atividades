<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Book;
use App\Policies\BookPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        User::class => UserPolicy::class, // registra a UserPolicy
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Apenas admin pode gerenciar usuários (index, create, edit, delete)
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Bibliotecário ou admin podem gerenciar livros, categorias, autores e editoras
        Gate::define('manage-categories', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-books', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-authors', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-publishers', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        // Emprestar: permita apenas admin ou bibliotecario (ajuste conforme desejar)
        Gate::define('borrow-books', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        // Retornar: admin ou bibliotecario
        Gate::define('return-books', function (User $user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });
    }
}
