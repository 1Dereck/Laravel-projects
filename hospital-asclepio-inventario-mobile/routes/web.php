<?php

use App\Http\Controllers\PdfController;
use App\Livewire\Auth\Login;
use App\Livewire\BuscaSetor;
use App\Livewire\Configuracoes;
use App\Livewire\Dashboard;
use App\Livewire\EquipamentoForm;
use App\Livewire\LevantamentoQuantidades;
use App\Livewire\LixeiraManager;
use App\Livewire\PerifericoManager;
use App\Livewire\SetorManager;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');

    Route::get('/', Dashboard::class)->name('home');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/equipamentos', EquipamentoForm::class)->name('equipamentos.index');
    Route::get('/perifericos', PerifericoManager::class)->name('perifericos.index');
    Route::get('/configuracoes', Configuracoes::class)->name('configuracoes.index');

    // Admin & Director Routes
    Route::middleware('role:diretor,administrador')->group(function () {
        Route::get('/setores', SetorManager::class)->name('setores.index');
    });

    // Admin, Director, Coordenador & Usuario Report & Quantidades Routes
    Route::middleware('role:diretor,administrador,coordenador,usuario')->group(function () {
        Route::get('/relatorios', BuscaSetor::class)->name('relatorios.index');
        Route::get('/quantidades', LevantamentoQuantidades::class)->name('quantidades.index');
        Route::get('/quantidades/secretaria/{secretaria}/pdf', [PdfController::class, 'gerarRelatorioQuantidadesSecretaria'])->name('quantidades.secretaria.pdf');
        Route::get('/quantidades/local/{setor}/pdf', [PdfController::class, 'gerarRelatorioQuantidadesLocal'])->name('quantidades.local.pdf');
        Route::get('/relatorios/setor/{setor}/pdf', [PdfController::class, 'gerarRelatorioSetor'])->name('relatorios.pdf');
        Route::get('/relatorios/secretaria/{secretaria}/pdf', [PdfController::class, 'gerarRelatorioSecretaria'])->name('relatorios.secretaria.pdf');
    });

    // Director, Admin & Coordenador User Management
    Route::middleware('role:diretor,administrador,coordenador')->group(function () {
        Route::get('/usuarios', UserManagement::class)->name('usuarios.index');
    });

    // Director Exclusive Routes
    Route::middleware('role:diretor')->group(function () {
        Route::get('/lixeira', LixeiraManager::class)->name('lixeira.index');
    });
});
