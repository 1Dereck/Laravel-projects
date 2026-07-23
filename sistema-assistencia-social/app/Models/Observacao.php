<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'descricao',
    'tipo',
])]
class Observacao extends Model
{
    use HasFactory;

    protected $table = 'observacao';

    protected $primaryKey = 'id_observacao';

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_acolhimento' => 'integer',
            'id_assunto' => 'integer',
            'id_usuario' => 'integer',
            'ultima_data' => 'datetime',
        ];
    }

    /**
     * Relacionamento com o Acolhimento
     */
    public function acolhimento()
    {
        return $this->belongsTo(Acolhimento::class, 'id_acolhimento', 'id_acolhimento');
    }

    /**
     * Relacionamento com o Usuário/Técnico que inseriu a observação
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
