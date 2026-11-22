<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documento Tributario Electrónico - Folio {{ $order->document_number }}</title>
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      color: #000;
      background-color: #fff;
      padding: 20px;
      font-size: 15px;
      font-weight: 700;
      max-width: 400px;
      margin: 0 auto;
      border: 1px solid #ccc;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: 900; }
    
    .sii-header-box {
      border: 3px solid red;
      padding: 10px;
      color: red;
      font-weight: bold;
      text-align: center;
      margin-bottom: 15px;
    }
    .sii-header-box div {
      font-size: 16px;
      margin-bottom: 5px;
    }
    
    .company-details {
      margin-bottom: 15px;
      line-height: 1.4;
    }
    .divider {
      border-top: 1px dashed #000;
      margin: 10px 0;
    }
    
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    .items-table th {
      border-bottom: 1px solid #000;
      text-align: left;
      padding: 4px 0;
    }
    .items-table td {
      padding: 4px 0;
    }
    
    .totals-table {
      width: 100%;
      margin-top: 10px;
      line-height: 1.6;
    }

    .barcode-box {
      border: 2px solid red;
      padding: 10px;
      margin-top: 30px;
      text-align: center;
      color: red;
      font-size: 10px;
    }
    .barcode-img {
      width: 250px;
      height: 80px;
      background: repeating-linear-gradient(
        90deg,
        #000,
        #000 2px,
        #fff 2px,
        #fff 4px
      );
      margin: 5px auto;
    }

    @media print {
      @page {
        size: 80mm auto;
        margin: 0;
      }
      body {
        border: none;
        padding: 0;
        width: 280px;
        max-width: 280px;
        margin: 0 auto;
      }
      .barcode-img {
        width: 200px;
      }
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>

  <!-- Print Button for Operator -->
  <div class="text-center no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 6px 15px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
      Imprimir Documento (Térmica)
    </button>
  </div>

  <!-- SII Tax Stamp Box -->
  <div class="sii-header-box">
    <div>R.U.T.: 76.543.210-K</div>
    <div style="text-transform: uppercase;">
      @if($order->invoice_type === 'factura')
        FACTURA ELECTRÓNICA
      @else
        BOLETA ELECTRÓNICA
      @endif
    </div>
    <div>N° FOLIO: {{ $order->document_number ?? 'MOCK' }}</div>
    <div style="font-size: 11px; font-weight: bold; margin-top: 5px; color: #000;">S.I.I. - SANTIAGO ORIENTE</div>
  </div>

  <!-- Company Logo and Business Description -->
  <div class="company-details text-center">
    <span class="bold" style="font-size: 18px;">COMIDA AL PASO LIMITADA</span><br>
    Restaurantes y Sándwiches Tradicionales<br>
    Casa Matriz: Av. Providencia 1234, Santiago<br>
    Teléfono: +56 9 8765 4321<br>
    Sucursal: Santiago Centro, Chile
  </div>

  <div class="divider"></div>

  <!-- Date and Client details -->
  <div>
    <strong>Fecha Emisión:</strong> {{ $order->created_at->format('d-m-Y H:i') }}<br>
    <strong>Tipo Entrega:</strong> {{ strtoupper($order->type) }}<br>
    <strong>Cliente:</strong> {{ $order->client_name }}<br>
    @if($order->type === 'delivery')
      <strong>Dirección:</strong> {{ $order->address }} ({{ $order->commune->name ?? 'S/C' }})<br>
    @endif
    @if($order->invoice_type === 'factura')
      <strong>RUT Empresa:</strong> 77.111.222-3<br>
      <strong>Razón Social:</strong> Inversiones Gastronómicas S.A.<br>
      <strong>Giro:</strong> Comidas al paso y catering<br>
    @endif
  </div>

  <div class="divider"></div>

  <!-- Items list -->
  <table class="items-table">
    <thead>
      <tr>
        <th>Cant</th>
        <th>Descripción</th>
        <th class="text-right">Unit</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->orderItems as $item)
        <tr>
          <td>{{ $item->quantity }}</td>
          <td>{{ $item->product->name }}</td>
          <td class="text-right">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
          <td class="text-right">${{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
        </tr>
      @endforeach
      @if($order->delivery_fee > 0)
        <tr>
          <td>1</td>
          <td>Costo Despacho (Delivery)</td>
          <td class="text-right">${{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
          <td class="text-right">${{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
        </tr>
      @endif
    </tbody>
  </table>

  <div class="divider"></div>

  <!-- Net, IVA, and Total calculations -->
  <table class="totals-table">
    @if($order->invoice_type === 'factura')
      <tr>
        <td>Monto Neto:</td>
        <td class="text-right">${{ number_format($net, 0, ',', '.') }} CLP</td>
      </tr>
      <tr>
        <td>I.V.A. (19%):</td>
        <td class="text-right">${{ number_format($iva, 0, ',', '.') }} CLP</td>
      </tr>
    @endif
    <tr class="bold" style="font-size: 17px;">
      <td>TOTAL:</td>
      <td class="text-right">${{ number_format($order->total, 0, ',', '.') }} CLP</td>
    </tr>
  </table>

  <!-- Bottom Message -->
  <div class="text-center" style="margin-top: 15px; font-size: 11px; font-weight: bold; line-height: 1.3;">
    Ley N° 20.727: Timbre Electrónico SII<br>
    El acuse de recibo de esta guía de despacho o factura electrónica debe ser enviado electrónicamente.<br>
    ¡Gracias por su preferencia!
  </div>

  <!-- PDF417 Stamp Sim -->
  <div class="barcode-box">
    <div class="barcode-img"></div>
    <div class="bold">TIMBRE ELECTRÓNICO S.I.I.</div>
    <div>Res. N° 80 del 2014 - Verifique documento en www.sii.cl</div>
  </div>

</body>
</html>
