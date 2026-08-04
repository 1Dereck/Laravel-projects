<?php

namespace App\Models;

use Database\Factories\LocalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_local
 * @property int|null $secretaria_id
 * @property string|null $local
 * @property string|null $telefone
 * @property string|null $bairro
 * @property string|null $rua
 * @property int|null $numero
 * @property string|null $cep
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $status
 * @property string $ultima_atualizacao
 * @property string|null $ip_onu
 * @property string|null $tipo_local
 * @property string|null $flag_situacao
 */
class Local extends Model
{
    /** @use HasFactory<LocalFactory> */
    use HasFactory;

    protected $table = 'local';

    protected $primaryKey = 'id_local';

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_local',
        'secretaria_id',
        'local',
        'telefone',
        'bairro',
        'rua',
        'numero',
        'cep',
        'latitude',
        'longitude',
        'status',
        'ultima_atualizacao',
        'ip_onu',
        'tipo_local',
        'flag_situacao',
    ];

    /**
     * @return BelongsTo<Secretaria, $this>
     */
    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id', 'id_secretarias');
    }

    /**
     * @return HasMany<Equipamento, $this>
     */
    public function equipamentos(): HasMany
    {
        return $this->hasMany(Equipamento::class, 'setor_id', 'id_local');
    }

    /**
     * @return HasMany<Periferico, $this>
     */
    public function perifericos(): HasMany
    {
        return $this->hasMany(Periferico::class, 'setor_id', 'id_local');
    }
}
