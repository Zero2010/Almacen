@extends('admin.layout')

@section('title', 'Control de Mesas')

@section('styles')
<style>
  .table-card {
    background: var(--panel-dark);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
  }
  .table-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
  }
  
  .table-free { border-top: 5px solid var(--success); }
  .table-occupied { border-top: 5px solid var(--danger); }
  .table-reserved { border-top: 5px solid var(--warning); }
  .table-awaiting_payment { border-top: 5px solid var(--secondary); }

  .zone-title {
    color: var(--text);
    font-weight: 700;
    margin-top: 25px;
    margin-bottom: 15px;
    border-bottom: 2px solid var(--glass-border);
    padding-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-table" style="color: var(--primary);"></i> Control y Distribución de Mesas</h1>
  @if(Auth::user()->isAdmin())
    <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()"><i class="fa fa-plus"></i> Nueva Mesa</button>
  @endif
</div>

<!-- Grid of Tables by Zone -->
@php
  $zones = ['interior' => 'Salón Interior', 'terraza' => 'Terraza Exterior', 'privado' => 'Salón Privado'];
@endphp

@foreach($zones as $zoneKey => $zoneName)
  <h3 class="zone-title">
    <i class="fa @if($zoneKey === 'interior') fa-home @elseif($zoneKey === 'terraza') fa-sun-o @else fa-lock @endif" style="color: var(--text-muted);"></i>
    {{ $zoneName }} ({{ $tables->where('zone', $zoneKey)->count() }} Mesas)
  </h3>
  <div class="row">
    @forelse($tables->where('zone', $zoneKey) as $tbl)
      <div class="col-md-3 col-sm-6">
        <div class="table-card table-{{ $tbl->status }}">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h4 class="font-weight-bold m-0" style="font-size: 1.35rem; color: var(--text);">Mesa #{{ $tbl->number }}</h4>
            <span class="badge-role @if($tbl->status === 'free') badge-chef @elseif($tbl->status === 'occupied') badge-admin @elseif($tbl->status === 'reserved') badge-waiter @else badge-delivery @endif" style="font-size: 0.75rem; text-transform: uppercase;">
              @if($tbl->status === 'free') Libre
              @elseif($tbl->status === 'occupied') Ocupada
              @elseif($tbl->status === 'reserved') Reservada
              @else Esperando Pago
              @endif
            </span>
          </div>
          
          <p class="text-muted mb-2" style="font-size: 0.9rem;">
            <i class="fa fa-users"></i> Capacidad: <strong>{{ $tbl->capacity }} personas</strong>
          </p>

          <div class="mb-3" style="min-height: 40px;">
            @if($tbl->waiter_name)
              <span class="text-muted" style="font-size: 0.85rem; display: block; line-height: 1.3;">
                <i class="fa fa-user-circle"></i> Garzón: <strong style="color: var(--text);">{{ $tbl->waiter_name }}</strong>
              </span>
            @else
              <span class="text-muted" style="font-size: 0.85rem; font-style: italic;">Sin garzón asignado</span>
            @endif
          </div>

          <!-- Quick Actions depending on Status -->
          <div class="d-flex gap-2 mt-2">
            <button type="button" class="btn btn-block" onclick="openStatusModal({{ $tbl->id }}, '{{ $tbl->status }}')" style="font-size: 0.85rem; padding: 8px 12px; background-color: #f1f5f9; color: #475569; border: none; border-radius: 20px; font-weight: 600; transition: all 0.2s; margin-top: 0;">
              <i class="fa fa-refresh mr-1"></i> Estado
            </button>
            <button type="button" class="btn btn-block mt-0" onclick="openAssignModal({{ $tbl->id }}, '{{ $tbl->waiter_name }}')" style="font-size: 0.85rem; padding: 8px 12px; background-color: #fff7ed; color: var(--primary-dark); border: none; border-radius: 20px; font-weight: 600; transition: all 0.2s;">
              <i class="fa fa-user-plus mr-1"></i> Garzón
            </button>
          </div>

          @if(Auth::user()->isAdmin())
            <div class="d-flex justify-content-center gap-4 mt-3 pt-3" style="border-top: 1px dashed var(--glass-border);">
              <button type="button" class="btn btn-link text-info p-0 font-weight-bold" style="text-decoration: none;" onclick="openEditModal({{ $tbl->id }}, {{ $tbl->number }}, {{ $tbl->capacity }}, '{{ $tbl->zone }}', '{{ $tbl->status }}')" title="Editar Mesa">
                <i class="fa fa-edit"></i> Editar
              </button>
              <form action="{{ route('admin.tables.destroy', $tbl->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Seguro que desea eliminar esta mesa?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link text-danger p-0 font-weight-bold" style="text-decoration: none;" title="Eliminar Mesa">
                  <i class="fa fa-trash"></i> Eliminar
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12">
        <p class="text-muted italic px-3">No hay mesas configuradas para esta zona.</p>
      </div>
    @endforelse
  </div>
@endforeach

<!-- Modals -->

<!-- Create / Edit Table Modal -->
@if(Auth::user()->isAdmin())
<div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary-dark); font-weight: bold;">Agregar Nueva Mesa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.tables.store') }}" method="POST" id="tableForm">
        @csrf
        <div id="methodContainer"></div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="number">Número de Mesa *</label>
            <input type="number" name="number" id="number" class="form-control" required min="1">
          </div>
          <div class="form-group mb-3">
            <label for="capacity">Capacidad (Comensales) *</label>
            <input type="number" name="capacity" id="capacity" class="form-control" required min="1" placeholder="Ej. 4">
          </div>
          <div class="form-group mb-3">
            <label for="zone">Zona de Ubicación *</label>
            <select name="zone" id="zone" class="form-control" required>
              <option value="interior">Salón Interior</option>
              <option value="terraza">Terraza Exterior</option>
              <option value="privado">Salón Privado</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label for="status">Estado Inicial *</label>
            <select name="status" id="status" class="form-control" required>
              <option value="free">Libre</option>
              <option value="occupied">Ocupada</option>
              <option value="reserved">Reservada</option>
              <option value="awaiting_payment">Esperando Pago</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Mesa</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<!-- Quick Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" style="color: var(--primary-dark); font-weight: bold;">Cambiar Estado de Mesa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="statusForm">
        @csrf
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="quick_status">Estado de Mesa *</label>
            <select name="status" id="quick_status" class="form-control" required>
              <option value="free">Libre</option>
              <option value="occupied">Ocupada</option>
              <option value="reserved">Reservada</option>
              <option value="awaiting_payment">Esperando Pago (Cuentas)</option>
            </select>
          </div>
          <p class="text-muted" style="font-size: 0.85rem;"><i class="fa fa-info-circle"></i> Al cambiar a <strong>Libre</strong> se desvinculará automáticamente el garzón asignado a la mesa.</p>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Actualizar Estado</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Assign Waiter Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" style="color: var(--primary-dark); font-weight: bold;">Asignar Garzón de Turno</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="assignForm">
        @csrf
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="quick_waiter">Seleccionar Garzón</label>
            <select name="waiter_name" id="quick_waiter" class="form-control">
              <option value="">Ninguno (Vacante)</option>
              @foreach($waiters as $wtr)
                <option value="{{ $wtr->name }}">{{ $wtr->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Asignar Garzón</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  @if(Auth::user()->isAdmin())
  function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Agregar Nueva Mesa';
    document.getElementById('tableForm').action = "{{ route('admin.tables.store') }}";
    document.getElementById('methodContainer').innerHTML = '';
    
    document.getElementById('number').value = '';
    document.getElementById('capacity').value = '';
    document.getElementById('zone').value = 'interior';
    document.getElementById('status').value = 'free';
    
    $('#tableModal').modal('show');
  }

  function openEditModal(id, number, capacity, zone, status) {
    document.getElementById('modalTitle').innerText = `Editar Mesa #${number}`;
    document.getElementById('tableForm').action = `/intranet/mesas/${id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';
    
    document.getElementById('number').value = number;
    document.getElementById('capacity').value = capacity;
    document.getElementById('zone').value = zone;
    document.getElementById('status').value = status;
    
    $('#tableModal').modal('show');
  }
  @endif

  function openStatusModal(id, currentStatus) {
    document.getElementById('statusForm').action = `/intranet/mesas/${id}/estado`;
    document.getElementById('quick_status').value = currentStatus;
    $('#statusModal').modal('show');
  }

  function openAssignModal(id, currentWaiter) {
    document.getElementById('assignForm').action = `/intranet/mesas/${id}/garzon`;
    document.getElementById('quick_waiter').value = currentWaiter;
    $('#assignModal').modal('show');
  }
</script>
@endsection
