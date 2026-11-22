@extends('admin.layout')

@section('title', 'Pedidos y Cocina')

@section('styles')
<style>
  .badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }
  .status-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
  .status-preparing { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
  .status-ready { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
  .status-delivering { background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); }
  .status-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
  .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .order-card {
    background: #ffffff;
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  }
  
  /* Kanban Styles */
  .kanban-col {
    background: #f8fafc;
    border-radius: 16px;
    padding: 15px;
    min-height: 500px;
  }
  .ticket-card {
    background: #fff;
    border-left: 4px solid var(--primary);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
  }
  .ticket-card.preparing { border-left-color: #3b82f6; }
  .ticket-card.ready { border-left-color: #10b981; }
  .ticket-item {
    font-size: 0.9rem;
    font-weight: 500;
    border-bottom: 1px dashed #e2e8f0;
    padding: 4px 0;
  }
  .ticket-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-shopping-basket" style="color: var(--primary);"></i> Control de Pedidos y Cocina</h1>
  @if(Auth::user()->hasRole(['admin', 'waiter']))
    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary-glass">
      <i class="fa fa-plus"></i> Nueva Venta
    </a>
  @endif
</div>

<ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist" style="border-bottom: 2px solid var(--glass-border);">
  <li class="nav-item">
    <a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab" style="color: var(--text); font-weight: 600;"><i class="fa fa-list"></i> Lista de Pedidos</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="kanban-tab" data-toggle="tab" href="#kanban" role="tab" style="color: var(--text); font-weight: 600;"><i class="fa fa-cutlery"></i> Tablero Cocina (Comandas)</a>
  </li>
</ul>

<div class="tab-content" id="orderTabsContent">
  <div class="tab-pane fade show active" id="list" role="tabpanel">

    <!-- Search and Filter Bar -->
    <div class="glass-panel mb-4">
      <form action="{{ route('admin.orders.list') }}" method="GET">
        <div class="row align-items-center">
          <div class="col-md-4 mb-2 mb-md-0">
            <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre, Teléfono o ID..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <select name="status" class="form-control">
              <option value="">Todos los Estados</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
              <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparando (En Cocina)</option>
              <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Listo (Despachado Cocina)</option>
              <option value="delivering" {{ request('status') === 'delivering' ? 'selected' : '' }}>En Reparto</option>
              <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Entregado</option>
              <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
          </div>
          <div class="col-md-3 mb-2 mb-md-0">
            <select name="type" class="form-control">
              <option value="">Todas las Entregas</option>
              <option value="delivery" {{ request('type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
              <option value="pickup" {{ request('type') === 'pickup' ? 'selected' : '' }}>Retiro</option>
              <option value="in_person" {{ request('type') === 'in_person' ? 'selected' : '' }}>Presencial (Mesa)</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-secondary-glass btn-block">
              <i class="fa fa-filter"></i> Filtrar
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Orders Table -->
    <div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente / Contacto</th>
          <th>Detalle de Compra</th>
          <th>Tipo / Entrega</th>
          <th>Monto Total</th>
          <th>Documento</th>
          <th>Estado del Pedido</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>
              <span class="font-weight-bold" style="color: var(--primary);">#{{ $order->id }}</span>
              <br>
              <small class="text-muted">{{ $order->created_at->format('d/m H:i') }}</small>
            </td>
            <td>
              <strong>{{ $order->client_name }}</strong>
              <br>
              <small><i class="fa fa-phone"></i> {{ $order->client_phone }}</small>
              @if($order->client_email)
                <br><small><i class="fa fa-envelope-o"></i> {{ $order->client_email }}</small>
              @endif
            </td>
            <td>
              <ul style="padding-left: 15px; margin: 0; font-size: 0.85rem; max-width: 250px;">
                @foreach($order->orderItems as $item)
                  <li>{{ $item->quantity }}x {{ $item->product->name }} <span class="text-muted">(${{ number_format($item->unit_price, 0, ',', '.') }})</span></li>
                @endforeach
              </ul>
              @if($order->notes)
                <div class="text-warning mt-1" style="font-size: 0.8rem;">
                  <i class="fa fa-sticky-note-o"></i> {{ $order->notes }}
                </div>
              @endif
            </td>
            <td>
              <span class="text-dark" style="font-weight: 500;">
                @if($order->type === 'delivery')
                  <i class="fa fa-motorcycle text-info"></i> Delivery
                  <br>
                  <small class="text-muted" style="display: block; max-width: 150px; line-height: 1.2;">{{ $order->address }} ({{ $order->commune->name ?? 'S/C' }})</small>
                @elseif($order->type === 'pickup')
                  <i class="fa fa-shopping-bag text-warning"></i> Retiro en Local
                @else
                  <i class="fa fa-cutlery text-success"></i> En Mesa (Mesa {{ $order->table_number }})
                  @if($order->waiter_name)
                    <br><small class="text-muted" style="display: block; line-height: 1.2;"><i class="fa fa-user-o"></i> Atiende: {{ $order->waiter_name }}</small>
                  @endif
                  @if($order->party_size)
                    <small class="text-muted" style="display: block; line-height: 1.2;"><i class="fa fa-users"></i> Comensales: {{ $order->party_size }}</small>
                  @endif
                @endif
              </span>
            </td>
            <td>
              <span class="font-weight-bold" style="color: var(--primary);">${{ number_format($order->total, 0, ',', '.') }}</span>
              <br>
              <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem;">{{ $order->payment_method === 'transbank' ? 'Tarjeta' : ($order->payment_method === 'transfer' ? 'Transf.' : 'Efectivo') }}</small>
            </td>
            <td>
              @if($order->invoice_type === 'boleta')
                <span class="badge badge-info" style="font-size: 0.75rem;">Boleta</span>
              @elseif($order->invoice_type === 'factura')
                <span class="badge badge-primary" style="font-size: 0.75rem;">Factura</span>
              @else
                <span class="badge badge-secondary" style="font-size: 0.75rem;">Sin Doc.</span>
              @endif
              
              @if($order->document_number)
                <br>
                <a href="{{ route('admin.orders.documento', $order->id) }}" target="_blank" class="text-success" style="font-size: 0.8rem; font-weight: 600;">
                  <i class="fa fa-file-pdf-o"></i> Folio #{{ $order->document_number }}
                </a>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="m-0">
                @csrf
                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="padding: 4px 8px !important; width: 140px; font-size: 0.85rem; font-weight: 600;">
                  <option value="pending" class="text-warning" {{ $order->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                  <option value="preparing" class="text-info" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparando</option>
                  <option value="ready" class="text-success" {{ $order->status === 'ready' ? 'selected' : '' }}>Listo (Cocina)</option>
                  <option value="delivering" class="text-primary" {{ $order->status === 'delivering' ? 'selected' : '' }}>En Reparto</option>
                  <option value="completed" class="text-success" {{ $order->status === 'completed' ? 'selected' : '' }}>Entregado</option>
                  <option value="cancelled" class="text-danger" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
              </form>
            </td>
            <td>
              @if(Auth::user()->hasRole(['admin', 'waiter']))
                <button type="button" class="btn btn-action" onclick="openAssignModal({{ $order->id }})" title="Asignar Personal / Tarea">
                  <i class="fa fa-user-plus"></i> Asignar
                </button>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
              No se encontraron pedidos con los filtros actuales.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="d-flex justify-content-center mt-3">
    {{ $orders->appends(request()->input())->links() }}
  </div>
</div>
  </div> <!-- End List Tab -->

  <div class="tab-pane fade" id="kanban" role="tabpanel">
    <div class="row">
      <!-- Nuevas / Pendientes -->
      <div class="col-md-4">
        <div class="kanban-col">
          <h5 class="text-center mb-3" style="font-weight: 700; color: #b45309;">
            <i class="fa fa-bell-o"></i> Nuevas ({{ $comandas->where('status', 'pending')->count() }})
          </h5>
          @foreach($comandas->where('status', 'pending') as $cmd)
            <div class="ticket-card">
              <div class="d-flex justify-content-between mb-2">
                <span style="font-weight: 800; font-size: 1.1rem; color: var(--primary);">#{{ $cmd->id }}</span>
                <span class="badge badge-warning">{{ $cmd->created_at->format('H:i') }}</span>
              </div>
              <p class="mb-1" style="font-size: 0.85rem; color: #64748b;">
                <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
              </p>
              <div class="ticket-items mt-2 mb-3">
                @foreach($cmd->orderItems as $item)
                  <div class="ticket-item">
                    <span style="font-weight: 700;">{{ $item->quantity }}x</span> {{ $item->product->name }}
                  </div>
                @endforeach
              </div>
              @if($cmd->notes)
                <div class="alert alert-warning p-2" style="font-size: 0.8rem; margin-bottom: 10px;">
                  <i class="fa fa-exclamation-triangle"></i> {{ $cmd->notes }}
                </div>
              @endif
              <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="preparing">
                <button type="submit" class="btn btn-action btn-block" style="background: #eff6ff; color: #2563eb; font-weight: 600;">
                  Comenzar a Preparar <i class="fa fa-arrow-right"></i>
                </button>
              </form>
            </div>
          @endforeach
        </div>
      </div>

      <!-- En Preparación -->
      <div class="col-md-4">
        <div class="kanban-col">
          <h5 class="text-center mb-3" style="font-weight: 700; color: #1d4ed8;">
            <i class="fa fa-fire"></i> Cocinando ({{ $comandas->where('status', 'preparing')->count() }})
          </h5>
          @foreach($comandas->where('status', 'preparing') as $cmd)
            <div class="ticket-card preparing">
              <div class="d-flex justify-content-between mb-2">
                <span style="font-weight: 800; font-size: 1.1rem; color: #2563eb;">#{{ $cmd->id }}</span>
                <span class="badge badge-info">{{ $cmd->updated_at->diffForHumans() }}</span>
              </div>
              <p class="mb-1" style="font-size: 0.85rem; color: #64748b;">
                <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
              </p>
              <div class="ticket-items mt-2 mb-3">
                @foreach($cmd->orderItems as $item)
                  <div class="ticket-item">
                    <span style="font-weight: 700;">{{ $item->quantity }}x</span> {{ $item->product->name }}
                  </div>
                @endforeach
              </div>
              @if($cmd->notes)
                <div class="alert alert-warning p-2" style="font-size: 0.8rem; margin-bottom: 10px;">
                  <i class="fa fa-exclamation-triangle"></i> {{ $cmd->notes }}
                </div>
              @endif
              <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="btn btn-action btn-block" style="background: #f0fdf4; color: #16a34a; font-weight: 600;">
                  Marcar Listo <i class="fa fa-check"></i>
                </button>
              </form>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Listas -->
      <div class="col-md-4">
        <div class="kanban-col">
          <h5 class="text-center mb-3" style="font-weight: 700; color: #15803d;">
            <i class="fa fa-check-circle"></i> Listas p/Despacho ({{ $comandas->where('status', 'ready')->count() }})
          </h5>
          @foreach($comandas->where('status', 'ready') as $cmd)
            <div class="ticket-card ready">
              <div class="d-flex justify-content-between mb-2">
                <span style="font-weight: 800; font-size: 1.1rem; color: #16a34a;">#{{ $cmd->id }}</span>
                <span class="badge badge-success">Listo</span>
              </div>
              <p class="mb-1" style="font-size: 0.85rem; color: #64748b;">
                <strong>{{ $cmd->type === 'in_person' ? 'En Mesa ('.$cmd->table_number.')' : ($cmd->type === 'delivery' ? 'Delivery' : 'Para Llevar') }}</strong>
              </p>
              <div class="mt-3 text-center">
                @if($cmd->type === 'in_person')
                  <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-action btn-block" style="background: #f8fafc; color: #475569; font-weight: 600;">Entregado a Mesa</button>
                  </form>
                @else
                  <form action="{{ route('admin.orders.updateStatus', $cmd->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="status" value="delivering">
                    <button type="submit" class="btn btn-action btn-block" style="background: #fdf4ff; color: #c026d3; font-weight: 600;">Despachar a Repartidor</button>
                  </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div> <!-- End Kanban Tab -->
</div>


<!-- Assign Employee Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Asignar Tarea de Pedido</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="assignForm">
        @csrf
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="employee_id">Personal Operativo *</label>
            <select name="employee_id" id="employee_id" class="form-control" required>
              <option value="">Seleccione empleado...</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ strtoupper($emp->role) }})</option>
              @endforeach
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="description">Instrucción / Tarea *</label>
            <input type="text" name="description" id="description" class="form-control" placeholder="Ej. Preparar Sándwich Barros Luco o Llevar despacho a Providencia." required>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Asignar Tarea</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openAssignModal(orderId) {
    const form = document.getElementById('assignForm');
    form.action = `/intranet/pedidos/${orderId}/asignar`;
    
    document.getElementById('modalTitle').innerText = `Asignar Tarea para Pedido #${orderId}`;
    $('#assignModal').modal('show');
  }

  setInterval(function() {
    window.location.reload();
  }, 10000);
</script>
@endsection
