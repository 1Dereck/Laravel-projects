<?php

namespace App\Policies;

use App\Models\Equipamento;
use App\Models\User;

class EquipamentoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Equipamento $equipamento): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Equipamento $equipamento): bool
    {
        return true;
    }

    public function delete(User $user, Equipamento $equipamento): bool
    {
        return $user->isDiretor();
    }

    public function restore(User $user, Equipamento $equipamento): bool
    {
        return $user->isDiretor();
    }

    public function forceDelete(User $user, Equipamento $equipamento): bool
    {
        return $user->isDiretor();
    }
}
