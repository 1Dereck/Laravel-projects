<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Afazer = 'a_fazer';
    case EmAndamento = 'em_andamento';
    case EmRevisao = 'em_revisao';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    /**
     * Retorna o texto egível para o usuário final.
     */
    public function label(): string
    {
        return match ($this) {
            self::Afazer => 'A Fazer',
            self::EmAndamento => 'Em Andamento',
            self::EmRevisao => 'Em Revisão',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }

    /**
     * Retorna a cor compatível com Flux UI / Tailwind badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::Afazer => 'zinc',
            self::EmAndamento => 'sky',
            self::EmRevisao => 'amber',
            self::Concluida => 'green',
            self::Cancelada => 'rose',
        };
    }

    /**
     * Retornar o ícone do Flux UI / Heroicons.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Afazer => 'inbox',
            self::EmAndamento => 'play',
            self::EmRevisao => 'eye',
            self::Concluida => 'check-circle',
            self::Cancelada => 'x-circle',
        };
    }

    /**
     * Verificar se a tarefa já está finalizada (concluída ou cancelada).
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Concluida, self::Cancelada]);
    }

    /**
     * Retorna um array com apenas os valores primitivos (strings).
     * Muito útil para a migration e validações!
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
