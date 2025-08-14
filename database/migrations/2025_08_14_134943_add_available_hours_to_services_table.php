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
        Schema::table('services', function (Blueprint $table) {
            // Horários específicos do serviço (JSON)
            $table->json('available_hours')->nullable()->after('duration_minutes');
            
            // Se o serviço tem horário específico ou usa o do petshop
            $table->boolean('use_petshop_hours')->default(true)->after('available_hours');
            
            // Dias da semana que o serviço está disponível
            $table->json('available_days')->nullable()->after('use_petshop_hours');
            
            // Tempo de intervalo entre agendamentos (em minutos)
            $table->integer('buffer_time')->default(0)->after('available_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'available_hours',
                'use_petshop_hours',
                'available_days',
                'buffer_time'
            ]);
        });
    }
};
