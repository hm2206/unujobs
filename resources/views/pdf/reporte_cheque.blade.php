<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de cheques {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <body class="bg-white">
                
        <table>
            <thead>
                <tr>
                    <th>
                        <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                    </th>
                    <th>
                        <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                        <div class="ml-1 text-sm">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                        <div class="ml-1 text-sm">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                    </th>
                </tr>
            </thead>
        </table>

        <br>

        <table class="table">
            <thead>
                <tr>
                    <td>N°</td>
                    <td>N° de Documento</td>
                    <td>Nombre Completo</td>
                    <td>Numero de cuenta</td>
                </tr>
            </thead>
            @foreach ($works as $iter => $work)
                <tbody>
                    <tr>
                        <td>{{ $iter + 1 }}</td>
                        <td>{{ $work->numero_de_documento }}</td>
                        <td>{{ $work->nombre_completo }}</td>
                        <td>{{ $work->numero_de_cuenta }}</td>
                    </tr>
                </tbody>
            @endforeach
        </table>
            
    </body>
</html>