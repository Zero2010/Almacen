@extends('admin.layout')

@section('title', 'Receta de ' . $product->name)

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-list-ol" style="color: var(--primary);"></i> Receta e Ingredientes: {{ $product->name }}</h1>
  <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-glass"><i class="fa fa-arrow-left"></i> Volver a Platos</a>
</div>

<div class="row">
  <!-- Mapped Ingredients List -->
  <div class="col-lg-7 mb-4 mb-lg-0">
    <div class="glass-panel">
      <h4 style="color: var(--primary); margin-bottom: 20px;"><i class="fa fa-cutlery"></i> Estructura del Plato</h4>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Ingrediente / Insumo</th>
              <th>Cantidad Requerida</th>
              <th>Unidad</th>
              <th>Costo Proporcional</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totalCost = 0;
            @endphp
            @forelse($recipes as $rec)
              @php
                $itemCost = $rec->quantity_required * $rec->ingredient->cost_per_unit;
                $totalCost += $itemCost;
              @endphp
              <tr>
                <td style="font-weight: 500; color: var(--text);">{{ $rec->ingredient->name }}</td>
                <td>{{ $rec->quantity_required }}</td>
                <td style="text-transform: lowercase;">{{ $rec->ingredient->unit }}</td>
                <td>${{ number_format($itemCost, 0, ',', '.') }} CLP</td>
                <td>
                  <form action="{{ route('admin.recipes.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('¿Remover este insumo de la receta?')" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-action btn-action-danger">
                      <i class="fa fa-trash"></i> Quitar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 20px;">Esta receta no tiene ingredientes asociados aún.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($recipes->count() > 0)
        <hr style="border-color: var(--glass-border);">
        <div class="d-flex justify-content-between font-weight-bold" style="font-size: 1.1rem; color: var(--primary);">
          <span>Costo Total Ingredientes:</span>
          <span>${{ number_format($totalCost, 0, ',', '.') }} CLP</span>
        </div>
        <div class="d-flex justify-content-between text-muted" style="font-size: 0.9rem; margin-top: 5px;">
          <span>Precio de Venta del Plato:</span>
          <span>${{ number_format($product->price, 0, ',', '.') }} CLP</span>
        </div>
        <div class="d-flex justify-content-between text-success font-weight-bold" style="font-size: 1rem; margin-top: 5px;">
          <span>Margen de Contribución:</span>
          <span>${{ number_format($product->price - $totalCost, 0, ',', '.') }} CLP ({{ round((($product->price - $totalCost) / $product->price) * 100) }}%)</span>
        </div>
      @endif
    </div>
  </div>

  <!-- Add Ingredient Form -->
  <div class="col-lg-5">
    <div class="glass-panel">
      <h4 style="color: var(--primary);"><i class="fa fa-plus-circle"></i> Agregar Insumo a la Receta</h4>
      <p class="text-muted">Seleccione un insumo del almacén y defina la cantidad exacta consumida por plato.</p>
      <hr style="border-color: var(--glass-border);">

      <form action="{{ route('admin.recipes.store', $product->id) }}" method="POST">
        @csrf
        <div class="form-group mb-3">
          <label for="ingredient_id">Insumo / Ingrediente *</label>
          <select name="ingredient_id" id="ingredient_id" class="form-control" required>
            <option value="">Seleccione insumo...</option>
            @foreach($ingredients as $ing)
              <option value="{{ $ing->id }}">{{ $ing->name }} (Unidad: {{ $ing->unit }} | Stock: {{ $ing->stock }})</option>
            @endforeach
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="quantity_required">Cantidad Requerida por Plato *</label>
          <input type="number" step="0.01" name="quantity_required" id="quantity_required" class="form-control" placeholder="Ej. 150 (para 150g) o 1 (para 1 pan)" min="0.01" required>
          <small class="text-muted">Ingrese la cantidad en la unidad correspondiente del insumo (ej: gr, ml o unidades).</small>
        </div>

        <button type="submit" class="btn btn-primary-glass btn-block">
          <i class="fa fa-save"></i> Agregar Ingrediente
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
