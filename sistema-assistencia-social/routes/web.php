<?php

use App\Http\Controllers\AcolhimentoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\ObservacaoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas de Autenticação (Acesso Público)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas Protegidas por Autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard redireciona para a listagem principal
    Route::get('/', function () {
        return redirect()->route('acolhimentos.index');
    })->name('dashboard');

    // CRUD de Acolhimento
    Route::resource('acolhimentos', AcolhimentoController::class)->only([
        'index', 'create', 'store', 'show', 'edit', 'update',
    ]);

    // Gerar PDF do Acolhido
    Route::get('/acolhimentos/{id}/pdf', [AcolhimentoController::class, 'gerarPdf'])->name('acolhimentos.pdf');

    // Upload de Foto (Webcam ou Arquivo)
    Route::post('/acolhimentos/{id}/foto', [AcolhimentoController::class, 'uploadFoto'])->name('acolhimentos.foto');

    // Alternar Ocultar Acolhido da listagem
    Route::post('/acolhimentos/{id}/toggle-oculto', [AcolhimentoController::class, 'toggleOculto'])->name('acolhimentos.toggle-oculto');

    // Upload de Anexos/Documentos
    Route::post('/acolhimentos/{id}/arquivos', [AcolhimentoController::class, 'uploadArquivo'])->name('acolhimentos.arquivos.store');

    // Download de Documentos
    Route::get('/arquivos/{id}/download', [AcolhimentoController::class, 'downloadArquivo'])->name('acolhimentos.arquivos.download');

    // Registro de Observações/Evoluções
    Route::post('/acolhimentos/{id}/observacoes', [ObservacaoController::class, 'store'])->name('acolhimentos.observacoes.store');

    // Configurações e Perfil do Usuário
    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes');
    Route::post('/configuracoes/senha', [ConfiguracaoController::class, 'updatePassword'])->name('configuracoes.senha');
    Route::post('/configuracoes/tema', [ConfiguracaoController::class, 'updateTheme'])->name('configuracoes.tema');

    // Gerenciamento de Contas (Admin / Diretor)
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
});
