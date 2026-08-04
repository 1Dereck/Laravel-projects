<?php

use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Secretaria;
use App\Models\User;
use Livewire\Livewire;

test('equipamentos page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/equipamentos')
        ->assertStatus(200);
});

test('user can create a new equipamento with dynamic monitores and tipo_desempenho', function () {
    $user = User::factory()->create();
    $sec = Secretaria::create(['secretaria' => 'S.M. OBRAS', 'nome_extenso' => 'SECRETARIA DE OBRAS']);
    $local = Local::factory()->create(['local' => 'Sec. Obras', 'ip_onu' => '10.20.47.1', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('equipamento-form')
        ->set('secretaria_id', $sec->id_secretarias)
        ->set('setor_id', $local->id_local)
        ->set('tipo', 'desktop')
        ->set('tipo_desempenho', 'avancado')
        ->set('serial', 'SN-TEST-1234')
        ->set('marca_modelo', 'Dell OptiPlex')
        ->set('responsavel_levantamento', 'João Silva')
        ->set('kit_teclado_mouse_locado', true)
        ->set('monitores', [
            ['id' => null, 'numero' => 1, 'serial' => 'MON-001', 'marca_modelo' => 'Dell P2419H'],
            ['id' => null, 'numero' => 2, 'serial' => 'MON-002', 'marca_modelo' => 'LG 24MK430H'],
        ])
        ->call('salvar');

    $this->assertDatabaseHas('equipamentos', [
        'serial' => 'SN-TEST-1234',
        'setor_id' => $local->id_local,
        'tipo_desempenho' => 'avancado',
        'responsavel_levantamento' => 'João Silva',
        'kit_teclado_mouse_locado' => true,
    ]);

    $equipamento = Equipamento::where('serial', 'SN-TEST-1234')->first();
    $this->assertCount(2, $equipamento->monitores);
    $this->assertDatabaseHas('monitores', [
        'equipamento_id' => $equipamento->id,
        'serial' => 'MON-001',
        'marca_modelo' => 'Dell P2419H',
    ]);
});

test('marca_modelo, responsavel_levantamento, and monitores marca_modelo are required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('equipamento-form')
        ->set('serial', 'SN-TEST-999')
        ->set('marca_modelo', '')
        ->set('responsavel_levantamento', '')
        ->set('monitores', [
            ['id' => null, 'numero' => 1, 'serial' => 'MON-999', 'marca_modelo' => ''],
        ])
        ->call('salvar')
        ->assertHasErrors([
            'secretaria_id' => 'required',
            'setor_id' => 'required',
            'marca_modelo' => 'required',
            'responsavel_levantamento' => 'required',
            'monitores.0.marca_modelo' => 'required',
        ]);
});

test('user can edit equipment tipo_desempenho', function () {
    $user = User::factory()->create();
    $sec = Secretaria::create(['secretaria' => 'S.M. OBRAS', 'nome_extenso' => 'SECRETARIA DE OBRAS']);
    $local = Local::factory()->create(['local' => 'Sec. Obras', 'secretaria_id' => $sec->id_secretarias]);
    $equipamento = Equipamento::factory()->create([
        'setor_id' => $local->id_local,
        'tipo_desempenho' => 'administrativo',
    ]);

    Livewire::actingAs($user)
        ->test('equipamento-form')
        ->call('editarEquipamento', $equipamento->id)
        ->assertSet('tipo_desempenho', 'administrativo')
        ->assertSet('secretaria_id', $sec->id_secretarias)
        ->set('tipo_desempenho', 'avancado')
        ->call('salvar');

    $this->assertDatabaseHas('equipamentos', [
        'id' => $equipamento->id,
        'tipo_desempenho' => 'avancado',
    ]);
});

test('only diretor can delete equipamento', function () {
    $admin = User::factory()->create(['role' => 'administrador']);
    $diretor = User::factory()->create(['role' => 'diretor']);
    $local = Local::factory()->create(['local' => 'Sec. Obras']);
    $equipamento = Equipamento::factory()->create(['setor_id' => $local->id_local]);

    Livewire::actingAs($admin)
        ->test('equipamento-form')
        ->call('excluirEquipamento', $equipamento->id)
        ->assertForbidden();

    Livewire::actingAs($diretor)
        ->test('equipamento-form')
        ->call('excluirEquipamento', $equipamento->id);

    $this->assertSoftDeleted('equipamentos', [
        'id' => $equipamento->id,
    ]);
});

test('coordenador cannot create or edit equipamentos', function () {
    $local = Local::factory()->create(['local' => 'Sec. Obras']);
    $coord = User::factory()->coordenador($local->id_local)->create();
    $equipamento = Equipamento::factory()->create(['setor_id' => $local->id_local]);

    Livewire::actingAs($coord)
        ->test('equipamento-form')
        ->assertDontSee('Cadastrar Equipamento')
        ->assertDontSee('Editar')
        ->call('novoEquipamento')
        ->assertStatus(403);
});
