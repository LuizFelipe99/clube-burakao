<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssociadoController;
use App\Http\Controllers\CotaController;
use App\Http\Controllers\AssociadoCotaController;


// ROTAS PÚBLICAS (não precisam de login)

// Registro de usuário
Route::post('/register', [AuthController::class, 'register']);
// Login (retorna token)
Route::post('/login', [AuthController::class, 'login']);

//  ROTAS PROTEGIDAS (precisam de autenticação - Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // AUTENTICAÇÃO

    // Logout (revoga token atual)
    Route::post('/logout', [AuthController::class, 'logout']);
    // Retorna usuário autenticado
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // ASSOCIADOS (CRUD)

    // CRUD completo de associados
    Route::apiResource('associados', AssociadoController::class);

    // COTAS / PLANOS (CRUD)
    // CRUD completo de cotas (planos)
    Route::apiResource('cotas', CotaController::class);

    // Listar apenas subcotas (dependentes estruturais)
    Route::get('/subcotas', [CotaController::class, 'subcotas']);

    // Listar associados de uma cota (titular + dependentes)
    Route::get('/cotas/{id}/associados', [CotaController::class, 'associados']);

    // Adicionar dependente a uma cota (cria/vincula via subcota)
    Route::post('/cotas/{id}/dependentes', [CotaController::class, 'adicionarDependente']);

    //VÍNCULOS (Relacionamentos)
    // Vincular associado a uma cota (titular OU subcota manual)
    Route::post('/associados/{id}/cotas', [AssociadoCotaController::class, 'vincular']);

});