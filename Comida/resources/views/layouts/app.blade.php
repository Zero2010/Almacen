<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="comida al paso, sangucheria chile, completos, delivery chile" />
  <meta name="description" content="Comida al Paso - Exquisitos sándwiches, completos y pizzas tradicionales chilenas con despacho a domicilio." />
  <meta name="author" content="Comida al Paso" />
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

  <title>@yield('title', 'Comida al paso')</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
  <!-- responsive style -->
  <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />

  @yield('styles')
</head>

<body class="@yield('body_class')">

  <div class="hero_area">
    <div class="bg-box">
      <img src="{{ asset('images/hero-bg.jpg') }}" alt="Fondo hero">
    </div>
    <!-- header section starts -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="{{ route('home') }}">
            <span>
              {{ $cms['app_name'] ?? 'Comida al paso' }}
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  mx-auto ">
              <li class="nav-item {{ Route::is('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}">Inicio @if(Route::is('home'))<span class="sr-only">(current)</span>@endif</a>
              </li>
              <li class="nav-item {{ Route::is('menu') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('menu') }}">Menú @if(Route::is('menu'))<span class="sr-only">(current)</span>@endif</a>
              </li>
              <li class="nav-item {{ Route::is('about') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('about') }}">Nosotros @if(Route::is('about'))<span class="sr-only">(current)</span>@endif</a>
              </li>
              <li class="nav-item {{ Route::is('pedidos') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pedidos') }}">Pedidos @if(Route::is('pedidos'))<span class="sr-only">(current)</span>@endif</a>
              </li>
            </ul>
            <div class="user_option">
              @auth
                <span class="mr-3 text-white" style="font-size: 0.9rem;">
                  <i class="fa fa-user-circle"></i> {{ Auth::user()->name }}
                </span>
                <a href="{{ route('admin.dashboard') }}" class="order_online">
                  Intranet
                </a>
              @else
                <a href="{{ route('login') }}" class="order_online">
                  Ingresar
                </a>
              @endauth
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->

    @yield('hero')
  </div>

  @yield('content')

  <!-- footer section -->
  <footer class="footer_section">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-col">
          <div class="footer_contact">
            <h4>
              ¿Dónde nos ubican?
            </h4>
            <div class="contact_link_box">
              <a href="https://maps.google.com/?q={{ urlencode($cms['contact_address'] ?? 'Av. Providencia 1234, Santiago, Chile') }}" target="_blank">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
                <span>
                  {{ $cms['contact_address'] ?? 'Av. Providencia 1234, Santiago, Chile' }}
                </span>
              </a>
              <a href="tel:{{ str_replace(' ', '', $cms['contact_phone'] ?? '+56987654321') }}">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <span>
                  Llamar {{ $cms['contact_phone'] ?? '+56 9 8765 4321' }}
                </span>
              </a>
              <a href="mailto:{{ $cms['contact_email'] ?? 'contacto@comidaalpaso.cl' }}">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <span>
                  {{ $cms['contact_email'] ?? 'contacto@comidaalpaso.cl' }}
                </span>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 footer-col">
          <div class="footer_detail">
            <a href="{{ route('home') }}" class="footer-logo">
              {{ $cms['app_name'] ?? 'Comida al paso' }}
            </a>
            <p>
              {{ $cms['footer_about'] ?? 'Comida al Paso ofrece la mejor calidad en sándwiches tradicionales chilenos, completos italianos y pizzas familiares.' }}
            </p>
            <div class="footer_social">
              <a href="">
                <i class="fa fa-facebook" aria-hidden="true"></i>
              </a>
              <a href="">
                <i class="fa fa-twitter" aria-hidden="true"></i>
              </a>
              <a href="">
                <i class="fa fa-linkedin" aria-hidden="true"></i>
              </a>
              <a href="">
                <i class="fa fa-instagram" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 footer-col">
          <h4>
            Horario de Atención
          </h4>
          <p>
            {{ $cms['opening_days'] ?? 'Lunes a Viernes' }}
          </p>
          <p>
            {{ $cms['opening_hours'] ?? '20.00 PM - 02.00 AM' }}
          </p>
        </div>
      </div>
      <div class="footer-info">
        <p>
          &copy; <span id="displayYear"></span> Todos los derechos reservados.
        </p>
      </div>
    </div>
  </footer>
  <!-- footer section -->

  <!-- jQery -->
  <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <!-- bootstrap js -->
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
  </script>
  <!-- isotope js -->
  <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
  <!-- nice select -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
  <!-- custom js -->
  <script src="{{ asset('js/custom.js') }}"></script>

  <script>
    document.getElementById('displayYear').innerText = new Date().getFullYear();
  </script>

  @yield('scripts')

</body>

</html>
