@extends('admin.layout')

@section('title', 'Libro de Finanzas')

@section('styles')
<style>
  .finance-card {
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    text-align: center;
  }
  .amount-income { color: #10b981; font-weight: 600; }
  .amount-expense { color: #ef4444; font-weight: 600; }
  
  @media print {
    body * {
      visibility: hidden;
    }
    .print-area, .print-area * {
      visibility: visible;
    }
    .print-area {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
    .no-print {
      display: none !important;
    }
    .finance-card {
      border: 1px solid #ccc !important;
      break-inside: avoid;
    }
    .glass-panel {
      border: none !important;
      box-shadow: none !important;
    }
  }
</style>
@endsection

@section('content')
<div class="page-header no-print">
  <h1 class="page-title"><i class="fa fa-money" style="color: var(--primary);"></i> Libro de Finanzas e Ingresos / Egresos</h1>
  <button type="button" class="btn btn-primary-glass" onclick="window.print()">
    <i class="fa fa-print"></i> Imprimir / Exportar a PDF
  </button>
</div>

<div class="print-area">
  <h2 class="d-none d-print-block text-center mb-4 border-bottom pb-2">Reporte Financiero - Comida al Paso</h2>


<!-- Profit Summary -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="glass-panel finance-card" style="border-top: 4px solid var(--success);">
      <h5 class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Ingresos</h5>
      <h2 class="text-success font-weight-bold" style="margin: 10px 0 0 0;">+${{ number_format($totalIncomes, 0, ',', '.') }}</h2>
    </div>
  </div>

  <div class="col-md-4">
    <div class="glass-panel finance-card" style="border-top: 4px solid var(--danger);">
      <h5 class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Egresos</h5>
      <h2 class="text-danger font-weight-bold" style="margin: 10px 0 0 0;">-${{ number_format($totalExpenses, 0, ',', '.') }}</h2>
    </div>
  </div>

  <div class="col-md-4">
    <div class="glass-panel finance-card" style="border-top: 4px solid var(--primary);">
      <h5 class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Resultado Neto (Utilidad)</h5>
      <h2 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold" style="margin: 10px 0 0 0;">
        {{ $netProfit >= 0 ? '+' : '-' }}${{ number_format(abs($netProfit), 0, ',', '.') }}
      </h2>
    </div>
  </div>
</div>

<div class="row">
  <!-- Record Transaction Form -->
  <div class="col-lg-4 mb-4 mb-lg-0">
    <div class="glass-panel">
      <h4 style="color: var(--primary);"><i class="fa fa-plus-circle"></i> Registrar Transacción</h4>
      <p class="text-muted">Añada un registro contable de ingreso o gasto operativo.</p>
      <hr style="border-color: var(--glass-border);">

      <form action="{{ route('admin.finances.storeTransaction') }}" method="POST">
        @csrf
        
        <div class="form-group mb-3">
          <label for="type">Tipo de Flujo *</label>
          <select name="type" id="type" class="form-control" required>
            <option value="expense">Egreso / Gasto (Salida)</option>
            <option value="income">Ingreso / Venta (Entrada)</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="category">Categoría *</label>
          <select name="category" id="category" class="form-control" required>
            <option value="purchase">Compra de Insumos (Inventario)</option>
            <option value="salary">Sueldos y Honorarios</option>
            <option value="utilities">Servicios Básicos (Luz, Agua, Gas)</option>
            <option value="sales">Venta de Platos</option>
            <option value="merger">Merma de Alimentos / Mermas</option>
            <option value="other">Otros Gastos/Ingresos</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="amount">Monto de la Transacción *</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">$</span>
            </div>
            <input type="number" name="amount" id="amount" class="form-control" placeholder="Ej. 15000" min="1" required>
            <div class="input-group-append">
              <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">CLP</span>
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label for="date">Fecha *</label>
          <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="form-group mb-3">
          <label for="payment_method">Método de Flujo *</label>
          <select name="payment_method" id="payment_method" class="form-control" required>
            <option value="cash">Efectivo</option>
            <option value="transfer">Transferencia Bancaria</option>
            <option value="transbank">Tarjeta (Transbank)</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="description">Descripción / Detalle *</label>
          <input type="text" name="description" id="description" class="form-control" placeholder="Ej. Compra de 20kg de Palta molida" required>
        </div>

        <button type="submit" class="btn btn-primary-glass btn-block">
          <i class="fa fa-save"></i> Registrar en Libro Diario
        </button>
      </form>
    </div>
  </div>

  <!-- Transactions List -->
  <div class="col-lg-8">
    <div class="glass-panel">
      <h4 style="color: var(--primary); margin-bottom: 20px;"><i class="fa fa-list"></i> Historial Contable</h4>
      <div class="table-responsive">
        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Categoría</th>
              <th>Descripción</th>
              <th>Método</th>
              <th>Monto</th>
            </tr>
          </thead>
          <tbody>
            @forelse($finances as $fin)
              <tr>
                <td>{{ $fin->date->format('d/m/Y') }}</td>
                <td style="text-transform: capitalize;">
                  @if($fin->category === 'purchase')
                    <span class="badge badge-danger">Compra Insumos</span>
                  @elseif($fin->category === 'salary')
                    <span class="badge badge-warning">Sueldos</span>
                  @elseif($fin->category === 'utilities')
                    <span class="badge badge-secondary">Servicios</span>
                  @elseif($fin->category === 'sales')
                    <span class="badge badge-success">Venta</span>
                  @elseif($fin->category === 'merger')
                    <span class="badge badge-dark">Merma</span>
                  @else
                    <span class="badge badge-light">Otro</span>
                  @endif
                </td>
                <td>{{ $fin->description }}</td>
                <td style="text-transform: capitalize; font-size: 0.85rem;">{{ $fin->payment_method }}</td>
                <td>
                  <span class="{{ $fin->type === 'income' ? 'amount-income' : 'amount-expense' }}">
                    {{ $fin->type === 'income' ? '+' : '-' }}${{ number_format($fin->amount, 0, ',', '.') }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 20px;">No hay registros contables en este período.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-center mt-3 no-print">
        {{ $finances->links() }}
      </div>
    </div>
  </div>
</div>
</div>
@endsection
