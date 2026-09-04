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

test('o enum user role retorna a cor compativel com flux ui / tailwind badges', function () {
    expect(UserRole::Administrador->color())->toBe('purple')
        ->and(UserRole::Gerente->color())->toBe('amber')
        ->and(UserRole::Desenvolvedor->color())->toBe('sky')
        ->and(UserRole::Cliente->color())->toBe('zinc');
});

test('apenas administrador e gerente podem gerenciar usuarios', function () {
    expect(UserRole::Administrador->canManageUsers())->toBeTrue()
        ->and(UserRole::Gerente->canManageUsers())->toBeTrue();

    expect(UserRole::Desenvolvedor->canManageUsers())->toBeFalse()
        ->and(UserRole::Cliente->canManageUsers())->toBeFalse();
});

test('o metodo isDesenvolvedor identifica corretamente o papel', function () {
    expect(UserRole::Desenvolvedor->isDesenvolvedor())->toBeTrue();

    expect(UserRole::Administrador->isDesenvolvedor())->toBeFalse()
        ->and(UserRole::Gerente->isDesenvolvedor())->toBeFalse()
        ->and(UserRole::Cliente->isDesenvolvedor())->toBeFalse();
});

test('os metodos de verificacao de papel identificam corretamente seus papeis', function () {
    expect(UserRole::Administrador->isAdministrador())->toBeTrue()
        ->and(UserRole::Gerente->isAdministrador())->toBeFalse()
        ->and(UserRole::Desenvolvedor->isAdministrador())->toBeFalse()
        ->and(UserRole::Cliente->isAdministrador())->toBeFalse();

    expect(UserRole::Gerente->isGerente())->toBeTrue()
        ->and(UserRole::Administrador->isGerente())->toBeFalse()
        ->and(UserRole::Desenvolvedor->isGerente())->toBeFalse()
        ->and(UserRole::Cliente->isGerente())->toBeFalse();

    expect(UserRole::Cliente->isCliente())->toBeTrue()
        ->and(UserRole::Administrador->isCliente())->toBeFalse()
        ->and(UserRole::Gerente->isCliente())->toBeFalse()
        ->and(UserRole::Desenvolvedor->isCliente())->toBeFalse();
});
