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
        Schema::table('transacciones', function (Blueprint $table) {
            Schema::table('transacciones', function (Blueprint $table) {
                $table->foreignId('cupon_id')->nullable()->constrained('cupones')->nullOnDelete();
                $table->float('comision')->default(0);
                $table->float('subtotal')->default(0);
                $table->float('descuento')->default(0);
                $table->float('total')->default(0);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacciones', function (Blueprint $table) {
            //
        });
    }
};
