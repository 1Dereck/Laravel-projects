<?php

namespace Database\Seeders;

use App\Models\Equipamento;
use App\Models\Local;
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
        $diretor = User::updateOrCreate(
            ['role' => 'diretor'],
            [
                'name' => 'Diretor',
                'username' => 'Diretor',
                'password' => Hash::make('DTi@123'),
            ]
        );

        $admin = User::updateOrCreate(
            ['role' => 'administrador'],
            [
                'name' => 'Administrador',
                'username' => 'Administrador',
                'password' => Hash::make('DTi@123'),
                'created_by' => $diretor->id,
            ]
        );

        $setoresNomes = [
            'Ambulatório de Neurologia & Laudos',
            'UTI Adulto - Posto Central',
            'Radiologia & Tomografia Computadorizada',
            'Recepção Central & Triagem de Pacientes',
            'Recursos Humanos & Faturamento Hospitalar',
            'Departamento de TI & Prontuário Eletrônico',
        ];

        foreach ($setoresNomes as $nomeSetor) {
            $local = Local::create([
                'local' => $nomeSetor,
                'status' => '1',
            ]);

            $setor = Setor::create([
                'nome' => $nomeSetor,
                'created_by' => $admin->id,
            ]);

            // Create 2-4 equipments per setor
            $numEq = rand(2, 4);
            for ($i = 0; $i < $numEq; $i++) {
                $tipo = rand(0, 1) === 0 ? 'desktop' : 'notebook';
                $equipamento = Equipamento::create([
                    'setor_id' => $local->id_local,
                    'tipo' => $tipo,
                    'tipo_desempenho' => rand(0, 2) === 0 ? 'basico' : (rand(0, 1) === 0 ? 'intermediario' : 'avancado'),
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
                'setor_id' => $local->id_local,
                'tipo' => 'Impressora Laser Multifuncional',
                'serial_patrimonio' => 'PAT-'.fake()->numerify('######'),
                'observacoes' => 'Alocada na sala principal do setor',
                'created_by' => $admin->id,
            ]);
        }

        $firstLocalId = Local::query()->value('id_local');

        User::updateOrCreate(
            ['role' => 'coordenador'],
            [
                'name' => 'Coordenador',
                'username' => 'Coordenador',
                'password' => Hash::make('DTi@123'),
                'setor_id' => $firstLocalId,
                'created_by' => $admin->id,
            ]
        );

        User::updateOrCreate(
            ['role' => 'usuario'],
            [
                'name' => 'Usuário',
                'username' => 'Usuário',
                'password' => Hash::make('DTi@123'),
                'setor_id' => $firstLocalId,
                'created_by' => $admin->id,
            ]
        );
    }
}
