<?php

use App\Models\Equipamento;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('admin cannot access lixeira page', function () {
    $admin = User::factory()->create(['role' => 'administrador']);

    actingAs($admin)
        ->get('/lixeira')
        ->assertStatus(403);
});

test('diretor can access lixeira page', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);

    actingAs($diretor)
        ->get('/lixeira')
        ->assertStatus(200);
});

test('diretor can restore soft-deleted items', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);
    $equipamento = Equipamento::factory()->create();
    $equipamento->delete();

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->call('restaurar', Equipamento::class, $equipamento->id);

    assertDatabaseHas('equipamentos', [
        'id' => $equipamento->id,
        'deleted_at' => null,
    ]);
});

test('force delete requires exact CONFIRMAR string', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);
    $equipamento = Equipamento::factory()->create();
    $equipamento->delete();

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->set('targetModelType', Equipamento::class)
        ->set('targetModelId', $equipamento->id)
        ->set('confirmInput', 'confirmar') // lowercase fails
        ->call('expurgarDefinitivamente')
        ->assertHasErrors(['confirmInput']);

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->set('targetModelType', Equipamento::class)
        ->set('targetModelId', $equipamento->id)
        ->set('confirmInput', 'CONFIRMAR') // exact uppercase succeeds
        ->call('expurgarDefinitivamente');

    assertDatabaseMissing('equipamentos', [
        'id' => $equipamento->id,
    ]);
});
