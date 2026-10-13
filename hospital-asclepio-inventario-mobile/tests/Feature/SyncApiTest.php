<?php

use App\Models\Equipamento;
use App\Models\Monitor;
use App\Models\Periferico;
use Illuminate\Support\Str;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

test('ping endpoint returns online status', function (): void {
    $response = getJson(route('api.sync.ping'));

    $response->assertOk()
        ->assertJson([
            'status' => 'online',
            'servidor' => 'Hospital Asclépio Central',
        ]);
});

test('obter dados mestres returns catalog structures', function (): void {
    $response = getJson(route('api.sync.dados-mestres'));

    $response->assertOk()
        ->assertJsonStructure([
            'secretarias',
            'locais',
            'setores',
            'versao_catalogo',
        ]);
});

test('receber lote processes items transactionally and returns ack uuids', function (): void {
    $uuid1 = (string) Str::uuid();
    $uuid2 = (string) Str::uuid();
    $uuid3 = (string) Str::uuid();

    $payload = [
        'dispositivo_id' => 'MOBILE-TEST-01',
        'operador_email' => 'tech@asclepio.org.br',
        'lote' => [
            [
                'uuid' => $uuid1,
                'entidade' => 'equipamento',
                'dados' => [
                    'patrimonio' => 'EQ-TEST-9999',
                    'tipo' => 'desktop',
                    'tipo_desempenho' => 'Alto',
                    'marca_modelo' => 'Dell OptiPlex 7090',
                ],
            ],
            [
                'uuid' => $uuid2,
                'entidade' => 'monitor',
                'dados' => [
                    'patrimonio' => 'MON-TEST-8888',
                    'equipamento_serial' => 'EQ-TEST-9999',
                    'numero' => 1,
                ],
            ],
            [
                'uuid' => $uuid3,
                'entidade' => 'periferico',
                'dados' => [
                    'patrimonio' => 'PER-TEST-7777',
                    'tipo_periferico' => 'Scanner Zebra',
                    'observacoes' => 'Leitor fixo na triagem',
                ],
            ],
        ],
    ];

    $response = postJson(route('api.sync.enviar-lote'), $payload);

    $response->assertOk()
        ->assertJson([
            'sucesso' => true,
            'total_processados' => 3,
            'processados_uuids' => [$uuid1, $uuid2, $uuid3],
            'falhas' => [],
        ]);

    expect(Equipamento::where('serial', 'EQ-TEST-9999')->exists())->toBeTrue();
    expect(Monitor::where('serial', 'MON-TEST-8888')->exists())->toBeTrue();
    expect(Periferico::where('serial_patrimonio', 'PER-TEST-7777')->exists())->toBeTrue();
});

test('receber lote is idempotent when repeated', function (): void {
    $uuid = (string) Str::uuid();

    $payload = [
        'dispositivo_id' => 'MOBILE-TEST-02',
        'lote' => [
            [
                'uuid' => $uuid,
                'entidade' => 'equipamento',
                'dados' => [
                    'patrimonio' => 'EQ-IDEMP-001',
                    'tipo_desempenho' => 'Médio',
                ],
            ],
        ],
    ];

    $res1 = postJson(route('api.sync.enviar-lote'), $payload);
    $res1->assertOk()->assertJson(['sucesso' => true, 'total_processados' => 1]);

    $res2 = postJson(route('api.sync.enviar-lote'), $payload);
    $res2->assertOk()->assertJson(['sucesso' => true, 'total_processados' => 1]);

    expect(Equipamento::where('serial', 'EQ-IDEMP-001')->count())->toBe(1);
});

test('receber lote fails validation when payload is invalid', function (): void {
    $response = postJson(route('api.sync.enviar-lote'), []);

    $response->assertUnprocessable();
});
