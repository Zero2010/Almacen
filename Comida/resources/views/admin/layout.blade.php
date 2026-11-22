<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') - Intranet Comida al Paso</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Bootstrap Core CSS -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
  
  <style>
    :root {
      --bg-dark: #f8fafc;
      --bg-darker: #ffffff;
      --panel-dark: #ffffff;
      --primary: #f59e0b;
      --primary-hover: #fbbf24;
      --primary-dark: #d97706;
      --primary-rgb: 245, 158, 11;
      --secondary: #3b82f6;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --text: #0f172a;
      --text-muted: #64748b;
      --glass-bg: #ffffff;
      --glass-border: #e2e8f0;
      --sidebar-width: 260px;
    }

    body {
      font-family: 'Outfit', sans-serif;
      background-color: var(--bg-dark);
      color: var(--text);
      margin: 0;
      overflow-x: hidden;
      min-height: 100vh;
      font-size: 1rem;
      line-height: 1.5;
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* Sidebar Styling */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: var(--sidebar-width);
      background-color: var(--bg-darker);
      border-right: 1px solid var(--glass-border);
      padding: 20px 10px;
      display: flex;
      flex-direction: column;
      z-index: 100;
      transition: all 0.3s;
      box-shadow: 2px 0 10px rgba(0,0,0,0.02);
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 15px;
      margin-bottom: 30px;
      color: #0f172a;
      font-size: 1.45rem;
      font-weight: 700;
      text-decoration: none !important;
    }

    .sidebar-brand i {
      color: var(--primary);
    }

    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
      flex-grow: 1;
      overflow-y: auto;
    }

    .sidebar-item {
      margin-bottom: 5px;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 13px 18px;
      color: var(--text-muted);
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 500;
      text-decoration: none !important;
      transition: all 0.3s;
    }

    .sidebar-link:hover {
      color: var(--primary-dark);
      background: #fffbeb;
    }

    .sidebar-link.active {
      color: #ffffff;
      background-color: var(--primary);
      box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.3);
      font-weight: 600;
    }

    .sidebar-link.active i {
      color: #ffffff;
    }

    .sidebar-link i {
      font-size: 1.25rem;
      width: 20px;
      text-align: center;
      transition: color 0.3s;
    }

    .sidebar-footer {
      padding: 15px;
      border-top: 1px solid var(--glass-border);
      margin-top: 15px;
    }

    /* Main Content */
    .main-wrapper {
      margin-left: var(--sidebar-width);
      width: calc(100% - var(--sidebar-width));
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: all 0.3s;
    }

    .topbar {
      width: 100%;
      height: 70px;
      border-bottom: 1px solid var(--glass-border);
      background-color: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 0 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 90;
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .content-area {
      padding: 30px;
      flex-grow: 1;
    }

    /* Glass Panels */
    .glass-panel {
      background: var(--panel-dark);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
      margin-bottom: 30px;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    .page-title {
      font-size: 1.85rem;
      font-weight: 700;
      margin: 0;
      color: var(--text);
    }

    /* Form Fields Styling */
    .form-control:not(select), textarea {
      background-color: #f8fafc !important;
      border: 1px solid #cbd5e1 !important;
      color: var(--text) !important;
      border-radius: 8px !important;
      padding: 12px 14px !important;
      font-size: 0.95rem !important;
      transition: all 0.3s ease !important;
    }

    select, select.form-control {
      background-color: #f8fafc !important;
      border: 1px solid #cbd5e1 !important;
      color: var(--text) !important;
      border-radius: 8px !important;
      padding: 8px 14px !important;
      font-size: 0.95rem !important;
      height: 46px !important;
      transition: all 0.3s ease !important;
      display: inline-block;
      vertical-align: middle;
    }

    select.form-control-sm {
      height: 38px !important;
      padding: 4px 10px !important;
      font-size: 0.85rem !important;
    }

    .form-control:focus, select:focus, textarea:focus {
      background-color: #ffffff !important;
      border-color: var(--primary) !important;
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
      outline: none !important;
    }

    .form-control::placeholder {
      color: #94a3b8 !important;
    }

    select option {
      background-color: #ffffff !important;
      color: var(--text) !important;
    }

    /* Table styling */
    .table {
      color: var(--text);
    }

    .table th {
      border-top: none;
      border-bottom: 2px solid #f1f5f9;
      color: var(--text-muted);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      padding: 12px 10px;
    }

    .table td {
      border-top: 1px solid #f1f5f9;
      vertical-align: middle;
      color: var(--text);
      padding: 14px 10px;
      font-size: 0.95rem;
    }

    .table-striped tbody tr:nth-of-type(odd) td {
      background-color: #f8fafc !important;
    }

    .table-hover tbody tr:hover td {
      background-color: #f1f5f9 !important;
      color: var(--text) !important;
    }

    /* Buttons */
    .btn-primary-glass {
      background-color: var(--primary);
      color: #ffffff;
      font-weight: 600;
      border: none;
      padding: 10px 22px;
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
      box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.25);
    }

    .btn-primary-glass:hover {
      background-color: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(var(--primary-rgb), 0.35);
      color: #fff;
    }

    .btn-secondary-glass {
      background-color: #f1f5f9;
      color: #475569;
      border: 1px solid #cbd5e1;
      padding: 10px 22px;
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s;
    }

    .btn-secondary-glass:hover {
      background-color: #e2e8f0;
      color: #0f172a;
      border-color: #94a3b8;
    }

    /* Close Button on modals & alerts */
    .close {
      color: #64748b !important;
      text-shadow: none !important;
      opacity: 0.6;
    }
    .close:hover {
      color: #0f172a !important;
      opacity: 1;
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .modal-header, .modal-footer {
      border-color: #e2e8f0 !important;
    }

    /* Roles badges */
    .badge-role {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .badge-admin { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .badge-chef { background: #dcfce7; color: #10b981; border: 1px solid #bbf7d0; }
    .badge-waiter { background: #fef3c7; color: #f59e0b; border: 1px solid #fde68a; }
    .badge-delivery { background: #dbeafe; color: #3b82f6; border: 1px solid #bfdbfe; }
    .badge-accountant { background: #f3e8ff; color: #8b5cf6; border: 1px solid #e9d5ff; }
    .badge-client { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #f8fafc;
    }
    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    @media (max-width: 991px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-wrapper {
        margin-left: 0;
      }
    }
    .btn-logout {
    border-radius: 20px;
    padding: 6px 16px;
    font-weight: 600;
    background: #fee2e2;
    color: #dc2626;
    border: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .btn-logout:hover {
    background: #fecaca;
    color: #b91c1c;
    transform: translateY(-1px);
  }

  .btn-action {
    border: none;
    background: #f1f5f9;
    color: #64748b;
    border-radius: 8px;
    padding: 5px 10px;
    transition: all 0.2s;
  }
  .btn-action:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: translateY(-1px);
  }
  .btn-action-danger {
    color: #ef4444;
  }
  .btn-action-danger:hover {
    background: #fee2e2;
    color: #dc2626;
  }
  .btn-action-success {
    color: #10b981;
  }
  .btn-action-success:hover {
    background: #d1fae5;
    color: #059669;
  }
</style>
  @yield('styles')
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
      <i class="fa fa-cutlery"></i>
      <span>Comida al Paso</span>
    </a>

    <ul class="sidebar-menu">
      <li class="sidebar-item">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
          <i class="fa fa-tachometer"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="sidebar-item">
        <a href="{{ route('admin.orders.list') }}" class="sidebar-link {{ Route::is('admin.orders.list') ? 'active' : '' }}">
          <i class="fa fa-shopping-basket"></i>
          <span>Pedidos y Cocina</span>
        </a>
      </li>

      <li class="sidebar-item">
        <a href="{{ route('admin.kitchen.index') }}" class="sidebar-link {{ Route::is('admin.kitchen.index') ? 'active' : '' }}" style="border-left: 3px solid var(--primary); background: rgba(245, 158, 11, 0.05);">
          <i class="fa fa-television" style="color: var(--primary);"></i>
          <span style="font-weight: 600; color: var(--primary-dark);">KDS Pantalla Cocina</span>
        </a>
      </li>

      @if(Auth::user()->hasRole(['admin', 'waiter']))
        <li class="sidebar-item">
          <a href="{{ route('admin.orders.create') }}" class="sidebar-link {{ Route::is('admin.orders.create') ? 'active' : '' }}">
            <i class="fa fa-plus-circle"></i>
            <span>Venta Presencial</span>
          </a>
        </li>
      @endif

      @if(Auth::user()->hasRole(['admin', 'waiter']))
        <li class="sidebar-item">
          <a href="{{ route('admin.finances.caja') }}" class="sidebar-link {{ Route::is('admin.finances.caja') ? 'active' : '' }}">
            <i class="fa fa-calculator"></i>
            <span>Control de Caja</span>
          </a>
        </li>
      @endif

      <li class="sidebar-item">
        <a href="{{ route('admin.tables.index') }}" class="sidebar-link {{ Route::is('admin.tables.index') ? 'active' : '' }}">
          <i class="fa fa-table"></i>
          <span>Control de Mesas</span>
        </a>
      </li>

      <li class="sidebar-item">
        <a href="{{ route('admin.waiters.index') }}" class="sidebar-link {{ Route::is('admin.waiters.index') ? 'active' : '' }}">
          <i class="fa fa-users"></i>
          <span>Garzones</span>
        </a>
      </li>

      @if(Auth::user()->hasRole(['admin', 'waiter']))
        <li class="sidebar-item">
          <a href="{{ route('admin.reservations.index') }}" class="sidebar-link {{ Route::is('admin.reservations.index') ? 'active' : '' }}">
            <i class="fa fa-calendar"></i>
            <span>Reservas</span>
          </a>
        </li>
      @endif

      <li class="sidebar-item">
        <a href="{{ route('admin.cleaning.index') }}" class="sidebar-link {{ Route::is('admin.cleaning.index') ? 'active' : '' }}">
          <i class="fa fa-refresh"></i>
          <span>Tareas de Limpieza</span>
        </a>
      </li>

      @if(Auth::user()->hasRole(['admin', 'accountant']))
        <li class="sidebar-item">
          <a href="{{ route('admin.finances.index') }}" class="sidebar-link {{ Route::is('admin.finances.index') ? 'active' : '' }}">
            <i class="fa fa-money"></i>
            <span>Finanzas y Libro</span>
          </a>
        </li>
      @endif

      @if(Auth::user()->hasRole(['admin', 'chef']))
        <li class="sidebar-item">
          <a href="{{ route('admin.ingredients.index') }}" class="sidebar-link {{ Route::is('admin.ingredients.index') ? 'active' : '' }}">
            <i class="fa fa-archive"></i>
            <span>Inventario Insumos</span>
          </a>
        </li>
      @endif

      @if(Auth::user()->hasRole(['admin', 'accountant', 'chef']))
        <li class="sidebar-item">
          <a href="{{ route('admin.suppliers.index') }}" class="sidebar-link {{ Route::is('admin.suppliers.index') ? 'active' : '' }}">
            <i class="fa fa-truck"></i>
            <span>Proveedores</span>
          </a>
        </li>
      @endif

      <!-- Admin Restricted Operations -->
      @if(Auth::user()->isAdmin())
        <li class="sidebar-item mt-4">
          <small class="text-uppercase text-muted px-3" style="font-size: 0.75rem; letter-spacing: 1px;">Mantenimiento</small>
        </li>

        <li class="sidebar-item">
          <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ Route::is('admin.products.index') || Route::is('admin.recipes.index') ? 'active' : '' }}">
            <i class="fa fa-cutlery"></i>
            <span>Platos y Recetas</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ Route::is('admin.categories.index') ? 'active' : '' }}">
            <i class="fa fa-tags"></i>
            <span>Categorías</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ Route::is('admin.users.index') ? 'active' : '' }}">
            <i class="fa fa-users"></i>
            <span>Usuarios y Personal</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a href="{{ route('admin.cms.index') }}" class="sidebar-link {{ Route::is('admin.cms.index') ? 'active' : '' }}">
            <i class="fa fa-pencil-square-o"></i>
            <span>Contenido Web (CMS)</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a href="{{ route('admin.audit') }}" class="sidebar-link {{ Route::is('admin.audit') ? 'active' : '' }}">
            <i class="fa fa-history"></i>
            <span>Bitácora Auditoría</span>
          </a>
        </li>
      @endif
    </ul>

    <div class="sidebar-footer">
      <div class="d-flex align-items-center gap-2">
        <span class="badge-role badge-{{ Auth::user()->role }}" style="font-size: 0.8rem; text-transform: uppercase;">
          {{ Auth::user()->role }}
        </span>
      </div>
    </div>
  </aside>

  <!-- Main Wrapper -->
  <div class="main-wrapper">
    
    <!-- Topbar -->
    <header class="topbar">
      <div>
        <button class="btn btn-light d-lg-none" onclick="toggleSidebar()" style="border: 1px solid #cbd5e1;"><i class="fa fa-bars"></i></button>
        <span class="text-muted d-none d-md-inline" id="live-time"></span>
      </div>
      
      <div class="d-flex align-items-center gap-3">
        @php
          $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
        @endphp
        
        <!-- Notification Bell -->
        <a href="{{ route('admin.kitchen.index') }}" class="position-relative d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
          <i class="fa fa-bell" style="font-size: 1.1rem;"></i>
          @if($pendingOrdersCount > 0)
            <span class="position-absolute d-flex align-items-center justify-content-center" style="top: -2px; right: -2px; width: 20px; height: 20px; background: var(--danger); color: white; font-size: 0.7rem; font-weight: bold; border-radius: 50%; box-shadow: 0 0 0 2px #fff;">
              {{ $pendingOrdersCount }}
            </span>
          @endif
        </a>

        <!-- User Profile -->
        <div class="d-flex align-items-center" style="background: #ffffff; border: 1px solid var(--glass-border); border-radius: 40px; padding: 4px 6px 4px 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
          <div style="display: flex; align-items: center; gap: 10px; margin-right: 15px;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #f59e0b 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3);">
              <i class="fa fa-user"></i>
            </div>
            <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
              {{ Auth::user()->name }}
            </span>
          </div>
          <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn-logout">
              <i class="fa fa-sign-out"></i> Salir
            </button>
          </form>
        </div>
      </div>
    </header>

    <!-- Content Area -->
    <main class="content-area">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #dcfce7; border: 1px solid #bbf7d0; color: #16a34a; border-radius: 12px; margin-bottom: 25px; padding: 15px;">
          <i class="fa fa-check-circle"></i> {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="top: 0; padding: 15px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; border-radius: 12px; margin-bottom: 25px; padding: 15px;">
          <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="top: 0; padding: 15px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @if($errors->has('stock'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="background: #fef3c7; border: 1px solid #fde68a; color: #d97706; border-radius: 12px; margin-bottom: 25px; padding: 15px;">
          <i class="fa fa-warning"></i> {{ $errors->first('stock') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="top: 0; padding: 15px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      @endif

      @yield('content')
    </main>

  </div>

  <!-- jQuery & Bootstrap Scripts -->
  <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
    }

    // Set live time in topbar
    function updateTime() {
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'America/Santiago' };
      const today = new Date();
      document.getElementById('live-time').innerText = today.toLocaleDateString('es-CL', options);
    }
    setInterval(updateTime, 1000);
    updateTime();
  </script>
  
  @yield('scripts')
</body>
</html>
