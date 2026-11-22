@extends('layout')

@section('title', 'Seguimiento de Pedido')

@section('styles')
<style>
  .track-hero {
    background: linear-gradient(135deg, var(--primary) 0%, #f59e0b 100%);
    padding: 60px 0 40px;
    color: white;
    text-align: center;
    border-radius: 0 0 30px 30px;
    margin-bottom: 40px;
  }
  .track-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    max-width: 600px;
    margin: 0 auto;
  }
  .search-input {
    border-radius: 30px;
    padding: 15px 25px;
    border: 2px solid #e2e8f0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text);
  }
  .search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
  }
  .btn-search {
    border-radius: 30px;
    padding: 12px 30px;
    font-weight: 700;
    font-size: 1.1rem;
  }
  
  /* Progress Bar Styles */
  .tracking-progress {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin: 40px 0;
  }
  .tracking-progress::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    background: #e2e8f0;
    transform: translateY(-50%);
    z-index: 1;
  }
  .progress-step {
    position: relative;
    z-index: 2;
    text-align: center;
    background: white;
    padding: 0 10px;
  }
  .step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f8fafc;
    border: 3px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #94a3b8;
    margin: 0 auto 10px;
    transition: all 0.3s;
  }
  .step-text {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
  }
  
  /* Active Step */
  .progress-step.active .step-icon {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
  }
  .progress-step.active .step-text {
    color: var(--primary);
  }
  
  /* Completed Step */
  .progress-step.completed .step-icon {
    background: #10b981;
    border-color: #10b981;
    color: white;
  }
  .progress-step.completed .step-text {
    color: #10b981;
  }
  
  .order-details {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-top: 30px;
  }
</style>
@endsection

@section('content')
<div class="track-hero">
  <div class="container">
    <h1 style="font-weight: 800; font-size: 2.5rem;"><i class="fa fa-map-marker"></i> Sigue tu Pedido</h1>
    <p style="font-size: 1.1rem; opacity: 0.9;">Revisa en tiempo real el estado de tu comida</p>
  </div>
</div>

<div class="container mb-5">
  <div class="track-card">
    
    @if(!isset($order))
      <div class="text-center">
        <h4 style="font-weight: 700; color: var(--text); margin-bottom: 20px;">¿Cuál es tu número de pedido?</h4>
        <div class="form-group mb-4">
          <input type="number" id="orderSearchInput" class="form-control search-input" placeholder="Ej. 1024" required>
        </div>
        <button class="btn btn-primary btn-search w-100" onclick="searchOrder()">Rastrear Pedido</button>
      </div>
    @else
      <div class="text-center mb-4">
        <h3 style="font-weight: 800; color: var(--primary);">Pedido #{{ $order->id }}</h3>
        <p class="text-muted">A nombre de: <strong>{{ $order->client_name }}</strong></p>
      </div>
      
      @php
        $statusLevels = [
            'pending' => 1,
            'preparing' => 2,
            'ready' => 3,
            'delivering' => 4,
            'completed' => 4
        ];
        $currentLevel = $statusLevels[$order->status] ?? 0;
        if($order->status === 'cancelled') $currentLevel = -1;
      @endphp
      
      @if($currentLevel === -1)
        <div class="alert alert-danger text-center" style="border-radius: 12px;">
          <i class="fa fa-times-circle fa-2x mb-2"></i>
          <h5 class="m-0">Tu pedido ha sido cancelado.</h5>
        </div>
      @else
        <div class="tracking-progress">
          <div class="progress-step {{ $currentLevel >= 1 ? ($currentLevel > 1 ? 'completed' : 'active') : '' }}">
            <div class="step-icon"><i class="fa fa-clock-o"></i></div>
            <div class="step-text">Recibido</div>
          </div>
          <div class="progress-step {{ $currentLevel >= 2 ? ($currentLevel > 2 ? 'completed' : 'active') : '' }}">
            <div class="step-icon"><i class="fa fa-fire"></i></div>
            <div class="step-text">Cocinando</div>
          </div>
          <div class="progress-step {{ $currentLevel >= 3 ? ($currentLevel > 3 ? 'completed' : 'active') : '' }}">
            <div class="step-icon">
              <i class="fa {{ $order->type === 'delivery' ? 'fa-motorcycle' : 'fa-bell' }}"></i>
            </div>
            <div class="step-text">{{ $order->type === 'delivery' ? 'En Camino' : 'Listo p/Retiro' }}</div>
          </div>
          <div class="progress-step {{ $currentLevel >= 4 ? 'completed' : '' }}">
            <div class="step-icon"><i class="fa fa-check"></i></div>
            <div class="step-text">Entregado</div>
          </div>
        </div>
        
        <!-- Live Status Text -->
        <div class="text-center mt-4">
          @if($currentLevel == 1)
            <h5 style="color: var(--text); font-weight: 700;">¡Hemos recibido tu orden! <span class="spinner-grow text-warning spinner-grow-sm ml-2"></span></h5>
            <p class="text-muted">Estamos preparando la cocina para empezar a trabajar en tu comida.</p>
          @elseif($currentLevel == 2)
            <h5 style="color: var(--text); font-weight: 700;">¡Tu comida está en el fuego! <i class="fa fa-fire text-danger ml-1"></i></h5>
            <p class="text-muted">Nuestros chefs están preparando tu pedido con mucho cariño.</p>
          @elseif($currentLevel == 3)
            @if($order->type === 'delivery')
              <h5 style="color: var(--text); font-weight: 700;">¡Tu pedido va en camino! <i class="fa fa-motorcycle text-info ml-1"></i></h5>
              <p class="text-muted">Nuestro repartidor ya salió con tu comida. ¡Prepárate para disfrutar!</p>
            @else
              <h5 style="color: var(--text); font-weight: 700;">¡Tu pedido está listo! <i class="fa fa-bell text-warning ml-1"></i></h5>
              <p class="text-muted">Puedes acercarte al local a retirar tu comida.</p>
            @endif
          @elseif($currentLevel == 4)
            <h5 style="color: #10b981; font-weight: 700;">¡Pedido Completado! <i class="fa fa-check-circle ml-1"></i></h5>
            <p class="text-muted">Esperamos que hayas disfrutado tu comida. ¡Vuelve pronto!</p>
          @endif
        </div>
      @endif

      <div class="order-details">
        <h6 style="font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;"><i class="fa fa-list"></i> Detalle del Pedido</h6>
        @foreach($order->orderItems as $item)
          <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
            <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
            <span style="font-weight: 600;">${{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
          </div>
        @endforeach
        
        <div class="d-flex justify-content-between mt-3 pt-2" style="border-top: 1px solid #e2e8f0; font-size: 1.1rem; font-weight: 700; color: var(--primary);">
          <span>Total:</span>
          <span>${{ number_format($order->total, 0, ',', '.') }} CLP</span>
        </div>
      </div>
      
      <div class="text-center mt-4">
        <a href="{{ route('seguimiento.form') }}" class="btn btn-light" style="border-radius: 20px; font-weight: 600;"><i class="fa fa-arrow-left"></i> Rastrear otro pedido</a>
      </div>
    @endif
    
  </div>
</div>
@endsection

@section('scripts')
<script>
  function searchOrder() {
    const orderId = document.getElementById('orderSearchInput').value;
    if(orderId) {
      window.location.href = `/seguimiento/${orderId}`;
    }
  }
  
  // Refresh page every 30 seconds if tracking an active order
  @if(isset($order) && !in_array($order->status, ['completed', 'delivered', 'cancelled']))
    setTimeout(function() {
      window.location.reload();
    }, 30000);
  @endif
</script>
@endsection
