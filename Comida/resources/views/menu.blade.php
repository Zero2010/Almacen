@extends('layouts.app')

@section('title', 'Nuestro Menú - Comida al paso')
@section('body_class', 'sub_page')

@section('content')
  <!-- food section -->
  <section class="food_section layout_padding">
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
          Nuestro Menú
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
                      {{ $product->description }}
                    </p>
                    <div class="options">
                      <h6>
                        ${{ number_format($product->price, 0, ',', '.') }} CLP
                      </h6>
                      <a href="{{ route('pedidos', ['product_id' => $product->id]) }}" title="Ordenar ahora">
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
        <a href="{{ route('pedidos') }}">
          Realizar Pedido Online
        </a>
      </div>
    </div>
  </section>
  <!-- end food section -->
@endsection