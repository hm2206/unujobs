<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de cheques {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <style>
        
        .font-12 {
            font-size: 12px;
        }

    </style>

    <body class="bg-white text-dark">
                
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
                        <div class="ml-1 font-12 mt-3">
                            <h5><b>Planilla con Neto por Cheque Banco de la Nación</b></h5>
                        </div>
                    </th>
                </tr>
            </thead>
        </table>

        <br>

        <h5 class="font-12"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
        <h5 class="font-12">
            <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
        </h5>

        <table class="table mt-2 table-bordered">
            <thead>
                <tr>
                    <th class="py-1"><small>N°</small></th>
                    <th class="py-1"><small>N° de Documento</small></th>
                    <th class="py-1"><small>Nombre Completo</small></th>
                    <th class="py-1"><small>Numero de cuenta</small></th>
                </tr>
            </thead>
            @foreach ($works as $iter => $work)
                <tbody>
                   <tr>
                        <td class="py-1"><small class="font-12">{{ $iter + 1 }}</small></td>
                        <td class="py-1"><small class="font-12">{{ $work->numero_de_documento }}</small></td>
                        <td class="py-1"><small class="font-12">{{ $work->nombre_completo }}</small></td>
                        <td class="py-1"><small class="font-12">{{ $work->numero_de_cuenta }}</small></td>
                    </tr>
                </tbody>
            @endforeach
        </table>
            
    </body>
</html>