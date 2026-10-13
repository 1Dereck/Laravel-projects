<?php

namespace App\Models;

use Database\Factories\SyncQueueFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncQueue extends Model
{
    /** @use HasFactory<SyncQueueFactory> */
    use HasFactory;

    protected $table = 'sync_queue';

    protected $fillable = [
        'uuid',
        'entidade',
        'acao',
        'payload',
        'status',
        'erro_mensagem',
        'tentativas',
        'coletado_em',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'coletado_em' => 'datetime',
            'tentativas' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->coletado_em)) {
                $model->coletado_em = now();
            }
        });
    }

    /**
     * @param  Builder<SyncQueue>  $query
     * @return Builder<SyncQueue>
     */
    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', 'pendente');
    }
}
