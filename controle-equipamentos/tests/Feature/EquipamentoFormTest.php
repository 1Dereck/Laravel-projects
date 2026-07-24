<?php

use App\Models\Equipamento;
use App\Models\Setor;
use App\Models\User;
use Livewire\Livewire;

test('equipamentos page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/equipamentos')
        ->assertStatus(200);
});

test('user can create a new equipamento with dynamic monitores', function () {
    $user = User::factory()->create();
    $setor = Setor::factory()->create();

    Livewire::actingAs($user)
        ->test('equipamento-form')
        ->set('setor_id', $setor->id)
        ->set('tipo', 'desktop')
        ->set('serial', 'SN-TEST-1234')
        ->set('marca_modelo', 'Dell OptiPlex')
        ->set('kit_teclado_mouse_locado', true)
        ->set('monitores', [
            ['id' => null, 'numero' => 1, 'serial' => 'MON-001'],
            ['id' => null, 'numero' => 2, 'serial' => 'MON-002'],
        ])
        ->call('salvar');

    $this->assertDatabaseHas('equipamentos', [
        'serial' => 'SN-TEST-1234',
        'setor_id' => $setor->id,
        'kit_teclado_mouse_locado' => true,
    ]);

    $equipamento = Equipamento::where('serial', 'SN-TEST-1234')->first();
    $this->assertCount(2, $equipamento->monitores);
});

test('only diretor can delete equipamento', function () {
    $admin = User::factory()->create(['role' => 'administrador']);
    $diretor = User::factory()->create(['role' => 'diretor']);
    $equipamento = Equipamento::factory()->create();

    Livewire::actingAs($admin)
        ->test('equipamento-form')
        ->call('excluirEquipamento', $equipamento->id)
        ->assertForbidden();

    Livewire::actingAs($diretor)
        ->test('equipamento-form')
        ->call('excluirEquipamento', $equipamento->id);

    $this->assertSoftDeleted('equipamentos', [
        'id' => $equipamento->id,
    ]);
});
