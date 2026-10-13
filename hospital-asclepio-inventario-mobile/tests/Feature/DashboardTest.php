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

test('director trash alert banner auto-dismisses and re-appears when another item is trashed', function () {
    $diretor = User::factory()->create(['role' => 'diretor']);

    $equipamento1 = Equipamento::factory()->create();
    $equipamento1->delete(); // Move to trash

    $test = Livewire::actingAs($diretor)
        ->test(Dashboard::class)
        ->assertSee('Atenção, Diretor: Itens na Lixeira')
        ->call('dismissTrashAlert')
        ->assertDontSee('Atenção, Diretor: Itens na Lixeira');

    // On subsequent render with same trash state, banner remains hidden
    Livewire::actingAs($diretor)
        ->test(Dashboard::class)
        ->assertDontSee('Atenção, Diretor: Itens na Lixeira');

    // When another item is deleted, the banner appears again
    sleep(1); // Ensure distinct deleted_at timestamp
    $equipamento2 = Equipamento::factory()->create();
    $equipamento2->delete();

    Livewire::actingAs($diretor)
        ->test(Dashboard::class)
        ->assertSee('Atenção, Diretor: Itens na Lixeira');
});
