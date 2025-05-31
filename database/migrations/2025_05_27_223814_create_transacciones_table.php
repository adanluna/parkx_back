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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->double('monto')->default(0);
            $table->string('tarjeta')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('estacionamiento_id')->nullable();
            $table->enum('tipo', ['abono', 'retiro']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
