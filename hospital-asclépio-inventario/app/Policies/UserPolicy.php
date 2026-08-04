<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDiretor() || $user->isAdmin() || $user->isCoordenador();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isDiretor() || $user->isAdmin() || $user->id === $model->id) {
            return true;
        }

        if ($user->isCoordenador()) {
            return $model->isUsuario() && $user->belongsToSameSector($model);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isDiretor() || $user->isAdmin() || $user->isCoordenador();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isDiretor() || $user->id === $model->id) {
            return true;
        }

        if ($user->isAdmin()) {
            return in_array($model->role, ['administrador', 'coordenador'], true);
        }

        if ($user->isCoordenador()) {
            return $model->isUsuario() && $user->belongsToSameSector($model);
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->isDiretor()) {
            return true;
        }

        if ($user->isAdmin()) {
            return in_array($model->role, ['administrador', 'coordenador'], true);
        }

        if ($user->isCoordenador()) {
            return $model->isUsuario() && $user->belongsToSameSector($model);
        }

        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->isDiretor();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isDiretor() && $user->id !== $model->id;
    }
}
