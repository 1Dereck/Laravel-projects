<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDiretor();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isDiretor() || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isDiretor();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isDiretor() || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isDiretor() && $user->id !== $model->id;
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
