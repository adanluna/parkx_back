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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('verification_code', 5)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->rememberToken(); // agrega campo 'remember_token' (usado por Laravel)
            $table->timestamps();    // agrega campos 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            //
        });
    }
};
