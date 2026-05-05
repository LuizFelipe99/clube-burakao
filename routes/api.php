<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociadoController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('associados', AssociadoController::class);
    
});


//visualiza quem está logado
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

