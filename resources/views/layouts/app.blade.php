<!DOCTYPE html>
<html lang="es_Es">

<head>

  @php
      $config = App\Models\Config::first();
  @endphp

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Sistema Recursos Humanos @yield('titulo')</title>

  <!-- Custom fonts for this template-->
  {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css"> --}}
  <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" type="text/css">
  <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">

  <!-- Custom styles for this template-->

</head>

<body>

  <div id="app">
  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-text mx-3">SGRHP</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Inicio</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <menu-bar param="{{ auth()->user()->id }}"></menu-bar>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      

      <!-- Main Content -->
      <div id="content">

        <div>
            
            <authentication></authentication>
            <!-- Topbar -->
              <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">


                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                  <search></search>

                  <!-- Nav Item - Alerts -->
                  <notification></notification>

                  <div class="topbar-divider d-none d-sm-block main"></div>

                  <!-- Nav Item - User Information -->
                  <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="mr-2 d-none d-lg-inline text-gray-600 small capitalize">{{ auth()->user()->nombre_completo }}</span>
                      <img class="img-profile rounded-circle" src="{{ asset('img/perfil.png') }}">
                    </a>
                    <!-- Dropdown - User Information -->
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                      @csrf
                      {{-- <a class="dropdown-item" href="#">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                      </a>
                      <a class="dropdown-item" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                      </a>
                      <a class="dropdown-item" href="#">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Activity Log
                      </a> --}}
                      {{-- <div class="dropdown-divider"></div> --}}

                      <a class="dropdown-item" href="{{ url('/pdf/manual.pdf') }}" target="__blank">
                        <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                        Manual de usuario
                      </a> 

                      <button class="dropdown-item">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Cerrar Sesión
                      </button>
                    </form>
                  </li>

                </ul>

              </nav>
              <!-- End of Topbar -->

              <!-- Begin Page Content -->
              <div class="container-fluid">

                @if (session('warning'))
                  <div class="alert alert-warning">
                    {{ session('warning') }}
                  </div>
                @endif

                @yield('content')

              </div>
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; {{ $config->copyright }} - {{ $config->alias }} 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('js/app.js') }}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>


</body>

</html>
