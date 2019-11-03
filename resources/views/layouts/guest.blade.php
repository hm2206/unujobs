<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- CSS Files -->

    <link rel="stylesheet" href="{{ asset('css/sb-admin-2.css') }}">

    <title>RRHH @yield('titulo')</title>
</head>
    <body>
        <!DOCTYPE html>
        <html lang="en">
        
        <head>
        
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <meta name="description" content="">
          <meta name="author" content="">
        
        
          <!-- Custom fonts for this template-->
          <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
        
          <!-- Custom styles for this template-->
          <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
        
        </head>
        
        <body class="bg-gradient-success">
        
          <div class="container">
        
            <!-- Outer Row -->
                <div class="container mt-5">
                    @yield('content')
                <div>
           
        
          </div>
        
          <!-- Bootstrap core JavaScript-->
          <script src="{{ asset('js/app.js') }}"></script>
        
          <!-- Custom scripts for all pages-->
          <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
        
        </body>
        
        </html>
        

    </body>
</html>