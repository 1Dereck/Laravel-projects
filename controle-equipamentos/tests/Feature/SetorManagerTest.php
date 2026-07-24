<?php

use App\Models\Setor;
use App\Models\User;
use Livewire\Livewire;

test('setores page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/setores')
        ->assertStatus(200);
});

test('user can create a new setor', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('setor-manager')
        ->set('nome', 'Secretaria da Saúde')
        ->call('salvar');

    $this->assertDatabaseHas('setores', [
        'nome' => 'Secretaria da Saúde',
    ]);
});

test('only diretor can soft delete a setor', function () {
    $admin = User::factory()->create(['role' => 'administrador']);
    $diretor = User::factory()->create(['role' => 'diretor']);
    $setor = Setor::factory()->create();

    // Admin should fail deleting
    Livewire::actingAs($admin)
        ->test('setor-manager')
        ->call('excluirSetor', $setor->id)
        ->assertForbidden();

    // Diretor should succeed
    Livewire::actingAs($diretor)
        ->test('setor-manager')
        ->call('excluirSetor', $setor->id);

    $this->assertSoftDeleted('setores', [
        'id' => $setor->id,
    ]);
});
