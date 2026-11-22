@extends('admin.layout')

@section('title', 'Usuarios y Personal')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-users" style="color: var(--primary);"></i> Usuarios y Personal Operativo</h1>
  <button type="button" class="btn btn-primary-glass" onclick="openCreateModal()">
    <i class="fa fa-plus"></i> Nuevo Usuario / Empleado
  </button>
</div>

<div class="glass-panel">
  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>RUT</th>
          <th>Rol</th>
          <th>Contacto</th>
          <th>Comuna</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td style="vertical-align: middle;">
              <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 40px; height: 40px; border-radius: 50%; background-color: #fff7ed; color: var(--primary-dark); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; border: 1px solid #ffedd5;">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                  <h6 class="mb-0" style="font-weight: 700; color: var(--text);">{{ $user->name }}</h6>
                  <small class="text-muted">{{ $user->email }}</small>
                </div>
              </div>
            </td>
            <td style="vertical-align: middle;">{{ $user->rut ?? 'N/A' }}</td>
            <td style="vertical-align: middle;">
              <span class="badge-role badge-{{ $user->role }}" style="text-transform: uppercase;">
                {{ $user->role }}
              </span>
            </td>
            <td style="vertical-align: middle;">{{ $user->phone ?? 'N/A' }}</td>
            <td style="vertical-align: middle;">{{ $user->commune->name ?? 'N/A' }}</td>
            <td style="vertical-align: middle;">
              <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}" style="padding: 6px 10px; border-radius: 6px;">
                {{ $user->is_active ? 'Activo' : 'Desactivado' }}
              </span>
            </td>
            <td style="vertical-align: middle;">
              <div class="d-flex gap-2">
                <button type="button" class="btn" style="background: #f1f5f9; color: #475569; border-radius: 8px; border: none; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'" onclick="openEditModal({{ $user }})" title="Editar">
                  <i class="fa fa-pencil"></i>
                </button>
                @if($user->id !== Auth::id())
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario permanentemente?')" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background: #fee2e2; color: #dc2626; border-radius: 8px; border: none; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Eliminar">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                @else
                  <button type="button" class="btn" disabled style="background: #f8fafc; color: #cbd5e1; border-radius: 8px; border: none; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;" title="Eres tú">
                    <i class="fa fa-ban"></i>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted" style="padding: 20px;">No hay usuarios registrados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {{ $users->links() }}
  </div>
</div>

<!-- Create / Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="background-color: var(--panel-dark); border: 1px solid var(--glass-border); border-radius: 15px; color: var(--text);">
      <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
        <h5 class="modal-title" id="modalTitle" style="color: var(--primary); font-weight: bold;">Nuevo Usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="userForm">
        @csrf
        <div id="methodContainer"></div>

        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nombre Completo *</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Carlos Muñoz" required>
          </div>

          <div class="form-group mb-3">
            <label for="email">Correo Electrónico *</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="carlos@comida.cl" required>
          </div>

          <div class="form-group mb-3">
            <label for="password" id="passLabel">Contraseña *</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 6 caracteres">
            <small class="text-muted" id="passHelp">Deje en blanco para conservar la contraseña actual al editar.</small>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="rut">RUT Chileno</label>
                <input type="text" name="rut" id="rut" class="form-control" placeholder="Ej. 19.876.543-2">
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="role">Rol / Perfil *</label>
                <select name="role" id="role" class="form-control" required>
                  <option value="client">Cliente Registrado</option>
                  <option value="waiter">Cajero / Garzón</option>
                  <option value="chef">Chef / Cocinero</option>
                  <option value="delivery">Repartidor (Delivery)</option>
                  <option value="accountant">Contador</option>
                  <option value="admin">Administrador</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="phone">Teléfono</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Ej. +56999998888">
              </div>
            </div>
            <div class="col-6">
              <div class="form-group mb-3">
                <label for="commune_id">Comuna de Residencia</label>
                <select name="commune_id" id="commune_id" class="form-control">
                  <option value="">Ninguna</option>
                  @foreach($communes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="address">Dirección de Residencia</label>
            <input type="text" name="address" id="address" class="form-control" placeholder="Ej. Alameda 450">
          </div>

          <div class="form-group mb-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" checked>
              <label class="custom-control-label" for="is_active">Usuario activo (Permitir iniciar sesión y operar)</label>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
          <button type="button" class="btn btn-secondary-glass" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-glass">Guardar Usuario</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function openCreateModal() {
    const form = document.getElementById('userForm');
    form.action = "{{ route('admin.users.store') }}";
    document.getElementById('methodContainer').innerHTML = '';

    document.getElementById('modalTitle').innerText = 'Registrar Usuario / Personal';
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    
    const password = document.getElementById('password');
    password.value = '';
    password.required = true;
    
    document.getElementById('passLabel').innerText = 'Contraseña *';
    document.getElementById('passHelp').style.display = 'none';

    document.getElementById('rut').value = '';
    document.getElementById('role').value = 'client';
    document.getElementById('phone').value = '';
    document.getElementById('commune_id').value = '';
    document.getElementById('address').value = '';
    document.getElementById('is_active').checked = true;

    $('#userModal').modal('show');
  }

  function openEditModal(user) {
    const form = document.getElementById('userForm');
    form.action = `/intranet/usuarios/${user.id}`;
    document.getElementById('methodContainer').innerHTML = '@method("PUT")';

    document.getElementById('modalTitle').innerText = `Editar Usuario: ${user.name}`;
    document.getElementById('name').value = user.name;
    document.getElementById('email').value = user.email;
    
    const password = document.getElementById('password');
    password.value = '';
    password.required = false;
    
    document.getElementById('passLabel').innerText = 'Nueva Contraseña (Opcional)';
    document.getElementById('passHelp').style.display = 'block';

    document.getElementById('rut').value = user.rut || '';
    document.getElementById('role').value = user.role;
    document.getElementById('phone').value = user.phone || '';
    document.getElementById('commune_id').value = user.commune_id || '';
    document.getElementById('address').value = user.address || '';
    document.getElementById('is_active').checked = user.is_active;

    $('#userModal').modal('show');
  }
</script>
@endsection
