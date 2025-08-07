<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixOrdersStatusColumnFinal extends Migration
{
    public function up()
    {
        // Primeiro, precisamos verificar se há registros com status que não existem no novo enum
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Reverter para o enum anterior se necessário
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'processing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
}