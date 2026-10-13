<?php

namespace Database\Factories;

use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setor>
 */
class SetorFactory extends Factory
{
    protected $model = Setor::class;

    public function definition(): array
    {
        $setoresExemplo = [
            'Departamento de Saúde - Recepção',
            'Departamento de Educação - Protocolo',
            'Tecnologia da Informação - Suporte',
            'Recursos Humanos - Admissão',
            'Finanças & Contabilidade',
            'Diretoria Executiva',
            'Infraestrutura & Obras',
            'Atendimento Geral',
            'Meio Ambiente & Fiscalização',
            'Segurança & Controle de Acesso',
        ];

        return [
            'nome' => fake()->unique()->randomElement($setoresExemplo),
            'created_by' => User::factory(),
        ];
    }
}
