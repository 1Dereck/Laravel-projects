<?php

namespace Database\Factories;

use App\Models\OfflineDadoMestre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfflineDadoMestre>
 */
class OfflineDadoMestreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo' => fake()->randomElement(['secretaria', 'local', 'setor']),
            'servidor_id' => fake()->numberBetween(1, 1000),
            'nome' => fake()->words(2, true),
            'pai_id' => null,
            'metadados' => null,
        ];
    }
}
