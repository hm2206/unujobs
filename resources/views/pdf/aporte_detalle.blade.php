<!DOCTYPE html>
<html lang="es_Es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>REPORTE DE APORTACION DE {{ $type->descripcion }} | {{ $cronograma->mes }} - {{ $cronograma->año }}</title>
    </head>

    <style>

        html {
            padding: 0px;
            margin: 0px;
        }

        body {
            padding: 1em;
        }
        
        .font-12 {
            font-size: 12px;
        }
    
    </style>

    @foreach ($bodies as $historial)
        <body class="bg-white text-negro">
                    
            <table class="text-negro">
                <thead>
                    <tr>
                        <th>
                            <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                        </th>
                        <th>
                            <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                            <div class="ml-1 text-sm font-11">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                        </th>
                    </tr>
                </thead>
            </table>

            <h5 class="font-11 mt-2"><b>Declaración de Aportes a {{ $type->descripcion }}</b></h5>

            <h5 class="font-10">
                <b>
                    PLANILLA: 
                    @if ($cronograma->adicional)
                        ADICIONAL >> {{ $cronograma->numero }}
                    @else
                        {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                    @endif
                </b>
            </h5>
            <h5 class="font-10">
                <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
            </h5>

            <table class="table mt-2 table-bordered table-sm text-negro">
                <thead>
                    <tr>
                        <th class="py-0 font-10 pl-1 text-center" width="7%"><small class="font-10">N°</small></th>
                        <th class="py-0 font-10 pl-1 text-center" width="10%"><small class="font-10">Autogenerado <br/> Cta. Cte.Indicidual</small></th>
                        <th class="py-0 font-10 pl-1 text-center" width="50%"><small class="font-10">Apellidos y Nombres</small></th>
                        <th class="py-0 font-10 pl-1 text-center"><small class="font-10">Base Imponible</small></th>
                        <th class="py-0 font-10 text-center pr-1"><small class="font-10">Total <br> Aportación</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historial as $history)
                        <tr>
                            <th class="py-0 text-center"><small class="font-9">{{ $history->count }}</small></th>
                            <th class="py-0 pl-1"><small class="font-9">{{ $history->numero_de_essalud }}</small></th>
                            <th class="py-0 pl-1"><small class="font-9 uppercase">{{ $history->work ? $history->work->nombre_completo : '' }}</small></th>
                            <th class="py-0 text-center pr-1"><small class="font-9">{{ $money->parseTo($history->base) }}</small></th>
                            <th class="py-0 text-center pr-1"><small class="font-9">{{ $money->parseTo($history->monto) }}</small></th>
                        </tr> 
                    @endforeach
                    <tr>
                        <th class="py-0 text-right pr-1" colspan="4"><b class="font-10 pr-1">Total: S/. {{ $money->parseTo(round($historial->sum('base'), 2)) }}</b></th>
                        <th class="py-0 text-right pr-1" colspan="1"><b class="font-10 pr-1">{{ $money->parseTo(round($historial->sum('monto'), 2)) }}</b></th>
                    </tr>
                </tbody>
            </table>
                
        </body>
    @endforeach
</html>