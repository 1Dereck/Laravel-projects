<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'administrador';
    case Gerente = 'gerente';
    case Desenvolvedor = 'desenvolvedor';
    case Cliente = 'cliente';

    /**
     * Retorna um label legível para o usuário final.
     */
    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Gerente => 'Gerente',
            self::Desenvolvedor => 'Desenvolvedor',
            self::Cliente => 'Cliente',
        };
    }

    /**
     * Verifica se o usuário pode gerenciar projetos.
     */
    public function canManageProjects(): bool
    {
        return in_array($this, [self::Administrador, self::Gerente]);
    }

    /**
     * Verifica se o usuário é administrador.
     */
    public function isAdministrador(): bool
    {
        return $this === self::Administrador;
    }

    /**
     * Verifica se o usuário é gerente.
     */
    public function isGerente(): bool
    {
        return $this === self::Gerente;
    }

    /**
     * Verifica se o usuário é desenvolvedor.
     */
    public function isDesenvolvedor(): bool
    {
        return $this === self::Desenvolvedor;
    }

    /**
     * Verifica se o usuário é cliente.
     */
    public function isCliente(): bool
    {
        return $this === self::Cliente;
    }

    /**
     * Verifica se o usuário pode gerenciar usuários.
     */
    public function canManageUsers(): bool
    {
        return in_array($this, [self::Administrador, self::Gerente]);
    }

    /**
     * Retorna a cor compatível com Flux UI / Tailwind badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::Administrador => 'purple',
            self::Gerente => 'amber',
            self::Desenvolvedor => 'sky',
            self::Cliente => 'zinc',
        };
    }

    /**
     * Retorna um array com apenas os valores primitivos (strings).
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
