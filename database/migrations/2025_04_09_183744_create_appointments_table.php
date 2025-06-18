<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('pet_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->dateTime('appointment_datetime');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}