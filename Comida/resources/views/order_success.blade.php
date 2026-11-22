@extends('layouts.app')

@section('title', 'Pedido Recibido - Comida al paso')
@section('body_class', 'sub_page')

@section('content')
<section class="about_section layout_padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 40px; text-align: center; color: #fff;">
          <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(40, 167, 69, 0.15); border: 2px solid #28a745; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
            <i class="fa fa-check" style="font-size: 2.5rem; color: #28a745;"></i>
          </div>
          <h2 style="color: #ffbe33; font-weight: bold; margin-bottom: 10px;">¡Hemos Recibido tu Pedido!</h2>
          <p style="font-size: 1.1rem; color: #ccc;">Tu pedido ha sido registrado con éxito en nuestro sistema y está en cola para la cocina.</p>

          <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 20px; text-align: left; margin: 30px 0;">
            <h5 style="color: #ffbe33; font-weight: bold; margin-bottom: 15px;"><i class="fa fa-file-text-o"></i> Resumen de Pedido #{{ $order->id }}</h5>
            
            <div class="row mb-2">
              <div class="col-sm-4 text-muted">Cliente:</div>
              <div class="col-sm-8"><strong>{{ $order->client_name }}</strong></div>
            </div>
            <div class="row mb-2">
              <div class="col-sm-4 text-muted">Teléfono:</div>
              <div class="col-sm-8">{{ $order->client_phone }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-sm-4 text-muted">Tipo de Entrega:</div>
              <div class="col-sm-8">
                @if($order->type === 'delivery')
                  <span>Delivery (Despacho a Domicilio)</span>
                @elseif($order->type === 'pickup')
                  <span>Retiro en Local</span>
                @else
                  <span>Presencial (Mesa {{ $order->table_number }})</span>
                @endif
              </div>
            </div>
            @if($order->type === 'delivery')
              <div class="row mb-2">
                <div class="col-sm-4 text-muted">Dirección:</div>
                <div class="col-sm-8">{{ $order->address }} ({{ $order->commune->name ?? '' }})</div>
              </div>
            @endif
            <div class="row mb-2">
              <div class="col-sm-4 text-muted">Pago:</div>
              <div class="col-sm-8" style="text-transform: capitalize;">{{ $order->payment_method === 'transbank' ? 'Tarjeta (Transbank)' : ($order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo') }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-sm-4 text-muted">Documento:</div>
              <div class="col-sm-8" style="text-transform: capitalize;">{{ $order->invoice_type === 'none' ? 'Sin Documento' : ($order->invoice_type === 'boleta' ? 'Boleta Electrónica' : 'Factura Electrónica') }}</div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1);">
            
            <h6 style="color: #ffbe33; margin-bottom: 10px;">Detalle de Productos:</h6>
            @foreach($order->orderItems as $item)
              <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.95rem;">
                <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                <span>${{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }} CLP</span>
              </div>
            @endforeach
            
            @if($order->delivery_fee > 0)
              <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.95rem; color: #ccc;">
                <span>Costo de Despacho (Delivery)</span>
                <span>${{ number_format($order->delivery_fee, 0, ',', '.') }} CLP</span>
              </div>
            @endif

            <hr style="border-color: rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: bold; color: #ffbe33;">
              <span>Total:</span>
              <span>${{ number_format($order->total, 0, ',', '.') }} CLP</span>
            </div>
          </div>

          <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; align-items: center; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; padding: 20px; margin-bottom: 30px;">
    <div>
      <small style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Código de Seguimiento</small>
      <div style="font-size: 1.5rem; font-weight: 800; color: #ffbe33; letter-spacing: 1px; font-family: monospace;">{{ $order->tracking_code }}</div>
    </div>
    <div style="flex: 1; min-width: 200px;">
      <small style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Estado Actual</small>
      <div style="font-size: 1rem; font-weight: 600; color: #8cd9f0; text-transform: capitalize;">{{ str_replace('_', ' ', $order->current_stage ?? 'recibido') }}</div>
    </div>
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->client_phone) }}?text={{ urlencode('Hola! Mi código de pedido es: ' . $order->tracking_code . '. ¿En qué estado está mi pedido?') }}" target="_blank" class="btn btn-success" style="background-color: #25D366; color: #fff; border: none; font-weight: 600; border-radius: 8px; padding: 10px 20px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
      <i class="fa fa-whatsapp"></i> Consultar por WhatsApp
    </a>
  </div>

          <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('home') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">Volver al Inicio</a>
            <a href="{{ route('menu') }}" class="btn btn-warning" style="background-color: #ffbe33; color: #222831; border: none; font-weight: 600; border-radius: 8px; padding: 10px 20px;">Ver más Platos</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
