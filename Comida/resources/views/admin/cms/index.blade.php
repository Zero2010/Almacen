@extends('admin.layout')

@section('title', 'Gestionar Contenido Web')

@section('content')
<div class="page-header">
  <h1 class="page-title"><i class="fa fa-pencil-square-o" style="color: var(--primary);"></i> Contenido de Página Web (CMS)</h1>
</div>

<div class="glass-panel">
  <h4 style="color: var(--primary); margin-bottom: 10px;"><i class="fa fa-sliders"></i> Editor de Sitio Público</h4>
  <p class="text-muted">Modifique los textos, horarios y datos de contacto de la página de inicio pública.</p>
  <hr style="border-color: var(--glass-border); margin-bottom: 25px;">

  <form action="{{ route('admin.cms.update') }}" method="POST">
    @csrf

    <div class="row">
      <!-- Sliders text -->
      <div class="col-lg-6 mb-4">
        <h5 style="color: var(--text); font-weight: 600; margin-bottom: 15px;"><i class="fa fa-image"></i> Banners Promocionales (Carrusel)</h5>
        
        @foreach($cmsContents->filter(function($c) { return str_contains($c->key, 'hero_'); }) as $item)
          <div class="form-group mb-3">
            <label class="font-weight-bold" style="font-size: 0.85rem; color: var(--primary);">
              {{ $item->description }} (Llave: <code>{{ $item->key }}</code>)
            </label>
            @if(str_contains($item->key, '_desc'))
              <textarea name="{{ $item->key }}" class="form-control" rows="3" required>{{ $item->value }}</textarea>
            @else
              <input type="text" name="{{ $item->key }}" class="form-control" value="{{ $item->value }}" required>
            @endif
          </div>
        @endforeach
      </div>

      <!-- Contact details & Hours -->
      <div class="col-lg-6 mb-4">
        <h5 style="color: var(--text); font-weight: 600; margin-bottom: 15px;"><i class="fa fa-phone"></i> Contacto y Horarios de Atención</h5>
        
        @foreach($cmsContents->filter(function($c) { return !str_contains($c->key, 'hero_'); }) as $item)
          <div class="form-group mb-3">
            <label class="font-weight-bold" style="font-size: 0.85rem; color: var(--primary);">
              {{ $item->description }} (Llave: <code>{{ $item->key }}</code>)
            </label>
            @if($item->key === 'footer_about')
              <textarea name="{{ $item->key }}" class="form-control" rows="4" required>{{ $item->value }}</textarea>
            @else
              <input type="text" name="{{ $item->key }}" class="form-control" value="{{ $item->value }}" required>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    <div class="text-right">
      <button type="submit" class="btn btn-primary-glass">
        <i class="fa fa-save"></i> Publicar Cambios en Sitio Web
      </button>
    </div>
  </form>
</div>
@endsection
