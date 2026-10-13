<?php

use App\Models\SyncQueue;
use App\Services\MobileSyncService;
use Illuminate\Support\Facades\Http;

test('verificar conexao returns true when ping responds online', function (): void {
    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
    ]);

    $service = new MobileSyncService('http://127.0.0.1:8000');

    expect($service->verificarConexao())->toBeTrue();
});

test('verificar conexao returns false when server is unreachable', function (): void {
    Http::fake([
        '*/api/sync/ping' => Http::response([], 500),
    ]);

    $service = new MobileSyncService('http://127.0.0.1:8000');

    expect($service->verificarConexao())->toBeFalse();
});

test('sincronizar e limpar sends pending items, deletes confirmed ones and purges', function (): void {
    $item1 = SyncQueue::factory()->create(['status' => 'pendente']);
    $item2 = SyncQueue::factory()->create(['status' => 'pendente']);

    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
        '*/api/sync/enviar-lote' => Http::response([
            'sucesso' => true,
            'total_processados' => 2,
            'processados_uuids' => [$item1->uuid, $item2->uuid],
            'falhas' => [],
        ], 200),
    ]);

    $service = new MobileSyncService('http://127.0.0.1:8000');
    $resultado = $service->sincronizarELimpar();

    expect($resultado['sucesso'])->toBeTrue()
        ->and($resultado['itens_enviados'])->toBe(2);

    // Records must be purged locally to not burden the mobile device
    expect(SyncQueue::whereIn('uuid', [$item1->uuid, $item2->uuid])->exists())->toBeFalse();
    expect(SyncQueue::pendentes()->count())->toBe(0);
});

test('sincronizar e limpar restores pending status if server fails', function (): void {
    $item = SyncQueue::factory()->create(['status' => 'pendente']);

    Http::fake([
        '*/api/sync/ping' => Http::response(['status' => 'online'], 200),
        '*/api/sync/enviar-lote' => Http::response('Server Error', 500),
    ]);

    $service = new MobileSyncService('http://127.0.0.1:8000');
    $resultado = $service->sincronizarELimpar();

    expect($resultado['sucesso'])->toBeFalse();
    expect($item->fresh()->status)->toBe('pendente');
});
