@extends('admin.layout')

@section('title', 'Dashboard Principal')

@section('styles')
<style>
  /* Welcome Banner - Light */
  .welcome-banner {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
    color: #064e3b;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  }
  .welcome-banner h2 {
    font-weight: 700;
    font-size: 2.1rem;
    margin-bottom: 8px;
    color: #065f46;
  }
  .welcome-banner p {
    color: #047857;
    font-size: 1.05rem;
    margin-bottom: 0;
    max-width: 600px;
  }
  .welcome-banner-illustration {
    position: absolute;
    right: 40px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 4.5rem;
    color: rgba(16, 185, 129, 0.15);
    pointer-events: none;
  }

  /* Metric Cards - Light */
  .metric-card {
    background: #ffffff !important;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    height: 100%;
  }
  
  .card-orange { border: 1px solid #fde68a !important; }
  .card-blue   { border: 1px solid #bfdbfe !important; }
  .card-red    { border: 1px solid #fecaca !important; }
  .card-green  { border: 1px solid #bbf7d0 !important; }
  .card-purple { border: 1px solid #e9d5ff !important; }

  .metric-card:hover {
    transform: translateY(-4px) scale(1.02);
  }
  .card-orange:hover { border-color: #f59e0b !important; box-shadow: 0 10px 25px rgba(245, 158, 11, 0.15); }
  .card-blue:hover   { border-color: #3b82f6 !important; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15); }
  .card-red:hover    { border-color: #ef4444 !important; box-shadow: 0 10px 25px rgba(239, 68, 68, 0.15); }
  .card-green:hover  { border-color: #10b981 !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15); }
  .card-purple:hover { border-color: #8b5cf6 !important; box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15); }

  .metric-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    transition: all 0.3s;
  }
  .card-orange:hover .metric-icon { background-color: var(--primary) !important; color: #fff !important; }
  .card-blue:hover .metric-icon   { background-color: var(--secondary) !important; color: #fff !important; }
  .card-red:hover .metric-icon    { background-color: var(--danger) !important; color: #fff !important; }
  .card-green:hover .metric-icon  { background-color: var(--success) !important; color: #fff !important; }
  .card-purple:hover .metric-icon { background-color: #8b5cf6 !important; color: #fff !important; }

  .metric-info h3 {
    margin: 0;
    font-size: 2.25rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
  }
  .metric-info p {
    margin: 5px 0 0 0;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    font-weight: 600;
  }

  /* Tables Usability */
  .badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
  }
  .status-pending { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
  .status-preparing { background: #dbeafe; color: #2563eb; border: 1px solid #bfdbfe; }
  .status-ready { background: #dcfce7; color: #059669; border: 1px solid #bbf7d0; }
  .status-delivering { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
  .status-completed { background: #dcfce7; color: #059669; border: 1px solid #bbf7d0; }
  .status-cancelled { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

  .table td, .table th {
    vertical-align: middle !important;
  }

  .quick-link-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.3s ease;
  }
  
  .quick-link-btn-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
  .quick-link-btn-warning:hover { background: #f59e0b; border-color: #d97706; color: #fff; }
  
  .quick-link-btn-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
  .quick-link-btn-danger:hover { background: #ef4444; border-color: #dc2626; color: #fff; }

  .quick-link-btn-success { background: #dcfce7; color: #059669; border: 1px solid #bbf7d0; }
  .quick-link-btn-success:hover { background: #10b981; border-color: #059669; color: #fff; }
</style>
@endsection

@section('content')

<!-- Welcome Banner (Usability: Orients user upon logging in) -->
<div class="welcome-banner">
  <h2>¡Hola de nuevo, {{ Auth::user()->name }}!</h2>
  <p>Aquí tienes un resumen de la actividad en cocina, despachos y finanzas para hoy. El sistema está operando con base de datos SQLite y bajo normativas tributarias chilenas.</p>
  <i class="fa fa-cutlery welcome-banner-illustration"></i>
</div>

<!-- 4 Key Stat Metrics Cards (Usability: Color-coded for quick visual reading) -->
<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
    <div class="metric-card card-orange">
      <div class="metric-info">
        <h3>{{ $ordersTodayCount }}</h3>
        <p>Pedidos de Hoy</p>
      </div>
      <div class="metric-icon" style="background: #fef3c7; color: #f59e0b;">
        <i class="fa fa-shopping-basket"></i>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
    <div class="metric-card card-blue">
      <div class="metric-info">
        <h3>{{ $pendingTasksCount }}</h3>
        <p>Tareas Pendientes</p>
      </div>
      <div class="metric-icon" style="background: #dbeafe; color: #3b82f6;">
        <i class="fa fa-tasks"></i>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
    <div class="metric-card card-red">
      <div class="metric-info">
        <h3>{{ $lowStockCount }}</h3>
        <p>Insumos Críticos</p>
      </div>
      <div class="metric-icon" style="background: #fee2e2; color: #ef4444;">
        <i class="fa fa-warning"></i>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="metric-card card-green">
      <div class="metric-info">
        <h3>${{ number_format($revenueToday, 0, ',', '.') }}</h3>
        <p>Ventas del Día</p>
      </div>
      <div class="metric-icon" style="background: #dcfce7; color: #10b981;">
        <i class="fa fa-money"></i>
      </div>
    </div>
  </div>
</div>

<!-- Salon, Reservas y Tareas de Sanitización -->
<div class="row mb-4">
  <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
    <div class="metric-card card-blue">
      <div class="metric-info">
        <h3>{{ $occupiedTablesCount }} / {{ $totalTablesCount }}</h3>
        <p>Mesas Ocupadas</p>
      </div>
      <div class="metric-icon" style="background: #dbeafe; color: #3b82f6;">
        <i class="fa fa-table"></i>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-md-6 mb-4 mb-xl-0">
    <div class="metric-card card-orange">
      <div class="metric-info">
        <h3>{{ $reservationsTodayCount }}</h3>
        <p>Reservas de Hoy</p>
      </div>
      <div class="metric-icon" style="background: #fef3c7; color: #f59e0b;">
        <i class="fa fa-calendar"></i>
      </div>
    </div>
  </div>

  <div class="col-xl-4 col-md-12">
    <div class="metric-card card-purple">
      <div class="metric-info">
        <h3>{{ $pendingCleaningCount }}</h3>
        <p>Tareas de Limpieza Hoy</p>
      </div>
      <div class="metric-icon" style="background: #f3e8ff; color: #8b5cf6;">
        <i class="fa fa-refresh"></i>
      </div>
    </div>
  </div>
</div>

<!-- Sales Trend Chart (Full Width - Usability: Core financial visual progress) -->
<div class="row mb-4">
  <div class="col-12">
    <div class="glass-panel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="bold" style="color: #0f172a; margin: 0;"><i class="fa fa-line-chart text-warning"></i> Tendencia de Ventas (Pesos CLP - Últimos 7 Días)</h5>
        @if(Auth::user()->hasRole(['admin', 'accountant']))
          <a href="{{ route('admin.finances.index') }}" class="btn btn-sm btn-link text-warning p-0 bold">Ver Libro Diario <i class="fa fa-arrow-right"></i></a>
        @endif
      </div>
      <div style="position: relative; height: 220px; width: 100%;">
        <canvas id="salesTrendChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Double Column Charts Section -->
<div class="row mb-4">
  <div class="col-lg-7 mb-4 mb-lg-0">
    <div class="glass-panel" style="height: 100%;">
      <h5 class="bold" style="color: #0f172a; margin-bottom: 20px;"><i class="fa fa-bar-chart text-info"></i> Ventas por Categoría de Menú (CLP)</h5>
      <div style="position: relative; height: 280px; width: 100%;">
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="glass-panel" style="height: 100%;">
      <h5 class="bold" style="color: #0f172a; margin-bottom: 20px;"><i class="fa fa-pie-chart text-primary"></i> Estados de Comandas y Pedidos</h5>
      <div style="position: relative; height: 280px; width: 100%;">
        <canvas id="statusChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Grid Lists Section -->
<div class="row">
  <!-- Recent Orders List (Usability: Clickable phone, direct actions, styled status badges) -->
  <div class="col-lg-7 mb-4 mb-lg-0">
    <div class="glass-panel" style="height: 100%;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="bold" style="color: #0f172a; margin: 0;"><i class="fa fa-shopping-cart text-success"></i> Pedidos Recientes</h5>
        <a href="{{ route('admin.orders.list') }}" class="quick-link-btn quick-link-btn-success">Control de Comandas <i class="fa fa-chevron-right"></i></a>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Pedido</th>
              <th>Cliente</th>
              <th>Tipo</th>
              <th>Monto</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $order)
              <tr>
                <td>
                  <span class="font-weight-bold" style="color: var(--primary-dark);">#{{ $order->id }}</span>
                  <br>
                  <small class="text-muted">{{ $order->created_at->format('H:i') }} hrs</small>
                </td>
                <td>
                  <strong style="color: #0f172a;">{{ $order->client_name }}</strong>
                  <br>
                  <a href="tel:{{ $order->client_phone }}" class="text-muted" style="font-size: 0.85rem;"><i class="fa fa-phone"></i> {{ $order->client_phone }}</a>
                </td>
                <td style="text-transform: capitalize;">
                  @if($order->type === 'delivery')
                    <span class="text-info"><i class="fa fa-motorcycle"></i> Despacho</span>
                  @elseif($order->type === 'pickup')
                    <span class="text-warning"><i class="fa fa-shopping-bag"></i> Retiro</span>
                  @else
                    <span class="text-success"><i class="fa fa-cutlery"></i> Mesa {{ $order->table_number }}</span>
                  @endif
                </td>
                <td class="font-weight-bold" style="color: #0f172a;">${{ number_format($order->total, 0, ',', '.') }}</td>
                <td>
                  <span class="badge-status status-{{ $order->status }}">
                    @if($order->status === 'pending') Pendiente
                    @elseif($order->status === 'preparing') Preparando
                    @elseif($order->status === 'ready') Listo
                    @elseif($order->status === 'delivering') En Reparto
                    @elseif($order->status === 'completed') Entregado
                    @else Cancelado
                    @endif
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 30px;">No hay pedidos registrados hoy.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Low Stock Alert & Quick Supply (Usability: Clickable supply links, warning status colors) -->
  <div class="col-lg-5">
    <div class="glass-panel" style="height: 100%;">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="bold" style="color: #0f172a; margin: 0;"><i class="fa fa-warning text-danger"></i> Alertas de Stock Crítico</h5>
        @if(Auth::user()->hasRole(['admin', 'chef']))
          <a href="{{ route('admin.ingredients.index') }}" class="quick-link-btn quick-link-btn-danger">Ver Inventario <i class="fa fa-chevron-right"></i></a>
        @endif
      </div>
      
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Insumo</th>
              <th>Mínimo</th>
              <th>Disponible</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            @forelse($lowStockIngredients as $ing)
              <tr>
                <td class="font-weight-bold" style="color: #0f172a;">{{ $ing->name }}</td>
                <td>{{ $ing->minimum_stock }} {{ $ing->unit }}</td>
                <td class="text-danger font-weight-bold" style="font-size: 1.05rem;">
                  {{ $ing->stock }} {{ $ing->unit }}
                </td>
                <td>
                  @if(Auth::user()->hasRole(['admin', 'accountant']))
                    <a href="{{ route('admin.finances.index') }}" class="quick-link-btn quick-link-btn-warning" title="Registrar compra en finanzas">
                      <i class="fa fa-plus-circle"></i> Comprar
                    </a>
                  @else
                    <span class="text-muted">Avisar Admin</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-success" style="padding: 40px 20px;">
                  <div style="font-size: 2.25rem; margin-bottom: 10px;"><i class="fa fa-check-circle-o"></i></div>
                  <strong style="font-size: 1rem; display: block; color: #059669;">¡Stock al Día!</strong>
                  <span class="text-muted" style="font-size: 0.85rem;">Todos los ingredientes disponen de stock suficiente.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // 1. Sales Trend Line Chart (Smooth Curve, gradient background)
  const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
  
  const trendLabels = [];
  const trendData = [];
  
  @foreach($salesTrend as $trend)
    trendLabels.push('{{ date("d/m", strtotime($trend->date)) }}');
    trendData.push({{ $trend->total }});
  @endforeach
  
  // Fallback if empty (populate last 7 days)
  if (trendLabels.length === 0) {
    for (let i = 6; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      trendLabels.push(d.getDate() + '/' + (d.getMonth() + 1));
      trendData.push(0);
    }
  }

  const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 220);
  trendGradient.addColorStop(0, 'rgba(245, 158, 11, 0.25)');
  trendGradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

  new Chart(trendCtx, {
    type: 'line',
    data: {
      labels: trendLabels,
      datasets: [{
        label: 'Ventas Diarias ($)',
        data: trendData,
        borderColor: '#d97706',
        borderWidth: 3,
        backgroundColor: trendGradient,
        fill: true,
        tension: 0.35,
        pointBackgroundColor: '#d97706',
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          },
          ticks: {
            color: '#64748b',
            font: {
              family: 'Outfit',
              size: 11
            }
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#64748b',
            font: {
              family: 'Outfit',
              size: 11
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // 2. Category Chart
  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  
  const categoryLabels = [];
  const categoryData = [];
  
  @foreach($salesByCategory as $item)
    categoryLabels.push('{{ $item->category }}');
    categoryData.push({{ $item->total }});
  @endforeach

  new Chart(categoryCtx, {
    type: 'bar',
    data: {
      labels: categoryLabels.length ? categoryLabels : ['Sin Datos'],
      datasets: [{
        label: 'Ventas por Categoría ($ CLP)',
        data: categoryData.length ? categoryData : [0],
        backgroundColor: 'rgba(59, 130, 246, 0.8)',
        borderColor: '#2563eb',
        borderWidth: 1.5,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          },
          ticks: {
            color: '#64748b',
            font: {
              family: 'Outfit',
              size: 11
            }
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#64748b',
            font: {
              family: 'Outfit',
              size: 11
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // 3. Status Chart
  const statusCtx = document.getElementById('statusChart').getContext('2d');
  
  const statusLabels = [];
  const statusData = [];
  const statusColors = [];

  const colorMapping = {
    pending: '#f59e0b',
    preparing: '#3b82f6',
    ready: '#10b981',
    delivering: '#8b5cf6',
    completed: '#059669',
    cancelled: '#ef4444'
  };

  const nameMapping = {
    pending: 'Pendiente',
    preparing: 'Preparando',
    ready: 'Listo en cocina',
    delivering: 'En reparto',
    completed: 'Entregado',
    cancelled: 'Cancelado'
  };

  @foreach($orderStatuses as $status)
    statusLabels.push(nameMapping['{{ $status->status }}'] || '{{ $status->status }}');
    statusData.push({{ $status->count }});
    statusColors.push(colorMapping['{{ $status->status }}'] || '#94a3b8');
  @endforeach

  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: statusLabels.length ? statusLabels : ['Sin Pedidos'],
      datasets: [{
        data: statusData.length ? statusData : [1],
        backgroundColor: statusColors.length ? statusColors : ['#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            color: '#475569',
            font: {
              family: 'Outfit',
              size: 12
            }
          }
        }
      },
      cutout: '65%'
    }
  });
</script>
@endsection
