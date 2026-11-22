<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status');
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('changed_at')->useCurrent();
        });

        Schema::create('blocked_customers', function (Blueprint $table) {
            $table->id();
            $table->string('rut')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('address')->nullable()->index();
            $table->text('reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('blocked_by_user_id')->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_customers');
        Schema::dropIfExists('order_status_histories');
    }
};
