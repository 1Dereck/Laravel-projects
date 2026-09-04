<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\assertDatabaseHas;

test('as tabelas principais do sistema foram criadas no banco', function () {
    // Verifca se a tabela users foi criada
    expect(Schema::hasTable('users'))->toBeTrue()
    // Verifca se a tabela projects foi criada
        ->and(Schema::hasTable('projects'))->toBeTrue()
        // Verifca se a tabela tasks foi criada
        ->and(Schema::hasTable('tasks'))->toBeTrue();

    expect(Schema::hasColumns('users', ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token', 'role', 'created_at', 'updated_at']))->toBeTrue();
    expect(Schema::hasColumns('projects', ['id', 'user_id', 'name', 'description', 'settings', 'api_secret', 'created_at', 'updated_at']))->toBeTrue();
    expect(Schema::hasColumns('tasks', ['id', 'project_id', 'assigned_to', 'title', 'description', 'status', 'deadline_at', 'priority', 'metadata', 'created_at', 'updated_at']))->toBeTrue();
});

test('consegue gravar o administrador no banco de dados', function () {
    // Act: criamos um usuário
    User::create([
        'name' => 'Dereck Administrador',
        'email' => 'dereck.administrador@test.com',
        'password' => bcrypt('123456'),
        'role' => UserRole::Administrador->value,
    ]);
    // Assert: checamos se ele realmente está gravado no banco SQLite
    assertDatabaseHas('users', [
        'email' => 'dereck.administrador@test.com',
        'role' => 'administrador',
    ]);
});

test('consegue gravar o gerente no banco de dados', function () {
    // Act: criamos um usuário
    User::create([
        'name' => 'Dereck Gerente',
        'email' => 'dereck.gerente@test.com',
        'password' => bcrypt('123456'),
        'role' => UserRole::Gerente->value,
    ]);
    // Assert: checamos se ele realmente está gravado no banco SQLite
    assertDatabaseHas('users', [
        'email' => 'dereck.gerente@test.com',
        'role' => 'gerente',
    ]);
});

test('consegue gravar o desenvolvedor no banco de dados', function () {
    // Act: criamos um usuário
    User::create([
        'name' => 'Dereck Desenvolvedor',
        'email' => 'dereck.desenvolvedor@test.com',
        'password' => bcrypt('123456'),
        'role' => UserRole::Desenvolvedor->value,
    ]);
    // Assert: checamos se ele realmente está gravado no banco SQLite
    assertDatabaseHas('users', [
        'email' => 'dereck.desenvolvedor@test.com',
        'role' => 'desenvolvedor',
    ]);
});

test('consegue gravar o cliente no banco de dados', function () {
    // Act: criamos um usuário
    User::create([
        'name' => 'Dereck Cliente',
        'email' => 'dereck.cliente@test.com',
        'password' => bcrypt('123456'),
        'role' => UserRole::Cliente->value,
    ]);
    // Assert: checamos se ele realmente está gravado no banco SQLite
    assertDatabaseHas('users', [
        'email' => 'dereck.cliente@test.com',
        'role' => 'cliente',
    ]);
});
