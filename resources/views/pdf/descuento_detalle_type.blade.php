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

    @foreach ($bodies as $infos)
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
            <h5 class="font-10"><b>{{ $type->descripcion }}</b></h5>
            <h5 class="font-10"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
            <h5 class="font-10">
                <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
            </h5>

            <table class="table mt-2 table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="py-0 font-10"><small class="font-10">N°</small></th>
                        <th class="py-0 font-10"><small class="font-10">Nombre Completo</small></th>
                        @foreach ($type_detalles as $type_detalle)
                            <th class="py-0 font-10 text-right"><small class="font-10">{{ $type_detalle->descripcion }}</small></th>
                        @endforeach
                        <th class="py-0 font-10"><small class="font-10">Total Genrl.</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($infos as $info)
                        @if(!$info->nivel)
                            <tr>
                                <td class="py-0"><small class="font-9">{{ $info->count }}</small></td>
                                <td class="py-0"><small class="font-9">{{ $info->work ? $info->work->nombre_completo : '' }}</small></td>
                                @foreach ($info->type_detalles as $type_detalle)
                                    <td class="py-0 text-right">
                                        <small class="font-9">
                                            {{ $detalles->where("info_id", $info->id)->where("type_detalle_id", $type_detalle->id)->sum("monto") }}
                                        </small>
                                    </td>
                                @endforeach
                                <th class="py-0 text-right"><b class="font-9">{{ $info->total }}</b></th>
                            </tr> 
                        @else
                            <tr>
                                <th class="py-0 text-right" colspan="2"><b class="font-10"> TOTALES S/.</b></th>
                                @foreach ($info->body as $item)
                                    <th class="py-0 text-right"><b class="font-10">{{ $item->total }}</b></th>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
                
        </body>
    @endforeach
</html>