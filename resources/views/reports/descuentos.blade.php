<!DOCTYPE html>
<html lang="es_Es">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/pdf.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/print/A4.css") }}">
        <title>REPORTE DE LOS DESCUENTOS DE {{ $mes }} - {{ $cronograma->año }}</title>
    </head>

    
<body class="bg-white text-negro">
        
    @php
        $count = 1;
    @endphp

    @foreach ($historial->chunk(60) as $iter => $historial)
        <div class="page-only w-100 pt-2">
            <table class="text-negro">
                <thead>
                    <tr>
                        <th>
                            <img src="{{ asset($config->logo) }}" width="50" alt="">
                        </th>
                        <th>
                            <div><b>{{ $config->nombre }}</b></div>
                            <div class="ml-1 text-sm font-11">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                            <div class="ml-1 text-sm font-11">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                        </th>
                    </tr>
                </thead>
            </table>

            <br>

            <h5 class="font-12">
                <b class="uppercase">
                    PLANILLA:
                    @if ($cronograma->adicional)
                        ADICIONAL {{ $cronograma->numero }}
                    @else
                        {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                    @endif
                </b>
                <b style="float: right; padding-right: 0.5em;" class="font-11">Página: {{ $iter + 1 }}</b>
            </h5>
            <h5 class="font-12 uppercase">
                <b>MES DE: {{ $mes }} {{ $cronograma->año }}</b>
            </h5>

            <table class="table mt-2 table-bordered table-sm text-negro uppercase">
                <thead>
                    <tr>
                        <th class="py-0 pl-1"><b class="pl-1 font-10">N°</b></th>
                        <th class="py-0 pl-1"><b class="pl-1 font-10">Nombre Completo</b></th>
                        <th class="py-0 pl-1"><b class="pl-1 font-10">N° de Documento</b></th>
                        <th class="py-0 text-right"><b class="pr-1 font-10">Total Bruto</b></th>
                        <th class="py-0 text-right"><b class="pr-1 font-10">Base Imponible</b></th>
                        <th class="py-0 text-right"><b class="pr-1 font-10">Total Descuento</b></th>
                        <th class="py-0 text-right"><b class="pr-1 font-10">Total Neto</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historial as $history)
                        <tr>
                            <th class="py-0"><small class="font-12 pl-1">{{ $money->num($count) }}</small></th>
                            <th class="py-0"><small class="font-12 pl-1">{{ $history->work ? $history->work->nombre_completo : '' }}</small></th>
                            <th class="py-0"><small class="font-12 pl-1">{{ $history->work ? $history->work->numero_de_documento : '' }}</small></th>
                            <th class="py-0 text-right"><small class="font-12 pr-1">{{ $money->parseTo($history->total_bruto) }}</small></th>
                            <th class="py-0 text-right"><small class="font-12 text-right pr-1">{{ $money->parseTo(round($history->base, 2)) }}</small></th>
                            <th class="py-0 text-right"><small class="font-12 text-right pr-1">{{ $money->parseTo(round($history->total_desct, 2)) }}</small></th>
                            <th class="py-0 text-right">
                                <small class="font-12 text-right pr-1 {{ $history->total_neto > 0 ? '' : 'text-danger' }}">
                                    {{ $money->parseTo(round($history->total_neto, 2)) }}
                                </small>
                            </th>
                        </tr>
                        @php
                            $count++;
                        @endphp
                    @endforeach
                    <tr>
                        <th class="py-0 text-center" colspan="3"><b class="font-12">TOTALES</b></th>
                        <th class="py-0 text-right">
                            <b class="font-12 pr-1">S/. {{ $money->parseTo(round($historial->sum('total_bruto'), 2)) }}</b>
                        </th>
                        <th class="py-0 text-right">
                            <b class="font-12 pr-1">S/. {{ $money->parseTo(round($historial->sum('base'), 2)) }}</b>
                        </th>
                        <th class="py-0 text-right">
                            <b class="font-12 pr-1">S/. {{ $money->parseTo(round($historial->sum('total_desct'), 2)) }}</b>
                        </th>
                        <th class="py-0 text-right">
                            <b class="font-12 pr-1">S/. {{ $money->parseTo(round($historial->sum('total_neto'), 2)) }}</b>
                        </th>
                    </tr>
                </tbody>
            </table>

            @php
                $cronograma->total_bruto += $historial->sum('total_bruto');
                $cronograma->total_base += $historial->sum('base');
                $cronograma->total_desct += $historial->sum('total_desct');
                $cronograma->total_neto += $historial->sum('total_neto');
            @endphp

            <table class="table table-sm table-bordered text-negro">
                <tr>
                    <th class="py-0 text-right">
                        <b class="font-12 pr-1">TOTAL BRUTO S/. {{ $money->parseTo(round($cronograma->total_bruto, 2)) }}</b>
                    </th>
                    <th class="py-0 text-right">
                        <b class="font-12 pr-1">TOTAL BASE IMP. S/. {{ $money->parseTo(round($cronograma->total_base, 2)) }}</b>
                    </th>
                    <th class="py-0 text-right">
                        <b class="font-12 pr-1">TOTAL DESCUENTOS S/. {{ $money->parseTo(round($cronograma->total_desct, 2)) }}</b>
                    </th>
                    <th class="py-0 text-right">
                        <b class="font-12 pr-1">TOTAL NETO S/. {{ $money->parseTo(round($cronograma->total_neto, 2)) }}</b>
                    </th>
                </tr>
            </table>
        </div>    
    @endforeach
</body>
</html>