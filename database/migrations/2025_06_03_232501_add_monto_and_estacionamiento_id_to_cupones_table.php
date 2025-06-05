<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cupones', function (Blueprint $table) {
            $table->double('monto')->nullable()->default(0);
            $table->foreignId('estacionamiento_id')->nullable()->constrained('estacionamientos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cupones', function (Blueprint $table) {
            $table->dropColumn('monto');
            $table->dropForeign(['estacionamiento_id']);
            $table->dropColumn('estacionamiento_id');
        });
    }
};
