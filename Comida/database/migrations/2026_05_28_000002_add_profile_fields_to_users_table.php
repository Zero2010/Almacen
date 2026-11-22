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
        Schema::table('users', function (Blueprint $table) {
            $table->string('rut', 15)->nullable()->unique()->after('email');
            $table->string('role', 20)->default('client')->after('password'); // admin, chef, waiter, delivery, accountant, client
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('address')->nullable()->after('phone');
            $table->foreignId('commune_id')->nullable()->after('address')->constrained('communes')->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('commune_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['commune_id']);
            $table->dropColumn(['rut', 'role', 'phone', 'address', 'commune_id', 'is_active']);
        });
    }
};
