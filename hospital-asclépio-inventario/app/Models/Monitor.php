<?php

namespace App\Models;

use Database\Factories\MonitorFactory;
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
 * @property int $equipamento_id
 * @property int $numero
 * @property string $serial
 * @property string|null $marca_modelo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable([
    'equipamento_id',
    'numero',
    'serial',
    'marca_modelo',
])]
class Monitor extends Model
{
    /** @use HasFactory<MonitorFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'monitores';

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
            'numero' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Equipamento, $this>
     */
    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class, 'equipamento_id');
    }
}
