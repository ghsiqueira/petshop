<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->string('coupon_code')->nullable(); // Guardar código para histórico
            $table->decimal('coupon_discount', 8, 2)->default(0); // Valor do desconto aplicado
            $table->decimal('subtotal', 8, 2)->after('total_amount'); // Valor antes do desconto
            
            // Renomear total_amount seria ideal, mas vamos adicionar uma coluna para o subtotal
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_code', 'coupon_discount', 'subtotal']);
        });
    }
};