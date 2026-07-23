<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'login',
    'nome_usu',
])]
#[Hidden([
    'senha',
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->senha;
    }

    /**
     * Get the column name for the primary key.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    /**
     * Get the remember token name (not used in legacy database).
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Get the remember token value (not used in legacy database).
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the remember token value (not used in legacy database).
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Do nothing since column does not exist
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
            'id_usuario_alteracao' => 'integer',
            'dt_alteracao' => 'datetime',
        ];
    }

    /**
     * Verifica se o usuário é um Diretor.
     */
    public function isDiretor(): bool
    {
        return $this->permissao === 'd';
    }

    /**
     * Verifica se o usuário é um Administrador.
     */
    public function isAdmin(): bool
    {
        return $this->permissao === 'a';
    }

    /**
     * Verifica se o usuário é um Usuário.
     */
    public function isUsuario(): bool
    {
        return $this->permissao === 'n' || (! $this->isAdmin() && ! $this->isDiretor());
    }

    /**
     * Relacionamentos
     */
    public function acolhimentosResponsaveis()
    {
        return $this->hasMany(Acolhimento::class, 'id_tecnico_resp', 'id_usuario');
    }

    public function acolhimentosAlterados()
    {
        return $this->hasMany(Acolhimento::class, 'id_usuario_alteracao', 'id_usuario');
    }

    public function observacoes()
    {
        return $this->hasMany(Observacao::class, 'id_usuario', 'id_usuario');
    }
}
