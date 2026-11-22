<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->foreignId('waiter_id')->nullable()->constrained('waiters')->nullOnDelete();
            $table->string('waiter_name')->nullable(); // so we can store direct string or waiter name
            $table->integer('party_size')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['table_id', 'waiter_id', 'waiter_name', 'party_size', 'opened_at', 'closed_at']);
        });
    }
};
