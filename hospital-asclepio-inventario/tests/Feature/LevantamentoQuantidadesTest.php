<?php

use App\Livewire\LevantamentoQuantidades;
use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Secretaria;
use App\Models\User;
use Livewire\Livewire;

test('pagina de levantamento de quantidades pode ser acessada por usuario autenticado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/quantidades')
        ->assertStatus(200);
});

test('diretor e admin entram com todas as secretarias selecionadas por padrao', function () {
    $diretor = User::factory()->diretor()->create();

    $secretaria1 = Secretaria::create([
        'secretaria' => 'S.M. OBRAS',
        'chave_secretaria' => 'OBRAS',
        'nome_extenso' => 'Secretaria Municipal de Obras',
        'portaria' => '123/2024',
    ]);

    $secretaria2 = Secretaria::create([
        'secretaria' => 'S.M. SAÚDE',
        'chave_secretaria' => 'SAUDE',
        'nome_extenso' => 'Secretaria Municipal de Saúde',
        'portaria' => '456/2024',
    ]);

    $local1 = Local::create([
        'local' => 'Garagem Central',
        'secretaria_id' => $secretaria1->id_secretarias,
    ]);

    $local2 = Local::create([
        'local' => 'Posto Central',
        'secretaria_id' => $secretaria2->id_secretarias,
    ]);

    Equipamento::create([
        'setor_id' => $local1->id_local,
        'tipo' => 'desktop',
        'tipo_desempenho' => 'medio',
        'serial' => 'DESK-001',
        'marca_modelo' => 'Dell OptiPlex',
        'kit_teclado_mouse_locado' => true,
    ]);

    Equipamento::create([
        'setor_id' => $local2->id_local,
        'tipo' => 'notebook',
        'tipo_desempenho' => 'alto',
        'serial' => 'NOTE-001',
        'marca_modelo' => 'Lenovo ThinkPad',
        'kit_teclado_mouse_locado' => false,
    ]);

    Livewire::actingAs($diretor)
        ->test(LevantamentoQuantidades::class)
        ->assertSet('modoQuantidades', 'secretaria')
        ->assertSet('quantidadesSecretariaId', null)
        ->assertSee('Todas as Secretarias (Geral)')
        ->assertSee('Desktops')
        ->assertSee('Notebooks')
        ->assertSee('Total de PCs')
        ->set('quantidadesSecretariaId', $secretaria1->id_secretarias)
        ->assertSee('S.M. OBRAS')
        ->call('verLocalIsolado', $local1->id_local)
        ->assertSet('modoQuantidades', 'local')
        ->assertSet('quantidadesLocalId', $local1->id_local);
});

test('modo local permite selecionar todos os locais por padrao ou isolar um local especifico', function () {
    $diretor = User::factory()->diretor()->create();

    $local1 = Local::create(['local' => 'Local Alfa']);
    $local2 = Local::create(['local' => 'Local Beta']);

    Equipamento::create([
        'setor_id' => $local1->id_local,
        'tipo' => 'desktop',
        'tipo_desempenho' => 'medio',
        'serial' => 'ALFA-01',
    ]);

    Equipamento::create([
        'setor_id' => $local2->id_local,
        'tipo' => 'notebook',
        'tipo_desempenho' => 'alto',
        'serial' => 'BETA-01',
    ]);

    Livewire::actingAs($diretor)
        ->test(LevantamentoQuantidades::class)
        ->set('modoQuantidades', 'local')
        ->assertSet('quantidadesLocalId', null)
        ->assertSee('Todos os Locais / Setores (Geral)')
        ->assertSee('Desktops')
        ->assertSee('Notebooks')
        ->assertSee('Total de PCs')
        ->set('quantidadesLocalId', $local1->id_local)
        ->assertSee('Local Alfa');
});

test('coordenador fica restrito a sua secretaria no levantamento', function () {
    $secretaria = Secretaria::create([
        'secretaria' => 'S.M. SAÚDE',
        'chave_secretaria' => 'SAUDE',
        'nome_extenso' => 'Secretaria Municipal de Saúde',
        'portaria' => '456/2024',
    ]);

    $local1 = Local::create([
        'local' => 'Posto Central',
        'secretaria_id' => $secretaria->id_secretarias,
    ]);

    $coordenador = User::factory()->coordenador($local1->id_local)->create();

    $outraSecretaria = Secretaria::create([
        'secretaria' => 'S.M. EDUCAÇÃO',
        'chave_secretaria' => 'EDUC',
        'nome_extenso' => 'Secretaria Municipal de Educação',
        'portaria' => '789/2024',
    ]);

    $localOutro = Local::create([
        'local' => 'Escola Central',
        'secretaria_id' => $outraSecretaria->id_secretarias,
    ]);

    Livewire::actingAs($coordenador)
        ->test(LevantamentoQuantidades::class)
        ->assertSet('quantidadesSecretariaId', $secretaria->id_secretarias)
        ->call('verLocalIsolado', $localOutro->id_local)
        ->assertNotSet('quantidadesLocalId', $localOutro->id_local);
});

test('usuario comum ve apenas o levantamento isolado do seu local', function () {
    $local = Local::create([
        'local' => 'Posto de Saude Sul',
    ]);

    $usuario = User::factory()->usuario($local->id_local)->create();

    Equipamento::create([
        'setor_id' => $local->id_local,
        'tipo' => 'desktop',
        'tipo_desempenho' => 'basico',
        'serial' => 'DESK-SUL-1',
        'marca_modelo' => 'HP ProDesk',
        'kit_teclado_mouse_locado' => true,
    ]);

    Livewire::actingAs($usuario)
        ->test(LevantamentoQuantidades::class)
        ->assertSet('modoQuantidades', 'local')
        ->assertSet('quantidadesLocalId', $local->id_local)
        ->assertSee('Posto de Saude Sul')
        ->assertDontSee('1. Por Secretaria');
});

test('pode gerar pdf de levantamento de secretaria e local inclusive geral', function () {
    $diretor = User::factory()->diretor()->create();

    $secretaria = Secretaria::create([
        'secretaria' => 'S.M. OBRAS',
        'chave_secretaria' => 'OBRAS',
        'nome_extenso' => 'Secretaria Municipal de Obras',
        'portaria' => '123/2024',
    ]);

    $local = Local::create([
        'local' => 'Almoxarifado',
        'secretaria_id' => $secretaria->id_secretarias,
    ]);

    $this->actingAs($diretor)
        ->get('/quantidades/secretaria/0/pdf')
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($diretor)
        ->get("/quantidades/secretaria/{$secretaria->id_secretarias}/pdf")
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($diretor)
        ->get('/quantidades/local/0/pdf')
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($diretor)
        ->get("/quantidades/local/{$local->id_local}/pdf")
        ->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
});
