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
        <title>Reporte de Relacion de Personal de l planilla {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</title>
    </head>
    
    <body class="bg-white text-negro">
        @php
            $num = 1;
        @endphp
        @forelse($historial->chunk(60) as $num_page => $historial)
            <div class="page-only pt-2 pr-3 w-100">            
                <table class="text-negro">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ asset($config->logo ) }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="pl-1 font-14 text-negro">{{ $config->nombre }}</b></div>
                                <div class="ml-1 font-12 text-negro">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 font-12 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h5><b>RELACIÓN DEL PERSONAL MES DE {{ $mes }} - {{ $cronograma->año }}</b></h5>
                </div>


                <h5 class="font-12 uppercase">
                    <b>PLANILLA: 
                        @if ($cronograma->adicional)
                            ADICIONAL >> {{ $cronograma->numero }}
                        @else
                            {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                        @endif
                    </b>
                </h5>
                <h5 class="font-12 uppercase">
                    <b>MES DE {{ $mes}} - {{ $cronograma->año }}</b>

                    <b class="font-10" style="float: right">Página N° {{ $num_page + 1 }}</b>
                </h5>

                <table class="table w-100 mt-2 table-bordered text-negro uppercase">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" width="35%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>Categoria</b></th>
                            <th class="py-0 font-10 text-center pl-1 pr-1" width="7%"><b>Dedicacion</b></th>
                            <th class="py-0 font-10 text-center" width="20%"><b>Cargo</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>N° DNI</b></th>
                            <th class="py-0 font-10 text-center"><b>Sueldo Bruto</b></th>
                            <th class="py-0 font-10 text-center"><b>Sueldo a Pagar</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $history)
                            <tr>
                                <td class="py-0 font-10 text-left">
                                    <spna class="pl-1">{{ $money->num($num++) }}</spna>
                                </td>
                                <td class="py-0 font-10 text-left">
                                    <span class="pl-1">{{ $history->work->nombre_completo }}</span>
                                </td>
                                <td class="py-0 font-10 text-center">{{ $history->categoria->descripcion }}</td>
                                <td class="py-0 font-10 text-center">{{ $history->categoria->dedicacion }}</td>
                                <td class="py-0 font-10 text-center">{{ str_limit($history->perfil, 20) }}</td>
                                <td class="py-0 font-10 text-center">{{ $history->work->numero_de_documento }}</td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_bruto) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_neto) }}
                                    </b>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="py-0 text-right font-11" colspan="7">
                                <b class="font-11 text-right py-0 pr-1">
                                    @php
                                        $cronograma->total_bruto += $historial->sum("total_bruto");
                                        $cronograma->total_neto += $historial->sum("total_neto");
                                    @endphp
                                    Totales S/.&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $money->parseTo(round($historial->sum("total_bruto"), 2)) }}
                                </b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">
                                    {{ $money->parseTo(round($historial->sum("total_neto"))) }}
                                </b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                <table class="w-100 table table-bordered text-negro">
                    <tr>
                        <th class="py-0 text-right font-11">
                            <b class="font-11 text-right py-0 pr-2">
                                TOTAL SUELDO BRUTO S/.&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $money->parseTo(round($cronograma->total_bruto, 2)) }}
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </b>
                        </th>
                        <th class="py-0 text-center font-11">
                            <b class="font-11 py-0 text-center pr-1">
                                TOTAL SUELDO A PAGAR S/.&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $money->parseTo(round($cronograma->total_neto, 2)) }}
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </b>
                        </th>
                    </tr>
                </table>
            </div> 
        @empty
            <div>No hay Registros</div>
        @endforelse
    </body>

</html>