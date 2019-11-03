<!DOCTYPE html>
<html lang="es_Es">
    
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>
        @yield('titulo')
    </title>
</head>

<style>
    html {
        margin: 0px;
        padding: 0px;
    }

    body {
        padding: 1.5em;
    }
    
</style>

<body class="bg-white text-negro h-100">

    <div class="text-center"></div>
        
    <table>
        <thead>
            <tr>
                <th>
                    <img src="{{ public_path() . $config->logo }}" width="50" alt="">
                </th>
                <th>
                    <div><b class="font-14">{{ $config->nombre }}</b></div>
                    <div class="ml-1 text-sm"><b class="font-13">OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                    <div class="ml-1 text-sm"><b class="font-13">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                </th>
            </tr>
        </thead>
    </table>

    <h6 class="mt-1 text-center mb-2 uppercase"><b class="font-13"></b></h6>

    @yield('content')

</body>
</html>