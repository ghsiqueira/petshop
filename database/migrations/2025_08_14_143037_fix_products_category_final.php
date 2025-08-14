<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro, expandir o ENUM para aceitar todos os valores do formulário
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('food', 'toys', 'accessories', 'health', 'racao', 'petiscos', 'brinquedos', 'higiene', 'acessorios', 'medicamentos', 'outros', 'comida', 'saude') NOT NULL DEFAULT 'outros'");
        
        // Converter valores existentes para o novo padrão
        DB::statement("UPDATE products SET category = 'racao' WHERE category IN ('food', 'comida')");
        DB::statement("UPDATE products SET category = 'brinquedos' WHERE category IN ('toys')");
        DB::statement("UPDATE products SET category = 'acessorios' WHERE category IN ('accessories')");
        DB::statement("UPDATE products SET category = 'medicamentos' WHERE category IN ('health', 'saude')");
        
        // Finalizar com apenas os valores do formulário
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('racao', 'petiscos', 'brinquedos', 'higiene', 'acessorios', 'medicamentos', 'outros') NOT NULL DEFAULT 'outros'");
    }

    public function down(): void
    {
        // Reverter para inglês original
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('food', 'toys', 'accessories', 'health', 'racao', 'petiscos', 'brinquedos', 'higiene', 'acessorios', 'medicamentos', 'outros') NOT NULL DEFAULT 'accessories'");
        
        DB::statement("UPDATE products SET category = 'food' WHERE category IN ('racao', 'petiscos')");
        DB::statement("UPDATE products SET category = 'toys' WHERE category = 'brinquedos'");
        DB::statement("UPDATE products SET category = 'accessories' WHERE category IN ('acessorios', 'higiene', 'outros')");
        DB::statement("UPDATE products SET category = 'health' WHERE category = 'medicamentos'");
        
        DB::statement("ALTER TABLE `products` MODIFY COLUMN `category` ENUM('food', 'toys', 'accessories', 'health') NOT NULL DEFAULT 'accessories'");
    }
};