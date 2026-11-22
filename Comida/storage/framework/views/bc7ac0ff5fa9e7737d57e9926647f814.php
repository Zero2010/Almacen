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
  <link rel="shortcut icon" href="<?php echo e(asset('images/favicon.png')); ?>" type="image/x-icon">

  <title><?php echo $__env->yieldContent('title', 'Comida al paso'); ?></title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/bootstrap.css')); ?>" />

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="<?php echo e(asset('css/font-awesome.min.css')); ?>" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet" />
  <!-- responsive style -->
  <link href="<?php echo e(asset('css/responsive.css')); ?>" rel="stylesheet" />

  <?php echo $__env->yieldContent('styles'); ?>
</head>

<body class="<?php echo $__env->yieldContent('body_class'); ?>">

  <div class="hero_area">
    <div class="bg-box">
      <img src="<?php echo e(asset('images/hero-bg.jpg')); ?>" alt="Fondo hero">
    </div>
    <!-- header section starts -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
            <span>
              <?php echo e($cms['app_name'] ?? 'Comida al paso'); ?>

            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  mx-auto ">
              <li class="nav-item <?php echo e(Route::is('home') ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo e(route('home')); ?>">Inicio <?php if(Route::is('home')): ?><span class="sr-only">(current)</span><?php endif; ?></a>
              </li>
              <li class="nav-item <?php echo e(Route::is('menu') ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo e(route('menu')); ?>">Menú <?php if(Route::is('menu')): ?><span class="sr-only">(current)</span><?php endif; ?></a>
              </li>
              <li class="nav-item <?php echo e(Route::is('about') ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo e(route('about')); ?>">Nosotros <?php if(Route::is('about')): ?><span class="sr-only">(current)</span><?php endif; ?></a>
              </li>
              <li class="nav-item <?php echo e(Route::is('pedidos') ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo e(route('pedidos')); ?>">Pedidos <?php if(Route::is('pedidos')): ?><span class="sr-only">(current)</span><?php endif; ?></a>
              </li>
            </ul>
            <div class="user_option">
              <?php if(auth()->guard()->check()): ?>
                <span class="mr-3 text-white" style="font-size: 0.9rem;">
                  <i class="fa fa-user-circle"></i> <?php echo e(Auth::user()->name); ?>

                </span>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="order_online">
                  Intranet
                </a>
              <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="order_online">
                  Ingresar
                </a>
              <?php endif; ?>
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->

    <?php echo $__env->yieldContent('hero'); ?>
  </div>

  <?php echo $__env->yieldContent('content'); ?>

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
              <a href="https://maps.google.com/?q=<?php echo e(urlencode($cms['contact_address'] ?? 'Av. Providencia 1234, Santiago, Chile')); ?>" target="_blank">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
                <span>
                  <?php echo e($cms['contact_address'] ?? 'Av. Providencia 1234, Santiago, Chile'); ?>

                </span>
              </a>
              <a href="tel:<?php echo e(str_replace(' ', '', $cms['contact_phone'] ?? '+56987654321')); ?>">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <span>
                  Llamar <?php echo e($cms['contact_phone'] ?? '+56 9 8765 4321'); ?>

                </span>
              </a>
              <a href="mailto:<?php echo e($cms['contact_email'] ?? 'contacto@comidaalpaso.cl'); ?>">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <span>
                  <?php echo e($cms['contact_email'] ?? 'contacto@comidaalpaso.cl'); ?>

                </span>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 footer-col">
          <div class="footer_detail">
            <a href="<?php echo e(route('home')); ?>" class="footer-logo">
              <?php echo e($cms['app_name'] ?? 'Comida al paso'); ?>

            </a>
            <p>
              <?php echo e($cms['footer_about'] ?? 'Comida al Paso ofrece la mejor calidad en sándwiches tradicionales chilenos, completos italianos y pizzas familiares.'); ?>

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
            <?php echo e($cms['opening_days'] ?? 'Lunes a Viernes'); ?>

          </p>
          <p>
            <?php echo e($cms['opening_hours'] ?? '20.00 PM - 02.00 AM'); ?>

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
  <script src="<?php echo e(asset('js/jquery-3.4.1.min.js')); ?>"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <!-- bootstrap js -->
  <script src="<?php echo e(asset('js/bootstrap.js')); ?>"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
  </script>
  <!-- isotope js -->
  <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
  <!-- nice select -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
  <!-- custom js -->
  <script src="<?php echo e(asset('js/custom.js')); ?>"></script>

  <script>
    document.getElementById('displayYear').innerText = new Date().getFullYear();
  </script>

  <?php echo $__env->yieldContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\wamp64\www\Comida\resources\views/layouts/app.blade.php ENDPATH**/ ?>