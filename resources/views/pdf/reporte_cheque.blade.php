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

    <style>

        * {
            margin: 0px;
            padding: 0px;
        }

        body {
            padding: 1.5em 1em;
        }
    
    </style>

    @foreach ($works->chunk(23) as $works)

            <body class="bg-white text-negro">
                        
                <table class="text-negro">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                <div class="ml-1 text-sm">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 text-sm">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                                <div class="ml-1 font-11 text-center mt-3">
                                    <h5><b>Planilla con Neto por Cheque</b></h5>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
                    <b style="float: right;">Página: {{ $num_page }}</b>
                </h5>

                <table class="table w-100 mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-0 pl-1 font-10"><small class="text-center font-10"><b>N°</b></small></th>
                            <th class="py-0 font-10 text-center" width="37%"><small class="font-10"><b>Nombre Completo</b></small></th>
                            @foreach ($bonificaciones as $bonificacion)
                                <th class="py-0 font-10 text-center">
                                    <small class="font-10"><b>{{ $bonificacion->descripcion }}</b></small>
                                </th>
                            @endforeach
                            <th class="py-0 font-10 text-center"><small class="font-10"><b>Neto a Pagar</b></small></th>
                            <th class="py-0 font-10 text-center"><small class="font-10"><b>Firma</b></small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($works as $work)
                            <tr>
                                <td class="py-0 pl-1"><small class="font-10 text-center">{{ $num_work }}</small></td>
                                <td class="py-0 pl-1"><small class="font-10">{{ $work->nombre_completo }}</small></td>
                                @foreach ($work->bonificaciones as $bonificacion)
                                    <td class="py-0 text-center pt-0">
                                        <small class="font-10 text-center">
                                            {{ $bonificacion->monto }}
                                        </small>
                                    </td>
                                @endforeach
                                <td class="py-0 text-center"><small class="font-10 text-center">{{ $work->total_neto }}</small></td>
                                <td class="py-0 text-center pt-1">
                                    <small class="font-10 text-center">
                                        {{ $work->numero_de_documento }}
                                    </small>
                                </td>
                            </tr>
                            @php
                                $num_work++;
                            @endphp
                        @endforeach
                        <tr>
                            <th class="py-1 font-11 text-center" colspan="2">
                                <small><b>&nbsp;</b></small>
                            </th>
                            @foreach ($bonificaciones as $bon)
                                @php

                                    $monto = $remuneraciones->where("type_remuneracion_id", $bon->id)
                                        ->whereIn("work_id", $works->pluck(['id']))
                                        ->sum('monto');

                                    if (isset($beforeBon[$bon->key])) {
                                        $beforeBon[$bon->key] += $monto;
                                    }else {
                                        $beforeBon[$bon->key] = $monto;
                                    }

                                @endphp
                                <th class="py-1 font-11 pt-1 text-center">
                                    <small><b>{{ $beforeBon[$bon->key] }}</b></small>
                                </th>
                            @endforeach
                            <th class="py-1 text-center font-11" colspan="2">
                                @php
                                    $beforeTotal += $works->sum('total_neto');
                                @endphp
                                <b class="font-11 text-center">Total S/. {{ $beforeTotal }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                    
            </body>
            @php
                $num_page++;
            @endphp
    @endforeach
</html>