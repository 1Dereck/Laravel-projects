<?php

use App\Enums\TaskStatus;

test('o enum task status possui os casos e valores corretos', function () {
    expect(TaskStatus::Afazer->value)->toBe('a_fazer')
        ->and(TaskStatus::EmAndamento->value)->toBe('em_andamento')
        ->and(TaskStatus::EmRevisao->value)->toBe('em_revisao')
        ->and(TaskStatus::Concluida->value)->toBe('concluida')
        ->and(TaskStatus::Cancelada->value)->toBe('cancelada');
});

test('o enum task status retorna a label amigavel', function () {
    expect(TaskStatus::Afazer->label())->toBe('A Fazer')
        ->and(TaskStatus::EmAndamento->label())->toBe('Em Andamento')
        ->and(TaskStatus::EmRevisao->label())->toBe('Em Revisão')
        ->and(TaskStatus::Concluida->label())->toBe('Concluída')
        ->and(TaskStatus::Cancelada->label())->toBe('Cancelada');
});

test('o enum task status retorna a cor compatível com flux ui / tailwind badges', function () {
    expect(TaskStatus::Afazer->color())->toBe('zinc')
        ->and(TaskStatus::EmAndamento->color())->toBe('sky')
        ->and(TaskStatus::EmRevisao->color())->toBe('amber')
        ->and(TaskStatus::Concluida->color())->toBe('green')
        ->and(TaskStatus::Cancelada->color())->toBe('rose');
});

test('o enum task status retorna o icone do flux ui / heroicons', function () {
    expect(TaskStatus::Afazer->icon())->toBe('inbox')
        ->and(TaskStatus::EmAndamento->icon())->toBe('play')
        ->and(TaskStatus::EmRevisao->icon())->toBe('eye')
        ->and(TaskStatus::Concluida->icon())->toBe('check-circle')
        ->and(TaskStatus::Cancelada->icon())->toBe('x-circle');
});

test('o enum task status retorna um array com todos os valores', function () {
    expect(TaskStatus::values())->toBe([
        'a_fazer',
        'em_andamento',
        'em_revisao',
        'concluida',
        'cancelada',
    ]);
});

test('identifica corretamente se o status e final', function () {
    expect(TaskStatus::Concluida->isFinal())->toBeTrue()
        ->and(TaskStatus::Cancelada->isFinal())->toBeTrue();

    expect(TaskStatus::Afazer->isFinal())->toBeFalse()
        ->and(TaskStatus::EmAndamento->isFinal())->toBeFalse()
        ->and(TaskStatus::EmRevisao->isFinal())->toBeFalse();
});
