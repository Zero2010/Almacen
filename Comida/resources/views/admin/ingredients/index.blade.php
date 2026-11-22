@extends('admin.layout')

@section('title', 'Inventario Insumos')

@section('styles')
<style>
  .alert-critical td {
    background-color: #fef2f2 !important;
    border-color: #fecaca !important;
  }
  .stock-warning { color: #dc2626; font-weight: 700; }
  .stock-safe { color: #059669; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-archive" style="color: var(--primary);"></i> Control de Inventario e Insumos</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()">
    <i class="fa fa-plus"></i> Nuevo Insumo
  </button>
</div>

<!-- Stock Alerts Banner -->
@php
  $criticalItems = $ingredients->filter(function($i) { return $i->stock <= $i->minimum_stock; });
@endphp
@if($criticalItems->count() > 0)
  <div class="alert alert-warning alert-dismissible fade show" role="alert" style="background: rgba(245, 158, 11, 0.15); border: 1px solid var(--warning); color: #fbbf24; border-radius: 12px; margin-bottom: 25px; padding: 15px;">
    <strong><i class="fa fa-warning"></i> ¡Alerta de Abastecimiento!</strong> Tienes {{ $criticalItems->count() }} insumos con existencias por debajo del límite mínimo. Por favor reabastézcalos a la brevedad.
  </div>
@endif

<!-- Ingredients Table -->
<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Insumo</th>
          <th>Stock Actual</th>
          <th>Stock Mínimo</th>
          <th>Costo Unit.</th>
          <th>Proveedor / Lote</th>
          <th>Vencimiento</th>
          <th style="min-width: 120px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($ingredients as $ing)
          <tr class="{{ $ing->stock <= $ing->minimum_stock ? 'alert-critical' : '' }}">
            <td style="vertical-align: middle;">
              <strong style="color: var(--text); font-size: 1.05rem;">{{ $ing->name }}</strong>
              <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Medida: {{ $ing->unit }}</div>
            </td>
            <td class="{{ $ing->stock <= $ing->minimum_stock ? 'stock-warning' : 'stock-safe' }}" style="vertical-align: middle; font-size: 1.1rem;">
              {{ $ing->stock }} <span style="font-size: 0.8rem; color: #94a3b8;">{{ $ing->unit }}</span>
              @if($ing->stock <= $ing->minimum_stock)
                <i class="fa fa-exclamation-triangle text-danger ml-1" title="Bajo el mínimo"></i>
              @endif
            </td>
            <td style="vertical-align: middle; color: #64748b;">{{ $ing->minimum_stock }} {{ $ing->unit }}</td>
            <td style="vertical-align: middle; font-weight: 600;">${{ number_format($ing->cost_per_unit, 0, ',', '.') }}</td>
            <td style="vertical-align: middle;">
              <div style="font-weight: 600; color: var(--text);">{{ $ing->supplier->name ?? 'Sin Proveedor' }}</div>
              <span class="badge" style="background: #f1f5f9; color: #475569; font-family: monospace; font-size: 0.8rem;">Lote: {{ $ing->lot_number ?? 'N/A' }}</span>
            </td>
            <td style="vertical-align: middle;">
              @if($ing->expiration_date)
                @if($ing->expiration_date->isPast())
                  <span class="badge" style="background: #fee2e2; color: #dc2626; padding: 6px 10px; border-radius: 6px;">Vencido: {{ $ing->expiration_date->format('d/m/Y') }}</span>
                @elseif($ing->expiration_date->diffInDays(now()) < 5)
                  <span class="badge" style="background: #fef3c7; color: #d97706; padding: 6px 10px; border-radius: 6px;">Pronto: {{ $ing->expiration_date->format('d/m/Y') }}</span>
                @else
                  <span class="badge" style="background: #d1fae5; color: #059669; padding: 6px 10px; border-radius: 6px;">{{ $ing->expiration_date->format('d/m/Y') }}</span>
                @endif
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td style="vertical-align: middle;">
              <div class="d-flex gap-2 flex-nowrap">
                <button type="button" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 8px; border: none; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'" onclick="openEditModal({{ $ing }})" title="Editar Insumo">
                  <i class="fa fa-pencil"></i>
                </button>
                <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este insumo del inventario?')" class="m-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn" style="background: #fee2e2; color: #dc2626; border-radius: 8px; border: none; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Eliminar Insumo">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted" style="padding: 20px;">No hay insumos registrados en el inventario.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $ingredients->links() }}
  </div>
</div>

<!-- Create / Edit Ingredient Modal -->
<div class="modal fade" id="ingredientModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Nuevo Insumo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="ingredientForm">
        @csrf
        <div id="methodContainer"></div>
        
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre del Insumo *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Palta Hass molida" required>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="unit">Unidad de Medida *</label>
                <select name="unit" id="unit" class="form-control" required>
                  <option value="gr">Gramos (gr)</option>
                  <option value="ml">Mililitros (ml)</option>
                  <option value="und">Unidades (und)</option>
                  <option value="kg">Kilogramos (kg)</option>
                </select>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="cost_per_unit">Costo por Unidad ($) *</label>
                <input type="number" name="cost_per_unit" id="cost_per_unit" class="form-control" placeholder="Ej. 6" min="0" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="stock">Stock Inicial / Actual *</label>
                <input type="number" step="0.01" name="stock" id="stock" class="form-control" placeholder="Ej. 12000" min="0" required>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="minimum_stock">Alerta de Stock Mínimo *</label>
                <input type="number" step="0.01" name="minimum_stock" id="minimum_stock" class="form-control" placeholder="Ej. 2000" min="0" required>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="supplier_id">Proveedor Asociado</label>
            <select name="supplier_id" id="supplier_id" class="form-control">
              <option value="">Ninguno</option>
              @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="lot_number">Lote Trazabilidad</label>
                <input type="text" name="lot_number" id="lot_number" class="form-control" placeholder="Ej. LOTE-P320">
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="expiration_date">Fecha de Vencimiento</label>
                <input type="date" name="expiration_date" id="expiration_date" class="form-control">
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Insumo</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    const form = document.getElementById('ingredientForm');
    form.action = "{{ route('admin.ingredients.store') }}";
    document.getElementById('methodContainer').innerHTML = '';
    
    document.getElementById('modalTitle').innerText = 'Nuevo Insumo / Ingrediente';
    document.getElementById('name').value = '';
    document.getElementById('unit').value = 'gr';
    document.getElementById('cost_per_unit').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('minimum_stock').value = '';
    document.getElementById('supplier_id').value = '';
    document.getElementById('lot_number').value = '';
    document.getElementById('expiration_date').value = '';

    $('#ingredientModal').modal('show');
  }

  function openEditModal(ing) {
    const form = document.getElementById('ingredientForm');
    form.action = `/intranet/inventario/${ing.id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';

    document.getElementById('modalTitle').innerText = `Editar Insumo: ${ing.name}`;
    document.getElementById('name').value = ing.name;
    document.getElementById('unit').value = ing.unit;
    document.getElementById('cost_per_unit').value = ing.cost_per_unit;
    document.getElementById('stock').value = ing.stock;
    document.getElementById('minimum_stock').value = ing.minimum_stock;
    document.getElementById('supplier_id').value = ing.supplier_id || '';
    document.getElementById('lot_number').value = ing.lot_number || '';
    
    if (ing.expiration_date) {
      // format YYYY-MM-DD
      const date = new Date(ing.expiration_date);
      const yyyy = date.getFullYear();
      const mm = String(date.getMonth() + 1).padStart(2, '0');
      const dd = String(date.getDate()).padStart(2, '0');
      document.getElementById('expiration_date').value = `${yyyy}-${mm}-${dd}`;
    } else {
      document.getElementById('expiration_date').value = '';
    }

    $('#ingredientModal').modal('show');
  }
</script>
@endsection
