<?php

use App\Models\Local;
use App\Models\Secretaria;

test('secretaria model has expected configuration', function () {
    $model = new Secretaria;

    expect($model->getTable())->toBe('secretarias');
    expect($model->getKeyName())->toBe('id_secretarias');
    expect($model->timestamps)->toBeFalse();
    expect($model->getFillable())->toBe([
        'id_secretarias',
        'secretaria',
        'chave_secretaria',
        'nome_extenso',
        'nome_secretario',
        'funcao',
        'portaria',
        'data_ext_port',
        'ano_portaria',
    ]);
});

test('local model has expected configuration', function () {
    $model = new Local;

    expect($model->getTable())->toBe('local');
    expect($model->getKeyName())->toBe('id_local');
    expect($model->timestamps)->toBeFalse();
    expect($model->getFillable())->toBe([
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
    ]);
});
