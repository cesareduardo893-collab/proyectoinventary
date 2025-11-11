<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'I-DEB ERP')</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
  @yield('styles')
</head>
<body>
  <!-- Botón para móviles -->
  <button class="menu-toggle" id="mobile-toggle">
    <i class="fas fa-bars"></i>
  </button>

  <!-- HEADER -->
  <div class="header">
    <div class="header-logo">
      <img src="{{ asset('images/logo.jpeg') }}" alt="Logo I-DEB México">
      <p>I-DEB México</p>
    </div>
    
    <div class="user-info">
      @if(session()->has('name'))
        <span>Bienvenido, <strong>{{ session('name') }}</strong></span>
      @endif
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-light">Salir</button>
      </form>
    </div>
  </div>

  <!-- SIDEBAR -->
  <nav class="sidebar" id="sidebar">
    <!-- Botón retraíble -->
    <button class="toggle-btn" id="toggleSidebar">
      <i class="fas fa-angle-double-left"></i>
    </button>

    <ul class="sidebar-menu">
      @if (session('role') !== '1' && session('role') !== '2')
        <li><a href="{{ route('start.index') }}"><i class="fas fa-home"></i><span> Inicio</span></a></li>
        <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i><span> Usuarios</span></a></li>
      @endif
      <li><a href="{{ route('products.index') }}"><i class="fas fa-boxes"></i><span> Inventario</span></a></li>
      @if (session('role') === '0' || session('role') === '1')
        <li><a href="{{ route('categories.index') }}"><i class="fas fa-tags"></i><span> Categorías</span></a></li>
      @endif
      <li><a href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i><span> Proveedores</span></a></li>
      @if (session('role') === '0' || session('role') === '1')
        <li><a href="{{ route('projects.index') }}"><i class="fas fa-project-diagram"></i><span> Proyectos</span></a></li>
      @endif
      <!-- NUEVO MÓDULO DE UBICACIONES -->
      @if (session('role') === '0' || session('role') === '1')
        <li><a href="{{ route('locations.index') }}"><i class="fas fa-map-marker-alt"></i><span> Ubicaciones</span></a></li>
      @endif
      <li><a href="{{ route('entrances.index') }}"><i class="fas fa-sign-in-alt"></i><span> Entradas</span></a></li>
      <li><a href="{{ route('outputs.index') }}"><i class="fas fa-sign-out-alt"></i><span> Salidas</span></a></li>
      @if (session('role') === '0' || session('role') === '1')
        <li><a href="{{ route('loans.index') }}"><i class="fas fa-hand-holding"></i><span> Préstamos</span></a></li>
      @endif
      @if (session('role') !== '1' && session('role') !== '2')
        <li><a href="{{ route('databases.index') }}"><i class="fas fa-database"></i><span> Bases de datos</span></a></li>
      @endif
      <li class="logout">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit"><i class="fas fa-sign-out-alt"></i><span> Salir</span></button>
        </form>
      </li>
    </ul>
  </nav>

  <!-- CONTENIDO PRINCIPAL -->
  <div class="main-container" id="mainContainer">
    <!-- Mostrar mensajes de éxito -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <!-- Mostrar mensajes de error -->
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <!-- Mostrar errores de validación -->
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <!-- Contenido específico de cada página -->
    @yield('content')
  </div>

  <!-- FOOTER -->
  <footer class="footer" id="footer">
    <div class="footer-content">
      <div class="footer-section footer-contact">
        <h4>Contacto</h4>
        <p><i class="fas fa-map-marker-alt"></i> Fuente Alpaca #271, Villa Fontana, San Pedro Tlaquepaque, Jal.</p>
        <p><i class="fas fa-envelope"></i> contacto@idebmexico.com</p>
        <p><i class="fas fa-phone"></i> 33 1592 2676</p>
      </div>
      <div class="footer-section footer-logo">
        <img src="{{ asset('images/mexico.jpeg') }}" alt="Logo I-DEB México">
        <p>I-DEB México</p>
      </div>
      <div class="footer-section footer-social">
        <h4>Redes</h4>
        <div class="social-icons-container">
          <a href="https://www.facebook.com/IvDEB/?locale=es_LA" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="https://mx.linkedin.com/company/i-deb" target="_blank" aria-label="Linkedin"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-version">
      Versión 4.0
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="{{ asset('js/layout.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  
  <!-- Scripts específicos de cada página -->
  @yield('scripts')
</body>
</html>