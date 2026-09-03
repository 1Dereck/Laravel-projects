<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Baixo = 'baixo';
    case Medio = 'medio';
    case Alto = 'alto';
    case Urgente = 'urgente';

    /**
     * Texto em português para a interface
     */
    public function label(): string
    {
        return match ($this) {
            self::Baixo => 'Baixo',
            self::Medio => 'Médio',
            self::Alto => 'Alto',
            self::Urgente => 'Urgente',
        };
    }

    /**
     * Cores compatíveis com Flux UI / Tailwind.
     */
    public function color(): string
    {
        return match ($this) {
            self::Baixo => 'zinc',
            self::Medio => 'sky',
            self::Alto => 'amber',
            self::Urgente => 'rose',
        };
    }

    /**
     * Valores para a migration.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
