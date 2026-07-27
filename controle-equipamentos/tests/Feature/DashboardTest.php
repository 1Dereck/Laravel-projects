<?php

use App\Livewire\Dashboard;
use App\Models\Equipamento;
use App\Models\User;
use Livewire\Livewire;

test('dashboard component formats activity log events in Portuguese', function () {
    $user = User::factory()->create();

    $equipamento = Equipamento::factory()->create([
        'tipo' => 'desktop',
        'serial' => 'SN-TEST-123',
    ]);

    // Triggers activity log for created
    activity()
        ->performedOn($equipamento)
        ->causedBy($user)
        ->log('created');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Cadastrou')
        ->assertSee('Computador (SN-TEST-123)');
});

test('director can dismiss and re-show trash alert banner on dashboard', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);

    $equipamento = Equipamento::factory()->create();
    $equipamento->delete(); // Move to trash

    Livewire::actingAs($diretor)
        ->test(Dashboard::class)
        ->assertSee('Atenção, Diretor: Itens na Lixeira')
        ->call('dismissTrashAlert')
        ->assertSee('Aviso da Lixeira ocultado temporariamente')
        ->assertDontSee('Atenção, Diretor: Itens na Lixeira')
        ->call('showTrashAlert')
        ->assertSee('Atenção, Diretor: Itens na Lixeira');
});
