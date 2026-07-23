<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ref_ano',
])]
class Ano extends Model
{
    use HasFactory;

    protected $table = 'ano';

    protected $primaryKey = 'id_ano';

    public $timestamps = false;
}
