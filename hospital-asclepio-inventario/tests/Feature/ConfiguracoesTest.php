<?php

use App\Livewire\Configuracoes;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        ->assertSee('Alterar Minha Senha')
        ->assertSee(config('app.version'));
});

test('configuracoes livewire component can be tested directly', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Configuracoes::class)
        ->assertStatus(200)
        ->assertSee('Painel de Configurações')
        ->assertSee('Alterar Minha Senha');
});

test('user can update password with valid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('senha123'),
    ]);

    Livewire::actingAs($user)
        ->test(Configuracoes::class)
        ->set('current_password', 'senha123')
        ->set('new_password', 'novaSenha456')
        ->set('new_password_confirmation', 'novaSenha456')
        ->call('alterarSenha')
        ->assertHasNoErrors()
        ->assertSee('Sua senha foi alterada com sucesso!');

    expect(Hash::check('novaSenha456', $user->fresh()->password))->toBeTrue();
});

test('user cannot update password with invalid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('senha123'),
    ]);

    Livewire::actingAs($user)
        ->test(Configuracoes::class)
        ->set('current_password', 'senhaErrada')
        ->set('new_password', 'novaSenha456')
        ->set('new_password_confirmation', 'novaSenha456')
        ->call('alterarSenha')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('senha123', $user->fresh()->password))->toBeTrue();
});

test('user cannot update password if confirmation does not match', function () {
    $user = User::factory()->create([
        'password' => Hash::make('senha123'),
    ]);

    Livewire::actingAs($user)
        ->test(Configuracoes::class)
        ->set('current_password', 'senha123')
        ->set('new_password', 'novaSenha456')
        ->set('new_password_confirmation', 'diferente789')
        ->call('alterarSenha')
        ->assertHasErrors(['new_password']);

    expect(Hash::check('senha123', $user->fresh()->password))->toBeTrue();
});
