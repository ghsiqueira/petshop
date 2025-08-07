<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro vamos ver qual é o tipo atual da coluna status
        Schema::table('orders', function (Blueprint $table) {
            // Alterar a coluna status para aceitar os valores corretos
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Reverter para o tipo original se necessário
            $table->string('status')->change();
        });
    }
};