@extends('admin.layout')

@section('title', 'Categorías de Productos')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-tags" style="color: var(--primary);"></i> Categorías de Productos</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()">
    <i class="fa fa-plus"></i> Nueva Categoría
  </button>
</div>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Identificador (Slug)</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $cat)
          <tr>
            <td>#{{ $cat->id }}</td>
            <td style="font-weight: 500; color: var(--text);">{{ $cat->name }}</td>
            <td><code>{{ $cat->slug }}</code></td>
            <td>{{ $cat->description ?? 'Sin descripción' }}</td>
            <td>
              <div class="btn-group">
                <button type="button" class="btn btn-action mr-1" onclick="openEditModal({{ $cat }})">
                  <i class="fa fa-pencil"></i>
                </button>
                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría? Esto podría afectar a los platos asociados.')" style="margin:0;">
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
            <td colspan="5" class="text-center text-muted" style="padding: 20px;">No hay categorías registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="d-flex justify-content-center mt-3">
    {{ $categories->links() }}
  </div>
</div>

<!-- Create / Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Nueva Categoría</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="categoryForm">
        @csrf
        <div id="methodContainer"></div>

        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre de la Categoría *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Promociones" required>
          </div>

          <div class="form-group mb-3">
            <label for="description">Descripción / Detalle</label>
            <input type="text" name="description" id="description" class="form-control" placeholder="Ej. Combos y ofertas especiales por el mes">
          </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Categoría</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    const form = document.getElementById('categoryForm');
    form.action = "{{ route('admin.categories.store') }}";
    document.getElementById('methodContainer').innerHTML = '';

    document.getElementById('modalTitle').innerText = 'Nueva Categoría';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';

    $('#categoryModal').modal('show');
  }

  function openEditModal(cat) {
    const form = document.getElementById('categoryForm');
    form.action = `/intranet/categorias/${cat.id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';

    document.getElementById('modalTitle').innerText = `Editar Categoría: ${cat.name}`;
    document.getElementById('name').value = cat.name;
    document.getElementById('description').value = cat.description || '';

    $('#categoryModal').modal('show');
  }
</script>
@endsection
