<?php

use App\Models\Acolhimento;
use App\Models\Ano;
use App\Models\Estado;
use App\Models\Observacao;
use App\Models\SolicitacaoArquivo;
use App\Models\User;

test('models have correct tables and keys configured', function () {
    $user = new User;
    expect($user->getTable())->toBe('users')
        ->and($user->getKeyName())->toBe('id_usuario')
        ->and($user->timestamps)->toBeFalse();

    $acolhimento = new Acolhimento;
    expect($acolhimento->getTable())->toBe('acolhimento')
        ->and($acolhimento->getKeyName())->toBe('id_acolhimento')
        ->and($acolhimento->timestamps)->toBeFalse();

    $ano = new Ano;
    expect($ano->getTable())->toBe('ano')
        ->and($ano->getKeyName())->toBe('id_ano')
        ->and($ano->timestamps)->toBeFalse();

    $estado = new Estado;
    expect($estado->getTable())->toBe('estados')
        ->and($estado->getKeyName())->toBe('id_estados')
        ->and($estado->timestamps)->toBeFalse();

    $observacao = new Observacao;
    expect($observacao->getTable())->toBe('observacao')
        ->and($observacao->getKeyName())->toBe('id_observacao')
        ->and($observacao->timestamps)->toBeFalse();

    $arquivo = new SolicitacaoArquivo;
    expect($arquivo->getTable())->toBe('solicitacao_arquivos')
        ->and($arquivo->getKeyName())->toBe('id_solicitacao_arquivo')
        ->and($arquivo->timestamps)->toBeFalse();
});

test('model relationships are defined', function () {
    $user = new User;
    expect(method_exists($user, 'acolhimentosResponsaveis'))->toBeTrue()
        ->and(method_exists($user, 'acolhimentosAlterados'))->toBeTrue()
        ->and(method_exists($user, 'observacoes'))->toBeTrue();

    $acolhimento = new Acolhimento;
    expect(method_exists($acolhimento, 'tecnicoResponsavel'))->toBeTrue()
        ->and(method_exists($acolhimento, 'usuarioAlteracao'))->toBeTrue()
        ->and(method_exists($acolhimento, 'observacoes'))->toBeTrue()
        ->and(method_exists($acolhimento, 'arquivos'))->toBeTrue();

    $observacao = new Observacao;
    expect(method_exists($observacao, 'acolhimento'))->toBeTrue()
        ->and(method_exists($observacao, 'usuario'))->toBeTrue();

    $arquivo = new SolicitacaoArquivo;
    expect(method_exists($arquivo, 'acolhimento'))->toBeTrue();
});
