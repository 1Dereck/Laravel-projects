<?php

namespace App\Policies;

use App\Models\Setor;
use App\Models\User;

class SetorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Setor $setor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Setor $setor): bool
    {
        return true;
    }

    public function delete(User $user, Setor $setor): bool
    {
        return $user->isDiretor();
    }

    public function restore(User $user, Setor $setor): bool
    {
        return $user->isDiretor();
    }

    public function forceDelete(User $user, Setor $setor): bool
    {
        return $user->isDiretor();
    }
}
