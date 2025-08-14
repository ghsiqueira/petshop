<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro, ampliar o ENUM para aceitar tanto valores em inglês quanto português
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('food', 'toys', 'accessories', 'health', 'comida', 'brinquedos', 'acessorios', 'saude') NOT NULL DEFAULT 'accessories'");
        
        // Agora atualizar valores existentes para português
        DB::statement("UPDATE products SET category = 'brinquedos' WHERE category = 'toys'");
        DB::statement("UPDATE products SET category = 'comida' WHERE category = 'food'");
        DB::statement("UPDATE products SET category = 'acessorios' WHERE category = 'accessories'");
        DB::statement("UPDATE products SET category = 'saude' WHERE category = 'health'");
        
        // Finalmente, reduzir o ENUM para apenas valores em português
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('comida', 'brinquedos', 'acessorios', 'saude') NOT NULL DEFAULT 'acessorios'");
    }

    public function down(): void
    {
        // Reverter valores para inglês
        DB::statement("UPDATE products SET category = 'toys' WHERE category = 'brinquedos'");
        DB::statement("UPDATE products SET category = 'food' WHERE category = 'comida'");
        DB::statement("UPDATE products SET category = 'accessories' WHERE category = 'acessorios'");
        DB::statement("UPDATE products SET category = 'health' WHERE category = 'saude'");
        
        // Reverter ENUM para inglês
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('food', 'toys', 'accessories', 'health') NOT NULL DEFAULT 'accessories'");
    }
};