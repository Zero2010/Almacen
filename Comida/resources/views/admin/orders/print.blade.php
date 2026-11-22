<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Comanda y Boleta - Pedido #{{ $order->id }}</title>
    <style>
        /* Thermal Printer Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace; /* Monospace is best for thermal */
            font-size: 18px; /* Increased from 14px to make everything larger */
            font-weight: 900; /* Force extreme bold on everything */
            color: #000;
            background: #e2e8f0; /* Browser background */
        }
        
        .print-container {
            width: 380px; /* standard 80mm thermal paper width scaled up visually */
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-2 { margin-top: 10px; }
        
        .divider {
            border-top: 2px dashed #000; /* Thicker divider */
            margin: 12px 0;
        }

        /* Kitchen Ticket Specific */
        .ticket-title { font-size: 1.5em; font-weight: bold; }
        .ticket-order-no { font-size: 2.8em; font-weight: 900; margin: 10px 0; border: 4px solid #000; padding: 5px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 1.3em; font-weight: 900; }
        .item-qty { font-size: 1.4em; margin-right: 10px; }

        /* Boleta Specific */
        .boleta-title { font-size: 1.2em; font-weight: bold; }
        .boleta-header { font-size: 0.9em; margin-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { text-align: left; padding: 3px 0; font-size: 0.9em;}
        th.right, td.right { text-align: right; }
        
        .totals-row { display: flex; justify-content: space-between; font-size: 1.1em; font-weight: bold; margin-top: 5px; }

        /* Hide buttons when printing */
        .no-print {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            padding: 10px 20px; font-size: 16px; cursor: pointer;
            background: #10b981; color: #fff; border: none; border-radius: 5px;
            text-decoration: none; display: inline-block; margin: 5px;
            font-family: sans-serif;
            font-weight: bold;
        }
        .btn-secondary { background: #64748b; }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto; /* XPrinter 80mm support */
            }
            body { 
                background: #fff; 
                width: 280px;
                max-width: 280px;
                margin: 0; 
                padding: 0; 
            }
            .print-container { 
                margin: 0 auto; 
                padding: 2px;
                box-shadow: none; 
                width: 280px; 
            }
            .no-print { display: none; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Re-Imprimir</button>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-secondary">🔙 Volver al POS</a>
        <a href="{{ route('admin.orders.list') }}" class="btn btn-secondary">📋 Ver Pedidos</a>
    </div>

    @if($doc == 'comanda' || $doc == 'all')
    <!-- TICKET 1: COMANDA (KITCHEN) -->
    <div class="print-container {{ $doc == 'all' ? 'page-break' : '' }}">
        <div class="text-center mb-2">
            <div class="ticket-title">COMANDA COCINA</div>
            <div class="ticket-order-no">#{{ $order->id }}</div>
            
            <div class="bold" style="font-size: 1.2em;">
                @if($order->type == 'delivery')
                    🛵 DELIVERY
                @elseif($order->type == 'pickup')
                    🛍️ RETIRO
                @else
                    🍽️ MESA {{ $order->table_number ?? 'S/N' }}
                @endif
            </div>
            
            @if($order->type == 'delivery')
            <div style="font-size: 0.9em; margin-top: 5px; text-align: left; border: 1px dashed #000; padding: 5px;">
                <div><span class="bold">Dir:</span> {{ $order->address }}</div>
                <div><span class="bold">Comuna:</span> {{ $order->commune ? $order->commune->name : '' }}</div>
                @if($order->commune && $order->commune->region)
                <div><span class="bold">Región:</span> {{ $order->commune->region->name }}</div>
                @endif
                <div><span class="bold">Repartidor:</span> {{ $order->waiter_name ?? 'No asignado' }}</div>
                <div><span class="bold">V. Entrega:</span> ${{ number_format($order->delivery_fee, 0, ',', '.') }}</div>
            </div>
            @endif
            
            <div class="mt-2">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="divider"></div>
        
        <div>
            @foreach($order->orderItems as $item)
                <div class="item-row">
                    <div>
                        <span class="item-qty">[{{ $item->quantity }}]</span>
                        <span>{{ $item->product->name }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($order->notes)
            <div class="divider"></div>
            <div class="bold">NOTAS:</div>
            <div style="font-size: 1.1em; border: 1px solid #000; padding: 5px; margin-top: 5px;">
                {{ $order->notes }}
            </div>
        @endif
        
        <div class="divider"></div>
        <div class="text-center" style="font-size: 0.9em;">-- Fin de Comanda --</div>
    </div>
    @endif

    @if($doc == 'boleta' || $doc == 'all')
    <!-- TICKET 2: BOLETA (CUSTOMER RECEIPT) -->
    <div class="print-container" style="font-weight: 700;">
        <div class="text-center boleta-header">
            <div class="boleta-title" style="font-size: 1.4em; font-weight: 900; margin-bottom: 5px;">
                {{ strtoupper($order->invoice_type == 'factura' ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA') }}
            </div>
            <div>COMIDA AL PASO LIMITADA</div>
            <div>RUT: 76.543.210-K</div>
            <div>Av. Providencia 1234, Santiago</div>
            <div>Giro: Restaurante</div>
            
            <div class="mt-2" style="font-size: 1.1em; font-weight: 900;">Ticket #{{ $order->id }}</div>
            <div>Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="divider"></div>

        <div style="font-size: 0.9em; margin-bottom: 5px;">
            <div><span class="bold">Cliente:</span> {{ $order->client_name ?? 'Cliente Genérico' }}</div>
            @if($order->type == 'delivery')
                <div><span class="bold">Dir:</span> {{ $order->address }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th>Cant</th>
                    <th>Artículo</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="right">${{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        @php
            $iva = $order->total - round($order->total / 1.19);
            $neto = $order->total - $iva;
        @endphp

        <div class="totals-row" style="font-size: 1em;">
            <span>Neto:</span>
            <span>${{ number_format($neto, 0, ',', '.') }}</span>
        </div>
        <div class="totals-row" style="font-size: 1em;">
            <span>IVA (19%):</span>
            <span>${{ number_format($iva, 0, ',', '.') }}</span>
        </div>
        <div class="totals-row" style="font-size: 1.4em; font-weight: 900; margin-top: 10px; padding-top: 5px; border-top: 1px solid #000;">
            <span>TOTAL:</span>
            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="text-center" style="font-size: 0.9em; margin-top: 10px;">
            <div>Pago: {{ strtoupper($order->payment_method) }}</div>
            <div class="mt-2 mb-2" style="font-weight: 900; font-size: 1.1em;">¡Gracias por su preferencia!</div>
            
            <!-- Firma Electrónica SII -->
            <div style="border: 2px solid #000; padding: 10px 5px; margin-top: 15px;">
                <!-- Simulated PDF417 SVG for better print compatibility -->
                <svg width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 5px;">
                    <rect x="0" y="0" width="120" height="40" fill="#fff"/>
                    <path d="M5,5 h2 v30 h-2 z M9,5 h4 v30 h-4 z M15,5 h1 v30 h-1 z M18,5 h3 v30 h-3 z M23,5 h2 v30 h-2 z M27,5 h1 v30 h-1 z M30,5 h4 v30 h-4 z M36,5 h2 v30 h-2 z M40,5 h1 v30 h-1 z M43,5 h3 v30 h-3 z M48,5 h1 v30 h-1 z M51,5 h4 v30 h-4 z M57,5 h2 v30 h-2 z M61,5 h1 v30 h-1 z M64,5 h3 v30 h-3 z M69,5 h2 v30 h-2 z M73,5 h1 v30 h-1 z M76,5 h4 v30 h-4 z M82,5 h2 v30 h-2 z M86,5 h1 v30 h-1 z M89,5 h3 v30 h-3 z M94,5 h1 v30 h-1 z M97,5 h4 v30 h-4 z M103,5 h2 v30 h-2 z M107,5 h1 v30 h-1 z M110,5 h3 v30 h-3 z M115,5 h2 v30 h-2 z" fill="#000"/>
                </svg>
                <div style="font-size: 0.9em; font-weight: 900; letter-spacing: 1px;">TIMBRE ELECTRÓNICO SII</div>
                <div style="font-size: 0.75em; margin-top: 2px;">Res. 80 de 2014 - Verifique en sii.cl</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
