<?php

namespace App\Policies;

use App\Models\Periferico;
use App\Models\User;

class PerifericoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Periferico $periferico): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isDiretor() || $user->isAdmin();
    }

    public function update(User $user, Periferico $periferico): bool
    {
        return $user->isDiretor() || $user->isAdmin();
    }

    public function delete(User $user, Periferico $periferico): bool
    {
        return $user->isDiretor();
    }

    public function restore(User $user, Periferico $periferico): bool
    {
        return $user->isDiretor();
    }

    public function forceDelete(User $user, Periferico $periferico): bool
    {
        return $user->isDiretor();
    }
}
