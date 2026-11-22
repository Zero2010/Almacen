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
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price'); // In CLP (Chilean Peso)
            $table->string('image_path')->nullable();
            $table->integer('estimated_prep_time')->default(15); // in minutes
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 3. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rut', 15)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 4. Ingredients (Insumos)
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit', 10); // gr, ml, und, kg, etc.
            $table->double('stock', 15, 2)->default(0.00);
            $table->double('minimum_stock', 15, 2)->default(0.00); // stock limit alert
            $table->integer('cost_per_unit')->default(0); // Cost in CLP
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('lot_number', 50)->nullable(); // For food traceability
            $table->date('expiration_date')->nullable(); // For food safety
            $table->timestamps();
        });

        // 5. Recipes (Recipe details linking products and ingredients)
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->double('quantity_required', 15, 2); // Quantity in ingredient unit (e.g. 150g, 1.5 units)
            $table->timestamps();
        });

        // 6. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Operator or registered user
            $table->string('client_name');
            $table->string('client_phone', 20);
            $table->string('client_email')->nullable();
            $table->string('type', 20); // delivery, pickup, in_person
            $table->string('table_number', 10)->nullable(); // For in-person orders
            $table->string('address')->nullable();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->onDelete('set null');
            $table->string('status', 20)->default('pending'); // pending, preparing, ready, delivering, completed, cancelled
            $table->integer('subtotal');
            $table->integer('delivery_fee')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total');
            $table->string('payment_method', 20); // cash, transfer, transbank
            $table->string('invoice_type', 20)->default('none'); // none (sin boleta), boleta, factura
            $table->string('document_number', 20)->nullable(); // Folio DTE (SII mock)
            $table->text('notes')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamps();
        });

        // 7. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Tasks (Kitchen tasks and Delivery assignments)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description');
            $table->string('status', 20)->default('pending'); // pending, doing, done
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 9. Cash Registers (Apertura y Cierre de caja)
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Employee opening the register
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->integer('initial_amount');
            $table->integer('final_amount')->nullable();
            $table->integer('actual_amount')->nullable(); // Counted at closure
            $table->string('status', 20)->default('open'); // open, closed
            $table->integer('difference')->nullable(); // final - actual
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // 10. Finances (Ingresos y Egresos)
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->onDelete('set null');
            $table->string('type', 10); // income, expense
            $table->string('category', 30); // sales, purchase, salary, utilities, merger, other
            $table->integer('amount');
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('payment_method', 20)->nullable();
            $table->string('document_path')->nullable(); // PDF voucher, receipt, etc.
            $table->timestamps();
        });

        // 11. CMS Content
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 12. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('finances');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
