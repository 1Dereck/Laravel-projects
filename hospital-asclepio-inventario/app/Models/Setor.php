<?php

namespace App\Models;

use Database\Factories\SetorFactory;
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
 * @property string $nome
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['nome', 'created_by'])]
class Setor extends Model
{
    /** @use HasFactory<SetorFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'setores';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<Equipamento, $this>
     */
    public function equipamentos(): HasMany
    {
        return $this->hasMany(Equipamento::class, 'setor_id');
    }

    /**
     * @return HasMany<Periferico, $this>
     */
    public function perifericos(): HasMany
    {
        return $this->hasMany(Periferico::class, 'setor_id');
    }
}
