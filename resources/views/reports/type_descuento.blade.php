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
        <link rel="stylesheet" href="{{ asset("/css/print/A4.css") }}" media="print">
        <title>Reporte de descuentos del {{ $mes }} - {{ $cronograma->año }}</title>
    </head>


<body class="bg-white text-negro">
    @php
        $num = 1;
    @endphp
    @forelse ($descuentos->chunk(58) as $descuentos)
        <div class="pl-2 pr-2 page-only">                
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

            <h5 class="font-12 uppercase">
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
            </h5>

            <table class="table mt-2 table-bordered table-sm text-negro uppercase">
                <thead>
                    <tr>
                        <th class="py-0 font-11 text-center"><b>N°</b></th>
                        <th class="py-0 font-11"><b class="pl-1">Nombre Completo</b></th>
                        <th class="py-0 font-11 text-center"><b class="pl-1">N° de Documento</b></th>
                        <th class="py-0 font-11 text-right"><b class="pr-1">{{ $type->descripcion }}</b></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($descuentos as $descuento)
                        @php
                            $work = $descuento->work;
                        @endphp
                        <tr>
                            <td class="py-0 text-center"><small class="font-11 pl-1">{{ $money->num($num++) }}</small></td>
                            <td class="py-0"><small class="font-11 pl-1">{{ $work->nombre_completo }}</small></td>
                            <td class="py-0 text-center"><small class="font-11">{{ $work->numero_de_documento }}</small></td>
                            <td class="py-0 text-right">
                                <small class="font-11 pr-1">{{ $money->parseTo(round($descuento->monto, 2)) }}</small>
                            </td>
                        </tr> 
                    @empty
                        <tr>
                            <td colspan="4">No hay Registros</td>
                        </tr>
                    @endforelse
                    <tr>
                        <th class="py-0 text-right pr-1" colspan="4">
                            <b class="font-10 pr-1">Total: S/. {{ $money->parseTo(round($descuentos->sum('monto'), 2)) }}</b>
                        </th>
                    </tr>
                </tbody>
            </table>

            @php
                $cronograma->total += $descuento->sum('monto');
            @endphp

            <table class="table table-sm table-bordered text-negro">
                <tr>
                    <th class="py-0 text-center">
                        <b class="font-12 pr-1">TOTALES S/. {{ $money->parseTo(round($cronograma->total, 2)) }}</b>
                    </th>
                </tr>
            </table>
            @empty
                <div>No hay registros</div>
            @endforelse
        </div>
    </body>
</html>