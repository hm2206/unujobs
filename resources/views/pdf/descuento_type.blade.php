<!DOCTYPE html>
<html lang="en">
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

    <body class="bg-white text-dark">
                
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

        <h5 class="font-12"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
        <h5 class="font-12">
            <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
        </h5>

        <table class="table mt-2 table-bordered table-sm">
            <thead>
                <tr>
                    <th class="py-0"><small>N°</small></th>
                    <th class="py-0"><small>Nombre Completo</small></th>
                    <th class="py-0"><small>N° de Documento</small></th>
                    @foreach ($types as $type)
                        <th class="py-0 text-right"><small>{{ $type->descripcion }}</small></th>
                    @endforeach
                    <th class="py-0 text-right"><small>Total</small></th>
                </tr>
            </thead>
            @foreach ($works as $work)
                <tbody>
                   @foreach ($work->tmp_infos as $info)
                        <tr>
                            <td class="py-0"><small class="font-12">{{ $info->count }}</small></td>
                            <td class="py-0"><small class="font-12">{{ $work->nombre_completo }}</small></td>
                            <td class="py-0"><small class="font-12">{{ $work->numero_de_documento }}</small></td>
                            @foreach ($info->descuentos as $descuento)
                                <td class="py-0 text-right"><small class="font-12">{{ $descuento->monto }}</small></td>
                            @endforeach
                            <th class="py-0 text-right"><small>{{ $info->total }}</small></th>
                        </tr>
                   @endforeach
                </tbody>
            @endforeach
        </table>
            
    </body>
</html>