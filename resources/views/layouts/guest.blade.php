<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="{{ asset('css/material-dashboard.css?v=2.1.1') }}" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{ asset('demo/demo.css') }}" rel="stylesheet" />
    
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
        
          <title>SB Admin 2 - Login</title>
        
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
          <script src="js/sb-admin-2.min.js"></script>
        
        </body>
        
        </html>
        

    </body>
</html>