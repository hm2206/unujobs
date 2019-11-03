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
        <title>Reporte de descuentos del {{ $cronograma->mes }} - {{ $cronograma->año }}</title>
    </head>

    <style>

        html {
            margin: 0px;
            padding: 0px;
        }

        body {
            padding: 1.5em;
        }
        
        .font-12 {
            font-size: 12px;
        }
    
    </style>
    
@foreach ($bodies as $iter => $historial)
    <body class="bg-white text-negro">
                
        <table class="text-negro">
            <thead>
                <tr>
                    <th>
                        <img src="{{ public_path() . $config->logo }}" width="50" alt="">
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
            <b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b>
            <small style="float: right; padding-right: 0.5em;">página: {{ $iter + 1 }}</small>
        </h5>
        <h5 class="font-12">
            <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
        </h5>

        <table class="table mt-2 table-bordered table-sm">
            <thead>
                <tr>
                    <th class="py-0 pl-1"><small>N°</small></th>
                    <th class="py-0 pl-1"><small>Nombre Completo</small></th>
                    <th class="py-0 pl-1"><small>N° de Documento</small></th>
                    <th class="py-0 text-right pr-1"><small>Total Bruto</small></th>
                    <th class="py-0 text-right pr-1"><small>Base Imponible</small></th>
                    <th class="py-0 text-right pr-1"><small>Total Descuento</small></th>
                    <th class="py-0 text-right pr-1"><small>Total Neto</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial as $history)
                    <tr>
                        <th class="py-0 pl-1"><small class="font-12">{{ $history->count }}</small></th>
                        <th class="py-0 pl-1 uppercase"><small class="font-12">{{ $history->work ? $history->work->nombre_completo : '' }}</small></th>
                        <th class="py-0 pl-1"><small class="font-12">{{ $history->work ? $history->work->numero_de_documento : '' }}</small></th>
                        <th class="py-0 text-right pr-1"><small class="font-12">{{ $history->total_bruto }}</small></th>
                        <th class="py-0 text-right pr-1"><small class="font-12 text-right">{{ round($history->base, 2) }}</small></th>
                        <th class="py-0 text-right pr-1"><small class="font-12 text-right">{{ round($history->total_desct, 2) }}</small></th>
                        <th class="py-0 text-right">
                            <small class="font-12 text-right {{ $history->total_neto > 0 ? '' : 'text-danger' }}">
                                {{ round($history->total_neto, 2) }}
                            </small>
                        </th>
                    </tr>
                @endforeach
                <tr>
                    <th class="py-0 text-center" colspan="7"><b class="font-12">Total S/. {{ round($historial->sum('total_neto'), 2) }}</b></th>
                </tr>
            </tbody>
        </table>
            
    </body>
@endforeach
</html>