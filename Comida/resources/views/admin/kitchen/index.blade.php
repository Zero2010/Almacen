@extends('admin.layout')

@section('title', 'KDS - Pantalla Cocina')

@section('styles')
<style>
  /* KDS Fullscreen Mode Styles */
  .kds-header {
    background: var(--panel-dark);
    padding: 15px 25px;
    border-radius: 16px;
    margin-bottom: 20px;
    border: 1px solid var(--glass-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .kanban-col {
    background: #1c1f2a;
    border-radius: 16px;
    padding: 15px;
    min-height: calc(100vh - 180px);
    border: 1px solid rgba(255,255,255,0.10);
  }
  
  .ticket-card {
    background: #252836;
    border-left: 5px solid var(--primary);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.35);
    transition: transform 0.2s;
    border-top: 1px solid rgba(255,255,255,0.06);
    border-right: 1px solid rgba(255,255,255,0.06);
    border-bottom: 1px solid rgba(255,255,255,0.06);
  }
  .ticket-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.45);
  }
  
  .ticket-card.preparing { border-left-color: #3b82f6; }
  .ticket-card.ready { border-left-color: #10b981; }
  
  .ticket-item {
    font-size: 1rem;
    font-weight: 500;
    border-bottom: 1px dashed rgba(255,255,255,0.10);
    padding: 6px 0;
    color: rgba(255,255,255,0.9);
  }
  .ticket-item:last-child { border-bottom: none; }

  /* KDS Countdown & Warning Styles */
  .timer-normal { background-color: #3b82f6; color: #fff; }
  .timer-warning { background-color: #f59e0b; color: #fff; animation: kds-pulse 1.5s infinite; }
  .timer-danger { background-color: #ef4444; color: #fff; animation: kds-blink 1s infinite; }
  .kds-header-badge,
  .badge-success,
  .kds-empty,
  .kds-note,
  .kds-meta,
  .kds-item,
  .ticket-item { color: rgba(255,255,255,0.85); }
  .kds-col-title { color: rgba(255,255,255,0.95) !important; }
  .kds-meta { color: rgba(255,255,255,0.55); border-bottom-color: rgba(255,255,255,0.10) !important; }
  .kds-item-qty { color: #ffbe33; }
  .kds-note { background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35); color: #fcd34d; }
  .kds-btn { font-weight: 700; font-size: 1.05rem; padding: 10px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); }
  .kds-btn-primary { background: rgba(59, 130, 246, 0.18); color: #60a5fa; border-color: rgba(59, 130, 246, 0.35); }
  .kds-btn-primary:hover { background: rgba(59, 130, 246, 0.28); }
  .kds-btn-success { background: rgba(16, 185, 129, 0.18); color: #34d399; border-color: rgba(16, 185, 129, 0.35); }
  .kds-btn-success:hover { background: rgba(16, 185, 129, 0.28); }
  .kds-empty { color: rgba(255,255,255,0.45); }
  .kds-empty-icon { color: rgba(255,255,255,0.35); }
  
  @keyframes kds-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.03); }
    100% { transform: scale(1); }
  }
  @keyframes kds-blink {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
  }
  
  #kds-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 350px;
  }
  
  .kds-toast {
    background: #ffffff;
    border-left: 6px solid #ef4444;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.15);
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    transform: translateX(120%);
    transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  }
  .kds-toast.show {
    transform: translateX(0);
  }

  /* Collapse sidebar by default on KDS for more space */
  @media (min-width: 992px) {
    #sidebar {
      display: none !important;
    }
    .main-wrapper {
      margin-left: 0 !important;
    }
  }

  body.kds-sidebar-open #sidebar {
    display: flex !important;
  }
  body.kds-sidebar-open .main-wrapper {
    margin-left: var(--sidebar-width) !important;
  }
</style>
@endsection

 @section('content')
 <div class="kds-header">
   <div>
     <span class="badge" style="font-size: 1rem; background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">
       <i class="fa fa-clock-o"></i> {{ now()->format('H:i') }}
     </span>
   </div>
   <div style="display:flex; align-items:center; gap:10px;">
     <button type="button" id="toggle-kds-sidebar-btn" onclick="toggleKdsSidebar()" class="btn btn-action">
       <i class="fa fa-bars"></i> Mostrar Menú
     </button>
     <a href="{{ route('admin.kitchen.index') }}" class="btn btn-action"><i class="fa fa-refresh"></i> Actualizar</a>
   </div>
 </div>

<div class="row">
  <!-- Nuevas / Pendientes -->
  <div class="col-md-4">
    <div class="kanban-col">
      <h4 class="text-center mb-3 kds-col-title" style="font-weight: 800; text-transform: uppercase;">
        <i class="fa fa-bell-o"></i> Nuevas ({{ $comandas->where('status', 'pending')->count() }})
      </h4>
      @forelse($comandas->where('status', 'pending') as $cmd)
        @php
          $maxPrep = $cmd->orderItems->max(fn($i) => $i->product->estimated_prep_time ?? 15) ?? 15;
          $limitTime = $cmd->created_at->addMinutes($maxPrep);
        @endphp
        <div class="ticket-card" data-id="{{ $cmd->id }}" data-limit="{{ $limitTime->toIso8601String() }}">
          <div class="d-flex justify-content-between mb-2">
            <span style="font-weight: 800; font-size: 1.3rem; color: var(--primary);">#{{ $cmd->id }}</span>
            <div class="d-flex align-items-center gap-2">
              <span class="badge timer-badge" id="timer-{{ $cmd->id }}" style="font-size: 1rem; font-weight: 700; padding: 4px 8px;">--:--</span>
              <span class="badge kds-header-badge" style="font-size: 0.9rem;">{{ $cmd->created_at->format('H:i') }}</span>
            </div>
          </div>
          <p class="mb-2 kds-meta" style="font-size: 1rem; border-bottom: 2px solid; padding-bottom: 8px;">
            <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
          </p>
          <div class="ticket-items mt-2 mb-3">
            @foreach($cmd->orderItems as $item)
              <div class="ticket-item">
                <span class="kds-item-qty" style="font-weight: 800;">{{ $item->quantity }}x</span> {{ $item->product->name }}
              </div>
            @endforeach
          </div>
          @if($cmd->notes)
            <div class="alert kds-note p-2" style="font-size: 0.95rem; margin-bottom: 15px; font-weight: 600;">
              <i class="fa fa-exclamation-triangle"></i> {{ $cmd->notes }}
            </div>
          @endif
          <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
            @csrf
            <input type="hidden" name="status" value="preparing">
            <button type="submit" class="btn btn-block kds-btn kds-btn-primary">
              Preparar <i class="fa fa-fire ml-1"></i>
            </button>
          </form>
        </div>
      @empty
        <p class="kds-empty text-center mt-5"><i class="fa fa-check-circle kds-empty-icon" style="font-size: 2rem;"></i><br>No hay comandas nuevas</p>
      @endforelse
    </div>
  </div>

  <!-- En Preparación -->
  <div class="col-md-4">
    <div class="kanban-col">
      <h4 class="text-center mb-3 kds-col-title" style="font-weight: 800; text-transform: uppercase; color: #60a5fa;">
        <i class="fa fa-fire"></i> Cocinando ({{ $comandas->where('status', 'preparing')->count() }})
      </h4>
      @forelse($comandas->where('status', 'preparing') as $cmd)
        @php
          $maxPrep = $cmd->orderItems->max(fn($i) => $i->product->estimated_prep_time ?? 15) ?? 15;
          $limitTime = $cmd->created_at->addMinutes($maxPrep);
        @endphp
        <div class="ticket-card preparing" data-id="{{ $cmd->id }}" data-limit="{{ $limitTime->toIso8601String() }}">
          <div class="d-flex justify-content-between mb-2">
            <span style="font-weight: 800; font-size: 1.3rem; color: #2563eb;">#{{ $cmd->id }}</span>
            <div class="d-flex align-items-center gap-2">
              <span class="badge timer-badge" id="timer-{{ $cmd->id }}" style="font-size: 1rem; font-weight: 700; padding: 4px 8px;">--:--</span>
              <span class="badge badge-info" style="font-size: 0.9rem;"><i class="fa fa-clock-o"></i> {{ $cmd->updated_at->diffForHumans() }}</span>
            </div>
          </div>
          <p class="mb-2 kds-meta" style="font-size: 1rem; border-bottom: 2px solid; padding-bottom: 8px;">
            <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
          </p>
          <div class="ticket-items mt-2 mb-3">
            @foreach($cmd->orderItems as $item)
              <div class="ticket-item">
                <span class="kds-item-qty" style="font-weight: 800;">{{ $item->quantity }}x</span> {{ $item->product->name }}
              </div>
            @endforeach
          </div>
          @if($cmd->notes)
            <div class="alert alert-warning p-2" style="font-size: 0.95rem; margin-bottom: 15px; font-weight: 600;">
              <i class="fa fa-exclamation-triangle"></i> {{ $cmd->notes }}
            </div>
          @endif
          <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
            @csrf
            <input type="hidden" name="status" value="ready">
            <button type="submit" class="btn btn-block kds-btn kds-btn-success">
              Marcar Listo <i class="fa fa-check ml-1"></i>
            </button>
          </form>
        </div>
      @empty
        <p class="text-muted text-center mt-5"><i class="fa fa-coffee" style="font-size: 2rem; color: #cbd5e1;"></i><br>Cocina despejada</p>
      @endforelse
    </div>
  </div>

  <!-- Listas -->
  <div class="col-md-4">
    <div class="kanban-col">
       <h4 class="text-center mb-3" style="font-weight: 800; color: #34d399; text-transform: uppercase;">
        <i class="fa fa-check-circle"></i> Despacho ({{ $comandas->where('status', 'ready')->count() }})
      </h4>
      @forelse($comandas->where('status', 'ready') as $cmd)
        @php
          $maxPrep = $cmd->orderItems->max(fn($i) => $i->product->estimated_prep_time ?? 15) ?? 15;
          $limitTime = $cmd->created_at->addMinutes($maxPrep);
        @endphp
        <div class="ticket-card ready" data-id="{{ $cmd->id }}" data-limit="{{ $limitTime->toIso8601String() }}">
          <div class="d-flex justify-content-between mb-2">
            <span style="font-weight: 800; font-size: 1.3rem; color: #16a34a;">#{{ $cmd->id }}</span>
            <div class="d-flex align-items-center gap-2">
              <span class="badge timer-badge" id="timer-{{ $cmd->id }}" style="font-size: 1rem; font-weight: 700; padding: 4px 8px;">--:--</span>
              <span class="badge badge-success" style="font-size: 0.9rem;">Terminado</span>
            </div>
          </div>
          <p class="mb-2" style="font-size: 1rem; color: #64748b; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">
            <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
          </p>
          <div class="ticket-items mt-2 mb-3 text-muted">
            <i class="fa fa-info-circle"></i> Platos listos para entregar.
          </div>
          <div class="mt-3">
            @if($cmd->type === 'in_person')
              <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="completed">
                <button type="submit" class="btn btn-block kds-btn kds-btn-success">Entregado a Mesa</button>
              </form>
            @else
              <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="delivering">
                <button type="submit" class="btn btn-block kds-btn kds-btn-primary">Despachar a Repartidor</button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <p class="text-muted text-center mt-5"><i class="fa fa-inbox" style="font-size: 2rem; color: #cbd5e1;"></i><br>Sin despachos pendientes</p>
      @endforelse
    </div>
  </div>
</div>

@section('scripts')
<script>
  function toggleKdsSidebar() {
    const sidebar = document.getElementById('sidebar');
    const btn = document.getElementById('toggle-kds-sidebar-btn');
    if (window.innerWidth < 992) {
      sidebar.classList.toggle('open');
      if (sidebar.classList.contains('open')) {
        btn.innerHTML = '<i class="fa fa-bars"></i> Ocultar Menú';
      } else {
        btn.innerHTML = '<i class="fa fa-bars"></i> Mostrar Menú';
      }
    } else {
      document.body.classList.toggle('kds-sidebar-open');
      if (document.body.classList.contains('kds-sidebar-open')) {
        btn.innerHTML = '<i class="fa fa-bars"></i> Ocultar Menú';
      } else {
        btn.innerHTML = '<i class="fa fa-bars"></i> Mostrar Menú';
      }
    }
  }

  // Auto refresh every 30 seconds for KDS
  setTimeout(function() {
    window.location.reload();
  }, 10000);

  document.addEventListener('DOMContentLoaded', function() {
    // Create alert container if it doesn't exist
    let container = document.getElementById('kds-alert-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'kds-alert-container';
      document.body.appendChild(container);
    }

    // Keep track of alerted order IDs to avoid repeating alarms upon page refreshes
    let alertedOrders = JSON.parse(sessionStorage.getItem('kds_alerted_orders') || '[]');

    function playAlertSound(orderId) {
      try {
        if ('speechSynthesis' in window) {
          const text = `Atención. El pedido número ${orderId} debe ser entregado en menos de tres minutos.`;
          const utterance = new SpeechSynthesisUtterance(text);
          utterance.lang = 'es-ES';
          utterance.rate = 0.9;
          window.speechSynthesis.speak(utterance);
        }
      } catch (e) {
        console.error("Vocal synthesis error:", e);
      }
    }

    function showVisualAlert(orderId, typeText) {
      const toast = document.createElement('div');
      toast.className = 'kds-toast';
      toast.innerHTML = `
        <div style="font-size: 1.8rem; color: #ef4444;"><i class="fa fa-exclamation-triangle"></i></div>
        <div style="flex: 1;">
          <h6 style="margin: 0; font-weight: 800; color: #ef4444; font-size: 0.95rem; text-transform: uppercase;">¡ENTREGAR PEDIDO!</h6>
          <p style="margin: 2px 0 0; font-size: 0.85rem; color: #475569; line-height: 1.3;">
            El pedido <strong>#${orderId}</strong> (${typeText}) está a menos de 3 minutos y debe ser entregado.
          </p>
        </div>
        <button type="button" class="close" onclick="this.parentElement.remove()" style="font-size: 1.25rem; outline: none; border: none; background: transparent; padding: 0 5px;">&times;</button>
      `;
      container.appendChild(toast);
      
      // Slide in animation
      setTimeout(() => toast.classList.add('show'), 50);
      
      // Auto remove after 10 seconds
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 10000);
    }

    function updateTimers() {
      const now = new Date();
      let updatedAlerted = [...alertedOrders];

      document.querySelectorAll('.ticket-card[data-limit]').forEach(card => {
        const limitStr = card.getAttribute('data-limit');
        const orderId = card.getAttribute('data-id');
        const timerBadge = document.getElementById(`timer-${orderId}`);
        if (!timerBadge) return;

        const limitDate = new Date(limitStr);
        const timeDiff = limitDate - now;

        const typeNode = card.querySelector('p strong');
        const typeText = typeNode ? typeNode.innerText.trim() : 'Pedido';

        if (timeDiff <= 0) {
          timerBadge.innerHTML = '<i class="fa fa-exclamation-circle"></i> ATRASADO';
          timerBadge.className = 'badge timer-danger';
          card.style.borderLeftColor = '#ef4444';
          
          if (!alertedOrders.includes(orderId)) {
            playAlertSound(orderId);
            showVisualAlert(orderId, typeText);
            updatedAlerted.push(orderId);
          }
        } else {
          const totalSeconds = Math.floor(timeDiff / 1000);
          const minutes = Math.floor(totalSeconds / 60);
          const seconds = totalSeconds % 60;
          const displaySecs = seconds < 10 ? '0' + seconds : seconds;
          
          timerBadge.innerText = `${minutes}:${displaySecs}`;

          if (minutes < 3) {
            timerBadge.className = 'badge timer-danger';
            card.style.borderLeftColor = '#f97316'; // Warning orange
            
            if (!alertedOrders.includes(orderId)) {
              playAlertSound(orderId);
              showVisualAlert(orderId, typeText);
              updatedAlerted.push(orderId);
            }
          } else if (minutes < 5) {
            timerBadge.className = 'badge timer-warning';
            card.style.borderLeftColor = '#f59e0b'; // Soft warning
          } else {
            timerBadge.className = 'badge timer-normal';
          }
        }
      });

      if (updatedAlerted.length !== alertedOrders.length) {
        alertedOrders = updatedAlerted;
        sessionStorage.setItem('kds_alerted_orders', JSON.stringify(alertedOrders));
      }
    }

    updateTimers();
    setInterval(updateTimers, 1000);
  });
</script>
@endsection
