<?php

use App\Models\Setor;
use App\Models\User;
use Livewire\Livewire;

test('admin cannot access lixeira page', function () {
    $admin = User::factory()->create(['role' => 'administrador']);

    $this->actingAs($admin)
        ->get('/lixeira')
        ->assertStatus(403);
});

test('diretor can access lixeira page', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);

    $this->actingAs($diretor)
        ->get('/lixeira')
        ->assertStatus(200);
});

test('diretor can restore soft-deleted items', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);
    $setor = Setor::factory()->create();
    $setor->delete();

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->call('restaurar', Setor::class, $setor->id);

    $this->assertDatabaseHas('setores', [
        'id' => $setor->id,
        'deleted_at' => null,
    ]);
});

test('force delete requires exact CONFIRMAR string', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);
    $setor = Setor::factory()->create();
    $setor->delete();

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->set('targetModelType', Setor::class)
        ->set('targetModelId', $setor->id)
        ->set('confirmInput', 'confirmar') // lowercase fails
        ->call('expurgarDefinitivamente')
        ->assertHasErrors(['confirmInput']);

    Livewire::actingAs($diretor)
        ->test('lixeira-manager')
        ->set('targetModelType', Setor::class)
        ->set('targetModelId', $setor->id)
        ->set('confirmInput', 'CONFIRMAR') // exact uppercase succeeds
        ->call('expurgarDefinitivamente');

    $this->assertDatabaseMissing('setores', [
        'id' => $setor->id,
    ]);
});
