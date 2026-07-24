<?php

use App\Models\User;

test('authenticated user can access all standard menu tabs', function () {
    $user = User::factory()->create([
        'role' => 'administrador',
    ]);

    $routes = [
        '/',
        '/dashboard',
        '/setores',
        '/equipamentos',
        '/perifericos',
        '/relatorios',
        '/configuracoes',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($user)->get($route);
        $response->assertStatus(200);
    }
});

test('director user can access administrative menu tabs', function () {
    $director = User::factory()->create([
        'role' => 'diretor',
    ]);

    $routes = [
        '/usuarios',
        '/lixeira',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($director)->get($route);
        $response->assertStatus(200);
    }
});

test('non-director user cannot access administrative menu tabs', function () {
    $admin = User::factory()->create([
        'role' => 'administrador',
    ]);

    $routes = [
        '/usuarios',
        '/lixeira',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($admin)->get($route);
        $response->assertStatus(403);
    }
});
