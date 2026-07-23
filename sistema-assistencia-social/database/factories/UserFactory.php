<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'login' => fake()->unique()->userName(),
            'senha' => static::$password ??= Hash::make('password'),
            'permissao' => fake()->randomElement(['a', 'n']),
            'id_usuario_alteracao' => 0,
            'nome_usu' => fake()->name(),
            'tipo_acesso' => 'n',
            'ativo' => 's',
        ];
    }
}
