<?php

namespace Database\Factories;

use App\Models\Equipamento;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipamento>
 */
class EquipamentoFactory extends Factory
{
    protected $model = Equipamento::class;

    public function definition(): array
    {
        $marcas = ['Dell OptiPlex 3080', 'HP ProDesk 400 G6', 'Lenovo ThinkCentre M70q', 'Dell Latitude 3420', 'HP ProBook 440 G8'];

        return [
            'setor_id' => Setor::factory(),
            'tipo' => fake()->randomElement(['notebook', 'desktop']),
            'serial' => 'SN-'.strtoupper(fake()->bothify('??###??#')),
            'marca_modelo' => fake()->randomElement($marcas),
            'kit_teclado_mouse_locado' => fake()->boolean(60),
            'responsavel_levantamento' => fake()->name(),
            'created_by' => User::factory(),
        ];
    }
}
