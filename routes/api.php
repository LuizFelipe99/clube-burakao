<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociadoController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\AssociadoCotaController;
use App\Http\Controllers\CotaController;

// rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    //SAIR / LOGOUT
    
    Route::post('/logout', [AuthController::class, 'logout']);
    //GET, POST, PUT ASSOCIADOS
    Route::apiResource('associados', AssociadoController::class);

    //crirar cota / plano
    Route::apiResource('cotas', CotaController::class);

    // adiciona um associado a uma cota
    Route::post('/associados/{id}/cotas', [AssociadoCotaController::class, 'vincular']);
    
});


//visualiza quem está logado
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});



