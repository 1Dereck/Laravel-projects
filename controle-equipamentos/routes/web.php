<?php

use App\Http\Controllers\OcrController;
use App\Http\Controllers\PdfController;
use App\Livewire\Auth\Login;
use App\Livewire\BuscaSetor;
use App\Livewire\Configuracoes;
use App\Livewire\Dashboard;
use App\Livewire\EquipamentoForm;
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

    Route::get('/setores', SetorManager::class)->name('setores.index');
    Route::get('/equipamentos', EquipamentoForm::class)->name('equipamentos.index');
    Route::get('/perifericos', PerifericoManager::class)->name('perifericos.index');
    Route::get('/relatorios', BuscaSetor::class)->name('relatorios.index');
    Route::get('/relatorios/setor/{setor}/pdf', [PdfController::class, 'gerarRelatorioSetor'])->name('relatorios.pdf');
    Route::get('/configuracoes', Configuracoes::class)->name('configuracoes.index');

    // Rota API de Leitura OCR para Números de Série
    Route::post('/api/ocr/read-serial', [OcrController::class, 'readSerial'])->name('api.ocr.read-serial');

    // Director Exclusive Routes
    Route::middleware('role:diretor')->group(function () {
        Route::get('/lixeira', LixeiraManager::class)->name('lixeira.index');
        Route::get('/usuarios', UserManagement::class)->name('usuarios.index');
    });
});
