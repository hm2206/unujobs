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
        <title>REPORTE CHEQUE {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    
    <body class="bg-white text-negro">
        @php
            $num = 1;
        @endphp
        @forelse ($historial->chunk(23) as $page => $historial)
            <div class="page-only pt-2 pr-3 uppercase">                
                <table class="text-negro">
                    <thead>
                        <tr>
                            <th class="p-relative pl-2">
                                <img src="{{ asset($config->logo) }}" class="p-absolute top-0 left-0" width="50" alt="">
                            </th>
                            <th class="pl-5">
                                <div><b class="font-14">{{ $config->nombre }}</b></div>
                                <div class="ml-1 text-sm font-12">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 text-sm font-12">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                                <div class="ml-1 font-12 text-center mt-3">
                                    <h5 class="text-center"><b>Planilla con Neto por Cheque</b></h5>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>


                <h5 class="font-12">
                    <b>
                        PLANILLA:
                        @if ($cronograma->adicional)
                            ADICIONAL >> {{ $cronograma->numero }}
                        @else
                            {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                        @endif
                    </b>
                </h5>
                <h5 class="font-12 uppercase">
                    <b>MES DE: {{ $mes }} {{ $cronograma->año }}</b>
                    <b style="float: right;">Página: {{ $page + 1 }}</b>
                </h5>

                <table class="table w-100 mt-2 table-bordered text-negro">
                    <thead>
                        <tr>
                            <th class="py-0 pl-1 font-11 text-center" with="3%">
                                <small class="text-center pl-1 font-10"><b>N°</b></small>
                            </th>
                            <th class="py-0 font-10 text-center" width="37%"><small class="font-10"><b>Apellidos y Nombres</b></small></th>
                            @foreach ($bonificaciones as $bonificacion)
                                <th class="py-0 font-11 text-center">
                                    <small class="font-11"><b>{{ $bonificacion->descripcion }}</b></small>
                                </th>
                            @endforeach
                            <th class="py-0 font-11 text-center"><small class="font-11"><b>Neto a Pagar</b></small></th>
                            <th class="py-0 font-11 text-center"><small class="font-11"><b>DNI</b></small></th>
                            <th class="py-0 font-11 text-center"><small class="font-11"><b>Firma</b></small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $history)
                            <tr>
                                <th class="py-0 pl-1 text-center"><b class="font-11 text-center pl-1">
                                    {{ $money->num($num++) }}</b>
                                </th>
                                <th class="py-0">
                                    <b class="font-10 pl-1 uppercase">{{ $history->work ? $history->work->nombre_completo : '' }}</b>
                                </th>
                                @foreach ($bonificaciones as $bonificacion)
                                    <th class="py-0 text-center pt-0">
                                        <b class="font-11 text-center">
                                            @php
                                                $monto = $remuneraciones->where('type_remuneracion_id', $bonificacion->id)
                                                    ->where('historial_id', $history->id)
                                                    ->sum('monto')
                                            @endphp
                                            {{ $money->parseTo(round($monto, 2)) }}
                                        </b>
                                    </th>
                                @endforeach
                                <th class="py-0 text-center"><b class="font-11 text-center">{{ round($history->total_neto, 2) }}</b></th>
                                <th class="py-0 text-center pt-1">
                                    <b class="font-11 text-center">
                                        {{ $history->work ? $history->work->numero_de_documento : '' }}
                                    </b>
                                </th>
                                <th class="py-1 font-11 pt-1 text-center bbt-1 bbl-1 bbr-1"></th>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="py-1 font-11 text-center" colspan="2">
                                <small><b>&nbsp;</b></small>
                            </th>
                            @foreach ($bonificaciones as $bon)
                                @php

                                    $monto = $remuneraciones->where("type_remuneracion_id", $bon->id)
                                        ->whereIn("historial_id", $historial->pluck(['id']))
                                        ->sum('monto');

                                    if (isset($beforeBon[$bon->key])) {
                                        $beforeBon[$bon->key] += $monto;
                                    }else {
                                        $beforeBon[$bon->key] = $monto;
                                    }

                                @endphp
                                <th class="py-1 font-11 pt-1 text-center">
                                    <small class="font-11"><b>{{ $money->parseTo(round($beforeBon[$bon->key], 2)) }}</b></small>
                                </th>
                            @endforeach
                            <th class="py-1 text-center font-11 bbr-1" colspan="3">
                                @php
                                    $beforeTotal += $historial->sum('total_neto');
                                @endphp
                                <b class="font-11 text-center">Total S/. {{ $money->parseTo(round($beforeTotal, 2)) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
            @empty
                <div>No hay registros</div>
            @endforelse
        </body>
</html>