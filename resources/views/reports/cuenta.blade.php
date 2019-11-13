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
        <title>REPORTE BANCOS {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <body class="bg-white text-negro">

        @php
            $num = 1;
        @endphp
   
        @foreach ($historial->chunk(40) as $page => $historial) 
            <div class="pt-2 page-only">
                <table class="text-negro">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ asset($config->logo) }}" width="40" alt="">
                            </th>
                            <th>
                                <div><b class="font-14 text-negro pl-1">{{ $config->nombre }}</b></div>
                                <div class="ml-1 font-12 text-negro">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 font-12 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-12 text-negro mt-2 uppercase">
                    <h5><b>Planilla con Neto por Cuenta BANCO DE LA NACIÓN</b></h5>
                </div>


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
                    <b>MES DE {{ $mes }} {{ $cronograma->año }}</b>
                    <b style="float: right;">Página: {{ $page + 1 }}</b>
                </h5>

                <table class="table mt-2 table-bordered text-negro uppercase">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" with="40%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" with="5%"><b>Numero de cuenta</b></th>
                            <th class="py-0 text-center font-10"><b>Neto a Pagar</b></th>
                            <th class="py-0 text-center font-10" with="5%"><b>DNI</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $iter => $history)
                            <tr>
                                <th class="py-2 font-11 text-center">{{ $money->num($num++) }}</th>
                                <th class="py-2 font-11 uppercase">{{ $history->work ? $history->work->nombre_completo : '' }}</th>
                                <th class="py-2 font-11 text-center">{{ $history->numero_de_cuenta }}</th>
                                <th class="py-2 font-11 text-center">{{ $history->total_neto }}</th>
                                <th class="py-2 font-11 text-center" style="padding-top: 1em;">
                                    <b class="text-center" style="padding-top: 1em;">
                                        {{ $history->work ? $history->work->numero_de_documento : '' }}
                                    </b>
                                </th>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="py-1 text-center font-10" colspan="5">
                                @php
                                    $totales += $historial->sum('total_neto');
                                @endphp
                                <b class="font-12 text-center">Total S/. {{ $money->parseTo(round($totales, 2)) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </body>
</html>