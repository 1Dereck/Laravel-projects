<?php

use App\Models\Setor;
use App\Models\User;
use Livewire\Livewire;

test('perifericos page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/perifericos')
        ->assertStatus(200);
});

test('user can open modal and see setoresList without error', function () {
    $user = User::factory()->create();
    Setor::factory()->create(['nome' => 'TI']);

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->call('novoPeriferico')
        ->assertSee('Cadastrar Periférico')
        ->assertSee('TI');
});

test('user can create a new periferico', function () {
    $user = User::factory()->create();
    $setor = Setor::factory()->create();

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->set('setor_id', $setor->id)
        ->set('tipo', 'Impressora HP')
        ->set('serial_patrimonio', 'PAT-999')
        ->call('salvar');

    $this->assertDatabaseHas('perifericos', [
        'tipo' => 'Impressora HP',
        'serial_patrimonio' => 'PAT-999',
        'setor_id' => $setor->id,
    ]);
});
