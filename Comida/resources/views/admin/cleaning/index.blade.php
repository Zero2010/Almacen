@extends('admin.layout')

@section('title', 'Tareas de Limpieza y Sanitización')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-refresh" style="color: var(--primary);"></i> Registro de Higiene y Sanitización</h1>
  @if(Auth::user()->isAdmin())
    <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()"><i class="fa fa-plus"></i> Nueva Tarea</button>
  @endif
</div>

<style>
  .table-danger-soft td {
    background-color: #fef2f2 !important;
    border-color: #fecaca !important;
  }
</style>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
          <th style="width: 25%;">Tarea</th>
          <th>Zona</th>
          <th>Frec.</th>
          <th>Fecha Comp.</th>
          <th>Responsable</th>
          <th>Vence</th>
          <th>Estado</th>
          <th style="min-width: 150px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tasks as $task)
          @php
            $isOverdue = !$task->last_completed_at || ($task->next_due_date && $task->next_due_date->isPast() && !$task->next_due_date->isToday());
            $isToday = $task->next_due_date && $task->next_due_date->isToday();
          @endphp
          <tr class="@if($isOverdue) table-danger-soft @endif">
            <td style="vertical-align: middle;">
              <strong style="color: var(--text); font-size: 1.05rem;">{{ $task->name }}</strong>
            </td>
            <td style="vertical-align: middle;">
              <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.85rem; padding: 6px 10px;">{{ ucfirst($task->zone ?? 'general') }}</span>
            </td>
            <td style="vertical-align: middle;">
              <span class="text-dark font-weight-bold" style="font-size: 0.9rem;">
                @if($task->frequency === 'daily') Diario
                @elseif($task->frequency === 'weekly') Semanal
                @else Mensual
                @endif
              </span>
            </td>
            <td style="vertical-align: middle; white-space: nowrap;">{{ $task->last_completed_at ? $task->last_completed_at->format('d/m/Y') : 'Nunca' }}</td>
            <td style="vertical-align: middle;">
              @if($task->completedBy)
                <div class="d-flex align-items-center gap-2">
                  <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                    {{ strtoupper(substr($task->completedBy->name, 0, 1)) }}
                  </div>
                  <span style="font-weight: 600; color: var(--text); font-size: 0.9rem;">{{ explode(' ', $task->completedBy->name)[0] }}</span>
                </div>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td style="vertical-align: middle; white-space: nowrap;">
              @if($task->next_due_date)
                <strong class="@if($isOverdue) text-danger @elseif($isToday) text-warning @else text-success @endif">
                  {{ $task->next_due_date->format('d/m/Y') }}
                </strong>
              @else
                -
              @endif
            </td>
            <td style="vertical-align: middle;">
              @if($isOverdue)
                <span class="badge" style="background: #fee2e2; color: #dc2626; padding: 6px 10px; border-radius: 6px;">Atrasado</span>
              @elseif($isToday)
                <span class="badge" style="background: #fef3c7; color: #d97706; padding: 6px 10px; border-radius: 6px;">Para Hoy</span>
              @else
                <span class="badge" style="background: #d1fae5; color: #059669; padding: 6px 10px; border-radius: 6px;">Al día</span>
              @endif
            </td>
            <td style="vertical-align: middle;">
              <div class="d-flex gap-2 flex-nowrap">
                <!-- Complete task action button -->
                <form action="{{ route('admin.cleaning.complete', $task->id) }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="btn" style="background: #d1fae5; color: #059669; border-radius: 20px; font-weight: 600; padding: 6px 12px; font-size: 0.85rem; border: none; transition: 0.2s;" onmouseover="this.style.background='#a7f3d0'" onmouseout="this.style.background='#d1fae5'" title="Marcar Completado">
                    <i class="fa fa-check mr-1"></i> Completar
                  </button>
                </form>

                @if(Auth::user()->isAdmin())
                  <button type="button" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 8px; border: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'" onclick="openEditModal({{ $task->id }}, '{{ $task->name }}', '{{ $task->zone }}', '{{ $task->frequency }}')" title="Editar Tarea">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <form action="{{ route('admin.cleaning.destroy', $task->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Seguro que desea eliminar esta tarea de limpieza?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: #fee2e2; color: #dc2626; border-radius: 8px; border: none; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Eliminar Tarea">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted" style="padding: 30px;">
              No hay tareas de limpieza registradas en el programa.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="d-flex justify-content-center mt-3">
    {{ $tasks->links() }}
  </div>
</div>

<!-- Add/Edit Cleaning Task Modal (Admin Only) -->
@if(Auth::user()->isAdmin())
<div class="modal fade" id="cleaningModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary-dark); font-weight: bold;">Agregar Tarea de Limpieza</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.cleaning.store') }}" method="POST" id="cleaningForm">
        @csrf
        <div id="methodContainer"></div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Descripción de Tarea *</label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="Ej. Desengrasar campana de cocina">
          </div>
          
          <div class="form-group mb-3">
            <label for="zone">Zona de Trabajo</label>
            <input type="text" name="zone" id="zone" class="form-control" placeholder="Ej. Cocina, Baños, Salón, etc.">
          </div>

          <div class="form-group mb-3">
            <label for="frequency">Frecuencia *</label>
            <select name="frequency" id="frequency" class="form-control" required>
              <option value="daily">Diario</option>
              <option value="weekly">Semanal</option>
              <option value="monthly">Mensual</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Tarea</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection

@section('scripts')
<script>
  @if(Auth::user()->isAdmin())
  function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Registrar Tarea de Limpieza';
    document.getElementById('cleaningForm').action = "{{ route('admin.cleaning.store') }}";
    document.getElementById('methodContainer').innerHTML = '';
    
    document.getElementById('name').value = '';
    document.getElementById('zone').value = '';
    document.getElementById('frequency').value = 'daily';
    
    $('#cleaningModal').modal('show');
  }

  function openEditModal(id, name, zone, frequency) {
    document.getElementById('modalTitle').innerText = `Editar Tarea: ${name}`;
    document.getElementById('cleaningForm').action = `/intranet/limpieza/${id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = name;
    document.getElementById('zone').value = zone || '';
    document.getElementById('frequency').value = frequency;
    
    $('#cleaningModal').modal('show');
  }
  @endif
</script>
@endsection
