<?php

use App\Models\Acolhimento;
use App\Models\Observacao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('guest is redirected to login page', function () {
    $this->get('/')
        ->assertRedirect('/login');
});

test('user can log in with correct credentials', function () {
    $user = User::factory()->create([
        'login' => 'testadmin',
        'senha' => Hash::make('secret123'),
        'ativo' => 's',
    ]);

    $this->post('/login', [
        'login' => 'testadmin',
        'senha' => 'secret123',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

test('user cannot log in with wrong password', function () {
    $user = User::factory()->create([
        'login' => 'testadmin',
        'senha' => Hash::make('secret123'),
        'ativo' => 's',
    ]);

    $this->post('/login', [
        'login' => 'testadmin',
        'senha' => 'wrongpass',
    ])->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('usuario cannot access create page', function () {
    $usuario = User::factory()->create([
        'permissao' => 'n', // Usuario
        'ativo' => 's',
    ]);

    $this->actingAs($usuario)
        ->get('/acolhimentos/create')
        ->assertStatus(403);
});

test('admin can access create page', function () {
    $admin = User::factory()->create([
        'permissao' => 'a', // Admin
        'ativo' => 's',
    ]);

    $this->actingAs($admin)
        ->get('/acolhimentos/create')
        ->assertStatus(200);
});

test('cpf is masked for usuarios and visible for admins', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);
    $usuario = User::factory()->create(['permissao' => 'n', 'ativo' => 's']);

    $acolhimento = Acolhimento::factory()->create([
        'cpf' => '12345678901',
        'id_tecnico_resp' => $admin->id_usuario,
    ]);

    // Test as Admin
    $this->actingAs($admin);
    expect($acolhimento->masked_cpf)->toBe('123.456.789-01');

    // Test as Usuario
    $this->actingAs($usuario);
    expect($acolhimento->fresh()->masked_cpf)->toBe('***.***.***-**');
});

test('common user can see cpf if they are the technical respondent', function () {
    $commonUser = User::factory()->create(['permissao' => 'n', 'ativo' => 's']);

    $acolhimento = Acolhimento::factory()->create([
        'cpf' => '12345678901',
        'id_tecnico_resp' => $commonUser->id_usuario,
    ]);

    $this->actingAs($commonUser);
    expect($acolhimento->masked_cpf)->toBe('123.456.789-01');
});

test('sigiloso observations are hidden from users without access', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's', 'tipo_acesso' => 's']);
    $commonUserWithoutAccess = User::factory()->create(['permissao' => 'n', 'ativo' => 's', 'tipo_acesso' => 'n']);

    $acolhimento = Acolhimento::factory()->create(['id_tecnico_resp' => $admin->id_usuario]);

    // Normal evolution
    $normalObs = new Observacao;
    $normalObs->id_acolhimento = $acolhimento->id_acolhimento;
    $normalObs->descricao = 'Normal observation text';
    $normalObs->tipo = 'n';
    $normalObs->id_usuario = $admin->id_usuario;
    $normalObs->id_assunto = 0;
    $normalObs->save();

    // Confidential evolution
    $sigilosaObs = new Observacao;
    $sigilosaObs->id_acolhimento = $acolhimento->id_acolhimento;
    $sigilosaObs->descricao = 'Confidential observation text';
    $sigilosaObs->tipo = 's';
    $sigilosaObs->id_usuario = $admin->id_usuario;
    $sigilosaObs->id_assunto = 0;
    $sigilosaObs->save();

    // Admin response sees both
    $this->actingAs($admin)
        ->get("/acolhimentos/{$acolhimento->id_acolhimento}")
        ->assertSee('Normal observation text')
        ->assertSee('Confidential observation text');

    // Usuario without access sees only normal
    $this->actingAs($commonUserWithoutAccess)
        ->get("/acolhimentos/{$acolhimento->id_acolhimento}")
        ->assertSee('Normal observation text')
        ->assertDontSee('Confidential observation text');
});

test('only admin can manage system users', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);
    $usuario = User::factory()->create(['permissao' => 'n', 'ativo' => 's']);

    // Usuario gets forbidden trying to seed/create user
    $this->actingAs($usuario)
        ->post('/usuarios', [
            'login' => 'newuser',
            'nome_usu' => 'New User',
            'senha' => 'password123',
            'permissao' => 'n',
            'tipo_acesso' => 'n',
        ])->assertStatus(403);

    // Admin successfully creates user
    $this->actingAs($admin)
        ->post('/usuarios', [
            'login' => 'newuser',
            'nome_usu' => 'New User',
            'senha' => 'password123',
            'permissao' => 'n',
            'tipo_acesso' => 'n',
        ])->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', ['login' => 'newuser']);
});

test('admin can see users page after search but cannot see other admins or diretor accounts', function () {
    $admin = User::factory()->create(['nome_usu' => 'Admin User A', 'permissao' => 'a', 'ativo' => 's']);
    $otherAdmin = User::factory()->create(['nome_usu' => 'Admin User B', 'permissao' => 'a', 'ativo' => 's']);
    $diretor = User::factory()->create(['nome_usu' => 'Diretor User X', 'permissao' => 'd', 'ativo' => 's']);
    $usuario1 = User::factory()->create(['nome_usu' => 'Usuario User A', 'permissao' => 'n', 'ativo' => 's']);
    $usuario2 = User::factory()->create(['nome_usu' => 'Usuario User B', 'permissao' => 'n', 'ativo' => 's']);

    // Without search, should not see usuarios by default (search-only mode)
    $response = $this->actingAs($admin)
        ->get('/usuarios');

    $response->assertStatus(200);
    $response->assertDontSee('Usuario User A');
    $response->assertDontSee('Usuario User B');

    // Test searching usuarios
    $searchResponse = $this->actingAs($admin)
        ->get('/usuarios?search_user=User A');

    $searchResponse->assertSee('Usuario User A');
    $searchResponse->assertDontSee('Usuario User B');

    // Admin NÃO deve ver a conta de outros administradores ou Diretor na listagem
    $searchAllResponse = $this->actingAs($admin)
        ->get('/usuarios?search_user=User');
    $searchAllResponse->assertDontSee('Admin User B');
    $searchAllResponse->assertDontSee('Diretor User X');
});

test('diretor can see all user types on users page', function () {
    $diretor = User::factory()->create(['nome_usu' => 'Diretor User X', 'permissao' => 'd', 'ativo' => 's']);
    $admin = User::factory()->create(['nome_usu' => 'Admin User A', 'permissao' => 'a', 'ativo' => 's']);
    $usuario = User::factory()->create(['nome_usu' => 'Usuario User A', 'permissao' => 'n', 'ativo' => 's']);

    $response = $this->actingAs($diretor)
        ->get('/usuarios');

    $response->assertStatus(200);

    // Diretor deve ver as contas de Diretor e Admin por padrão
    $response->assertSee('Diretor User X');
    $response->assertSee('Admin User A');

    // Usuários normais requerem busca
    $response->assertDontSee('Usuario User A');

    $searchResponse = $this->actingAs($diretor)
        ->get('/usuarios?search_user=Usuario');
    $searchResponse->assertSee('Usuario User A');
});

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can store acolhimento with uploaded file photo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($admin)
        ->post('/acolhimentos', [
            'nome_pessoa' => 'Acolhido Test Photo',
            'cpf' => '111.111.111-11',
            'dt_nascimento' => '1990-01-01',
            'foto' => $file,
        ]);

    $acolhimento = Acolhimento::where('nome_pessoa', 'Acolhido Test Photo')->first();
    expect($acolhimento)->not->toBeNull();

    $response->assertRedirect("/acolhimentos/{$acolhimento->id_acolhimento}");

    expect($acolhimento->nome_foto)->toBe('avatar.jpg');
    expect($acolhimento->nome_cript)->toBe("foto_{$acolhimento->id_acolhimento}.jpg");

    Storage::disk('public')->assertExists("fotos/foto_{$acolhimento->id_acolhimento}.jpg");
});

test('admin can store acolhimento with webcam photo', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);

    // Fake base64 image data
    $base64Image = 'data:image/jpeg;base64,'.base64_encode(UploadedFile::fake()->image('camera.jpg')->getContent());

    $response = $this->actingAs($admin)
        ->post('/acolhimentos', [
            'nome_pessoa' => 'Acolhido Test Webcam',
            'cpf' => '222.222.222-22',
            'dt_nascimento' => '1995-05-05',
            'webcam_image' => $base64Image,
        ]);

    $acolhimento = Acolhimento::where('nome_pessoa', 'Acolhido Test Webcam')->first();
    expect($acolhimento)->not->toBeNull();

    $response->assertRedirect("/acolhimentos/{$acolhimento->id_acolhimento}");

    expect($acolhimento->nome_foto)->toBe("foto_{$acolhimento->id_acolhimento}.jpg");
    expect($acolhimento->nome_cript)->toBe("foto_{$acolhimento->id_acolhimento}.jpg");

    Storage::disk('public')->assertExists("fotos/foto_{$acolhimento->id_acolhimento}.jpg");
});

test('rg is masked dynamically on model and cleaned on controller store/update', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);

    // 1. Testa o accessor getMaskedRgAttribute com 9 e 8 dígitos
    $acolhimento9 = Acolhimento::factory()->make(['rg' => '123456789']);
    expect($acolhimento9->masked_rg)->toBe('12.345.678-9');

    $acolhimento8 = Acolhimento::factory()->make(['rg' => '12345678']);
    expect($acolhimento8->masked_rg)->toBe('1.234.567-8');

    $acolhimentoOutro = Acolhimento::factory()->make(['rg' => '123-45']);
    expect($acolhimentoOutro->masked_rg)->toBe('123-45');

    // 2. Testa o salvamento limpo no Controller Store
    $this->actingAs($admin)
        ->post('/acolhimentos', [
            'nome_pessoa' => 'Acolhido Test RG',
            'cpf' => '333.333.333-33',
            'dt_nascimento' => '1990-01-01',
            'rg' => '12.345.678-9',
        ]);

    $created = Acolhimento::where('nome_pessoa', 'Acolhido Test RG')->first();
    expect($created)->not->toBeNull();
    // No banco deve estar limpo
    expect($created->rg)->toBe('123456789');

    // 3. Testa a atualização no Controller Update
    $this->actingAs($admin)
        ->put("/acolhimentos/{$created->id_acolhimento}", [
            'nome_pessoa' => 'Acolhido Test RG Edited',
            'cpf' => '333.333.333-33',
            'dt_nascimento' => '1990-01-01',
            'rg' => '1.234.567-8',
        ]);

    $updated = $created->fresh();
    expect($updated->nome_pessoa)->toBe('Acolhido Test RG Edited');
    // No banco deve estar limpo
    expect($updated->rg)->toBe('12345678');
});

test('diretor can delete users but admin cannot', function () {
    $diretor = User::factory()->create(['permissao' => 'd', 'ativo' => 's']);
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);
    $userToDelete = User::factory()->create(['permissao' => 'n', 'ativo' => 's']);

    // Admin attempts to delete user -> returns 403
    $this->actingAs($admin)
        ->delete("/usuarios/{$userToDelete->id_usuario}")
        ->assertStatus(403);

    $this->assertDatabaseHas('users', ['id_usuario' => $userToDelete->id_usuario]);

    // Diretor successfully deletes user
    $this->actingAs($diretor)
        ->delete("/usuarios/{$userToDelete->id_usuario}")
        ->assertRedirect('/usuarios');

    $this->assertDatabaseMissing('users', ['id_usuario' => $userToDelete->id_usuario]);
});

test('admin cannot create admin or diretor account', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);

    $this->actingAs($admin)
        ->post('/usuarios', [
            'login' => 'tryingadmin',
            'nome_usu' => 'Try Admin',
            'senha' => 'password123',
            'permissao' => 'a', // Forbidden permission type
            'tipo_acesso' => 'n',
        ])->assertSessionHasErrors('permissao');

    $this->actingAs($admin)
        ->post('/usuarios', [
            'login' => 'tryingdiretor',
            'nome_usu' => 'Try Diretor',
            'senha' => 'password123',
            'permissao' => 'd', // Forbidden permission type
            'tipo_acesso' => 'n',
        ])->assertSessionHasErrors('permissao');
});

test('diretor can create admin or diretor account', function () {
    $diretor = User::factory()->create(['permissao' => 'd', 'ativo' => 's']);

    $this->actingAs($diretor)
        ->post('/usuarios', [
            'login' => 'newadmin',
            'nome_usu' => 'New Admin',
            'senha' => 'password123',
            'permissao' => 'a',
            'tipo_acesso' => 'n',
        ])->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', ['login' => 'newadmin', 'permissao' => 'a']);

    $this->actingAs($diretor)
        ->post('/usuarios', [
            'login' => 'newdiretor',
            'nome_usu' => 'New Diretor',
            'senha' => 'password123',
            'permissao' => 'd',
            'tipo_acesso' => 'n',
        ])->assertRedirect('/usuarios');

    $this->assertDatabaseHas('users', ['login' => 'newdiretor', 'permissao' => 'd']);
});

test('hiding acolhido hides from list but search finds it', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);
    $visibleAcolhido = Acolhimento::factory()->create(['nome_pessoa' => 'Acolhido Visivel', 'oculto' => 'n', 'id_tecnico_resp' => $admin->id_usuario]);
    $hiddenAcolhido = Acolhimento::factory()->create(['nome_pessoa' => 'Acolhido Oculto', 'oculto' => 's', 'id_tecnico_resp' => $admin->id_usuario]);

    // 1. Visitar index sem busca -> vê apenas o visível
    $this->actingAs($admin)
        ->get('/acolhimentos')
        ->assertSee('Acolhido Visivel')
        ->assertDontSee('Acolhido Oculto');

    // 2. Ocultar o visível
    $this->actingAs($admin)
        ->post("/acolhimentos/{$visibleAcolhido->id_acolhimento}/toggle-oculto")
        ->assertRedirect();

    expect($visibleAcolhido->fresh()->oculto)->toBe('s');

    // 3. Buscar pelo visível (agora oculto) -> deve encontrar
    $this->actingAs($admin)
        ->get('/acolhimentos?search=Visivel')
        ->assertSee('Acolhido Visivel')
        ->assertSee('Ocultado');
});

test('user can generate pdf dossier for acolhido', function () {
    $admin = User::factory()->create(['permissao' => 'a', 'ativo' => 's']);
    $acolhimento = Acolhimento::factory()->create([
        'nome_pessoa' => 'Acolhido PDF Test',
        'id_tecnico_resp' => $admin->id_usuario,
    ]);

    $response = $this->actingAs($admin)
        ->get("/acolhimentos/{$acolhimento->id_acolhimento}/pdf");

    $response->assertStatus(200)
        ->assertSee('Acolhido PDF Test')
        ->assertSee('Logo Acolhimento');
});
