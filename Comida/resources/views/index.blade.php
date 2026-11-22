@extends('layouts.app')

@section('title', 'Comida al paso - Inicio')

@section('hero')
  <!-- slider section -->
  <section class="slider_section ">
    <div id="customCarousel1" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="container ">
            <div class="row">
              <div class="col-md-8 col-lg-5">
                <div class="detail-box">
                  <h1>
                    {{ $cms['hero_slide_1_title'] ?? 'Nuestras Promociones 2 X 1 en Sandwich' }}
                  </h1>
                  <p>
                    {{ $cms['hero_slide_1_desc'] ?? 'Los sándwiches chilenos son una parte importante de la comida chilena y destacan por su variedad de sabores.' }}
                  </p>
                  <div class="btn-box">
                    <a href="{{ route('pedidos') }}" class="btn1">
                      Ordene Ahora
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item ">
          <div class="container ">
            <div class="row">
              <div class="col-md-8 col-lg-5">
                <div class="detail-box">
                  <h1>
                    {{ $cms['hero_slide_2_title'] ?? 'Nuestras Promociones 2 X 1 en Vianesas' }}
                  </h1>
                  <p>
                    {{ $cms['hero_slide_2_desc'] ?? 'Los completos chilenos son una versión única de los hot dogs en Chile, hechos con vienesas y pan de primera calidad.' }}
                  </p>
                  <div class="btn-box">
                    <a href="{{ route('pedidos') }}" class="btn1">
                      Ordene Ahora
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="container ">
            <div class="row">
              <div class="col-md-8 col-lg-5">
                <div class="detail-box">
                  <h1>
                    {{ $cms['hero_slide_3_title'] ?? 'Promociones en pizzas familiares' }}
                  </h1>
                  <p>
                    {{ $cms['hero_slide_3_desc'] ?? 'La pizza familiar es perfecta para compartir, hecha al momento con masa artesanal y los mejores ingredientes.' }}
                  </p>
                  <div class="btn-box">
                    <a href="{{ route('pedidos') }}" class="btn1">
                      Ordene Ahora
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <ol class="carousel-indicators">
          <li data-target="#customCarousel1" data-slide-to="0" class="active"></li>
          <li data-target="#customCarousel1" data-slide-to="1"></li>
          <li data-target="#customCarousel1" data-slide-to="2"></li>
        </ol>
      </div>
    </div>
  </section>
  <!-- end slider section -->
@endsection

@section('content')
  <!-- offer section -->
  <section class="offer_section layout_padding-bottom">
    <div class="offer_container">
      <div class="container ">
        <div class="row">
          <div class="col-md-6">
            <div class="box ">
              <div class="img-box">
                <img src="{{ asset('images/o1.jpg') }}" alt="Promoción Jueves">
              </div>
              <div class="detail-box">
                <h5>
                  Jueves Sabrosos
                </h5>
                <h6>
                  <span>20%</span> Descuento
                </h6>
                <a href="{{ route('pedidos') }}">
                  Pedir Ahora <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box ">
              <div class="img-box">
                <img src="{{ asset('images/o2.jpg') }}" alt="Promoción Pizza">
              </div>
              <div class="detail-box">
                <h5>
                  Días de Pizza
                </h5>
                <h6>
                  <span>15%</span> Descuento
                </h6>
                <a href="{{ route('pedidos') }}">
                  Pedir Ahora <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end offer section -->

  <!-- food section -->
  <section class="food_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
          Nuestro Menú popular
        </h2>
      </div>

      <ul class="filters_menu">
        <li class="active" data-filter="*">Todo</li>
        @foreach($categories as $category)
          <li data-filter=".cat-{{ $category->slug }}">{{ $category->name }}</li>
        @endforeach
      </ul>

      <div class="filters-content">
        <div class="row grid">
          @foreach($products as $product)
            <div class="col-sm-6 col-lg-4 all cat-{{ $product->category->slug }}">
              <div class="box">
                <div>
                  <div class="img-box">
                    <img src="{{ asset($product->image_path ?? 'images/default-food.png') }}" alt="{{ $product->name }}">
                  </div>
                  <div class="detail-box">
                    <h5>
                      {{ $product->name }}
                    </h5>
                    <p>
                      {{ Str::limit($product->description, 80) }}
                    </p>
                    <div class="options">
                      <h6>
                        ${{ number_format($product->price, 0, ',', '.') }} CLP
                      </h6>
                      <a href="{{ route('pedidos', ['product_id' => $product->id]) }}">
                        <i class="fa fa-shopping-cart text-white" aria-hidden="true"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="btn-box">
        <a href="{{ route('menu') }}">
          Ver Todo el Menú
        </a>
      </div>
    </div>
  </section>
  <!-- end food section -->

  <!-- about section -->
  <section class="about_section layout_padding-bottom">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="img-box">
            <img src="{{ asset('images/about-img.png') }}" alt="Sobre Nosotros">
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container">
              <h2>
                ¿Quiénes Somos?
              </h2>
            </div>
            <p>
              {{ $cms['footer_about'] ?? 'Comida al Paso ofrece la mejor calidad en sándwiches tradicionales chilenos, completos italianos y pizzas familiares, elaborados con ingredientes de primera y bajo estrictas normas de trazabilidad sanitaria.' }}
            </p>
            <a href="{{ route('about') }}">
              Leer Más
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->

  <!-- client section -->
  <section class="client_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container heading_center psudo_white_primary_mb_35">
        <h2>
          ¿Qué dicen nuestros clientes?
        </h2>
      </div>
      <div class="carousel-wrap row">
        <div class="owl-carousel client_owl-carousel">
          <div class="item">
            <div class="box">
              <div class="detail-box">
                <p>
                  "El Barros Luco es espectacular, abundante carne y el queso perfectamente derretido. La entrega fue súper rápida y la boleta electrónica llegó de inmediato al mail."
                </p>
                <h6>
                  Juan Pérez
                </h6>
                <p>Providencia</p>
              </div>
              <div class="img-box">
                <img src="{{ asset('images/client1.jpg') }}" alt="Cliente 1" class="box-img">
              </div>
            </div>
          </div>
          <div class="item">
            <div class="box">
              <div class="detail-box">
                <p>
                  "Los completos italianos son de otro planeta, la palta fresquísima. Se nota la trazabilidad sanitaria y la higiene en todo el proceso. ¡Súper recomendado!"
                </p>
                <h6>
                  María José Díaz
                </h6>
                <p>Ñuñoa</p>
              </div>
              <div class="img-box">
                <img src="{{ asset('images/client2.jpg') }}" alt="Cliente 2" class="box-img">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end client section -->
@endsection