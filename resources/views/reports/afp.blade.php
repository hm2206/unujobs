<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/pdf.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/print/A4.css") }}" media="print">
        <title>REPORTE DE PAGO DE {{ $afp->nombre }} | {{ $mes}} - {{ $cronograma->año }}</title>
    </head>

    
    <body class="bg-white text-negro pr-3 uppercase">
        @php
            $num = 1;
        @endphp
        @foreach ($historial->chunk(58) as $page => $historial)
            <div class="page-only pt-2">       
                <table class="text-negro">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ asset($config->logo) }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-14 text-negro">{{ $config->nombre }}</b></div>
                                <div class="ml-1 font-12 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h5><b>REPORTE DE PAGO DE {{ $afp->nombre }}</b></h5>
                </div>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE: {{ $mes }} - {{ $cronograma->año }}</b>

                    <b class="font-10" style="float: right">Página N° {{ $page + 1 }}</b>
                </h5>

                <table class="table mt-2 table-bordered text-negro">
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
                                <th class="py-0 font-10 text-left">
                                    <span class="pl-2">{{ $money->num($num++) }}</span>
                                </th>
                                <th class="py-0 font-10 text-left uppercase">
                                    <span class="pl-1">{{ $history->work ? $history->work->nombre_completo : '' }}</span>    
                                </th>
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
                        @php
                            $cronograma->total_bruto += $historial->sum('total_bruto');
                            $cronograma->pension += $historial->sum('pension');
                            $cronograma->prima_seg += $historial->sum('prima_seg');
                            $cronograma->total_desct += $historial->sum('total_desct');
                        @endphp
                    </tbody>
                </table>
                <table class="table table-bordered text-negro">
                    <tr>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 text-center py-0">
                                TOTAL R.BRUTA S/. {{ $money->parseTo(round($cronograma->total_bruto, 2)) }}
                            </b>
                        </th>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 text-center py-0">
                                TOTAL F.PENSIÓN S/. {{ $money->parseTo(round($cronograma->pension, 2)) }}
                            </b>
                        </th>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 py-0 text-center">
                                TOTAL C.A S/. {{ $money->parseTo(round($cronograma->ca, 2)) }}
                            </b>
                        </th>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 text-center py-0">
                                TOTAL PRIMA SEG. S/. {{ $money->parseTo(round($cronograma->prima_seg, 2)) }}
                            </b>
                        </th>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 py-0 text-center">
                                TOTAL DSTCO. S/. {{ $money->parseTo(round($cronograma->total_desct), 2) }}
                            </b>
                        </th>
                    </tr>
                </table>
            </div>    
        @endforeach
    </body>

</html>