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
            // Horários de funcionamento (JSON para flexibilidade)
            $table->json('business_hours')->nullable()->after('description');
            
            // Configurações gerais de agendamento
            $table->integer('slot_duration')->default(30)->after('business_hours'); // duração em minutos
            $table->integer('advance_booking_days')->default(30)->after('slot_duration'); // quantos dias à frente pode agendar
            $table->boolean('allow_weekend_booking')->default(true)->after('advance_booking_days');
            $table->time('lunch_break_start')->nullable()->after('allow_weekend_booking');
            $table->time('lunch_break_end')->nullable()->after('lunch_break_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petshops', function (Blueprint $table) {
            $table->dropColumn([
                'business_hours',
                'slot_duration',
                'advance_booking_days',
                'allow_weekend_booking',
                'lunch_break_start',
                'lunch_break_end'
            ]);
        });
    }
};
