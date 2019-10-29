<!DOCTYPE html>
<html lang="es_Es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de descuentos del {{ $cronograma->mes }} - {{ $cronograma->año }}</title>
    </head>

    <style>
        
        .font-12 {
            font-size: 12px;
        }
    
    </style>

    @foreach ($bodies as $historial)
        <body class="bg-white text-negro">
                    
            <table class="text-dark">
                <thead>
                    <tr>
                        <th>
                            <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                        </th>
                        <th>
                            <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                            <div class="ml-1 text-sm">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                            <div class="ml-1 text-sm">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                        </th>
                    </tr>
                </thead>
            </table>

            <br>

            <h5 class="font-10"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
            <h5 class="font-10">
                <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
            </h5>

            <table class="table mt-2 table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="py-0 font-10 pl-1 text-center"><small class="font-10">N°</small></th>
                        <th class="py-0 font-10 pl-1"><small class="font-10">Nombre Completo</small></th>
                        <th class="py-0 font-10 pl-1 text-center"><small class="font-10">N° de Documento</small></th>
                        <th class="py-0 font-10 text-right pr-1"><small class="font-10">{{ $type->descripcion }}</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historial as $history)
                        <tr>
                            <td class="py-0 text-center"><small class="font-9">{{ $history->count }}</small></td>
                            <td class="py-0 pl-1"><small class="font-9">{{ $history->work ? $history->work->nombre_completo : '' }}</small></td>
                            <td class="py-0 text-center"><small class="font-9">{{ $history->work ? $history->work->numero_de_documento : '' }}</small></td>
                            <td class="py-0 text-right pr-1"><small class="font-9">{{ $history->monto }}</small></td>
                        </tr> 
                    @endforeach
                    <tr>
                        <th class="py-0 text-right pr-1" colspan="4"><b class="font-10 pr-1">Total: S/. {{ $historial->sum('monto') }}</b></th>
                    </tr>
                </tbody>
            </table>
                
        </body>
    @endforeach
</html>