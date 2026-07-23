<?php

namespace Database\Factories;

use App\Models\Acolhimento;
use App\Models\SolicitacaoArquivo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SolicitacaoArquivo>
 */
class SolicitacaoArquivoFactory extends Factory
{
    protected $model = SolicitacaoArquivo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_solicitacao' => Acolhimento::factory(),
            'observacao' => fake()->sentence(),
            'nome_arquivo' => fake()->word().'.pdf',
            'tipo_md5' => Str::uuid().'.pdf',
            'tipo' => 'n',
            'q_enviou' => fake()->userName(),
            'cancelado' => 0,
        ];
    }
}
