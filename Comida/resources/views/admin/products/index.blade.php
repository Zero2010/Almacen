@extends('admin.layout')

@section('title', 'Platos y Productos')

@section('styles')
<style>
  .product-img-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--glass-border);
  }
  .available-true { color: #10b981; font-weight: 600; }
  .available-false { color: #ef4444; }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-cutlery" style="color: var(--primary);"></i> Platos y Productos del Menú</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()">
    <i class="fa fa-plus"></i> Nuevo Plato
  </button>
</div>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Tiempo Prep.</th>
          <th>Disponibilidad</th>
          <th>Receta</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $prod)
          <tr>
            <td>
              <img src="{{ asset($prod->image_path ?? 'images/default-food.png') }}" class="product-img-thumb" alt="{{ $prod->name }}">
            </td>
            <td style="font-weight: 500; color: var(--text);">{{ $prod->name }}</td>
            <td>{{ $prod->category->name }}</td>
            <td style="color: var(--primary); font-weight: 600;">${{ number_format($prod->price, 0, ',', '.') }}</td>
            <td>{{ $prod->estimated_prep_time }} mins</td>
            <td>
              <span class="{{ $prod->is_available ? 'available-true' : 'available-false' }}">
                <i class="fa {{ $prod->is_available ? 'fa-check' : 'fa-times' }}"></i> {{ $prod->is_available ? 'Disponible' : 'Agotado' }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.recipes.index', $prod->id) }}" class="btn btn-action" style="font-size: 0.85rem;">
                <i class="fa fa-list-ol"></i> Ver Receta ({{ $prod->recipes->count() }} Insumos)
              </a>
            </td>
            <td>
              <div class="btn-group">
                <button type="button" class="btn btn-action mr-1" onclick="openEditModal({{ $prod }})">
                  <i class="fa fa-pencil"></i>
                </button>
                <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este plato?')" style="margin:0;">
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
            <td colspan="8" class="text-center text-muted" style="padding: 20px;">No hay productos registrados en el menú.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $products->links() }}
  </div>
</div>

<!-- Create / Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Nuevo Plato</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="productForm" enctype="multipart/form-data">
        @csrf
        <div id="methodContainer"></div>

        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre del Plato *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Completo Italiano Gigante" required>
          </div>

          <div class="form-group mb-3">
            <label for="category_id">Categoría del Menú *</label>
            <select name="category_id" id="category_id" class="form-control" required>
              <option value="">Seleccione categoría...</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="price">Precio Venta ($) *</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Ej. 3200" min="0" required>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="estimated_prep_time">Tiempo Prep. (mins) *</label>
                <input type="number" name="estimated_prep_time" id="estimated_prep_time" class="form-control" placeholder="Ej. 5" min="1" required>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="description">Descripción / Ingredientes visibles</label>
            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Ej. Pan suave de completo, salchicha vienesa premium, palta fresca..."></textarea>
          </div>

          <div class="form-group mb-3">
            <label for="image">Imagen del Plato</label>
            <input type="file" name="image" id="image" class="form-control-file form-control" style="height: auto !important;">
          </div>

          <div class="form-group mb-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="is_available" id="is_available" class="custom-control-input" value="1" checked>
              <label class="custom-control-label" for="is_available">Plato disponible para venta online e interna</label>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Plato</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    const form = document.getElementById('productForm');
    form.action = "{{ route('admin.products.store') }}";
    document.getElementById('methodContainer').innerHTML = '';

    document.getElementById('modalTitle').innerText = 'Nuevo Plato de Comida';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('price').value = '';
    document.getElementById('estimated_prep_time').value = '15';
    document.getElementById('description').value = '';
    document.getElementById('is_available').checked = true;

    $('#productModal').modal('show');
  }

  function openEditModal(prod) {
    const form = document.getElementById('productForm');
    form.action = `/intranet/productos/${prod.id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';

    document.getElementById('modalTitle').innerText = `Editar Plato: ${prod.name}`;
    document.getElementById('name').value = prod.name;
    document.getElementById('category_id').value = prod.category_id;
    document.getElementById('price').value = prod.price;
    document.getElementById('estimated_prep_time').value = prod.estimated_prep_time;
    document.getElementById('description').value = prod.description || '';
    document.getElementById('is_available').checked = prod.is_available;

    $('#productModal').modal('show');
  }
</script>
@endsection
