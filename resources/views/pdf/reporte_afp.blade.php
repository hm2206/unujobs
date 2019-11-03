<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de pago afp {{ $afp->nombre }} | {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <style>
        * {
            padding: 0em;
            margin: 0px;
        }

        body {
            padding: 1.5em 1em;
        }
    </style>

        @foreach ($pages as $page => $historial)
            <body class="bg-white text-negro" style="">
                        
                <table class="text-dark">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . $config->logo }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-12 text-negro">{{ $config->nombre }}</b></div>
                                <div class="ml-1 font-11 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h5><b>REPORTE DE PAGO AFP {{ $afp->nombre }}</b></h5>
                </div>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE: {{ $meses[$cronograma->mes - 1] }} - {{ $cronograma->año }}</b>

                    <b class="font-10" style="float: right">Página N° {{ $page + 1 }}</b>
                </h5>

                <table class="table mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" width="35%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>N° Cussp</b></th>
                            <th class="py-0 text-center font-10"><b>R. Bruta</b></th>
                            <th class="py-0 text-center font-10"><b>F. Pensión</b></th>
                            <th class="py-0 text-center font-10"><b>C.A.</b></th>
                            <th class="py-0 text-center font-10"><b>Prima Seg.</b></th>
                            <th class="py-0 text-center font-10"><b>Total Dscto.</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $iter => $history)
                            <tr>
                                <th class="py-0 font-10 text-left pl-1">{{ $iter + 1 }}</th>
                                <th class="py-0 font-10 text-left pl-1 uppercase">{{ $history->work ? $history->work->nombre_completo : '' }}</th>
                                <th class="py-0 font-10 text-center pl-1 pr-1">{{ $history->numero_de_cussp }}</th>
                                <th class="py-0 font-10 text-center" style="padding-top: 1em;">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_bruto) }}
                                    </b>
                                </th>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->pension) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->ca) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->prima_seg) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_desct) }}
                                    </b>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="py-0 text-center font-11" colspan="2">&nbsp;</th>
                            <th class="py-0 text-center font-11" colspan="2">
                                <b class="font-11 text-center py-0">Totales S/. {{ $money->parseTo($historial->sum("total_bruto")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 text-center py-0">{{ $money->parseTo($historial->sum("pension")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($historial->sum("ca")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 text-center py-0">{{ $money->parseTo($historial->sum("prima_seg")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($historial->sum("total_desct")) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                    
            </body>
        @endforeach

</html>