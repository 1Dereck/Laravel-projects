<?php

use App\Livewire\Configuracoes;
use App\Models\User;
use Livewire\Livewire;

test('unauthenticated users are redirected from settings page', function () {
    $this->get(route('configuracoes.index'))
        ->assertRedirect('/login');
});

test('authenticated users can render settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('configuracoes.index'))
        ->assertStatus(200)
        ->assertSee('Configurações do Sistema')
        ->assertSee('Modo Claro')
        ->assertSee('Modo Escuro')
        ->assertSee(config('app.version', 'v1.0.1'));
});

test('configuracoes livewire component can be tested directly', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Configuracoes::class)
        ->assertStatus(200)
        ->assertSee('Painel de Configurações');
});
