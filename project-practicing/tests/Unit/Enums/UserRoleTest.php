<?php

use App\Enums\UserRole;

test('o enum user role possu os casos e valores corretos', function () {
    expect(UserRole::Administrador->value)->toBe('administrador')
        ->and(UserRole::Gerente->value)->toBe('gerente')
        ->and(UserRole::Desenvolvedor->value)->toBe('desenvolvedor')
        ->and(UserRole::Cliente->value)->toBe('cliente');
});

test('o enum user role retorna a label amigavel', function () {
    expect(UserRole::Administrador->label())->toBe('Administrador')
        ->and(UserRole::Gerente->label())->toBe('Gerente')
        ->and(UserRole::Desenvolvedor->label())->toBe('Desenvolvedor')
        ->and(UserRole::Cliente->label())->toBe('Cliente');
});

test('apenas administrador e gerente podem gerenciar projetos', function () {
    expect(UserRole::Administrador->canManageProjects())->toBeTrue()
        ->and(UserRole::Gerente->canManageProjects())->toBeTrue();
    expect(UserRole::Desenvolvedor->canManageProjects())->toBeFalse()
        ->and(UserRole::Cliente->canManageProjects())->toBeFalse();
});

test('o metodo values retorna todas as strings permitidas', function () {
    expect(UserRole::values())->toBe([
        'administrador',
        'gerente',
        'desenvolvedor',
        'cliente',
    ]);
});
