@extends('admin.layout')

@section('title', 'Proveedores')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-truck" style="color: var(--primary);"></i> Gestión de Proveedores</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()">
    <i class="fa fa-plus"></i> Nuevo Proveedor
  </button>
</div>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Proveedor</th>
          <th>RUT</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Dirección</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($suppliers as $sup)
          <tr>
            <td style="font-weight: 500; color: var(--text);">{{ $sup->name }}</td>
            <td>{{ $sup->rut ?? 'N/A' }}</td>
            <td>{{ $sup->email ?? 'N/A' }}</td>
            <td>{{ $sup->phone ?? 'N/A' }}</td>
            <td>{{ $sup->address ?? 'N/A' }}</td>
            <td>
              <div class="btn-group">
                <button type="button" class="btn btn-action mr-1" onclick="openEditModal({{ $sup }})">
                  <i class="fa fa-pencil"></i>
                </button>
                <form action="{{ route('admin.suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')" style="margin:0;">
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
            <td colspan="6" class="text-center text-muted" style="padding: 20px;">No hay proveedores registrados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $suppliers->links() }}
  </div>
</div>

<!-- Create / Edit Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Nuevo Proveedor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="supplierForm">
        @csrf
        <div id="methodContainer"></div>

        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre / Razón Social *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Distribuidora Mayorista Alvi" required>
          </div>

          <div class="form-group mb-3">
            <label for="rut">RUT del Proveedor *</label>
            <input type="text" name="rut" id="rut" class="form-control" placeholder="Ej. 76.123.456-7" required>
          </div>

          <div class="form-group mb-3">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="proveedor@email.com">
          </div>

          <div class="form-group mb-3">
            <label for="phone">Teléfono de Contacto</label>
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Ej. +56912345678">
          </div>

          <div class="form-group mb-3">
            <label for="address">Dirección Comercial</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Ej. San Pablo 1200, Santiago">
          </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Proveedor</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    const form = document.getElementById('supplierForm');
    form.action = "{{ route('admin.suppliers.store') }}";
    document.getElementById('methodContainer').innerHTML = '';

    document.getElementById('modalTitle').innerText = 'Nuevo Proveedor';
    document.getElementById('name').value = '';
    document.getElementById('rut').value = '';
    document.getElementById('email').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('address').value = '';

    $('#supplierModal').modal('show');
  }

  function openEditModal(sup) {
    const form = document.getElementById('supplierForm');
    form.action = `/intranet/proveedores/${sup.id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';

    document.getElementById('modalTitle').innerText = `Editar Proveedor: ${sup.name}`;
    document.getElementById('name').value = sup.name;
    document.getElementById('rut').value = sup.rut || '';
    document.getElementById('email').value = sup.email || '';
    document.getElementById('phone').value = sup.phone || '';
    document.getElementById('address').value = sup.address || '';

    $('#supplierModal').modal('show');
  }
</script>
@endsection
