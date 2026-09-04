<?php

use App\Enums\TaskPriority;

test('o task priority possui os casos e valores corretos', function () {
    expect(TaskPriority::Baixo->value)->toBe('baixo')
        ->and(TaskPriority::Medio->value)->toBe('medio')
        ->and(TaskPriority::Alto->value)->toBe('alto')
        ->and(TaskPriority::Urgente->value)->toBe('urgente');
});

test('o enum task priority retorna a label amigavel', function () {
    expect(TaskPriority::Baixo->label())->toBe('Baixo')
        ->and(TaskPriority::Medio->label())->toBe('Médio')
        ->and(TaskPriority::Alto->label())->toBe('Alto')
        ->and(TaskPriority::Urgente->label())->toBe('Urgente');
});

test('o enum task priority retorna a cor compatível com flux ui / tailwind badges', function () {
    expect(TaskPriority::Baixo->color())->toBe('zinc')
        ->and(TaskPriority::Medio->color())->toBe('sky')
        ->and(TaskPriority::Alto->color())->toBe('amber')
        ->and(TaskPriority::Urgente->color())->toBe('rose');
});

test('o enum task priority retorna um array com todos os valores', function () {
    expect(TaskPriority::values())->toBe([
        'baixo',
        'medio',
        'alto',
        'urgente',
    ]);
});
