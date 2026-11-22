@extends('admin.layout')

@section('title', 'Control de Caja')

@section('styles')
<style>
  .shift-card {
    border-top: 4px solid var(--primary);
  }
  .diff-positive { color: #10b981; font-weight: 600; }
  .diff-negative { color: #ef4444; font-weight: 600; }
  .diff-zero { color: #9ca3af; }
</style>
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-calculator" style="color: var(--primary);"></i> Arqueo y Control de Caja</h1>
</div>

<div class="row">
  <!-- Active Shift Info or Open Form -->
  <div class="col-lg-5 mb-4 mb-lg-0">
    @if($activeShift)
      <!-- Active Shift - Details and Closing Form -->
      <div class="glass-panel shift-card">
        <h4 style="color: var(--primary);"><i class="fa fa-unlock"></i> Turno de Caja Activo</h4>
        <p class="text-muted">Iniciado por <strong>{{ $activeShift->user->name }}</strong> el {{ $activeShift->opened_at->format('d/m/Y H:i') }}</p>
        <hr style="border-color: var(--glass-border);">

        <div style="line-height: 1.8; margin-bottom: 25px;">
          <div class="d-flex justify-content-between">
            <span>Monto Inicial (Caja Chica):</span>
            <span class="font-weight-bold">${{ number_format($activeShift->initial_amount, 0, ',', '.') }} CLP</span>
          </div>
          <div class="d-flex justify-content-between">
            <span>Ingresos Registrados (Ventas Efectivo):</span>
            <span class="text-success font-weight-bold">+ ${{ number_format($shiftSalesTotal, 0, ',', '.') }} CLP</span>
          </div>
          <div class="d-flex justify-content-between">
            <span>Egresos Registrados (Gastos/Retiros):</span>
            <span class="text-danger font-weight-bold">- ${{ number_format($shiftWithdrawalsTotal, 0, ',', '.') }} CLP</span>
          </div>
          <hr style="border-color: var(--glass-border); margin: 10px 0;">
          <div class="d-flex justify-content-between font-weight-bold" style="font-size: 1.1rem; color: var(--primary);">
            <span>Saldo Esperado en Caja:</span>
            <span>${{ number_format($expectedAmount, 0, ',', '.') }} CLP</span>
          </div>
        </div>

        <!-- Closing Form -->
        <form action="{{ route('admin.finances.caja.cerrar') }}" method="POST">
          @csrf
          <h5 style="color: var(--text); font-weight: 600;"><i class="fa fa-lock"></i> Arqueo de Cierre</h5>
          
          <div class="form-group mb-3">
            <label for="actual_amount">Monto Físico Contado *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">$</span>
              </div>
              <input type="number" name="actual_amount" id="actual_amount" class="form-control" placeholder="Ingrese el dinero real en caja" required>
              <div class="input-group-append">
                <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">CLP</span>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="observations">Observaciones / Novedades</label>
            <textarea name="observations" id="observations" rows="3" class="form-control" placeholder="Escriba diferencias encontradas o justificaciones de retiros..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary-glass btn-block">
            <i class="fa fa-check-circle"></i> Cerrar Caja y Registrar Cuadratura
          </button>
        </form>
      </div>
    @else
      <!-- No Active Shift - Opening Form -->
      <div class="glass-panel">
        <h4 style="color: var(--primary);"><i class="fa fa-lock"></i> Abrir Turno de Caja</h4>
        <p class="text-muted">No existe una caja abierta para este turno. Ingrese el monto de apertura (sencillo/caja chica) para comenzar a vender.</p>
        <hr style="border-color: var(--glass-border);">

        <form action="{{ route('admin.finances.caja.abrir') }}" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label for="initial_amount">Monto Inicial de Caja *</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">$</span>
              </div>
              <input type="number" name="initial_amount" id="initial_amount" class="form-control" placeholder="Ej. 50000" value="50000" required>
              <div class="input-group-append">
                <span class="input-group-text" style="background: rgba(0,0,0,0.05); color: var(--text); border-color: rgba(0,0,0,0.1);">CLP</span>
              </div>
            </div>
            <small class="text-muted">Monto por defecto recomendado: $50.000 CLP.</small>
          </div>

          <button type="submit" class="btn btn-primary-glass btn-block">
            <i class="fa fa-unlock"></i> Abrir Caja e Iniciar Turno
          </button>
        </form>
      </div>
    @endif
  </div>

  <!-- History of shifts -->
  <div class="col-lg-7">
    <div class="glass-panel">
      <h4 style="color: var(--primary); margin-bottom: 20px;"><i class="fa fa-history"></i> Historial de Arqueos Recientes</h4>
      <div class="table-responsive">
        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th>Apertura</th>
              <th>Cierre</th>
              <th>Cajero</th>
              <th>Inicial</th>
              <th>Contado</th>
              <th>Diferencia</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todayShifts as $shift)
              <tr>
                <td>
                  {{ $shift->opened_at->format('d/m H:i') }}
                </td>
                <td>
                  {{ $shift->closed_at ? $shift->closed_at->format('d/m H:i') : 'Activo' }}
                </td>
                <td>{{ $shift->user->name }}</td>
                <td>${{ number_format($shift->initial_amount, 0, ',', '.') }}</td>
                <td>
                  {{ $shift->actual_amount ? '$' . number_format($shift->actual_amount, 0, ',', '.') : '-' }}
                </td>
                <td>
                  @if($shift->status === 'open')
                    <span class="text-muted">-</span>
                  @else
                    @if($shift->difference > 0)
                      <span class="diff-positive">+${{ number_format($shift->difference, 0, ',', '.') }}</span>
                    @elseif($shift->difference < 0)
                      <span class="diff-negative">${{ number_format($shift->difference, 0, ',', '.') }}</span>
                    @else
                      <span class="diff-zero">$0</span>
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted" style="padding: 20px;">No se registran arqueos previos en la base de datos.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-center mt-3">
        {{ $todayShifts->links() }}
      </div>
    </div>
  </div>

</div>
@endsection
