@extends('admin.layout')

@section('title', 'Gestión de Reservas')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
  .fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--glass-border);
  }
  .fc .fc-button-primary {
    background-color: var(--primary);
    border-color: var(--primary);
  }
  .fc .fc-button-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
  }
  .fc .fc-button-primary:not(:disabled):active, .fc .fc-button-primary:not(:disabled).fc-button-active {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
  }
  .fc-event {
    cursor: pointer;
  }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-calendar" style="color: var(--primary);"></i> Control de Reservas</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()"><i class="fa fa-plus"></i> Nueva Reserva</button>
</div>

<ul class="nav nav-tabs mb-4" id="reservationTabs" role="tablist" style="border-bottom: 2px solid var(--glass-border);">
  <li class="nav-item">
    <a class="nav-link active" id="list-tab" data-toggle="tab" href="#list" role="tab" style="color: var(--text); font-weight: 600;"><i class="fa fa-list"></i> Lista de Reservas</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab" style="color: var(--text); font-weight: 600;"><i class="fa fa-calendar"></i> Calendario Visual</a>
  </li>
</ul>

<div class="tab-content" id="reservationTabsContent">
  <div class="tab-pane fade show active" id="list" role="tabpanel">
    <div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Fecha / Hora</th>
          <th>Cliente</th>
          <th>Contacto</th>
          <th>Mesa Asignada</th>
          <th>Comensales</th>
          <th>Estado</th>
          <th>Notas</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reservations as $res)
          <tr>
            <td>
              <strong style="color: var(--primary-dark);">{{ $res->reserved_at->format('d/m/Y') }}</strong>
              <br>
              <small class="text-muted">{{ $res->reserved_at->format('H:i') }} hrs</small>
            </td>
            <td><strong>{{ $res->customer_name }}</strong></td>
            <td>{{ $res->customer_phone ?? 'Sin Teléfono' }}</td>
            <td>
              @if($res->table)
                <span class="text-dark font-weight-bold">Mesa #{{ $res->table->number }}</span>
                <br><small class="text-muted">({{ ucfirst($res->table->zone) }})</small>
              @else
                <span class="text-warning italic">Sin Mesa Asignada</span>
              @endif
            </td>
            <td>
              <span class="badge badge-light" style="font-size: 0.85rem; font-weight: 600; padding: 4px 10px;">{{ $res->party_size }} pers.</span>
            </td>
            <td>
              <span class="badge-role @if($res->status === 'pending') badge-waiter @elseif($res->status === 'confirmed') badge-chef @elseif($res->status === 'completed') badge-delivery @else badge-admin @endif" style="font-size: 0.75rem; text-transform: uppercase;">
                @if($res->status === 'pending') Pendiente
                @elseif($res->status === 'confirmed') Confirmada
                @elseif($res->status === 'completed') Completada
                @else Cancelada
                @endif
              </span>
            </td>
            <td style="max-width: 200px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;" title="{{ $res->notes }}">{{ $res->notes ?? '-' }}</td>
            <td>
              <div class="d-flex gap-2">
                @if($res->status === 'pending')
                  <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-action btn-action-success" title="Confirmar Reserva">
                      <i class="fa fa-check"></i> Confirmar
                    </button>
                  </form>
                @endif

                @if($res->status === 'confirmed')
                  <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-action btn-action-success" title="Marcar como Finalizada">
                      <i class="fa fa-check-circle"></i> Completar
                    </button>
                  </form>
                @endif

                @if(in_array($res->status, ['pending', 'confirmed']))
                  <form action="{{ route('admin.reservations.updateStatus', $res->id) }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-action" title="Cancelar Reserva">
                      <i class="fa fa-times"></i> Cancelar
                    </button>
                  </form>
                @endif
                
                @if($res->customer_phone)
                  <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $res->customer_phone) }}&text=Hola%20{{ urlencode($res->customer_name) }},%20te%20escribimos%20de%20Comida%20al%20Paso%20para%20confirmar%20tu%20reserva%20del%20{{ $res->reserved_at->format('d/m/Y') }}%20a%20las%20{{ $res->reserved_at->format('H:i') }}.%20%C2%BFNos%20confirmas%20tu%20asistencia?" target="_blank" class="btn btn-action btn-action-success" title="Confirmar por WhatsApp">
                    <i class="fa fa-whatsapp"></i> WSP
                  </a>
                @endif

                <button type="button" class="btn btn-action" onclick="openEditModal({{ $res->id }}, '{{ $res->customer_name }}', '{{ $res->customer_phone }}', {{ $res->party_size }}, '{{ $res->reserved_at->format('Y-m-d\TH:i') }}', '{{ $res->table_id }}', '{{ $res->notes }}')">
                  <i class="fa fa-edit"></i>
                </button>

                <form action="{{ route('admin.reservations.destroy', $res->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Seguro que desea eliminar esta reserva permanentemente?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-action btn-action-danger">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
              No hay reservas registradas en este momento.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $reservations->links() }}
  </div>
</div>
</div>
<div class="tab-pane fade" id="calendar" role="tabpanel">
  <div class="glass-panel">
    <div id="calendar-el"></div>
  </div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary-dark); font-weight: bold;">Registrar Nueva Reserva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.reservations.store') }}" method="POST" id="reservationForm">
        @csrf
        <div id="methodContainer"></div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="customer_name">Nombre de Cliente *</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="Ej. Beatriz Muñoz">
          </div>
          
          <div class="form-group mb-3">
            <label for="customer_phone">Teléfono de Contacto</label>
            <input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Ej. +56988887777">
          </div>

          <div class="form-group mb-3">
            <label for="party_size">Cantidad de Comensales *</label>
            <input type="number" name="party_size" id="party_size" class="form-control" required min="1" placeholder="Ej. 4">
          </div>

          <div class="form-group mb-3">
            <label for="reserved_at">Fecha y Hora de la Reserva *</label>
            <input type="datetime-local" name="reserved_at" id="reserved_at" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label for="table_id">Asignar Mesa (Opcional)</label>
            <select name="table_id" id="table_id" class="form-control">
              <option value="">Ninguna mesa asignada</option>
              @foreach($tables as $tbl)
                <option value="{{ $tbl->id }}">Mesa #{{ $tbl->number }} (Capacidad: {{ $tbl->capacity }} - {{ ucfirst($tbl->zone) }} - {{ $tbl->status === 'free' ? 'Libre' : ($tbl->status === 'reserved' ? 'Reservada' : 'Ocupada') }})</option>
              @endforeach
            </select>
            <small class="text-muted"><i class="fa fa-info-circle"></i> Se recomienda elegir una mesa libre con capacidad igual o superior al número de comensales.</small>
          </div>

          <div class="form-group mb-3">
            <label for="notes">Notas o Requerimientos Especiales</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Ej. Mesa cerca de la ventana, alérgica a mariscos, etc."></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Reserva</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar-el');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'es',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: [
        @foreach($reservations as $r)
        {
          title: '{{ $r->customer_name }} ({{ $r->party_size }}p)',
          start: '{{ $r->reserved_at->format("Y-m-d\TH:i:s") }}',
          color: '{{ $r->status === "pending" ? "#f59e0b" : ($r->status === "confirmed" ? "#10b981" : ($r->status === "completed" ? "#3b82f6" : "#ef4444")) }}',
          extendedProps: {
            id: {{ $r->id }},
            name: '{{ $r->customer_name }}',
            phone: '{{ $r->customer_phone }}',
            size: {{ $r->party_size }},
            datetime: '{{ $r->reserved_at->format("Y-m-d\TH:i") }}',
            tableId: '{{ $r->table_id }}',
            notes: '{{ str_replace(["\r", "\n"], " ", $r->notes) }}'
          }
        },
        @endforeach
      ],
      eventClick: function(info) {
        var p = info.event.extendedProps;
        openEditModal(p.id, p.name, p.phone, p.size, p.datetime, p.tableId, p.notes);
      }
    });
    
    // Render calendar when tab is shown
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      if(e.target.id === 'calendar-tab') {
        calendar.render();
      }
    });
  });

  function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Registrar Nueva Reserva';
    document.getElementById('reservationForm').action = "{{ route('admin.reservations.store') }}";
    document.getElementById('methodContainer').innerHTML = '';
    
    document.getElementById('customer_name').value = '';
    document.getElementById('customer_phone').value = '';
    document.getElementById('party_size').value = '';
    document.getElementById('reserved_at').value = '';
    document.getElementById('table_id').value = '';
    document.getElementById('notes').value = '';
    
    $('#reservationModal').modal('show');
  }

  function openEditModal(id, name, phone, size, datetime, tableId, notes) {
    document.getElementById('modalTitle').innerText = `Editar Reserva de: ${name}`;
    document.getElementById('reservationForm').action = `/intranet/reservas/${id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';
    
    document.getElementById('customer_name').value = name;
    document.getElementById('customer_phone').value = phone;
    document.getElementById('party_size').value = size;
    document.getElementById('reserved_at').value = datetime;
    document.getElementById('table_id').value = tableId || '';
    document.getElementById('notes').value = notes || '';
    
    $('#reservationModal').modal('show');
  }
</script>
@endsection
