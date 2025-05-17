<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->string('password_reset_code')->nullable();
            $table->timestamp('password_reset_code_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropColumn(['password_reset_code', 'password_reset_code_expires_at']);
        });
    }
};
