@extends('layouts.app')

@section('title', 'Nosotros - Comida al paso')
@section('body_class', 'sub_page')

@section('content')
  <!-- about section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="img-box">
            <img src="{{ asset('images/about-img.png') }}" alt="Nosotros imagen">
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container">
              <h2>
                Nosotros
              </h2>
            </div>
            <p>
              {{ $cms['footer_about'] ?? 'Comida al Paso ofrece la mejor calidad en sándwiches tradicionales chilenos, completos italianos y pizzas familiares, elaborados con ingredientes de primera y bajo estrictas normas de trazabilidad sanitaria.' }}
            </p>
            <p>
              Siempre nos preocupamos de mantener un balance perfecto entre calidad, precio y una excelente atención a nuestros clientes, garantizando la frescura y procedencia de cada ingrediente.
            </p>
            <a href="{{ route('menu') }}" class="btn1">
              Ver Menú
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->
@endsection