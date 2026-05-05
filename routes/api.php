<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociadoController;

Route::apiResource('associados', AssociadoController::class);