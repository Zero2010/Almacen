<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Region;
use App\Models\Commune;
use App\Models\CmsContent;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    private function getCms()
    {
        return CmsContent::pluck('value', 'key')->all();
    }

    public function index()
    {
        $cms = $this->getCms();
        $categories = Category::all();
        $products = Product::where('is_available', true)->take(9)->get();
        return view('index', compact('cms', 'categories', 'products'));
    }

    public function menu()
    {
        $cms = $this->getCms();
        $categories = Category::all();
        $products = Product::where('is_available', true)->get();
        return view('menu', compact('cms', 'categories', 'products'));
    }

    public function about()
    {
        $cms = $this->getCms();
        return view('about', compact('cms'));
    }

    public function book()
    {
        $cms = $this->getCms();
        $regions = Region::with('communes')->orderBy('name')->get();
        $products = Product::where('is_available', true)->get();
        $tables = \App\Models\Table::where('status', 'free')->orderBy('number')->get();
        return view('book', compact('cms', 'regions', 'products', 'tables'));
    }

    public function placeOrder(Request $request)
    {
        // Validation
        $rules = [
            'client_name' => 'required|string|max:100',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:100',
            'type' => 'required|in:delivery,pickup,in_person',
            'payment_method' => 'required|in:cash,transfer,transbank',
            'invoice_type' => 'required|in:none,boleta,factura',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500'
        ];

        if ($request->input('type') === 'delivery') {
            $rules['address'] = 'required|string|max:255';
            $rules['region_id'] = 'required|exists:regions,id';
            $rules['commune_id'] = 'required|exists:communes,id';
        } elseif ($request->input('type') === 'in_person') {
            $rules['table_id'] = 'required|exists:tables,id';
        }

        $validated = $request->validate($rules);

        // Calculate pricing
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
                'unit_price' => $price,
                'notes' => $item['notes'] ?? null
            ];
        }

        $delivery_fee = ($validated['type'] === 'delivery') ? 2000 : 0;
        $total = $subtotal + $delivery_fee;

        // Start transaction
        DB::beginTransaction();
        try {
            // Check ingredient stocks
            foreach ($itemsData as $itemData) {
                $product = Product::find($itemData['product_id']);
                foreach ($product->recipes as $recipe) {
                    $requiredQty = $recipe->quantity_required * $itemData['quantity'];
                    $ingredient = $recipe->ingredient;
                    if ($ingredient->stock < $requiredQty) {
                        return back()->withErrors(['stock' => "No hay suficiente stock de '{$ingredient->name}' para preparar '{$product->name}'. de stock actual: {$ingredient->stock} {$ingredient->unit}."])->withInput();
                    }
                }
            }

            // Create order
            $tableId = $validated['table_id'] ?? null;
            
             $order = Order::create([
                'client_name' => $validated['client_name'],
                'client_phone' => $validated['client_phone'],
                'client_email' => $validated['client_email'] ?? null,
                'type' => $validated['type'],
                'table_number' => $tableId ? \App\Models\Table::find($tableId)->number : null,
                'address' => $validated['address'] ?? null,
                'region_id'  => $validated['region_id'] ?? null,
                'commune_id' => $validated['commune_id'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $delivery_fee,
                'discount' => 0,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'invoice_type' => $validated['invoice_type'],
                'document_number' => null,
                'notes' => $request->input('notes') ?? null,
            ]);

            // Mark table as occupied if in-person order
            if ($tableId) {
                \App\Models\Table::where('id', $tableId)->update(['status' => 'occupied']);
            }

            // Save items
            foreach ($itemsData as $item) {
                $order->orderItems()->create($item);
            }

            DB::commit();
            
            return redirect()->route('pedidos.success', ['order' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al procesar el pedido. Por favor intente de nuevo: ' . $e->getMessage()])->withInput();
        }
    }

    public function orderSuccess(Order $order)
    {
        return view('order_success', compact('order'));
    }

    public function trackOrderForm()
    {
        $cms = $this->getCms();
        return view('track', compact('cms'));
    }

    public function trackOrder(Order $order)
    {
        $cms = $this->getCms();
        return view('track', compact('cms', 'order'));
    }

    public function getCommunesByRegion(Region $region)
    {
        $communes = $region->communes()->orderBy('name')->get(['id', 'name']);
        return response()->json([
            'comunas' => $communes
        ]);
    }
}
