<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar - Comida al Paso</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Bootstrap Core CSS -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
  
  <style>
    :root {
      --bg-dark: #0f1115;
      --primary: #ffbe33;
      --primary-hover: #e0a524;
      --glass-bg: rgba(255, 255, 255, 0.03);
      --glass-border: rgba(255, 255, 255, 0.08);
      --glass-focus-border: rgba(255, 190, 51, 0.4);
      --text-muted: #8a8f98;
    }
    
    body {
      font-family: 'Outfit', sans-serif;
      background: radial-gradient(circle at 10% 20%, var(--bg-dark) 0%, #1e222b 90%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      color: #fff;
      overflow: hidden;
    }

    .login-container {
      position: relative;
      width: 100%;
      max-width: 450px;
      padding: 20px;
    }

    .glass-card {
      background: var(--glass-bg);
      backdrop-filter: blur(16px) saturate(120%);
      -webkit-backdrop-filter: blur(16px) saturate(120%);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      padding: 40px 35px;
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo-section {
      text-align: center;
      margin-bottom: 30px;
    }

    .logo-section h2 {
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-top: 10px;
      margin-bottom: 5px;
      background: linear-gradient(45deg, #fff, var(--primary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .logo-section p {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin: 0;
    }

    .form-group {
      margin-bottom: 22px;
      position: relative;
    }

    .form-group label {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-bottom: 8px;
      display: block;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 1.1rem;
      transition: color 0.3s;
    }

    .form-input {
      width: 100%;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 10px;
      padding: 12px 15px 12px 45px;
      color: #fff;
      font-size: 0.95rem;
      transition: all 0.3s;
      outline: none;
    }

    .form-input:focus {
      border-color: var(--primary);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 0 10px rgba(255, 190, 51, 0.15);
    }

    .form-input:focus + i {
      color: var(--primary);
    }

    .btn-login {
      width: 100%;
      background: var(--primary);
      color: #222831;
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(255, 190, 51, 0.2);
    }

    .btn-login:hover {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 190, 51, 0.3);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .back-link {
      text-align: center;
      margin-top: 25px;
      font-size: 0.9rem;
    }

    .back-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }

    .back-link a:hover {
      color: var(--primary-hover);
      text-decoration: underline;
    }

    .credentials-box {
      margin-top: 20px;
      background: rgba(255, 255, 255, 0.02);
      border: 1px dashed var(--glass-border);
      border-radius: 12px;
      padding: 15px;
      font-size: 0.8rem;
    }

    .credentials-box h6 {
      margin-top: 0;
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 8px;
    }

    .credentials-box table {
      width: 100%;
      color: #ccc;
    }

    .credentials-box td {
      padding: 2px 0;
    }

    .alert {
      background: rgba(255, 51, 51, 0.15);
      border: 1px solid #ff3333;
      color: #ff9999;
      border-radius: 10px;
      padding: 12px 15px;
      font-size: 0.85rem;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="glass-card">
      <div class="logo-section">
        <span style="font-size: 2.5rem; color: var(--primary);"><i class="fa fa-cutlery"></i></span>
        <h2>Comida al Paso</h2>
        <p>Acceso al Panel de Control e Intranet</p>
      </div>

      @if($errors->any())
        <div class="alert">
          <i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <label for="email">Correo Electrónico</label>
          <div class="input-wrapper">
            <input type="email" name="email" id="email" class="form-input" placeholder="empleado@comida.cl" required autocomplete="email" autofocus>
            <i class="fa fa-envelope-o"></i>
          </div>
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="input-wrapper">
            <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
            <i class="fa fa-key"></i>
          </div>
        </div>

        <button type="submit" class="btn-login">
          <i class="fa fa-sign-in"></i> Iniciar Sesión
        </button>
      </form>

      <div class="credentials-box">
        <h6><i class="fa fa-info-circle"></i> Credenciales de Prueba:</h6>
        <table>
          <tr>
            <td><strong>Admin (CMS/Finanzas):</strong></td>
            <td>admin@comida.cl / comida2026</td>
          </tr>
          <tr>
            <td><strong>Cocina (Chef):</strong></td>
            <td>cocina@comida.cl / comida2026</td>
          </tr>
          <tr>
            <td><strong>Caja/Ventas (Garzón):</strong></td>
            <td>caja@comida.cl / comida2026</td>
          </tr>
          <tr>
            <td><strong>Despacho (Repartidor):</strong></td>
            <td>repartidor@comida.cl / comida2026</td>
          </tr>
        </table>
      </div>

      <div class="back-link">
        <a href="{{ route('home') }}"><i class="fa fa-arrow-left"></i> Volver a la Web Principal</a>
      </div>
    </div>
  </div>

</body>
</html>
