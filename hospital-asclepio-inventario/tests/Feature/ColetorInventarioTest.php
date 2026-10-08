<?php

use App\Livewire\Mobile\ColetorInventario;
use App\Models\OfflineDadoMestre;
use App\Models\SyncQueue;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('componente do coletor mobile renderiza com sucesso', function (): void {
    Livewire::test(ColetorInventario::class)
        ->assertSee('Asclépio Mobile')
        ->assertSee('Salvar Coleta no Celular');
});

test('salva coleta offline na fila local com status pendente', function (): void {
    Livewire::test(ColetorInventario::class)
        ->set('patrimonio', 'EQ-MOBI-001')
        ->set('tipo', 'equipamento')
        ->set('tipoDesempenho', 'Alto')
        ->set('observacoes', 'Leito 01 UTI')
        ->call('salvarColetaOffline')
        ->assertHasNoErrors()
        ->assertSet('patrimonio', '')
        ->assertSee('registrado no celular com sucesso');

    expect(SyncQueue::where('status', 'pendente')->count())->toBe(1);

    $coleta = SyncQueue::first();
    expect($coleta->payload['patrimonio'])->toBe('EQ-MOBI-001')
        ->and($coleta->payload['tipo_desempenho'])->toBe('Alto');
});

test('aciona sincronizacao e esvazia fila do celular quando servidor responde ACK', function (): void {
    $coleta = SyncQueue::factory()->create(['status' => 'pendente']);

    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
        '*/api/sync/enviar-lote' => Http::response([
            'sucesso' => true,
            'total_processados' => 1,
            'processados_uuids' => [$coleta->uuid],
            'falhas' => [],
        ], 200),
    ]);

    Livewire::test(ColetorInventario::class)
        ->call('executarSincronizacao')
        ->assertSee('Sincronização concluída com sucesso');

    expect(SyncQueue::count())->toBe(0);
});

test('atualiza catalogo de dados mestres no celular', function (): void {
    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
        '*/api/sync/dados-mestres' => Http::response([
            'secretarias' => [
                ['id' => 1, 'nome' => 'Saúde', 'nome_extenso' => 'Secretaria Municipal de Saúde'],
            ],
            'locais' => [
                ['id' => 10, 'secretaria_id' => 1, 'nome' => 'Hospital Central'],
            ],
            'setores' => [
                ['id' => 50, 'nome' => 'UTI Neonatal'],
            ],
            'versao_catalogo' => 123456,
        ], 200),
    ]);

    Livewire::test(ColetorInventario::class)
        ->call('atualizarDadosMestres')
        ->assertSee('Catálogo de locais e setores atualizado com sucesso');

    expect(OfflineDadoMestre::where('tipo', 'secretaria')->count())->toBe(1)
        ->and(OfflineDadoMestre::where('tipo', 'local')->count())->toBe(1)
        ->and(OfflineDadoMestre::where('tipo', 'setor')->count())->toBe(1);
});
