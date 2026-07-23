<?php

namespace App\Providers;

use App\Models\Acolhimento;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate para visualizar o CPF completo
        Gate::define('view-cpf', function (User $user, ?Acolhimento $acolhimento = null) {
            if ($user->isDiretor() || $user->isAdmin()) {
                return true;
            }
            if ($acolhimento && $acolhimento->id_tecnico_resp === $user->id_usuario) {
                return true;
            }

            return false;
        });

        // Gate para gerenciamento de contas de usuários (acessar a tela / gerenciar)
        Gate::define('manage-users', function (User $user) {
            return $user->isDiretor() || $user->isAdmin();
        });

        // Gate para editar dados (criar/editar/ocultar acolhimentos, observações, fotos, etc.)
        Gate::define('edit-data', function (User $user) {
            return $user->isDiretor() || $user->isAdmin();
        });

        // Gate específico para excluir contas (apenas Diretor)
        Gate::define('delete-user', function (User $user) {
            return $user->isDiretor();
        });

        // Gate específico para criar contas de administradores e Diretores (apenas Diretor)
        Gate::define('create-admin-user', function (User $user) {
            return $user->isDiretor();
        });
    }
}
