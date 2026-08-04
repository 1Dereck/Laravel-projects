<?php

use App\Models\User;

test('authenticated admin can access standard routes including usuarios', function () {
    $admin = User::factory()->create([
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
        '/usuarios',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($admin)->get($route);
        $response->assertStatus(200);
    }
});

test('director user can access all administrative routes including lixeira', function () {
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

test('non-director user cannot access lixeira route', function () {
    $admin = User::factory()->create([
        'role' => 'administrador',
    ]);

    $response = $this->actingAs($admin)->get('/lixeira');
    $response->assertStatus(403);
});

test('admin sees gestao de usuarios link in navigation sidebar', function () {
    $admin = User::factory()->create([
        'role' => 'administrador',
    ]);

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertStatus(200);
    $response->assertSee('Gestão de Usuários');
});
