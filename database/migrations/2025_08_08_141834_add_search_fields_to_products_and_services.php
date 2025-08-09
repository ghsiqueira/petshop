<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar campos de busca para produtos
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'search_keywords')) {
                $table->text('search_keywords')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0)->after('search_keywords');
            }
            if (!Schema::hasColumn('products', 'total_reviews')) {
                $table->integer('total_reviews')->default(0)->after('avg_rating');
            }
            if (!Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')->default(false)->after('total_reviews');
            }
            if (!Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('featured');
            }
            if (!Schema::hasColumn('products', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('tags');
            }
            if (!Schema::hasColumn('products', 'discount_start_date')) {
                $table->date('discount_start_date')->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('products', 'discount_end_date')) {
                $table->date('discount_end_date')->nullable()->after('discount_start_date');
            }
        });

        // Adicionar campos de busca para serviços
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'search_keywords')) {
                $table->text('search_keywords')->nullable()->after('description');
            }
            if (!Schema::hasColumn('services', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0)->after('search_keywords');
            }
            if (!Schema::hasColumn('services', 'total_reviews')) {
                $table->integer('total_reviews')->default(0)->after('avg_rating');
            }
            if (!Schema::hasColumn('services', 'featured')) {
                $table->boolean('featured')->default(false)->after('total_reviews');
            }
            if (!Schema::hasColumn('services', 'tags')) {
                $table->json('tags')->nullable()->after('featured');
            }
            // NÃO adicionar duration_minutes se já existir
            if (!Schema::hasColumn('services', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('services', 'requirements')) {
                $table->json('requirements')->nullable()->after('duration_minutes');
            }
            if (!Schema::hasColumn('services', 'max_pets_per_session')) {
                $table->integer('max_pets_per_session')->default(1)->after('requirements');
            }
            if (!Schema::hasColumn('services', 'requires_appointment')) {
                $table->boolean('requires_appointment')->default(true)->after('max_pets_per_session');
            }
            if (!Schema::hasColumn('services', 'advance_booking_days')) {
                $table->integer('advance_booking_days')->default(1)->after('requires_appointment');
            }
            if (!Schema::hasColumn('services', 'cancellation_hours')) {
                $table->integer('cancellation_hours')->default(24)->after('advance_booking_days');
            }
        });

        // Adicionar campos de busca para petshops
        Schema::table('petshops', function (Blueprint $table) {
            if (!Schema::hasColumn('petshops', 'search_keywords')) {
                $table->text('search_keywords')->nullable()->after('description');
            }
            if (!Schema::hasColumn('petshops', 'amenities')) {
                $table->json('amenities')->nullable()->after('search_keywords'); // wifi, parking, etc
            }
            if (!Schema::hasColumn('petshops', 'accepted_species')) {
                $table->json('accepted_species')->nullable()->after('amenities'); // dog, cat, bird, etc
            }
            if (!Schema::hasColumn('petshops', 'featured')) {
                $table->boolean('featured')->default(false)->after('accepted_species');
            }
            if (!Schema::hasColumn('petshops', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('featured');
            }
            if (!Schema::hasColumn('petshops', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('petshops', 'delivery_radius')) {
                $table->decimal('delivery_radius', 5, 2)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('petshops', 'minimum_order_value')) {
                $table->decimal('minimum_order_value', 8, 2)->nullable()->after('delivery_radius');
            }
            if (!Schema::hasColumn('petshops', 'delivery_fee')) {
                $table->decimal('delivery_fee', 6, 2)->nullable()->after('minimum_order_value');
            }
            if (!Schema::hasColumn('petshops', 'free_delivery_minimum')) {
                $table->decimal('free_delivery_minimum', 8, 2)->nullable()->after('delivery_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDropProducts = [
                'search_keywords', 'avg_rating', 'total_reviews', 'featured', 'tags',
                'discount_percentage', 'discount_start_date', 'discount_end_date'
            ];
            
            foreach ($columnsToDropProducts as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('services', function (Blueprint $table) {
            $columnsToDropServices = [
                'search_keywords', 'avg_rating', 'total_reviews', 'featured', 'tags',
                'requirements', 'max_pets_per_session', 'requires_appointment', 
                'advance_booking_days', 'cancellation_hours'
            ];
            
            // NÃO remover duration_minutes se já existia antes
            foreach ($columnsToDropServices as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('petshops', function (Blueprint $table) {
            $columnsToDropPetshops = [
                'search_keywords', 'amenities', 'accepted_species', 'featured',
                'latitude', 'longitude', 'delivery_radius', 'minimum_order_value',
                'delivery_fee', 'free_delivery_minimum'
            ];
            
            foreach ($columnsToDropPetshops as $column) {
                if (Schema::hasColumn('petshops', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};