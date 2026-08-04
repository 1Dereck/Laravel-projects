<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    protected $model = Monitor::class;

    public function definition(): array
    {
        return [
            'equipamento_id' => Equipamento::factory(),
            'numero' => 1,
            'serial' => 'MON-'.strtoupper(fake()->bothify('??###??#')),
            'marca_modelo' => fake()->randomElement(['Dell 24"', 'LG 22"', 'Samsung 27"', 'HP 21.5"']),
        ];
    }
}
