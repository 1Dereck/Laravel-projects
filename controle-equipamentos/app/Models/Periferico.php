<?php

namespace App\Models;

use Database\Factories\PerifericoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int $setor_id
 * @property int|null $equipamento_id
 * @property string $tipo
 * @property string|null $serial_patrimonio
 * @property string|null $observacoes
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'setor_id',
    'equipamento_id',
    'tipo',
    'serial_patrimonio',
    'observacoes',
    'created_by',
])]
class Periferico extends Model
{
    /** @use HasFactory<PerifericoFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'perifericos';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * @return BelongsTo<Setor, $this>
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    /**
     * @return BelongsTo<Equipamento, $this>
     */
    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class, 'equipamento_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
