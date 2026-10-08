<?php

namespace Database\Factories;

use App\Models\SyncQueue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SyncQueue>
 */
class SyncQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'entidade' => 'equipamento',
            'acao' => 'inventario_campo',
            'payload' => [
                'patrimonio' => 'EQ-'.fake()->numerify('#####'),
                'tipo_desempenho' => 'Alto',
                'observacoes' => fake()->sentence(),
            ],
            'status' => 'pendente',
            'erro_mensagem' => null,
            'tentativas' => 0,
            'coletado_em' => now(),
        ];
    }
}
