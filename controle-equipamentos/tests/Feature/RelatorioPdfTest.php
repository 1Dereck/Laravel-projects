<?php

use App\Models\Setor;
use App\Models\User;

test('relatorios page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/relatorios')
        ->assertStatus(200);
});

test('pdf report can be generated for a setor', function () {
    $user = User::factory()->create();
    $setor = Setor::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('relatorios.pdf', $setor));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});
