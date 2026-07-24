<?php

use App\Models\User;
use Livewire\Livewire;

test('login page can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using login component', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test('auth.login')
        ->set('username', $user->username)
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('authenticated users can access dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertStatus(200);
});

test('unauthenticated users are redirected to login', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});
