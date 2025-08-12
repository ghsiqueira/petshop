<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('petshops', function (Blueprint $table) {
            // Adicionar coluna opening_hours se ela não existir
            if (!Schema::hasColumn('petshops', 'opening_hours')) {
                $table->string('opening_hours')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petshops', function (Blueprint $table) {
            if (Schema::hasColumn('petshops', 'opening_hours')) {
                $table->dropColumn('opening_hours');
            }
        });
    }
};