<?php

namespace App\Models;

use Database\Factories\OfflineDadoMestreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineDadoMestre extends Model
{
    /** @use HasFactory<OfflineDadoMestreFactory> */
    use HasFactory;

    protected $table = 'offline_dados_mestres';

    protected $fillable = [
        'tipo',
        'servidor_id',
        'nome',
        'pai_id',
        'metadados',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'servidor_id' => 'integer',
            'pai_id' => 'integer',
            'metadados' => 'array',
        ];
    }
}
