<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('sync')->group(function (): void {
    Route::get('/ping', [SyncController::class, 'ping'])->name('api.sync.ping');
    Route::get('/dados-mestres', [SyncController::class, 'obterDadosMestres'])->name('api.sync.dados-mestres');
    Route::post('/enviar-lote', [SyncController::class, 'receberLote'])->name('api.sync.enviar-lote');
});
