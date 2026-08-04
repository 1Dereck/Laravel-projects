<?php

use App\Livewire\EquipamentoForm;
use App\Livewire\PerifericoManager;
use App\Livewire\UserManagement;
use App\Models\Equipamento;
use App\Models\Local;
use App\Models\Periferico;
use App\Models\Secretaria;
use App\Models\User;
use Livewire\Livewire;

test('diretor pode cadastrar um novo usuario do tipo usuario vinculado a um setor', function () {
    $diretor = User::factory()->diretor()->create();
    $sec = Secretaria::create(['secretaria' => 'SEMUS', 'chave_secretaria' => 'semus', 'nome_extenso' => 'Sec Saude', 'portaria' => '123']);
    $local = Local::create([
        'local' => 'Escola Municipal Monteiro Lobato',
        'secretaria_id' => $sec->id_secretarias,
        'status' => 'Ativo',
        'ultima_atualizacao' => now()->toDateTimeString(),
    ]);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->set('name', 'Servidor Escola')
        ->set('username', 'servidor.escola')
        ->set('password', 'senha123')
        ->set('role', 'usuario')
        ->set('secretaria_id', $sec->id_secretarias)
        ->set('setor_id', $local->id_local)
        ->call('salvar')
        ->assertHasNoErrors();

    $newUser = User::where('username', 'servidor.escola')->first();
    expect($newUser)->not->toBeNull()
        ->and($newUser->isUsuario())->toBeTrue()
        ->and($newUser->setor_id)->toBe($local->id_local);
});

test('usuario nao pode acessar setores mas pode acessar relatorios do seu setor', function () {
    $local = Local::create(['local' => 'Posto de Saude Central']);
    $usuario = User::factory()->usuario($local->id_local)->create();

    $this->actingAs($usuario)
        ->get(route('setores.index'))
        ->assertStatus(403);

    $this->actingAs($usuario)
        ->get(route('relatorios.index'))
        ->assertStatus(200);
});

test('usuario ve apenas equipamentos do seu setor', function () {
    $localA = Local::create(['local' => 'Setor A']);
    $localB = Local::create(['local' => 'Setor B']);

    $usuario = User::factory()->usuario($localA->id_local)->create();

    $eqA = Equipamento::create([
        'setor_id' => $localA->id_local,
        'tipo' => 'desktop',
        'serial' => 'SN-SETORA-001',
        'created_by' => $usuario->id,
    ]);

    $eqB = Equipamento::create([
        'setor_id' => $localB->id_local,
        'tipo' => 'desktop',
        'serial' => 'SN-SETORB-002',
        'created_by' => $usuario->id,
    ]);

    Livewire::actingAs($usuario)
        ->test(EquipamentoForm::class)
        ->assertSee('SN-SETORA-001')
        ->assertDontSee('SN-SETORB-002');
});

test('usuario ve apenas perifericos do seu setor', function () {
    $localA = Local::create(['local' => 'Setor A']);
    $localB = Local::create(['local' => 'Setor B']);

    $usuario = User::factory()->usuario($localA->id_local)->create();

    Periferico::create([
        'setor_id' => $localA->id_local,
        'tipo' => 'Impressora Setor A',
        'serial_patrimonio' => 'PAT-A-01',
        'created_by' => $usuario->id,
    ]);

    Periferico::create([
        'setor_id' => $localB->id_local,
        'tipo' => 'Impressora Setor B',
        'serial_patrimonio' => 'PAT-B-02',
        'created_by' => $usuario->id,
    ]);

    Livewire::actingAs($usuario)
        ->test(PerifericoManager::class)
        ->assertSee('PAT-A-01')
        ->assertDontSee('PAT-B-02');
});

test('diretor pode alternar abas e pesquisar usuarios na gestao de usuarios', function () {
    $diretor = User::factory()->diretor()->create(['name' => 'Diretor Carlos', 'username' => 'carlos.diretor']);
    $admin = User::factory()->admin()->create(['name' => 'Admin Ana', 'username' => 'ana.admin']);

    $local = Local::create(['local' => 'Secretaria de Saude']);
    $usuario = User::factory()->usuario($local->id_local)->create(['name' => 'Servidor Bruno', 'username' => 'bruno.servidor']);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->set('activeTab', 'usuario')
        ->assertSee('Servidor Bruno')
        ->assertDontSee('Diretor Carlos')
        ->assertDontSee('Admin Ana')
        ->call('setTab', 'diretor')
        ->assertSee('Diretor Carlos')
        ->assertDontSee('Servidor Bruno')
        ->call('setTab', 'administrador')
        ->assertSee('Admin Ana')
        ->assertDontSee('Servidor Bruno')
        ->call('setTab', 'usuario')
        ->set('search', 'Bruno')
        ->assertSee('Servidor Bruno');
});

test('diretor pode cadastrar um coordenador vinculado a um setor', function () {
    $diretor = User::factory()->diretor()->create();
    $sec = Secretaria::create(['secretaria' => 'SEMED', 'chave_secretaria' => 'semed', 'nome_extenso' => 'Sec Educacao', 'portaria' => '124']);
    $local = Local::create([
        'local' => 'Secretaria de Educacao',
        'secretaria_id' => $sec->id_secretarias,
        'status' => 'Ativo',
        'ultima_atualizacao' => now()->toDateTimeString(),
    ]);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->set('name', 'Coordenador Joao')
        ->set('username', 'joao.coordenador')
        ->set('password', 'senha123')
        ->set('role', 'coordenador')
        ->set('secretaria_id', $sec->id_secretarias)
        ->call('salvar')
        ->assertHasNoErrors();

    $newCoord = User::where('username', 'joao.coordenador')->first();
    expect($newCoord)->not->toBeNull()
        ->and($newCoord->isCoordenador())->toBeTrue()
        ->and($newCoord->setor_id)->toBe($local->id_local);
});

test('administrador ao criar conta vincula automaticamente ao setor T.I', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Novo Admin TI')
        ->set('username', 'admin.ti')
        ->set('password', 'senha123')
        ->set('role', 'administrador')
        ->call('salvar')
        ->assertHasNoErrors();

    $newAdmin = User::where('username', 'admin.ti')->first();
    expect($newAdmin)->not->toBeNull()
        ->and($newAdmin->isAdmin())->toBeTrue()
        ->and($newAdmin->setor->local)->toBe('T.I');
});

test('administrador nao pode criar conta do tipo diretor', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Tentativa Diretor')
        ->set('username', 'tentativa.diretor')
        ->set('password', 'senha123')
        ->set('role', 'diretor')
        ->call('salvar')
        ->assertStatus(403);
});

test('coordenador so visualiza usuarios do seu proprio setor', function () {
    $secA = Secretaria::create(['secretaria' => 'SEC_A', 'chave_secretaria' => 'seca', 'nome_extenso' => 'Secretaria A', 'portaria' => '1']);
    $localA = Local::create(['local' => 'Local A', 'secretaria_id' => $secA->id_secretarias]);

    $secB = Secretaria::create(['secretaria' => 'SEC_B', 'chave_secretaria' => 'secb', 'nome_extenso' => 'Secretaria B', 'portaria' => '2']);
    $localB = Local::create(['local' => 'Local B', 'secretaria_id' => $secB->id_secretarias]);

    $coordenador = User::factory()->coordenador($localA->id_local)->create();
    $userA = User::factory()->usuario($localA->id_local)->create(['name' => 'Usuario Mesma Sec', 'username' => 'user.mesma']);
    $userB = User::factory()->usuario($localB->id_local)->create(['name' => 'Usuario Outra Sec', 'username' => 'user.outra']);

    Livewire::actingAs($coordenador)
        ->test(UserManagement::class)
        ->assertSee('Usuario Mesma Sec')
        ->assertDontSee('Usuario Outra Sec');
});

test('administrador e coordenador realizam soft delete ao excluir conta', function () {
    $sec = Secretaria::create(['secretaria' => 'SEC_TEST', 'chave_secretaria' => 'sectest', 'nome_extenso' => 'Secretaria Teste', 'portaria' => '3']);
    $local = Local::create(['local' => 'Local Teste', 'secretaria_id' => $sec->id_secretarias]);

    $admin = User::factory()->admin()->create();
    $coord = User::factory()->coordenador($local->id_local)->create();
    $targetUser = User::factory()->usuario($local->id_local)->create();

    Livewire::actingAs($coord)
        ->test(UserManagement::class)
        ->call('inativarUsuario', $targetUser->id);

    expect(User::withTrashed()->find($targetUser->id))->not->toBeNull()
        ->and(User::find($targetUser->id))->toBeNull(); // Soft deleted
});

test('diretor realiza hard delete ao excluir conta', function () {
    $diretor = User::factory()->diretor()->create();
    $local = Local::create(['local' => 'Local Para Excluir']);
    $targetUser = User::factory()->usuario($local->id_local)->create();

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->call('inativarUsuario', $targetUser->id);

    expect(User::withTrashed()->find($targetUser->id))->toBeNull(); // Permanently removed from DB
});

test('ao editar um usuario preserva o setor e local previamente marcados', function () {
    $diretor = User::factory()->diretor()->create();
    $sec = Secretaria::create(['secretaria' => 'SEC_EDICAO', 'chave_secretaria' => 'secedicao', 'nome_extenso' => 'Secretaria Edicao', 'portaria' => '10']);
    $local = Local::create(['local' => 'Local Edicao', 'secretaria_id' => $sec->id_secretarias]);

    $user = User::factory()->usuario($local->id_local)->create([
        'name' => 'Nome Antigo',
        'username' => 'usuario.antigo',
    ]);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->call('editarUsuario', $user->id)
        ->assertSet('setor_id', $local->id_local)
        ->assertSet('secretaria_id', $sec->id_secretarias)
        ->assertSet('password', '')
        ->set('name', 'Nome Atualizado')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Nome Atualizado')
        ->and($user->fresh()->setor_id)->toBe($local->id_local);
});

test('ao editar um coordenador preserva o setor previamente marcado', function () {
    $diretor = User::factory()->diretor()->create();
    $sec = Secretaria::create(['secretaria' => 'SEC_COORD', 'chave_secretaria' => 'seccoord', 'nome_extenso' => 'Secretaria Coord', 'portaria' => '11']);
    $local = Local::create(['local' => 'Local Coord', 'secretaria_id' => $sec->id_secretarias]);

    $coord = User::factory()->coordenador($local->id_local)->create([
        'name' => 'Coordenador Antigo',
        'username' => 'coord.antigo',
    ]);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->call('editarUsuario', $coord->id)
        ->assertSet('setor_id', $local->id_local)
        ->assertSet('secretaria_id', $sec->id_secretarias)
        ->assertSet('password', '')
        ->set('name', 'Coordenador Novo Nome')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($coord->fresh()->name)->toBe('Coordenador Novo Nome')
        ->and($coord->fresh()->setor_id)->toBe($local->id_local);
});

test('permite salvar edicao deixando a senha em branco para manter a atual', function () {
    $diretor = User::factory()->diretor()->create();
    $sec = Secretaria::create(['secretaria' => 'SEC_TESTE', 'chave_secretaria' => 'secteste', 'nome_extenso' => 'Sec Teste', 'portaria' => '12']);
    $local = Local::create(['local' => 'Local Teste', 'secretaria_id' => $sec->id_secretarias]);

    $originalPasswordHash = Hash::make('senha123');
    $user = User::factory()->usuario($local->id_local)->create([
        'name' => 'Nome Original',
        'username' => 'user.original',
        'password' => $originalPasswordHash,
    ]);

    Livewire::actingAs($diretor)
        ->test(UserManagement::class)
        ->call('editarUsuario', $user->id)
        ->assertSet('password', '')
        ->set('name', 'Nome Alterado Sem Mudar Senha')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Nome Alterado Sem Mudar Senha')
        ->and($user->fresh()->password)->toBe($originalPasswordHash);
});
