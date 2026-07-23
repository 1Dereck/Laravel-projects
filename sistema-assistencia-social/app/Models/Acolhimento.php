<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

#[Fillable([
    'dt_nascimento',
    'nome_pessoa',
    'naturalidade',
    'estado_nasc',
    'nec_especial',
    'tipo_nec_especial',
    'depend_quimica',
    'tipo_dep_quimica',
    'transtorno',
    'tipo_transtorno',
    'cid_bairro_situacao',
    'pai',
    'mae',
    'parente_grau',
    'parente_end',
    'parente_nome',
    'parente_grau1',
    'parente_end1',
    'monitoramento',
    'obs_pessoa',
    'rg',
    'cpf',
    'recebe_beneficio',
    'tipo_beneficio',
    'nome_social',
])]
class Acolhimento extends Model
{
    use HasFactory;

    protected $table = 'acolhimento';

    protected $primaryKey = 'id_acolhimento';

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dt_cadastro' => 'date',
            'dt_nascimento' => 'date',
            'dt_alteracao' => 'datetime',
            'id_tecnico_resp' => 'integer',
            'id_usuario_alteracao' => 'integer',
        ];
    }

    /**
     * Relacionamento com o Técnico Responsável (User)
     */
    public function tecnicoResponsavel()
    {
        return $this->belongsTo(User::class, 'id_tecnico_resp', 'id_usuario');
    }

    /**
     * Relacionamento com o Usuário que alterou (User)
     */
    public function usuarioAlteracao()
    {
        return $this->belongsTo(User::class, 'id_usuario_alteracao', 'id_usuario');
    }

    /**
     * Relacionamento com as Observações/Evoluções
     */
    public function observacoes()
    {
        return $this->hasMany(Observacao::class, 'id_acolhimento', 'id_acolhimento');
    }

    /**
     * Relacionamento com os Documentos/Arquivos anexados
     */
    public function arquivos()
    {
        return $this->hasMany(SolicitacaoArquivo::class, 'id_solicitacao', 'id_acolhimento');
    }

    /**
     * Obtém o CPF formatado de acordo com a permissão do usuário logado.
     */
    public function getMaskedCpfAttribute()
    {
        $cpf = $this->cpf;
        if (empty($cpf)) {
            return '';
        }

        // Remove caracteres não-numéricos
        $cleanCpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cleanCpf) === 11) {
            $formatted = substr($cleanCpf, 0, 3).'.'.substr($cleanCpf, 3, 3).'.'.substr($cleanCpf, 6, 3).'-'.substr($cleanCpf, 9, 2);
        } else {
            // Se o CPF já possuir caracteres especiais salvos, usamos ele
            $formatted = $cpf;
        }

        // Verifica permissão usando o Gate de forma segura para evitar erros fora do contexto web
        try {
            if (Gate::allows('view-cpf', $this)) {
                return $formatted;
            }
        } catch (\Throwable $e) {
            // Ignora se o auth ou a sessão não estiverem inicializados (ex: no console, migrations)
        }

        // Se for um usuário e o cadastro for de OUTRA pessoa (não dele/não sob responsabilidade dele)
        return '***.***.***-**';
    }

    /**
     * Obtém o RG formatado de forma visual.
     */
    public function getMaskedRgAttribute()
    {
        $rg = $this->rg;
        if (empty($rg)) {
            return '';
        }

        // Remove caracteres não alfanuméricos
        $cleanRg = preg_replace('/[^0-9a-zA-Z]/', '', $rg);
        $len = strlen($cleanRg);

        if ($len === 9) {
            return substr($cleanRg, 0, 2).'.'.substr($cleanRg, 2, 3).'.'.substr($cleanRg, 5, 3).'-'.substr($cleanRg, 8, 1);
        } elseif ($len === 8) {
            return substr($cleanRg, 0, 1).'.'.substr($cleanRg, 1, 3).'.'.substr($cleanRg, 4, 3).'-'.substr($cleanRg, 7, 1);
        }

        return $rg;
    }
}
