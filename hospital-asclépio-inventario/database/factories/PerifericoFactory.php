<?php

namespace Database\Factories;

use App\Models\Local;
use App\Models\Periferico;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Periferico>
 */
class PerifericoFactory extends Factory
{
    protected $model = Periferico::class;

    public function definition(): array
    {
        $tipos = ['Impressora Laser HP', 'No-break Ragtech 1200VA', 'Leitor Código de Barras Honeywell', 'Webcam Logitech C920', 'Estabilizador SMS 1000VA'];

        return [
            'setor_id' => Local::factory(),
            'equipamento_id' => null,
            'tipo' => fake()->randomElement($tipos),
            'serial_patrimonio' => 'PAT-'.fake()->numerify('######'),
            'observacoes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
