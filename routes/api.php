<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\SearchApiController;

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

// Rotas de API para busca
Route::prefix('search')->group(function () {
    Route::get('/quick-search', [SearchApiController::class, 'quickSearch']);
    Route::get('/suggestions', [SearchApiController::class, 'suggestions']);
    Route::get('/autocomplete', [SearchApiController::class, 'autocomplete']);
    Route::get('/filters', [SearchApiController::class, 'filters']);
    Route::get('/stats', [SearchApiController::class, 'stats']);
});

// Rotas do carrinho (se ainda não existirem)
Route::middleware('auth')->prefix('cart')->group(function () {
    Route::post('/add', function(Request $request) {
        try {
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity', 1);
            
            // Aqui você implementaria a lógica real do carrinho
            // Por enquanto, apenas simulamos o sucesso
            
            return response()->json([
                'success' => true,
                'message' => 'Produto adicionado ao carrinho!',
                'cart_count' => 1 // Simular contador
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar produto ao carrinho'
            ], 500);
        }
    });
    
    Route::get('/count', function() {
        // Simular contador do carrinho
        // Implementar lógica real baseada no usuário autenticado
        return response()->json(['count' => 0]);
    });
});