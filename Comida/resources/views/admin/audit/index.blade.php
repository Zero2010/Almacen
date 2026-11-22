@extends('admin.layout')

@section('title', 'Bitácora de Auditoría')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-history" style="color: var(--primary);"></i> Bitácora de Auditoría del Sistema</h1>
</div>

<div class="glass-panel">
  <h4 style="color: var(--primary); margin-bottom: 20px;"><i class="fa fa-list"></i> Historial de Actividades Internas</h4>
  
  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Fecha / Hora</th>
          <th>Operador</th>
          <th>Acción</th>
          <th>Descripción</th>
          <th>Dirección IP</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>
              <span class="font-weight-bold" style="color: var(--primary);">{{ $log->created_at->format('d/m/Y') }}</span>
              <br>
              <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
            </td>
            <td>
              @if($log->user)
                <strong>{{ $log->user->name }}</strong>
                <br>
                <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem;">{{ $log->user->role }}</small>
              @else
                <span class="text-muted">Sistema Automático</span>
              @endif
            </td>
            <td>
              <span class="badge badge-light" style="text-transform: uppercase; font-size: 0.75rem; font-family: monospace;">
                {{ $log->action }}
              </span>
            </td>
            <td style="max-width: 400px; line-height: 1.3;">{{ $log->description }}</td>
            <td>
              <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted" style="padding: 20px;">No se registran eventos de auditoría.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $logs->links() }}
  </div>
</div>
@endsection
