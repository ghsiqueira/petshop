<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('query');
            $table->string('type')->default('general'); // general, product, service, petshop
            $table->json('filters')->nullable(); // Filtros aplicados
            $table->integer('results_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['query', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_searches');
    }
};
