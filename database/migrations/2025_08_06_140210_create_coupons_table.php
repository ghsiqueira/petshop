<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Código do cupom (ex: BLACKFRIDAY10)
            $table->string('name'); // Nome amigável do cupom
            $table->text('description')->nullable(); // Descrição do cupom
            $table->enum('type', ['percentage', 'fixed']); // Tipo: porcentagem ou valor fixo
            $table->decimal('value', 8, 2); // Valor do desconto (10 para 10% ou R$ 10,00)
            $table->decimal('minimum_amount', 8, 2)->nullable(); // Valor mínimo para usar o cupom
            $table->decimal('maximum_discount', 8, 2)->nullable(); // Desconto máximo (para % apenas)
            $table->integer('usage_limit')->nullable(); // Limite total de uso
            $table->integer('usage_limit_per_user')->default(1); // Limite por usuário
            $table->integer('used_count')->default(0); // Quantas vezes foi usado
            $table->datetime('starts_at')->nullable(); // Data de início
            $table->datetime('expires_at')->nullable(); // Data de expiração
            $table->boolean('is_active')->default(true); // Cupom ativo/inativo
            
            // Relacionamentos
            $table->foreignId('petshop_id')->nullable()->constrained()->onDelete('cascade'); // null = cupom global (admin)
            $table->foreignId('created_by')->constrained('users'); // Quem criou (admin ou petshop)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};