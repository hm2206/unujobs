<!DOCTYPE html>
<html lang="es_Es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>Reporte Planillas Meta x Metas </title>
</head>
<body class="bg-white">

    @foreach ($metas as $meta)

        @php
            $pagina++;
        @endphp

        <div>
            <div style="height:15%;">
                <table>
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
        
                <table class="table-sm w-100 uppercase">
                    <tr>
                        <th class="py-0" width="7%">SECTOR</th>
                        <th class="py-0">{{ $meta->sectorID }} {{ $meta->sector }}</th>
                        <th class="py-0" width="10%">UNIDAD EJECUTORA</th>
                        <th class="py-0">{{ $meta->unidadID }} {{ $meta->unidad_ejecutora }}</th>
                    </tr>
                    <tr>
                        <th class="py-0" width="7%">PLIEGO</th>
                        <th class="py-0">{{ $meta->pliegoID }} {{ $meta->pliego }}</th>
                        <th class="py-0" width="10%">FUNCION</th>
                        <th class="py-0">{{ $meta->funcionID }} {{ $meta->funcion }}</th>
                    </tr>
                    <tr>
                        <th class="py-0" width="7%">ACTIVIDAD</th>
                        <th class="py-0">{{ $meta->actividadID }} {{ $meta->actividad }}</th>
                        <th class="py-0" width="10%">PROGRAMA</th>
                        <th class="py-0">{{ $meta->programaID }} {{ $meta->programa }}</th>
                    </tr>
                    <tr>
                        <th class="py-0" colspan="2"><h5><b>PLANILLA UNICA DES PAGO NORMAL</b></h5></th>
                        <th class="py-0" width="10%">SUB PROGRAMA</th>
                        <th class="py-0">{{ $meta->subProgramaID }} {{ $meta->sub_programa }}</th>
                    </tr>
                </table>
        
                <table class="w-100 table-sm uppercase">
                    <tr>
                        <td class="py-0">OBS:</td>
                        <th><h5><b>MES DE {{ $meses[$meta->mes - 1] }}-{{ $meta->year }} </h5></b></th>
                        <td>META SIAF: {{ $meta->metaID }}</td>
                        <td></td>
                        <td></td>
                        <td>Página N° {{ $pagina }}</td>
                    </tr>
                </table>
            </div>
    
            <div style="height:80%;">

            @php
                $numero = 1;
            @endphp

            @foreach ($meta->works as $work)
                
                @foreach ($work->infos as $info)
                    <table class="table-sm w-100 uppercase">
                        <thead class="bbt-1 bbb-1">
                            <tr>
                                <td class="py-0">N° {{ $numero++ }} CODIGO {{ $work->id }} </td>
                                <td class="py-0">PLAZA {{ $work->plaza }}</td>
                                <td class="py-0">DEDICACION</td>
                                <td class="py-0">AFP {{ $work->afp ? $work->afp->descripcion : null }}</td>
                                <td class="py-0">N° CUSPP: {{ $work->numero_de_cuspp }}</td>
                                <td class="py-0">N° ESSALUD: {{ $work->numero_de_essalud }}</td>
                                <td class="py-0">OBS: {{ $info->observacion }}</td>
                            </tr>
                            <tr>
                                <td class="py-0" colspan="2">
                                    APELLIDOS Y NOMBRES: {{ $work->profesion }} {{ $work->nombre_completo }}
                                </td>
                                <td class="py-0" colspan="2">
                                    COND.LABORAL: {{ $info->cargo ? $info->cargo->descripcion : null }} - {{ $info->cargo ? $info->cargo->tag : null }}
                                </td>
                                <td class="py-0">CARGO: {{ $info->perfil }}</td>
                                <td class="py-0" colspan="2">DNI/LE: {{ $work->numero_de_documento }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    {{-- Tabla de remuneraciones --}}
                                    <table class="w-100">
                                        @foreach ($info->remuneraciones->chunk(3) as $row)
                                            <tr>
                                                @foreach ($row as $remuneracion)
                                                    <td class="py-0">
                                                        <table class="w-100">
                                                            <tr>
                                                                
                                                                @if (isset($remuneracion['nombre']))
                                                                    <th class="py-0" width="30%">
                                                                        <div class="bbt-2">
                                                                            {{ $remuneracion['nombre'] }}.-
                                                                        </div>
                                                                    </th>
                                                                @else
                                                                    <td class="py-0" width="30%">
                                                                        {{ $remuneracion->typeRemuneracion ?  
                                                                            $remuneracion->typeRemuneracion->key :  null}}.-
                                                                    </td>
                                                                @endif
                                                                
                                                                
                                                                @if (isset($remuneracion['nombre']))
                                                                    <th class="text-right py-0">
                                                                        <div class="bbt-2">
                                                                            {{ $remuneracion['monto'] }}
                                                                        </div>
                                                                    </th>
                                                                @else
                                                                    <td class="text-right py-0">
                                                                        {{ $remuneracion->monto }}
                                                                    </td>
                                                                @endif    
    
                                                            </tr>
                                                        </table>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td colspan="3">
                                    <table class="w-100">
                                        @foreach ($info->descuentos->chunk(4) as $row)
                                            <tr>
                                                @foreach ($row as $descuento)
                                                    <td class="py-0">
                                                        <table class="w-100">
                                                            <tr>
                                                                @if (isset($descuento['nombre']))
                                                                    <th class="py-0" width="30%">
                                                                        <div class="bbt-2">
                                                                            {{ $descuento['nombre'] }}.-
                                                                        </div>
                                                                    </th>
                                                                @else
                                                                    <td class="py-0" width="30%">
                                                                        <div>
                                                                            {{ $descuento->typeDescuento ?  
                                                                                $descuento->typeDescuento->key :  null}}.-
                                                                        </div>
                                                                    </td>
                                                                @endif
                                                                
                                                                
                                                                @if (isset($descuento['nombre']))
                                                                    <th class="text-right py-0">
                                                                        <div class="bbt-2">
                                                                            {{ $descuento['monto'] }}
                                                                        </div>
                                                                    </th>
                                                                @else
                                                                    <td class="text-right py-0">
                                                                        <div>
                                                                            {{ $descuento->monto }}
                                                                        </div>
                                                                    </td>
                                                                @endif    
                                                            </tr>
                                                        </table>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td colspan="2">
                                    <table class="w-100">
                                        <tr>
                                            <td class="py-0">BASE IMPONIBLE</td>
                                            <td class="py-0">{{ $info->base }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0">APORTE ESSALUD</td>
                                            <td class="py-0">{{ $info->essalud }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0">NETO A PAGAR</td>
                                            <td class="py-0">{{ $info->neto }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0"></td>
                                            <td class="py-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-0"></td>
                                            <td class="py-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-0"></td>
                                            <td class="py-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-0"></td>
                                            <td class="py-0"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-0"></td>
                                            <td class="py-0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="py-0">
                                                <div class="ml-3 pt-5">--------------------------------------</div>
                                                <div class="ml-5">FIRMAR</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-0" colspan="2">
                                                <div class="ml-3">
                                                    DNI:
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach

            @endforeach
            </div>

        </div>
    @endforeach

</body>
</html>