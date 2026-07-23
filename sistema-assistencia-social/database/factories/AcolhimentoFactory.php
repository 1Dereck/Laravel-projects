<?php

namespace Database\Factories;

use App\Models\Acolhimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Acolhimento>
 */
class AcolhimentoFactory extends Factory
{
    protected $model = Acolhimento::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dt_cadastro' => now()->format('Y-m-d'),
            'id_tecnico_resp' => User::factory(),
            'dt_nascimento' => fake()->date('Y-m-d', '-18 years'),
            'nome_pessoa' => fake()->name(),
            'naturalidade' => fake()->city(),
            'estado_nasc' => 'PR',
            'nec_especial' => 'Não',
            'depend_quimica' => 'Não',
            'transtorno' => 'Não',
            'monitoramento' => 'Não',
            'cpf' => sprintf('%011d', fake()->unique()->numberBetween(100000000, 999999999)),
            'rg' => fake()->numerify('########-#'),
            'recebe_beneficio' => 'Não',
            'id_usuario_alteracao' => User::factory(),
        ];
    }
}
