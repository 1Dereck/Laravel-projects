<?php

namespace Database\Factories;

use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Local>
 */
class LocalFactory extends Factory
{
    protected $model = Local::class;

    public function definition(): array
    {
        return [
            'local' => fake()->unique()->words(3, true),
            'ip_onu' => fake()->ipv4(),
            'status' => '1',
        ];
    }
}
