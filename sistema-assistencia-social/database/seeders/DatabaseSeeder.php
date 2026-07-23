<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpa usuários existentes para evitar duplicidade no seed
        User::query()->delete();

        User::factory()->create([
            'login' => 'dereck',
            'senha' => Hash::make('123456'),
            'permissao' => 'd', // Diretor
            'nome_usu' => 'Dereck',
            'tipo_acesso' => 's',
            'ativo' => 's',
        ]);

        User::factory()->create([
            'login' => 'luiz',
            'senha' => Hash::make('123456'),
            'permissao' => 'a', // Administrador
            'nome_usu' => 'Luiz',
            'tipo_acesso' => 's',
            'ativo' => 's',
        ]);

        User::factory()->create([
            'login' => 'fagner',
            'senha' => Hash::make('123456'),
            'permissao' => 'n', // Usuário
            'nome_usu' => 'Fagner',
            'tipo_acesso' => 'n',
            'ativo' => 's',
        ]);

        $this->call(EstadoSeeder::class);
    }
}
