<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para o formulário de agendamento
Route::get('/petshops/{petshop}/services', [ApiController::class, 'getServices']);
Route::get('/petshops/{petshop}/employees', [ApiController::class, 'getEmployees']);