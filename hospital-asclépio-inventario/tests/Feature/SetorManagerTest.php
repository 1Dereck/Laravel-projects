<?php

use App\Models\Local;
use App\Models\User;
use Livewire\Livewire;

test('setores page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/setores')
        ->assertStatus(200);
});

test('user can search existing local', function () {
    $user = User::factory()->create();
    Local::create([
        'local' => 'Sec. Obras Públicas',
        'ip_onu' => '10.20.47.1',
    ]);

    Livewire::actingAs($user)
        ->test('setor-manager')
        ->set('search', 'Obras')
        ->assertSee('Sec. Obras Públicas');
});
