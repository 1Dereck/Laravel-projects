<?php

namespace App\Models;

use Database\Factories\EquipamentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int $setor_id
 * @property string $tipo
 * @property string $tipo_desempenho
 * @property string $serial
 * @property string|null $marca_modelo
 * @property bool $kit_teclado_mouse_locado
 * @property string|null $responsavel_levantamento
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'setor_id',
    'tipo',
    'tipo_desempenho',
    'serial',
    'marca_modelo',
    'kit_teclado_mouse_locado',
    'responsavel_levantamento',
    'created_by',
])]
class Equipamento extends Model
{
    /** @use HasFactory<EquipamentoFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'equipamentos';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'kit_teclado_mouse_locado' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Setor, $this>
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    /**
     * @return BelongsTo<Local, $this>
     */
    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'setor_id', 'id_local');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<Monitor, $this>
     */
    public function monitores(): HasMany
    {
        return $this->hasMany(Monitor::class, 'equipamento_id');
    }

    /**
     * @return HasMany<Periferico, $this>
     */
    public function perifericos(): HasMany
    {
        return $this->hasMany(Periferico::class, 'equipamento_id');
    }
}
