<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\CmsContent;
use App\Models\Table;
use App\Models\Waiter;
use App\Models\Reservation;
use App\Models\CleaningTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Geographic seeding (Regions and Communes)
        $this->call(ChileSeeder::class);

        // 2. Seed Users (Roles)
        $users = [
            [
                'name' => 'mfeli',
                'email' => 'mfeli@comida.cl',
                'rut' => '1.111.111-1',
                'role' => 'admin',
                'phone' => '+56911112222',
                'address' => 'Av. Providencia 1234',
                'commune_id' => 20,
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Administrador Comida',
                'email' => 'admin@comida.cl',
                'rut' => '12.345.678-9',
                'role' => 'admin',
                'phone' => '+56912345678',
                'address' => 'Av. Providencia 1234',
                'commune_id' => 20, // Providencia
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Jefe de Cocina',
                'email' => 'cocina@comida.cl',
                'rut' => '15.555.555-5',
                'role' => 'chef',
                'phone' => '+56955555555',
                'address' => 'Alameda 340',
                'commune_id' => 19, // Santiago Centro
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Cajero y Garzón',
                'email' => 'caja@comida.cl',
                'rut' => '18.888.888-8',
                'role' => 'waiter',
                'phone' => '+56988888888',
                'address' => 'Av. Irarrázaval 2200',
                'commune_id' => 24, // Ñuñoa
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Repartidor Delivery',
                'email' => 'repartidor@comida.cl',
                'rut' => '20.222.222-2',
                'role' => 'delivery',
                'phone' => '+56922222222',
                'address' => 'Av. Vitacura 5000',
                'commune_id' => 22, // Vitacura
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Contador Financiero',
                'email' => 'contador@comida.cl',
                'rut' => '11.111.111-1',
                'role' => 'accountant',
                'phone' => '+56911111111',
                'address' => 'Apoquindo 4000',
                'commune_id' => 21, // Las Condes
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ],
            [
                'name' => 'Cliente Registrado',
                'email' => 'cliente@gmail.com',
                'rut' => '19.999.999-9',
                'role' => 'client',
                'phone' => '+56999999999',
                'address' => 'Pajaritos 1500',
                'commune_id' => 29, // Maipú
                'is_active' => true,
                'password' => Hash::make('comida2026'),
            ]
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 3. Seed Suppliers
        $suppliers = [
            [
                'id' => 1,
                'name' => 'Distribuidora Mayorista Alvi',
                'rut' => '76.123.456-7',
                'email' => 'contacto@alvi.cl',
                'phone' => '+56911112222',
                'address' => 'Av. Vespucio 1234, Pudahuel'
            ],
            [
                'id' => 2,
                'name' => 'Verdulería Don Tito',
                'rut' => '77.987.654-3',
                'email' => 'contacto@dontito.cl',
                'phone' => '+56933334444',
                'address' => 'Mercado Central Local 12, Santiago'
            ],
            [
                'id' => 3,
                'name' => 'Panificadora San Camilo',
                'rut' => '81.456.789-0',
                'email' => 'pedidos@sancamilo.cl',
                'phone' => '+56222223333',
                'address' => 'San Pablo 3210, Santiago'
            ]
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(['id' => $s['id']], $s);
        }

        // 4. Seed Categories
        $categories = [
            ['id' => 1, 'name' => 'Hamburguesas', 'slug' => 'hamburguesas', 'description' => 'Sándwiches tradicionales chilenos en pan frica.'],
            ['id' => 2, 'name' => 'Completos', 'slug' => 'completos', 'description' => 'Completos chilenos con vienesas y aderezos exquisitos.'],
            ['id' => 3, 'name' => 'Pizzas', 'slug' => 'pizzas', 'description' => 'Pizzas familiares horneadas al momento.'],
            ['id' => 4, 'name' => 'Acompañamientos', 'slug' => 'acompanamientos', 'description' => 'Papas fritas y acompañamientos crujientes.'],
            ['id' => 5, 'name' => 'Bebidas', 'slug' => 'bebidas', 'description' => 'Bebidas refrescantes de lata o botella.']
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['id' => $cat['id']], $cat);
        }

        // 5. Seed Ingredients
        $ingredients = [
            ['id' => 1, 'name' => 'Pan frica artesanal', 'unit' => 'und', 'stock' => 120, 'minimum_stock' => 20, 'cost_per_unit' => 300, 'supplier_id' => 3, 'lot_number' => 'LOTE-F120', 'expiration_date' => '2026-06-05'],
            ['id' => 2, 'name' => 'Pan de completo copihue', 'unit' => 'und', 'stock' => 150, 'minimum_stock' => 25, 'cost_per_unit' => 200, 'supplier_id' => 3, 'lot_number' => 'LOTE-C150', 'expiration_date' => '2026-06-05'],
            ['id' => 3, 'name' => 'Lomo de Vacuno laminado', 'unit' => 'gr', 'stock' => 25000, 'minimum_stock' => 5000, 'cost_per_unit' => 12, 'supplier_id' => 1, 'lot_number' => 'LOTE-V889', 'expiration_date' => '2026-06-15'],
            ['id' => 4, 'name' => 'Salchicha vienesa premium', 'unit' => 'und', 'stock' => 180, 'minimum_stock' => 30, 'cost_per_unit' => 150, 'supplier_id' => 1, 'lot_number' => 'LOTE-S180', 'expiration_date' => '2026-07-20'],
            ['id' => 5, 'name' => 'Palta molida Hass', 'unit' => 'gr', 'stock' => 12000, 'minimum_stock' => 2000, 'cost_per_unit' => 6, 'supplier_id' => 2, 'lot_number' => 'LOTE-P340', 'expiration_date' => '2026-05-31'],
            ['id' => 6, 'name' => 'Tomate picado fino', 'unit' => 'gr', 'stock' => 15000, 'minimum_stock' => 2500, 'cost_per_unit' => 2, 'supplier_id' => 2, 'lot_number' => 'LOTE-T150', 'expiration_date' => '2026-06-01'],
            ['id' => 7, 'name' => 'Mayonesa casera', 'unit' => 'ml', 'stock' => 8000, 'minimum_stock' => 1500, 'cost_per_unit' => 3, 'supplier_id' => 1, 'lot_number' => 'LOTE-M800', 'expiration_date' => '2026-06-02'],
            ['id' => 8, 'name' => 'Porotos verdes cocidos', 'unit' => 'gr', 'stock' => 6000, 'minimum_stock' => 1000, 'cost_per_unit' => 2, 'supplier_id' => 2, 'lot_number' => 'LOTE-PV60', 'expiration_date' => '2026-06-02'],
            ['id' => 9, 'name' => 'Ají verde picado', 'unit' => 'gr', 'stock' => 2000, 'minimum_stock' => 400, 'cost_per_unit' => 3, 'supplier_id' => 2, 'lot_number' => 'LOTE-AJ20', 'expiration_date' => '2026-06-05'],
            ['id' => 10, 'name' => 'Queso mantecoso fundido', 'unit' => 'gr', 'stock' => 10000, 'minimum_stock' => 2000, 'cost_per_unit' => 8, 'supplier_id' => 1, 'lot_number' => 'LOTE-Q100', 'expiration_date' => '2026-07-10'],
            ['id' => 11, 'name' => 'Masa para pizza familiar prepizza', 'unit' => 'und', 'stock' => 60, 'minimum_stock' => 10, 'cost_per_unit' => 1200, 'supplier_id' => 3, 'lot_number' => 'LOTE-MZ60', 'expiration_date' => '2026-06-03'],
            ['id' => 12, 'name' => 'Salsa pomodoro pizza', 'unit' => 'ml', 'stock' => 12000, 'minimum_stock' => 2000, 'cost_per_unit' => 1, 'supplier_id' => 1, 'lot_number' => 'LOTE-SP12', 'expiration_date' => '2026-09-10'],
            ['id' => 13, 'name' => 'Queso mozzarella rallado', 'unit' => 'gr', 'stock' => 20000, 'minimum_stock' => 3000, 'cost_per_unit' => 8, 'supplier_id' => 1, 'lot_number' => 'LOTE-QM20', 'expiration_date' => '2026-07-22'],
            ['id' => 14, 'name' => 'Pepperoni laminado premium', 'unit' => 'gr', 'stock' => 4000, 'minimum_stock' => 800, 'cost_per_unit' => 14, 'supplier_id' => 1, 'lot_number' => 'LOTE-PP40', 'expiration_date' => '2026-08-10'],
            ['id' => 15, 'name' => 'Papas cortadas prefritas', 'unit' => 'gr', 'stock' => 40000, 'minimum_stock' => 5000, 'cost_per_unit' => 2, 'supplier_id' => 1, 'lot_number' => 'LOTE-PAP4', 'expiration_date' => '2026-11-15'],
            ['id' => 16, 'name' => 'Aceite vegetal para freír', 'unit' => 'ml', 'stock' => 60000, 'minimum_stock' => 10000, 'cost_per_unit' => 2, 'supplier_id' => 1, 'lot_number' => 'LOTE-AC60', 'expiration_date' => '2026-12-01'],
            ['id' => 17, 'name' => 'Lata Coca-Cola Original 350ml', 'unit' => 'und', 'stock' => 120, 'minimum_stock' => 24, 'cost_per_unit' => 600, 'supplier_id' => 1, 'lot_number' => 'LOTE-CC12', 'expiration_date' => '2027-05-01']
        ];

        foreach ($ingredients as $i) {
            Ingredient::updateOrCreate(['id' => $i['id']], $i);
        }

        // 6. Seed Products (Menu Dishes)
        $products = [
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Chacarero Tradicional',
                'description' => 'Sándwich de lomo de vacuno, porotos verdes tiernos, rodajas de tomate y ají verde en pan frica con mayonesa.',
                'price' => 6900,
                'image_path' => 'images/f1.png',
                'estimated_prep_time' => 12,
                'is_available' => true
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Barros Luco Fundido',
                'description' => 'Sándwich de abundante lomo de vacuno con abundante queso mantecoso derretido en pan frica tostado.',
                'price' => 6500,
                'image_path' => 'images/f3.png',
                'estimated_prep_time' => 10,
                'is_available' => true
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'name' => 'Completo Italiano Gigante',
                'description' => 'Tradicional completo chileno en pan copihue con vienesa, tomate picado, palta molida y mayonesa casera abundante.',
                'price' => 3200,
                'image_path' => 'images/f2.png',
                'estimated_prep_time' => 5,
                'is_available' => true
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'As Italiano',
                'description' => 'Pan de completo copihue relleno con tiernas tiras de lomo de vacuno, tomate, palta y mayonesa casera.',
                'price' => 3800,
                'image_path' => 'images/f4.png',
                'estimated_prep_time' => 8,
                'is_available' => true
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'name' => 'Pizza Familiar Pepperoni',
                'description' => 'Base de salsa pomodoro artesanal, doble queso mozzarella y abundante pepperoni premium al horno.',
                'price' => 12900,
                'image_path' => 'images/f6.png',
                'estimated_prep_time' => 20,
                'is_available' => true
            ],
            [
                'id' => 6,
                'category_id' => 4,
                'name' => 'Papas Fritas Medianas',
                'description' => 'Papas fritas crujientes doradas al instante, ideales para compartir.',
                'price' => 2800,
                'image_path' => 'images/f5.png',
                'estimated_prep_time' => 7,
                'is_available' => true
            ],
            [
                'id' => 7,
                'category_id' => 5,
                'name' => 'Lata Coca-Cola Original 350ml',
                'description' => 'Refrescante bebida en lata helada.',
                'price' => 1500,
                'image_path' => 'images/f7.png', // We can placeholder or point to a default image
                'estimated_prep_time' => 2,
                'is_available' => true
            ]
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['id' => $p['id']], $p);
        }

        // 7. Seed Recipes (Relate Products to Ingredients)
        $recipes = [
            // Chacarero: 1 Pan frica, 150g Lomo, 80g Tomate, 50g Porotos verdes, 15g Aji verde, 40ml Mayo
            ['product_id' => 1, 'ingredient_id' => 1, 'quantity_required' => 1.00],
            ['product_id' => 1, 'ingredient_id' => 3, 'quantity_required' => 150.00],
            ['product_id' => 1, 'ingredient_id' => 6, 'quantity_required' => 80.00],
            ['product_id' => 1, 'ingredient_id' => 8, 'quantity_required' => 50.00],
            ['product_id' => 1, 'ingredient_id' => 9, 'quantity_required' => 15.00],
            ['product_id' => 1, 'ingredient_id' => 7, 'quantity_required' => 40.00],

            // Barros Luco: 1 Pan frica, 150g Lomo, 80g Queso mantecoso
            ['product_id' => 2, 'ingredient_id' => 1, 'quantity_required' => 1.00],
            ['product_id' => 2, 'ingredient_id' => 3, 'quantity_required' => 150.00],
            ['product_id' => 2, 'ingredient_id' => 10, 'quantity_required' => 80.00],

            // Completo Italiano: 1 Pan completo, 1 Vienesa, 80g Palta, 80g Tomate, 40ml Mayo
            ['product_id' => 3, 'ingredient_id' => 2, 'quantity_required' => 1.00],
            ['product_id' => 3, 'ingredient_id' => 4, 'quantity_required' => 1.00],
            ['product_id' => 3, 'ingredient_id' => 5, 'quantity_required' => 80.00],
            ['product_id' => 3, 'ingredient_id' => 6, 'quantity_required' => 80.00],
            ['product_id' => 3, 'ingredient_id' => 7, 'quantity_required' => 40.00],

            // As Italiano: 1 Pan completo, 100g Lomo, 80g Palta, 80g Tomate, 40ml Mayo
            ['product_id' => 4, 'ingredient_id' => 2, 'quantity_required' => 1.00],
            ['product_id' => 4, 'ingredient_id' => 3, 'quantity_required' => 100.00],
            ['product_id' => 4, 'ingredient_id' => 5, 'quantity_required' => 80.00],
            ['product_id' => 4, 'ingredient_id' => 6, 'quantity_required' => 80.00],
            ['product_id' => 4, 'ingredient_id' => 7, 'quantity_required' => 40.00],

            // Pizza Pepperoni: 1 Masa prepizza, 200ml Salsa pomodoro, 300g Mozzarella, 150g Pepperoni
            ['product_id' => 5, 'ingredient_id' => 11, 'quantity_required' => 1.00],
            ['product_id' => 5, 'ingredient_id' => 12, 'quantity_required' => 200.00],
            ['product_id' => 5, 'ingredient_id' => 13, 'quantity_required' => 300.00],
            ['product_id' => 5, 'ingredient_id' => 14, 'quantity_required' => 150.00],

            // Papas Fritas: 250g Papas prefritas, 50ml Aceite
            ['product_id' => 6, 'ingredient_id' => 15, 'quantity_required' => 250.00],
            ['product_id' => 6, 'ingredient_id' => 16, 'quantity_required' => 50.00],

            // Bebida: 1 Lata Coca-Cola
            ['product_id' => 7, 'ingredient_id' => 17, 'quantity_required' => 1.00]
        ];

        foreach ($recipes as $rec) {
            Recipe::updateOrCreate([
                'product_id' => $rec['product_id'],
                'ingredient_id' => $rec['ingredient_id']
            ], [
                'quantity_required' => $rec['quantity_required']
            ]);
        }

        // 8. Seed CMS Content
        $cms_data = [
            ['key' => 'hero_slide_1_title', 'value' => 'Nuestras Promociones 2 X 1 en Sandwich', 'description' => 'Título del primer banner principal.'],
            ['key' => 'hero_slide_1_desc', 'value' => 'Los sándwiches chilenos son una parte importante de la comida chilena y destacan por su variedad de sabores. Algunos de los más conocidos son el Barros Luco, el Chacarero, el Ave Palta y el Mechada.', 'description' => 'Descripción del primer banner principal.'],
            ['key' => 'hero_slide_2_title', 'value' => 'Nuestras Promociones 2 X 1 en Vianesas', 'description' => 'Título del segundo banner principal.'],
            ['key' => 'hero_slide_2_desc', 'value' => 'Los completos chilenos son una versión única de los hot dogs en Chile. Están hechos con vienesas de alta calidad, pan suave y se acompañan con ingredientes como salsa de tomate, mayonesa, palta, chucrut, cebolla y ají.', 'description' => 'Descripción del segundo banner principal.'],
            ['key' => 'hero_slide_3_title', 'value' => 'Promociones en pizzas familiares', 'description' => 'Título del tercer banner principal.'],
            ['key' => 'hero_slide_3_desc', 'value' => 'La pizza es un plato icónico de la cocina italiana que ha ganado popularidad a nivel mundial. Consiste en una masa plana horneada, cubierta con salsa de tomate, queso y diversos ingredientes como carnes, vegetales y especias.', 'description' => 'Descripción del tercer banner principal.'],
            ['key' => 'opening_days', 'value' => 'Lunes a Viernes', 'description' => 'Días de apertura de atención.'],
            ['key' => 'opening_hours', 'value' => '20.00 PM - 02.00 AM', 'description' => 'Horario de atención.'],
            ['key' => 'contact_phone', 'value' => '+56 9 8765 4321', 'description' => 'Teléfono de contacto.'],
            ['key' => 'contact_email', 'value' => 'contacto@comidaalpaso.cl', 'description' => 'Email de contacto.'],
            ['key' => 'contact_address', 'value' => 'Av. Providencia 1234, Santiago, Chile', 'description' => 'Dirección comercial.'],
            ['key' => 'footer_about', 'value' => 'Comida al Paso ofrece la mejor calidad en sándwiches tradicionales chilenos, completos italianos y pizzas familiares, elaborados con ingredientes de primera y bajo estrictas normas de trazabilidad sanitaria.', 'description' => 'Texto sobre nosotros en el pie de página.'],
        ];

        foreach ($cms_data as $cms) {
            CmsContent::updateOrCreate(['key' => $cms['key']], [
                'value' => $cms['value'],
                'description' => $cms['description']
            ]);
        }

        // 9. Seed Tables (Mesas)
        $tables = [
            ['number' => 1, 'capacity' => 2, 'zone' => 'interior', 'status' => 'free', 'waiter_name' => null],
            ['number' => 2, 'capacity' => 4, 'zone' => 'interior', 'status' => 'free', 'waiter_name' => null],
            ['number' => 3, 'capacity' => 4, 'zone' => 'interior', 'status' => 'free', 'waiter_name' => null],
            ['number' => 4, 'capacity' => 6, 'zone' => 'interior', 'status' => 'free', 'waiter_name' => null],
            ['number' => 5, 'capacity' => 2, 'zone' => 'terraza', 'status' => 'free', 'waiter_name' => null],
            ['number' => 6, 'capacity' => 4, 'zone' => 'terraza', 'status' => 'free', 'waiter_name' => null],
            ['number' => 7, 'capacity' => 4, 'zone' => 'terraza', 'status' => 'free', 'waiter_name' => null],
            ['number' => 8, 'capacity' => 6, 'zone' => 'terraza', 'status' => 'free', 'waiter_name' => null],
            ['number' => 9, 'capacity' => 4, 'zone' => 'privado', 'status' => 'free', 'waiter_name' => null],
            ['number' => 10, 'capacity' => 6, 'zone' => 'privado', 'status' => 'free', 'waiter_name' => null],
        ];

        foreach ($tables as $t) {
            Table::updateOrCreate(['number' => $t['number']], $t);
        }

        // 10. Seed Waiters (Garzones)
        $waiters = [
            ['name' => 'Claudio Soto', 'is_active' => true],
            ['name' => 'Andrea Vergara', 'is_active' => true],
            ['name' => 'Juan Garzón', 'is_active' => true],
        ];

        foreach ($waiters as $w) {
            Waiter::updateOrCreate(['name' => $w['name']], $w);
        }

        // 11. Seed Reservations (Reservas)
        $reservations = [
            [
                'table_id' => 2,
                'customer_name' => 'Beatriz Muñoz',
                'customer_phone' => '+56988887777',
                'party_size' => 4,
                'reserved_at' => date('Y-m-d H:i:s', strtotime('+1 day 20:30:00')),
                'status' => 'pending',
                'notes' => 'Mesa cerca de la ventana si es posible.'
            ],
            [
                'table_id' => 9,
                'customer_name' => 'Carlos Fuentes',
                'customer_phone' => '+56977776666',
                'party_size' => 3,
                'reserved_at' => date('Y-m-d H:i:s', strtotime('+2 days 21:00:00')),
                'status' => 'pending',
                'notes' => 'Celebración de cumpleaños en privado.'
            ]
        ];

        foreach ($reservations as $r) {
            Reservation::updateOrCreate([
                'customer_name' => $r['customer_name'],
                'reserved_at' => $r['reserved_at']
            ], $r);
        }

        // 12. Seed Cleaning Tasks (Tareas de Limpieza)
        $cleaning_tasks = [
            [
                'name' => 'Sanitización completa de baños',
                'zone' => 'baños',
                'frequency' => 'daily',
                'last_completed_at' => date('Y-m-d', strtotime('-1 day')),
                'completed_by_id' => 4, // Cajero y Garzón
                'next_due_date' => date('Y-m-d')
            ],
            [
                'name' => 'Limpieza y desengrasado de campana extractora',
                'zone' => 'cocina',
                'frequency' => 'weekly',
                'last_completed_at' => date('Y-m-d', strtotime('-5 days')),
                'completed_by_id' => 3, // Jefe de Cocina
                'next_due_date' => date('Y-m-d', strtotime('+2 days'))
            ],
            [
                'name' => 'Lavado profundo de pisos de salón',
                'zone' => 'salón',
                'frequency' => 'weekly',
                'last_completed_at' => date('Y-m-d', strtotime('-6 days')),
                'completed_by_id' => 4, // Cajero y Garzón
                'next_due_date' => date('Y-m-d', strtotime('+1 day'))
            ],
            [
                'name' => 'Limpieza profunda de bodegas e inventario',
                'zone' => 'bodega',
                'frequency' => 'monthly',
                'last_completed_at' => date('Y-m-d', strtotime('-25 days')),
                'completed_by_id' => 3, // Jefe de Cocina
                'next_due_date' => date('Y-m-d', strtotime('+5 days'))
            ],
        ];

        foreach ($cleaning_tasks as $ct) {
            CleaningTask::updateOrCreate(['name' => $ct['name']], $ct);
        }
    }
}
