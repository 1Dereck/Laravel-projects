<?php

use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Periferico;
use App\Models\Secretaria;
use App\Models\User;
use Livewire\Livewire;

test('equipamento form allows selecting secretaria and filtering locais', function () {
    $user = User::factory()->create();
    $sec1 = Secretaria::create([
        'secretaria' => 'S.M. SAÚDE',
        'nome_extenso' => 'SECRETARIA MUNICIPAL DE SAÚDE',
    ]);
    $sec2 = Secretaria::create([
        'secretaria' => 'S.M. EDUCAÇÃO',
        'nome_extenso' => 'SECRETARIA MUNICIPAL DE EDUCAÇÃO',
    ]);

    $local1 = Local::factory()->create(['local' => 'Posto de Saúde Central', 'secretaria_id' => $sec1->id_secretarias]);
    $local2 = Local::factory()->create(['local' => 'Escola Municipal 1', 'secretaria_id' => $sec2->id_secretarias]);

    Livewire::actingAs($user)
        ->test('equipamento-form')
        ->set('secretaria_id', $sec1->id_secretarias)
        ->set('setor_id', $local1->id_local)
        ->set('tipo', 'desktop')
        ->set('tipo_desempenho', 'administrativo')
        ->set('serial', 'SN-SEC-001')
        ->set('marca_modelo', 'Dell OptiPlex')
        ->set('responsavel_levantamento', 'Técnico TI')
        ->call('salvar');

    $this->assertDatabaseHas('equipamentos', [
        'serial' => 'SN-SEC-001',
        'setor_id' => $local1->id_local,
    ]);

    $equipamento = Equipamento::where('serial', 'SN-SEC-001')->first();
    expect($equipamento->local->secretaria_id)->toBe($sec1->id_secretarias);
});

test('periferico manager allows selecting secretaria and filtering locais', function () {
    $user = User::factory()->create();
    $sec = Secretaria::create([
        'secretaria' => 'S.M. OBRAS',
        'nome_extenso' => 'SECRETARIA MUNICIPAL DE OBRAS',
    ]);
    $local = Local::factory()->create(['local' => 'Almoxarifado Obras', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->set('secretaria_id', $sec->id_secretarias)
        ->set('setor_id', $local->id_local)
        ->set('tipo', 'Impressora Zebra')
        ->set('serial_patrimonio', 'PAT-OBRAS-001')
        ->call('salvar');

    $this->assertDatabaseHas('perifericos', [
        'serial_patrimonio' => 'PAT-OBRAS-001',
        'setor_id' => $local->id_local,
    ]);

    $periferico = Periferico::where('serial_patrimonio', 'PAT-OBRAS-001')->first();
    expect($periferico->local->secretaria_id)->toBe($sec->id_secretarias);
});

test('setor manager displays locais and secretarias correctly', function () {
    $user = User::factory()->create(['role' => 'diretor']);
    $sec = Secretaria::create([
        'secretaria' => 'S.M. MEIO AMBIENTE',
        'nome_extenso' => 'SECRETARIA MUNICIPAL DE MEIO AMBIENTE',
    ]);
    $local = Local::factory()->create(['local' => 'Horto Florestal', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('setor-manager')
        ->assertSee('Horto Florestal')
        ->assertSee('S.M. MEIO AMBIENTE');
});

test('setor manager truncates locais pertenentes after 2 and opens details modal with full list', function () {
    $user = User::factory()->create(['role' => 'diretor']);
    $sec = Secretaria::create([
        'secretaria' => 'S.M. INFRAESTRUTURA',
        'nome_extenso' => 'SECRETARIA MUNICIPAL DE INFRAESTRUTURA',
    ]);

    $loc1 = Local::factory()->create(['local' => 'Garagem Municipal', 'secretaria_id' => $sec->id_secretarias]);
    $loc2 = Local::factory()->create(['local' => 'Usina de Asfalto', 'secretaria_id' => $sec->id_secretarias]);
    $loc3 = Local::factory()->create(['local' => 'Oficina Mecânica', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('setor-manager')
        ->set('activeTab', 'secretarias')
        ->assertSee('Garagem Municipal')
        ->assertSee('Usina de Asfalto')
        ->assertSee('+1 ...')
        ->call('abrirDetalhesSecretaria', $sec->id_secretarias)
        ->assertSet('showSecretariaModal', true)
        ->assertSee('Todos os Locais Atribuídos (3)')
        ->assertSee('Oficina Mecânica')
        ->call('fecharModal')
        ->assertSet('showSecretariaModal', false);
});
