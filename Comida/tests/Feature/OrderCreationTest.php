<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_in_person_order_without_phone(): void
    {
        $user = User::create([
            'name' => 'Test Operator',
            'email' => 'operator_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'rut' => rand(10000000, 25000000) . '-k',
            'role' => 'admin',
            'is_active' => true
        ]);

        $category = Category::create([
            'name' => 'Test Pizza ' . uniqid(),
            'slug' => 'test-pizza-' . uniqid(),
            'description' => 'Pizza description'
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pizza Test Margherita',
            'price' => 5000,
            'estimated_prep_time' => 15,
            'is_available' => true
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.orders.storeInternal'), [
                'client_name' => 'Marta Gomez',
                'type' => 'in_person',
                'payment_method' => 'cash',
                'invoice_type' => 'none',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Pedido creado exitosamente.'
        ]);

        $this->assertDatabaseHas('orders', [
            'client_name' => 'Marta Gomez',
            'client_phone' => 'N/A',
            'type' => 'in_person',
            'status' => 'pending'
        ]);
    }

    public function test_can_create_pickup_order_without_phone(): void
    {
        $user = User::create([
            'name' => 'Test Operator 2',
            'email' => 'operator_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'rut' => rand(10000000, 25000000) . '-k',
            'role' => 'admin',
            'is_active' => true
        ]);

        $category = Category::create([
            'name' => 'Test Burgers ' . uniqid(),
            'slug' => 'test-burgers-' . uniqid(),
            'description' => 'Burger description'
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Burger Test Classic',
            'price' => 4500,
            'estimated_prep_time' => 10,
            'is_available' => true
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.orders.storeInternal'), [
                'client_name' => 'Carlos Perez',
                'type' => 'pickup',
                'payment_method' => 'transbank',
                'invoice_type' => 'boleta',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Pedido creado exitosamente.'
        ]);

        $this->assertDatabaseHas('orders', [
            'client_name' => 'Carlos Perez',
            'client_phone' => 'N/A',
            'type' => 'pickup',
            'status' => 'pending'
        ]);
    }

    public function test_kitchen_display_system_view_shows_orders_with_limit_times(): void
    {
        $user = User::create([
            'name' => 'Test Operator 3',
            'email' => 'operator_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'rut' => rand(10000000, 25000000) . '-k',
            'role' => 'admin',
            'is_active' => true
        ]);

        $category = Category::create([
            'name' => 'Test Drinks ' . uniqid(),
            'slug' => 'test-drinks-' . uniqid(),
            'description' => 'Drinks description'
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Soda Test',
            'price' => 1200,
            'estimated_prep_time' => 5,
            'is_available' => true
        ]);

        // Create one pending order, one preparing order, one ready order
        $order1 = Order::create([
            'user_id' => $user->id,
            'client_name' => 'Client One',
            'client_phone' => 'N/A',
            'type' => 'in_person',
            'status' => 'pending',
            'subtotal' => 1200,
            'delivery_fee' => 0,
            'total' => 1200,
            'payment_method' => 'cash',
            'invoice_type' => 'none',
            'opened_at' => now()
        ]);
        $order1->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1200
        ]);

        $order2 = Order::create([
            'user_id' => $user->id,
            'client_name' => 'Client Two',
            'client_phone' => 'N/A',
            'type' => 'pickup',
            'status' => 'preparing',
            'subtotal' => 2400,
            'delivery_fee' => 0,
            'total' => 2400,
            'payment_method' => 'cash',
            'invoice_type' => 'none',
            'opened_at' => now()
        ]);
        $order2->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 1200
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.kitchen.index'));

        $response->assertStatus(200);
        $response->assertSee('Kitchen Display System');
        
        // Assert we see the ordered product listed
        $response->assertSee('Soda Test');
        
        // Assert the data-limit HTML attributes are present
        $response->assertSee('data-id="' . $order1->id . '"', false);
        $response->assertSee('data-id="' . $order2->id . '"', false);
        $response->assertSee('data-limit');
        
        // Assert timer badges are present
        $response->assertSee('timer-' . $order1->id);
        $response->assertSee('timer-' . $order2->id);
    }
}
