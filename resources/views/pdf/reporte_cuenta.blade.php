<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de cuentas {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <style>
        
        .font-12 {
            font-size: 9px;
        }
    
    </style>

    @foreach ($bancos as $banco)

        @foreach ($banco->works as $works)
            <body class="bg-white text-dark">
                        
                <table class="text-dark">
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
                                    <h5><b>Planilla con Neto por Cuenta {{ $banco->nombre }}</b></h5>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>


                <h5 class="font-12"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-12">
                    <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
                </h5>

                <table class="table mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-1 font-12"><small class="text-center font-10"><b>N°</b></small></th>
                            <th class="py-1 font-12 text-center"><small class="font-10"><b>Apellidos y Nombres</b></small></th>
                            <th class="py-1 font-12"><small><b class="font-10">Numero de cuenta</b></small></th>
                            <th class="py-1 font-12 text-center"><small class="font-10"><b>Neto a Pagar</b></small></th>
                            <th class="py-1 font-12 text-center"><small class="font-10"><b>Firma</b></small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($works as $iter => $work)
                            @if(!$work->nivel)
                            <tr>
                                <td class="py-1"><small class="font-12 text-center">{{ $iter + 1 }}</small></td>
                                <td class="py-1"><small class="font-12">{{ $work->nombre_completo }}</small></td>
                                <td class="py-1 text-center"><small class="font-12 text-center">{{ $work->numero_de_cuenta }}</small></td>
                                <td class="py-1 text-center"><small class="font-12 text-center">{{ $work->total_neto }}</small></td>
                                <td class="py-1 text-center pt-1" style="padding-bottom: 1px;">
                                    <small class="font-12 text-center mt-5">
                                        {{ $work->numero_de_documento }}
                                    </small>
                                </td>
                            </tr>
                            @else
                                <tr>
                                    <th class="py-1 text-center" colspan="5">
                                        <b class="font-12 text-center">Total S/. {{ $work->total }}</b>
                                    </th>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                    
            </body>
        @endforeach

    @endforeach
</html>