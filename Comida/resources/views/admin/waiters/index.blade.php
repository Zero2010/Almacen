@extends('admin.layout')

@section('title', 'Garzones')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-users" style="color: var(--primary);"></i> Gestión de Garzones</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()"><i class="fa fa-plus"></i> Registrar Garzón</button>
</div>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Estado de Turno</th>
          <th>Última Actualización</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        @forelse($waiters as $waiter)
          <tr>
            <td><strong style="color: var(--primary-dark);">#{{ $waiter->id }}</strong></td>
            <td><strong>{{ $waiter->name }}</strong></td>
            <td>
              <span class="badge-role {{ $waiter->is_active ? 'badge-chef' : 'badge-client' }}" style="font-size: 0.8rem; text-transform: uppercase;">
                {{ $waiter->is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>{{ $waiter->updated_at->format('d/m/Y H:i') }} hrs</td>
            <td>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-action" onclick="openEditModal({{ $waiter->id }}, '{{ $waiter->name }}', {{ $waiter->is_active ? 1 : 0 }})">
                  <i class="fa fa-edit"></i> Editar
                </button>
                <form action="{{ route('admin.waiters.destroy', $waiter->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Seguro que desea eliminar a este garzón?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-action btn-action-danger">
                    <i class="fa fa-trash"></i> Eliminar
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted" style="padding: 30px;">
              No hay garzones registrados actualmente.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="d-flex justify-content-center mt-3">
    {{ $waiters->links() }}
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="waiterModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary-dark); font-weight: bold;">Agregar Garzón</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('admin.waiters.store') }}" method="POST" id="waiterForm">
        @csrf
        <div id="methodContainer"></div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre Completo *</label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="Ej. Claudio Soto">
          </div>
          <div class="form-group form-check mb-3 pl-4">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked>
            <label class="form-check-label" for="is_active">Garzón disponible para atención de mesas</label>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Garzón</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Registrar Nuevo Garzón';
    document.getElementById('waiterForm').action = "{{ route('admin.waiters.store') }}";
    document.getElementById('methodContainer').innerHTML = '';
    
    document.getElementById('name').value = '';
    document.getElementById('is_active').checked = true;
    
    $('#waiterModal').modal('show');
  }

  function openEditModal(id, name, isActive) {
    document.getElementById('modalTitle').innerText = `Editar Garzón: ${name}`;
    document.getElementById('waiterForm').action = `/intranet/garzones/${id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = name;
    document.getElementById('is_active').checked = isActive === 1;
    
    $('#waiterModal').modal('show');
  }
</script>
@endsection
