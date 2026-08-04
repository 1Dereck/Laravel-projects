<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

/**
 * @property int $id_secretarias
 * @property string $secretaria
 * @property string $chave_secretaria
 * @property string $nome_extenso
 * @property string|null $nome_secretario
 * @property string|null $funcao
 * @property string $portaria
 * @property string|null $data_ext_port
 * @property int|null $ano_portaria
 */
class Secretaria extends Model
{
    protected $table = 'secretarias';

    protected $primaryKey = 'id_secretarias';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_secretarias',
        'secretaria',
        'chave_secretaria',
        'nome_extenso',
        'nome_secretario',
        'funcao',
        'portaria',
        'data_ext_port',
        'ano_portaria',
    ];

    /**
     * @return HasMany<Local, $this>
     */
    public function locais(): HasMany
    {
        return $this->hasMany(Local::class, 'secretaria_id', 'id_secretarias');
    }

    /**
     * @return HasManyThrough<Equipamento, Local, $this>
     */
    public function equipamentos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Equipamento::class,
            Local::class,
            'secretaria_id',
            'setor_id',
            'id_secretarias',
            'id_local'
        );
    }

    /**
     * @return HasManyThrough<Periferico, Local, $this>
     */
    public function perifericos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Periferico::class,
            Local::class,
            'secretaria_id',
            'setor_id',
            'id_secretarias',
            'id_local'
        );
    }

    /**
     * Get only actual assigned sub-locations (excluding any local named after the secretaria itself).
     *
     * @return Collection<int, Local>
     */
    public function getLocaisAtribuidosAttribute(): Collection
    {
        $secName = mb_strtolower(trim($this->secretaria));
        $secExt = mb_strtolower(trim($this->nome_extenso));
        $cleanSec = preg_replace('/^(s\.m\.\s*|secretaria\s+municipal\s+de\s+|secretaria\s+municipal\s+do\s+|secretaria\s+municipal\s+da\s+|secretaria\s+)/iu', '', $secName);

        return $this->locais->reject(function (Local $l) use ($secName, $secExt, $cleanSec) {
            $locName = mb_strtolower(trim($l->local ?? ''));
            $cleanLoc = preg_replace('/^(sec\.\s*|secretaria\s+)/iu', '', $locName);

            return $locName === $secName
                || $locName === $secExt
                || ($cleanSec !== '' && $cleanLoc === $cleanSec);
        })->values();
    }
}
