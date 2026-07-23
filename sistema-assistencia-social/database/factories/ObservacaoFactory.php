<?php

namespace Database\Factories;

use App\Models\Acolhimento;
use App\Models\Observacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Observacao>
 */
class ObservacaoFactory extends Factory
{
    protected $model = Observacao::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_acolhimento' => Acolhimento::factory(),
            'descricao' => fake()->paragraph(),
            'id_assunto' => 0,
            'tipo' => 'n',
            'id_usuario' => User::factory(),
        ];
    }
}
