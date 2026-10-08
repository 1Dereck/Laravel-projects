<?php

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use App\Models\SyncQueue;
use App\Services\MobileSyncService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

test('simulacao de campo: coleta 10 itens offline, sincroniza com servidor e limpa celular', function (): void {
    // 1. Fase Offline: Técnico bipa 10 itens em áreas sem sinal (sync_queue)
    $uuids = [];
    for ($i = 1; $i <= 5; $i++) {
        $uuid = (string) Str::uuid();
        $uuids[] = $uuid;
        SyncQueue::create([
            'uuid' => $uuid,
            'entidade' => 'equipamento',
            'acao' => 'inventario_campo',
            'payload' => [
                'patrimonio' => "EQ-ALA-SUL-{$i}",
                'tipo' => 'desktop',
                'tipo_desempenho' => 'Alto',
                'observacoes' => "Leito {$i} verificado",
            ],
            'status' => 'pendente',
            'coletado_em' => now(),
        ]);
    }

    for ($i = 1; $i <= 3; $i++) {
        $uuid = (string) Str::uuid();
        $uuids[] = $uuid;
        SyncQueue::create([
            'uuid' => $uuid,
            'entidade' => 'monitor',
            'acao' => 'inventario_campo',
            'payload' => [
                'patrimonio' => "MON-ALA-SUL-{$i}",
                'numero' => 1,
            ],
            'status' => 'pendente',
            'coletado_em' => now(),
        ]);
    }

    for ($i = 1; $i <= 2; $i++) {
        $uuid = (string) Str::uuid();
        $uuids[] = $uuid;
        SyncQueue::create([
            'uuid' => $uuid,
            'entidade' => 'periferico',
            'acao' => 'inventario_campo',
            'payload' => [
                'patrimonio' => "PER-ALA-SUL-{$i}",
                'tipo_periferico' => 'Leitor de Código de Barras',
            ],
            'status' => 'pendente',
            'coletado_em' => now(),
        ]);
    }

    expect(SyncQueue::pendentes()->count())->toBe(10);

    // 2. Mock do envio HTTP ao servidor central
    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
        '*/api/sync/enviar-lote' => function (Request $request) {
            $dados = $request->data();
            $lote = $dados['lote'] ?? [];

            // Simula o processamento do servidor central
            $processados = [];
            foreach ($lote as $item) {
                $processados[] = $item['uuid'];
                if ($item['entidade'] === 'equipamento') {
                    Equipamento::updateOrCreate(['serial' => $item['dados']['patrimonio']], [
                        'tipo' => 'desktop',
                        'tipo_desempenho' => $item['dados']['tipo_desempenho'] ?? 'Médio',
                    ]);
                } elseif ($item['entidade'] === 'monitor') {
                    Monitor::updateOrCreate(['serial' => $item['dados']['patrimonio']], [
                        'equipamento_id' => 1,
                        'numero' => 1,
                    ]);
                } elseif ($item['entidade'] === 'periferico') {
                    Periferico::updateOrCreate(['serial_patrimonio' => $item['dados']['patrimonio']], [
                        'tipo' => $item['dados']['tipo_periferico'] ?? 'Geral',
                    ]);
                }
            }

            return Http::response([
                'sucesso' => true,
                'total_processados' => count($processados),
                'processados_uuids' => $processados,
                'falhas' => [],
            ], 200);
        },
    ]);

    // 3. Técnico conecta ao Wi-Fi e dispara sincronização com purge
    $service = new MobileSyncService('http://127.0.0.1:8000');
    $resultado = $service->sincronizarELimpar();

    // 4. Verificações do ACK e persistência
    expect($resultado['sucesso'])->toBeTrue()
        ->and($resultado['itens_enviados'])->toBe(10);

    // 5. Verificação da base central (os dados agora residem na base central)
    expect(Equipamento::where('serial', 'like', 'EQ-ALA-SUL-%')->count())->toBe(5);
    expect(Monitor::where('serial', 'like', 'MON-ALA-SUL-%')->count())->toBe(3);
    expect(Periferico::where('serial_patrimonio', 'like', 'PER-ALA-SUL-%')->count())->toBe(2);

    // 6. Verificação do Purge (o banco SQLite do celular foi completamente esvaziado)
    expect(SyncQueue::count())->toBe(0);
});
