<?php

use App\Models\Local;
use App\Models\Periferico;
use App\Models\Secretaria;
use App\Models\User;
use Livewire\Livewire;

test('perifericos page can be rendered by authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/perifericos')
        ->assertStatus(200);
});

test('user can open modal and see setoresList without error', function () {
    $user = User::factory()->create();
    Local::create(['local' => 'T.I']);

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->call('novoPeriferico')
        ->assertSee('Cadastrar Periférico')
        ->assertSee('T.I');
});

test('user can create a new periferico', function () {
    $user = User::factory()->create();
    $sec = Secretaria::create(['secretaria' => 'S.M. OBRAS', 'nome_extenso' => 'SECRETARIA DE OBRAS']);
    $local = Local::factory()->create(['local' => 'Sec. Obras', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->set('secretaria_id', $sec->id_secretarias)
        ->set('setor_id', $local->id_local)
        ->set('tipo', 'Impressora HP')
        ->set('serial_patrimonio', 'PAT-999')
        ->call('salvar');

    $this->assertDatabaseHas('perifericos', [
        'tipo' => 'Impressora HP',
        'serial_patrimonio' => 'PAT-999',
        'setor_id' => $local->id_local,
    ]);
});

test('periferico manager filters out local matching the selected secretaria name', function () {
    $user = User::factory()->create(['role' => 'administrador']);
    $sec = Secretaria::create(['secretaria' => 'CHEFE DE GABINETE DO PREFEITO', 'nome_extenso' => 'SECRETARIA MUNICIPAL DE GABINETE']);
    $subLocal = Local::create(['local' => 'Abrigo Institucional', 'secretaria_id' => $sec->id_secretarias]);
    $secLocal = Local::create(['local' => 'CHEFE DE GABINETE DO PREFEITO', 'secretaria_id' => $sec->id_secretarias]);

    Livewire::actingAs($user)
        ->test('periferico-manager')
        ->set('secretariaFilter', $sec->id_secretarias)
        ->set('secretaria_id', $sec->id_secretarias)
        ->assertViewHas('locais', fn ($locais) => $locais->contains($subLocal) && ! $locais->contains($secLocal))
        ->assertViewHas('modalLocais', fn ($modalLocais) => $modalLocais->contains($subLocal) && ! $modalLocais->contains($secLocal));
});

test('coordenador cannot create or edit perifericos', function () {
    $local = Local::create(['local' => 'Setor Teste']);
    $coord = User::factory()->coordenador($local->id_local)->create();
    $periferico = Periferico::create([
        'setor_id' => $local->id_local,
        'tipo' => 'Mouse Avulso',
        'serial_patrimonio' => 'PAT-12345',
        'created_by' => $coord->id,
    ]);

    Livewire::actingAs($coord)
        ->test('periferico-manager')
        ->assertDontSee('Novo Periférico')
        ->assertDontSee('Editar')
        ->call('novoPeriferico')
        ->assertStatus(403);
});
