<?php

use App\Livewire\BuscaSetor;
use App\Models\Local;
use App\Models\User;
use Livewire\Livewire;

test('relatorios page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/relatorios')
        ->assertStatus(200);
});

test('pdf report can be generated for a local', function () {
    $user = User::factory()->create();
    $local = Local::create(['local' => 'Sec. Obras', 'ip_onu' => '10.20.47.1']);

    $response = $this->actingAs($user)
        ->get(route('relatorios.pdf', $local->id_local));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('relatorios page does not contain secretaria options', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(BuscaSetor::class)
        ->assertDontSee('Selecionar Secretaria')
        ->assertDontSee('Exibir Todas as Secretarias em Botões');
});

test('usuario nao ve filtro de inventario nem selecao de local', function () {
    $local = Local::create(['local' => 'Posto de Saude Norte']);
    $usuario = User::factory()->usuario($local->id_local)->create();

    Livewire::actingAs($usuario)
        ->test(BuscaSetor::class)
        ->assertDontSee('Filtro de Inventário para Impressão')
        ->assertDontSee('Selecionar Local:')
        ->assertSee('Posto de Saude Norte')
        ->assertSee('Gerar Relatório PDF (Local)');
});
