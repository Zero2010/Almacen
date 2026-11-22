<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Task;
use App\Models\User;
use App\Models\Commune;
use App\Models\CashRegister;
use App\Models\Finance;
use App\Models\CmsContent;
use App\Models\ActivityLog;
use App\Models\Table;
use App\Models\Waiter;
use App\Models\Reservation;
use App\Models\CleaningTask;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ==========================================
    // 1. DASHBOARD & ANALYTICS
    // ==========================================
    public function dashboard()
    {
        $today = date('Y-m-d');
        
        // Key stats
        $ordersTodayCount = Order::whereDate('created_at', $today)->count();
        
        $pendingTasksCount = Task::where('status', '!=', 'done')->count();
        
        $lowStockCount = Ingredient::whereRaw('stock <= minimum_stock')->count();
        
        $revenueToday = Finance::whereDate('date', $today)
            ->where('type', 'income')
            ->where('category', 'sales')
            ->sum('amount');

        $occupiedTablesCount = Table::where('status', 'occupied')->count();
        $totalTablesCount = Table::count();
        $reservationsTodayCount = Reservation::whereDate('reserved_at', $today)->count();
        $pendingCleaningCount = CleaningTask::where(function($q) use ($today) {
            $q->whereNull('last_completed_at')
              ->orWhereDate('next_due_date', '<=', $today);
        })->count();
        
        // Recent orders
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        // Low stock details
        $lowStockIngredients = Ingredient::whereRaw('stock <= minimum_stock')->take(5)->get();

        // Chart 1: Sales by category (CLP)
        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category', DB::raw('SUM(order_items.quantity * order_items.unit_price) as total'))
            ->groupBy('categories.name')
            ->get();

        // Chart 2: Orders status distribution
        $orderStatuses = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Chart 3: 7-day sales trend (income from sales)
        $salesTrend = DB::table('finances')
            ->select('date', DB::raw('SUM(amount) as total'))
            ->where('type', 'income')
            ->where('category', 'sales')
            ->where('date', '>=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'ordersTodayCount',
            'pendingTasksCount',
            'lowStockCount',
            'revenueToday',
            'recentOrders',
            'lowStockIngredients',
            'salesByCategory',
            'orderStatuses',
            'salesTrend',
            'occupiedTablesCount',
            'totalTablesCount',
            'reservationsTodayCount',
            'pendingCleaningCount'
        ));
    }

    // ==========================================
    // 2. ORDER OPERATIONS & KITCHEN BOARD
    // ==========================================
    public function ordersList(Request $request)
    {
        $query = Order::orderBy('created_at', 'desc');

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $orders = $query->paginate(15);
        $employees = User::whereIn('role', ['chef', 'delivery', 'waiter', 'admin'])->get();
        
        $comandas = Order::with('orderItems.product')
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.orders.index', compact('orders', 'employees', 'comandas'));
    }

    public function kitchenDashboard()
    {
        $comandas = Order::with('orderItems.product')
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('admin.kitchen.index', compact('comandas'));
    }

    public function orderUpdateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivering,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'El pedido ya se encuentra en ese estado.');
        }

        DB::beginTransaction();
        try {
            // STOCK CONTROL RULE: Deduct stock when transition into 'preparing'
            if ($newStatus === 'preparing' && $oldStatus === 'pending') {
                foreach ($order->orderItems as $item) {
                    foreach ($item->product->recipes as $recipe) {
                        $ingredient = $recipe->ingredient;
                        $requiredQty = $recipe->quantity_required * $item->quantity;
                        
                        if ($ingredient->stock < $requiredQty) {
                            throw new \Exception("Stock insuficiente de '{$ingredient->name}' para preparar '{$item->product->name}'.");
                        }
                        
                        $ingredient->decrement('stock', $requiredQty);
                    }
                }
            }

            // STOCK CONTROL RULE: Add back stock if cancelled after being prepared
            if ($newStatus === 'cancelled' && in_array($oldStatus, ['preparing', 'ready', 'delivering', 'completed'])) {
                foreach ($order->orderItems as $item) {
                    foreach ($item->product->recipes as $recipe) {
                        $recipe->ingredient->increment('stock', $recipe->quantity_required * $item->quantity);
                    }
                }
            }

            // FINANCIAL RULE: Record cash register booking on order completion
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                // Generate Folio SII (electronic document number)
                if (empty($order->document_number)) {
                    $order->document_number = rand(2000000, 9999999);
                }

                $activeShift = CashRegister::where('status', 'open')->first();
                Finance::create([
                    'cash_register_id' => $activeShift ? $activeShift->id : null,
                    'type' => 'income',
                    'category' => 'sales',
                    'amount' => $order->total,
                    'date' => date('Y-m-d'),
                    'description' => "Venta Pedido #{$order->id} ({$order->client_name}) - SII Folio {$order->document_number}",
                    'payment_method' => $order->payment_method
                ]);
            }

            // Release associated table on completion or cancellation
            if (in_array($newStatus, ['completed', 'cancelled'])) {
                if ($order->table) {
                    $order->table->update([
                        'status' => 'free',
                        'waiter_name' => null
                    ]);
                }
                if ($newStatus === 'completed') {
                    $order->closed_at = now();
                }
            }

            // Update order
            $order->status = $newStatus;
            $order->save();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_order_status',
                'description' => "Cambió el estado del pedido #{$order->id} de '{$oldStatus}' a '{$newStatus}'."
            ]);

            DB::commit();
            
            if ($newStatus === 'completed') {
                return redirect()->route('admin.orders.print', ['order' => $order->id, 'doc' => 'boleta'])->with('success', "Pedido #{$order->id} pagado. Imprimiendo boleta.");
            }

            return back()->with('success', "Pedido #{$order->id} actualizado a '{$newStatus}' con éxito.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['stock' => $e->getMessage()]);
        }
    }

    public function orderCreate()
    {
        $products = Product::where('is_available', true)->get();
        $categories = Category::orderBy('name')->get();
        $regions = Region::with('communes')->orderBy('name')->get();
        $tables = Table::where('status', 'free')->orderBy('number')->get();
        $waiters = Waiter::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.create', compact('products', 'categories', 'regions', 'tables', 'waiters'));
    }

    public function orderStoreInternal(Request $request)
    {
        // Validación común (para AJAX y normal)
        $validated = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_phone' => 'required_if:type,delivery|nullable|string|max:20',
            'type' => 'required|in:delivery,pickup,in_person',
            'table_number' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'region_id' => 'required_if:type,delivery|nullable|exists:regions,id',
            'commune_id' => 'required_if:type,delivery|nullable|exists:communes,id',
            'payment_method' => 'required|in:cash,transfer,transbank',
            'invoice_type' => 'required|in:none,boleta,factura',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'table_id' => 'nullable|exists:tables,id',
            'waiter_id' => 'nullable|exists:waiters,id',
            'party_size' => 'nullable|integer|min:1'
        ]);

        $subtotal = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->price;
            $qty = (int)$item['quantity'];
            $subtotal += $price * $qty;
            $itemsData[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $price
            ];
        }

        $delivery_fee = ($validated['type'] === 'delivery') ? 2000 : 0;
        $total = $subtotal + $delivery_fee;

        $table = null;
        $waiter = null;

        if (!empty($validated['waiter_id'])) {
            $waiter = Waiter::findOrFail($validated['waiter_id']);
        }

        if ($validated['type'] === 'in_person' && !empty($validated['table_id'])) {
            $table = Table::findOrFail($validated['table_id']);
        }

        DB::beginTransaction();
        try {
            if ($table) {
                $table->update([
                    'status' => 'occupied',
                    'waiter_name' => $waiter ? $waiter->name : null
                ]);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'] ?? 'N/A',
                'type' => $validated['type'],
                'table_number' => $table ? (string)$table->number : ($validated['table_number'] ?? null),
                'table_id' => $table ? $table->id : null,
                'waiter_id' => $waiter ? $waiter->id : null,
                'waiter_name' => $waiter ? $waiter->name : null,
                'party_size' => $validated['party_size'] ?? null,
                'opened_at' => now(),
                'address' => $validated['address'] ?? null,
                'region_id' => $validated['region_id'] ?? null,
                'commune_id' => $validated['commune_id'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $delivery_fee,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'invoice_type' => $validated['invoice_type'],
                'notes' => $validated['notes'] ?? null
            ]);

            foreach ($itemsData as $item) {
                $order->orderItems()->create($item);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_order_internal',
                'description' => "Creó de forma interna el pedido #{$order->id} para el cliente {$order->client_name}."
            ]);

            DB::commit();

            // 🔥 RESPUESTA PARA AJAX (POS)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'message' => 'Pedido creado exitosamente.'
                ]);
            }

            // Respuesta normal (sin AJAX)
            return redirect()->route('admin.orders.print', ['order' => $order->id, 'doc' => 'comanda'])
                            ->with('success', "Pedido #{$order->id} creado con éxito.");

        } catch (\Exception $e) {
            DB::rollBack();

            // Respuesta de error para AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el pedido: ' . $e->getMessage()
                ], 500);
            }

            // Error normal
            return back()->withErrors(['error' => 'Ocurrió un error al crear el pedido: ' . $e->getMessage()]);
        }
    }

    public function orderPrint(Order $order, Request $request)
    {
        $order->load(['orderItems.product', 'table', 'waiter']);
        $doc = $request->query('doc', 'all');
        return view('admin.orders.print', compact('order', 'doc'));
    }

    public function orderAssignEmployee(Order $order, Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255'
        ]);

        $employee = User::findOrFail($request->employee_id);

        Task::create([
            'order_id' => $order->id,
            'employee_id' => $employee->id,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'assign_task',
            'description' => "Asignó una tarea del Pedido #{$order->id} al empleado {$employee->name}."
        ]);

        return back()->with('success', "Tarea asignada exitosamente a {$employee->name}.");
    }

    public function showSIIInvoice(Order $order)
    {
        $net = round($order->total / 1.19);
        $iva = $order->total - $net;
        return view('admin.orders.documento', compact('order', 'net', 'iva'));
    }

    // ==========================================
    // 3. CASH CONTROL (CAJA)
    // ==========================================
    public function cashRegister()
    {
        $activeShift = CashRegister::where('status', 'open')->first();
        
        $todayShifts = CashRegister::orderBy('opened_at', 'desc')->paginate(10);
        
        // If there's an active shift, calculate expected amount based on sales
        $expectedAmount = 0;
        if ($activeShift) {
            $shiftSalesTotal = Finance::where('cash_register_id', $activeShift->id)
                ->where('type', 'income')
                ->sum('amount');
            
            $shiftWithdrawalsTotal = Finance::where('cash_register_id', $activeShift->id)
                ->where('type', 'expense')
                ->sum('amount');

            $expectedAmount = $activeShift->initial_amount + $shiftSalesTotal - $shiftWithdrawalsTotal;
        } else {
            $shiftSalesTotal = 0;
            $shiftWithdrawalsTotal = 0;
        }

        return view('admin.finances.caja', compact(
            'activeShift', 
            'todayShifts', 
            'expectedAmount',
            'shiftSalesTotal',
            'shiftWithdrawalsTotal'
        ));
    }

    public function openCashRegister(Request $request)
    {
        $request->validate([
            'initial_amount' => 'required|integer|min:0'
        ]);

        if (CashRegister::where('status', 'open')->exists()) {
            return back()->withErrors(['caja' => 'Ya existe una caja abierta en este turno.']);
        }

        $shift = CashRegister::create([
            'user_id' => Auth::id(),
            'initial_amount' => $request->initial_amount,
            'status' => 'open'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'open_cash_register',
            'description' => "Abrió la caja con un monto inicial de $" . number_format($request->initial_amount, 0, ',', '.') . " CLP."
        ]);

        return back()->with('success', 'Caja abierta con éxito para este turno.');
    }

    public function closeCashRegister(Request $request)
    {
        $request->validate([
            'actual_amount' => 'required|integer|min:0',
            'observations' => 'nullable|string|max:500'
        ]);

        $shift = CashRegister::where('status', 'open')->first();
        if (!$shift) {
            return back()->withErrors(['caja' => 'No hay ninguna caja abierta actualmente.']);
        }

        // Calculate expected final amount
        $shiftSalesTotal = Finance::where('cash_register_id', $shift->id)
            ->where('type', 'income')
            ->sum('amount');
        
        $shiftWithdrawalsTotal = Finance::where('cash_register_id', $shift->id)
            ->where('type', 'expense')
            ->sum('amount');

        $finalExpected = $shift->initial_amount + $shiftSalesTotal - $shiftWithdrawalsTotal;
        $difference = $request->actual_amount - $finalExpected;

        $shift->closed_at = now();
        $shift->final_amount = $finalExpected;
        $shift->actual_amount = $request->actual_amount;
        $shift->status = 'closed';
        $shift->difference = $difference;
        $shift->observations = $request->observations;
        $shift->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'close_cash_register',
            'description' => "Cerró la caja. Monto contado: $" . number_format($request->actual_amount, 0, ',', '.') . " CLP. Diferencia: $" . number_format($difference, 0, ',', '.') . " CLP."
        ]);

        return back()->with('success', 'Caja cerrada y arqueo completado. Diferencia: $' . number_format($difference, 0, ',', '.') . ' CLP.');
    }

    // ==========================================
    // 4. FINANCIAL TRANSACTIONS
    // ==========================================
    public function financesIndex()
    {
        $finances = Finance::orderBy('date', 'desc')->paginate(15);
        
        $totalIncomes = Finance::where('type', 'income')->sum('amount');
        $totalExpenses = Finance::where('type', 'expense')->sum('amount');
        $netProfit = $totalIncomes - $totalExpenses;

        return view('admin.finances.index', compact('finances', 'totalIncomes', 'totalExpenses', 'netProfit'));
    }

    public function storeFinanceTransaction(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|in:sales,purchase,salary,utilities,merger,other',
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,transfer,transbank'
        ]);

        $activeShift = CashRegister::where('status', 'open')->first();

        $transaction = Finance::create([
            'cash_register_id' => $activeShift ? $activeShift->id : null,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method']
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_finance_transaction',
            'description' => "Registró un/a {$validated['type']} de $" . number_format($validated['amount'], 0, ',', '.') . " CLP en la categoría {$validated['category']}."
        ]);

        return back()->with('success', 'Transacción financiera registrada con éxito.');
    }

    // ==========================================
    // 5. INGREDIENTS & INVENTORY CRUD
    // ==========================================
    public function ingredientsIndex()
    {
        $ingredients = Ingredient::orderBy('name')->paginate(15);
        $suppliers = Supplier::all();
        return view('admin.ingredients.index', compact('ingredients', 'suppliers'));
    }

    public function ingredientsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:10',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'lot_number' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date'
        ]);

        Ingredient::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_ingredient',
            'description' => "Agregó un nuevo ingrediente al inventario: {$validated['name']}."
        ]);

        return back()->with('success', 'Ingrediente/Insumo agregado con éxito.');
    }

    public function ingredientsUpdate(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'unit' => 'required|string|max:10',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'lot_number' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date'
        ]);

        $ingredient->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_ingredient',
            'description' => "Modificó el ingrediente {$ingredient->name} (Stock actual: {$ingredient->stock})."
        ]);

        return back()->with('success', 'Ingrediente actualizado con éxito.');
    }

    public function ingredientsDestroy(Ingredient $ingredient)
    {
        $name = $ingredient->name;
        $ingredient->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_ingredient',
            'description' => "Eliminó el ingrediente {$name} del inventario."
        ]);

        return back()->with('success', 'Ingrediente eliminado del inventario.');
    }

    // ==========================================
    // 6. SUPPLIERS CRUD
    // ==========================================
    public function suppliersIndex()
    {
        $suppliers = Supplier::orderBy('name')->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function suppliersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rut' => 'required|string|max:15|unique:suppliers,rut',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ]);

        Supplier::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_supplier',
            'description' => "Creó el proveedor {$validated['name']}."
        ]);

        return back()->with('success', 'Proveedor creado exitosamente.');
    }

    public function suppliersUpdate(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rut' => 'required|string|max:15|unique:suppliers,rut,' . $supplier->id,
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255'
        ]);

        $supplier->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_supplier',
            'description' => "Actualizó los datos del proveedor {$supplier->name}."
        ]);

        return back()->with('success', 'Proveedor actualizado.');
    }

    public function suppliersDestroy(Supplier $supplier)
    {
        $name = $supplier->name;
        $supplier->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_supplier',
            'description' => "Eliminó el proveedor {$name}."
        ]);

        return back()->with('success', 'Proveedor eliminado.');
    }

    // ==========================================
    // 7. PRODUCTS (MENU DISHES) CRUD
    // ==========================================
    public function productsIndex()
    {
        $products = Product::orderBy('name')->paginate(15);
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'estimated_prep_time' => 'required|integer|min:1',
            'is_available' => 'boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Store locally in public/images
            $file = $request->file('image');
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $imagePath = 'images/' . $fileName;
        }

        $validated['image_path'] = $imagePath;
        $validated['is_available'] = $request->has('is_available');

        Product::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_product',
            'description' => "Creó el plato/producto {$validated['name']}."
        ]);

        return back()->with('success', 'Plato creado exitosamente.');
    }

    public function productsUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'estimated_prep_time' => 'required|integer|min:1',
            'is_available' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            // Store locally in public/images
            $file = $request->file('image');
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $product->image_path = 'images/' . $fileName;
        }

        $product->category_id = $validated['category_id'];
        $product->name = $validated['name'];
        $product->description = $validated['description'];
        $product->price = $validated['price'];
        $product->estimated_prep_time = $validated['estimated_prep_time'];
        $product->is_available = $request->has('is_available');
        $product->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_product',
            'description' => "Actualizó el plato/producto {$product->name}."
        ]);

        return back()->with('success', 'Plato actualizado.');
    }

    public function productsDestroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_product',
            'description' => "Eliminó el plato/producto {$name}."
        ]);

        return back()->with('success', 'Plato eliminado del menú.');
    }

    // ==========================================
    // 8. RECIPES MANAGEMENT (ingredients associated with products)
    // ==========================================
    public function recipesIndex(Product $product)
    {
        $recipes = Recipe::where('product_id', $product->id)->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('admin.recipes.index', compact('product', 'recipes', 'ingredients'));
    }

    public function recipesStore(Request $request, Product $product)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity_required' => 'required|numeric|min:0.01'
        ]);

        // Add or update recipe item
        Recipe::updateOrCreate([
            'product_id' => $product->id,
            'ingredient_id' => $validated['ingredient_id']
        ], [
            'quantity_required' => $validated['quantity_required']
        ]);

        $ingredient = Ingredient::find($validated['ingredient_id']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'add_recipe_item',
            'description' => "Asoció {$validated['quantity_required']} {$ingredient->unit} de {$ingredient->name} al plato {$product->name}."
        ]);

        return back()->with('success', 'Ingrediente agregado a la receta de este plato.');
    }

    public function recipesDestroy(Recipe $recipe)
    {
        $productName = $recipe->product->name;
        $ingredientName = $recipe->ingredient->name;
        $recipe->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_recipe_item',
            'description' => "Removió el ingrediente {$ingredientName} de la receta de {$productName}."
        ]);

        return back()->with('success', 'Ingrediente removido de la receta.');
    }

    // ==========================================
    // 9. CATEGORIES CRUD
    // ==========================================
    public function categoriesIndex()
    {
        $categories = Category::orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_category',
            'description' => "Creó la categoría {$validated['name']}."
        ]);

        return back()->with('success', 'Categoría creada con éxito.');
    }

    public function categoriesUpdate(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_category',
            'description' => "Actualizó la categoría {$category->name}."
        ]);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function categoriesDestroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_category',
            'description' => "Eliminó la categoría {$name}."
        ]);

        return back()->with('success', 'Categoría eliminada.');
    }

    // ==========================================
    // 10. USERS (EMPLOYEES & CLIENTS) CRUD
    // ==========================================
    public function usersIndex()
    {
        $users = User::orderBy('name')->paginate(15);
        $communes = Commune::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'communes'));
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'rut' => 'nullable|string|max:15|unique:users,rut',
            'role' => 'required|in:admin,chef,waiter,delivery,accountant,client',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'commune_id' => 'nullable|exists:communes,id',
            'is_active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_user',
            'description' => "Registró al usuario {$validated['name']} con el rol {$validated['role']}."
        ]);

        return back()->with('success', 'Usuario registrado con éxito.');
    }

    public function usersUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'rut' => 'nullable|string|max:15|unique:users,rut,' . $user->id,
            'role' => 'required|in:admin,chef,waiter,delivery,accountant,client',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'commune_id' => 'nullable|exists:communes,id',
            'is_active' => 'boolean'
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->rut = $validated['rut'] ?? null;
        $user->role = $validated['role'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->commune_id = $validated['commune_id'] ?? null;
        $user->is_active = $request->has('is_active');
        $user->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_user',
            'description' => "Modificó los datos del usuario {$user->name}."
        ]);

        return back()->with('success', 'Usuario actualizado con éxito.');
    }

    public function usersDestroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'No puedes eliminar tu propia cuenta en sesión.']);
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_user',
            'description' => "Eliminó la cuenta del usuario {$name}."
        ]);

        return back()->with('success', 'Usuario eliminado de la base de datos.');
    }

    // ==========================================
    // 11. CMS CONTENT PANEL
    // ==========================================
    public function cmsIndex()
    {
        $cmsContents = CmsContent::all();
        return view('admin.cms.index', compact('cmsContents'));
    }

    public function cmsUpdate(Request $request)
    {
        $cmsData = $request->except('_token');
        
        foreach ($cmsData as $key => $value) {
            CmsContent::where('key', $key)->update(['value' => $value]);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_cms',
            'description' => "Actualizó el contenido CMS de la página pública."
        ]);

        return back()->with('success', 'Contenido del sitio web actualizado.');
    }

    // ==========================================
    // 12. AUDIT TRAIL / ACTIVITY LOGS
    // ==========================================
    public function activityLogs()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')->paginate(30);
        return view('admin.audit.index', compact('logs'));
    }

    // ==========================================
    // 13. TABLES (MESAS) MANAGEMENT
    // ==========================================
    public function tablesIndex()
    {
        $tables = Table::orderBy('number')->get();
        $waiters = Waiter::where('is_active', true)->orderBy('name')->get();
        return view('admin.tables.index', compact('tables', 'waiters'));
    }

    public function tablesStore(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:tables,number',
            'capacity' => 'required|integer|min:1',
            'zone' => 'required|in:interior,terraza,privado',
            'status' => 'required|in:free,occupied,reserved,awaiting_payment'
        ]);

        $table = Table::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_table',
            'description' => "Creó la mesa #{$table->number} con capacidad para {$table->capacity} personas en la zona {$table->zone}."
        ]);

        return back()->with('success', "Mesa #{$table->number} creada con éxito.");
    }

    public function tablesUpdate(Request $request, Table $table)
    {
        $validated = $request->validate([
            'number' => 'required|integer|unique:tables,number,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'zone' => 'required|in:interior,terraza,privado',
            'status' => 'required|in:free,occupied,reserved,awaiting_payment'
        ]);

        $table->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_table',
            'description' => "Actualizó la mesa #{$table->number}."
        ]);

        return back()->with('success', "Mesa #{$table->number} actualizada.");
    }

    public function tablesDestroy(Table $table)
    {
        $num = $table->number;
        $table->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_table',
            'description' => "Eliminó la mesa #{$num}."
        ]);

        return back()->with('success', "Mesa #{$num} eliminada.");
    }

    public function tablesUpdateStatus(Table $table, Request $request)
    {
        $request->validate([
            'status' => 'required|in:free,occupied,reserved,awaiting_payment'
        ]);

        $oldStatus = $table->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'free') {
            $updateData['waiter_name'] = null;
        }

        $table->update($updateData);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_table_status',
            'description' => "Cambió el estado de la mesa #{$table->number} de '{$oldStatus}' a '{$newStatus}'."
        ]);

        return back()->with('success', "Mesa #{$table->number} actualizada a '{$newStatus}'.");
    }

    public function tablesAssignWaiter(Table $table, Request $request)
    {
        $request->validate([
            'waiter_name' => 'nullable|string|max:100'
        ]);

        $table->update([
            'waiter_name' => $request->waiter_name
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'assign_waiter_table',
            'description' => "Asignó el garzón '{$request->waiter_name}' a la mesa #{$table->number}."
        ]);

        return back()->with('success', "Garzón asignado a mesa #{$table->number}.");
    }

    // ==========================================
    // 14. WAITERS (GARZONES) MANAGEMENT
    // ==========================================
    public function waitersIndex()
    {
        $waiters = Waiter::orderBy('name')->paginate(15);
        return view('admin.waiters.index', compact('waiters'));
    }

    public function waitersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:waiters,name',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $waiter = Waiter::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_waiter',
            'description' => "Creó al garzón {$waiter->name}."
        ]);

        return back()->with('success', "Garzón {$waiter->name} agregado.");
    }

    public function waitersUpdate(Request $request, Waiter $waiter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:waiters,name,' . $waiter->id,
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $waiter->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_waiter',
            'description' => "Actualizó al garzón {$waiter->name}."
        ]);

        return back()->with('success', "Garzón {$waiter->name} actualizado.");
    }

    public function waitersDestroy(Waiter $waiter)
    {
        $name = $waiter->name;
        $waiter->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_waiter',
            'description' => "Eliminó al garzón {$name}."
        ]);

        return back()->with('success', "Garzón {$name} eliminado.");
    }

    // ==========================================
    // 15. RESERVATIONS (RESERVAS) MANAGEMENT
    // ==========================================
    public function reservationsIndex()
    {
        $reservations = Reservation::with('table')->orderBy('reserved_at', 'desc')->paginate(15);
        $tables = Table::orderBy('number')->get();
        return view('admin.reservations.index', compact('reservations', 'tables'));
    }

    public function reservationsStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'party_size' => 'required|integer|min:1',
            'reserved_at' => 'required|date',
            'table_id' => 'nullable|exists:tables,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // If table_id is provided, check capacity
        if (!empty($validated['table_id'])) {
            $table = Table::findOrFail($validated['table_id']);
            if ($table->capacity < $validated['party_size']) {
                return back()->withErrors(['table_id' => "La mesa #{$table->number} tiene una capacidad de {$table->capacity} personas, insuficiente para {$validated['party_size']}."])->withInput();
            }
        }

        $reservation = Reservation::create($validated);

        // If table is assigned, set table status to reserved
        if ($reservation->table_id && $reservation->status === 'pending') {
            $reservation->table->update(['status' => 'reserved']);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_reservation',
            'description' => "Creó una reserva para {$reservation->customer_name} (Pers: {$reservation->party_size}) el {$reservation->reserved_at}."
        ]);

        return back()->with('success', "Reserva de {$reservation->customer_name} agendada.");
    }

    public function reservationsUpdate(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'party_size' => 'required|integer|min:1',
            'reserved_at' => 'required|date',
            'table_id' => 'nullable|exists:tables,id',
            'notes' => 'nullable|string|max:500'
        ]);

        if (!empty($validated['table_id'])) {
            $table = Table::findOrFail($validated['table_id']);
            if ($table->capacity < $validated['party_size']) {
                return back()->withErrors(['table_id' => "La mesa #{$table->number} tiene una capacidad de {$table->capacity} personas, insuficiente para {$validated['party_size']}."])->withInput();
            }
        }

        // Handle old table status restore
        $oldTableId = $reservation->table_id;
        $newTableId = $validated['table_id'];

        if ($oldTableId !== $newTableId) {
            if ($oldTableId) {
                $oldTable = Table::find($oldTableId);
                if ($oldTable && $oldTable->status === 'reserved') {
                    $oldTable->update(['status' => 'free']);
                }
            }
            if ($newTableId && $reservation->status === 'pending') {
                $newTable = Table::find($newTableId);
                if ($newTable) {
                    $newTable->update(['status' => 'reserved']);
                }
            }
        }

        $reservation->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_reservation',
            'description' => "Actualizó la reserva de {$reservation->customer_name}."
        ]);

        return back()->with('success', "Reserva de {$reservation->customer_name} actualizada.");
    }

    public function reservationsDestroy(Reservation $reservation)
    {
        $name = $reservation->customer_name;
        
        // Release table if it was reserved
        if ($reservation->table && $reservation->table->status === 'reserved') {
            $reservation->table->update(['status' => 'free']);
        }

        $reservation->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_reservation',
            'description' => "Eliminó la reserva de {$name}."
        ]);

        return back()->with('success', "Reserva eliminada.");
    }

    public function reservationsUpdateStatus(Reservation $reservation, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $reservation->status;
        $newStatus = $request->status;

        $reservation->status = $newStatus;
        $reservation->save();

        // Release or reserve table based on status transition
        if ($reservation->table) {
            if ($newStatus === 'confirmed') {
                $reservation->table->update(['status' => 'reserved']);
            } elseif (in_array($newStatus, ['cancelled', 'completed'])) {
                if ($reservation->table->status === 'reserved') {
                    $reservation->table->update(['status' => 'free']);
                }
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_reservation_status',
            'description' => "Cambió el estado de la reserva de {$reservation->customer_name} de '{$oldStatus}' a '{$newStatus}'."
        ]);

        return back()->with('success', "Reserva actualizada a '{$newStatus}' con éxito.");
    }

    // ==========================================
    // 16. CLEANING TASKS (LIMPIEZA) MANAGEMENT
    // ==========================================
    public function cleaningTasksIndex()
    {
        $tasks = CleaningTask::with('completedBy')->orderBy('next_due_date', 'asc')->paginate(15);
        return view('admin.cleaning.index', compact('tasks'));
    }

    public function cleaningTasksStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
            'frequency' => 'required|in:daily,weekly,monthly'
        ]);

        // Default next due date is today
        $validated['next_due_date'] = date('Y-m-d');
        $task = CleaningTask::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_cleaning_task',
            'description' => "Creó la tarea de limpieza: {$task->name} ({$task->frequency})."
        ]);

        return back()->with('success', "Tarea de limpieza registrada.");
    }

    public function cleaningTasksUpdate(Request $request, CleaningTask $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
            'frequency' => 'required|in:daily,weekly,monthly'
        ]);

        $task->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_cleaning_task',
            'description' => "Actualizó la tarea de limpieza: {$task->name}."
        ]);

        return back()->with('success', "Tarea de limpieza actualizada.");
    }

    public function cleaningTasksDestroy(CleaningTask $task)
    {
        $name = $task->name;
        $task->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_cleaning_task',
            'description' => "Eliminó la tarea de limpieza: {$name}."
        ]);

        return back()->with('success', "Tarea de limpieza eliminada.");
    }

    public function cleaningTasksComplete(CleaningTask $task)
    {
        $today = date('Y-m-d');
        $days = 1;
        if ($task->frequency === 'weekly') {
            $days = 7;
        } elseif ($task->frequency === 'monthly') {
            $days = 30;
        }

        $nextDueDate = date('Y-m-d', strtotime("+{$days} days"));

        $task->update([
            'last_completed_at' => $today,
            'completed_by_id' => Auth::id(),
            'next_due_date' => $nextDueDate
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'complete_cleaning_task',
            'description' => "Completó la tarea de limpieza: {$task->name}."
        ]);

        return back()->with('success', "Tarea de limpieza marcada como completada. Próximo vencimiento: {$nextDueDate}.");
    }
}
