<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0.00)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
