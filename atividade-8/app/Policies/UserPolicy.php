<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Apenas admin e bibliotecário podem ver a lista e detalhes.
     * Cliente não pode ver nada.
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Apenas admin e bibliotecário podem visualizar um usuário.
     */
    public function view(User $user, User $model)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Apenas admin pode criar usuários.
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Apenas admin pode editar usuários.
     */
    public function update(User $user, User $model)
    {
        return $user->isAdmin();
    }

    /**
     * Apenas admin pode deletar usuários.
     */
    public function delete(User $user, User $model)
    {
        return $user->isAdmin();
    }
}
