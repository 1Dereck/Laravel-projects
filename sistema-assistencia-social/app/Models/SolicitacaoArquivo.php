<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'observacao',
    'tipo',
])]
class SolicitacaoArquivo extends Model
{
    use HasFactory;

    protected $table = 'solicitacao_arquivos';

    protected $primaryKey = 'id_solicitacao_arquivo';

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_solicitacao' => 'integer',
            'cancelado' => 'integer',
            'data_inclusao' => 'datetime',
        ];
    }

    /**
     * Relacionamento com o Acolhimento (a coluna id_solicitacao armazena id_acolhimento)
     */
    public function acolhimento()
    {
        return $this->belongsTo(Acolhimento::class, 'id_solicitacao', 'id_acolhimento');
    }
}
