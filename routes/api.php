<?php

use App\Http\Controllers\PorteiroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('porteiro')->group(function () {
    Route::get('/', [PorteiroController::class, 'index']); // listar
    Route::post('/', [PorteiroController::class, 'store']); // Criar
    Route::get('/{id}', [PorteiroController::class, 'show']); // Exibir
    Route::put('/{id}', [PorteiroController::class, 'update']); // Atualizar
    Route::delete('/{id}', [PorteiroController::class, 'destroy']); // Excluir
});
