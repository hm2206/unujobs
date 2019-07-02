<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Reporte @yield('titulo')</title>
</head>
<body class="bg-white">
    <div class="container-fluid mt-2">
        
        <div class="row">
            <div class="col-xs ml-1 mb-0">
                <img src="{{ asset('img/logo.png') }}" width="60px" alt="">
            </div>
            <div class="ml-1">
                <h5 style="margin:0px;"><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></h5>
                <small>OFICINA GENERAL DE RECURSOS HUMANOS</small> <br>
                <small>OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</small>
            </div>
        </div>

        <h6 class="text-center mt-3"><b>@yield('subtitulo')</b></h6>

        @yield('content')

    </div>
</body>
</html>