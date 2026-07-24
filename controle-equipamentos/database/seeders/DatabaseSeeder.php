<?php

namespace Database\Seeders;

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $diretor = User::factory()->diretor()->create([
            'name' => 'Dereck',
            'username' => 'dereck',
            'password' => Hash::make('123456'),
        ]);

        $admin = User::factory()->admin()->create([
            'name' => 'Maciel',
            'username' => 'maciel',
            'password' => Hash::make('123456'),
            'created_by' => $diretor->id,
        ]);

        $setoresNomes = [
            'Secretaria de Saúde - Recepção',
            'Secretaria de Educação - Protocolo',
            'Divisão de TI - Suporte Técnico',
            'Recursos Humanos - Admissão',
            'Finanças & Contabilidade',
        ];

        foreach ($setoresNomes as $nomeSetor) {
            $setor = Setor::create([
                'nome' => $nomeSetor,
                'created_by' => $admin->id,
            ]);

            // Create 2-4 equipments per setor
            $numEq = rand(2, 4);
            for ($i = 0; $i < $numEq; $i++) {
                $tipo = rand(0, 1) === 0 ? 'desktop' : 'notebook';
                $equipamento = Equipamento::create([
                    'setor_id' => $setor->id,
                    'tipo' => $tipo,
                    'serial' => 'SN-'.strtoupper(fake()->bothify('??###??#')),
                    'marca_modelo' => $tipo === 'desktop' ? 'Dell OptiPlex 3080' : 'Lenovo ThinkPad L14',
                    'kit_teclado_mouse_locado' => true,
                    'responsavel_levantamento' => $admin->name,
                    'created_by' => $admin->id,
                ]);

                // Desktops have 1 or 2 monitors
                $numMonitores = $tipo === 'desktop' ? rand(1, 2) : rand(0, 1);
                for ($m = 1; $m <= $numMonitores; $m++) {
                    Monitor::create([
                        'equipamento_id' => $equipamento->id,
                        'numero' => $m,
                        'serial' => 'MON-'.strtoupper(fake()->bothify('??###??#')),
                    ]);
                }
            }

            // Create 1-2 peripherals per setor
            Periferico::create([
                'setor_id' => $setor->id,
                'tipo' => 'Impressora Laser Multifuncional',
                'serial_patrimonio' => 'PAT-'.fake()->numerify('######'),
                'observacoes' => 'Alocada na sala principal do setor',
                'created_by' => $admin->id,
            ]);
        }
    }
}
