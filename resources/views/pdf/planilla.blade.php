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
<body class="bg-white text-negro" style="padding-top: 0px; margin-top: 0px;">


    @foreach ($meta->infos as $num => $item)
        <div>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                            </th>
                            <th>
                                <div class="font-13" style="margin: 0px;"><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                <div style="margin: 0px;" class="font-13 ml-1 text-sm"><b>OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                                <div  style="margin: 0px;" class="font-13 ml-1 text-sm"><b>OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                            </th>
                        </tr>
                    </thead>
                </table>
            
                <table class="table-sm w-100 uppercase mt-1">
                    <tr>
                        <th class="py-0 font-12" width="7%">SECTOR</th>
                        <th class="py-0 font-12">{{ $meta->sectorID }} {{ $meta->sector }}</th>
                        <th class="py-0 font-12" width="10%">UNIDAD EJECUTORA</th>
                        <th class="py-0 font-12">{{ $meta->unidadID }} {{ $meta->unidad_ejecutora }}</th>
                    </tr>
                    <tr>
                        <th class="py-0 font-12" width="7%">PLIEGO</th>
                        <th class="py-0 font-12">{{ $meta->pliegoID }} {{ $meta->pliego }}</th>
                        <th class="py-0 font-12" width="10%">FUNCION</th>
                        <th class="py-0 font-12">{{ $meta->funcionID }} {{ $meta->funcion }}</th>
                    </tr>
                    <tr>
                        <th class="py-0 font-12" width="7%">ACTIVIDAD</th>
                        <th class="py-0 font-12">{{ $meta->actividadID }} {{ $meta->actividad }}</th>
                        <th class="py-0 font-12" width="10%">PROGRAMA</th>
                        <th class="py-0 font-12">{{ $meta->programaID }} {{ $meta->programa }}</th>
                    </tr>
                    <tr>
                        <th class="py-0 font-12" colspan="2">
                            <h5><b>
                                PLANILLA UNICA DE PAGO: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                            </b></h5>
                        </th>
                        <th class="py-0 font-12" width="10%">SUB PROGRAMA</th>
                        <th class="py-0 font-12">{{ $meta->subProgramaID }} {{ $meta->sub_programa }}</th>
                    </tr>
                </table>
            
                <table class="w-100 table-sm uppercase">
                    <tr>
                        <td class="py-0 font-11">OBS:</td>
                        <th class="font-10"><h5><b>MES DE {{ $meses[$meta->mes - 1] }}-{{ $meta->year }} </h5></b></th>
                        <td class="font-11">META SIAF: {{ $meta->metaID }}</td>
                        <td class="font-11"></td>
                        <td class="font-11"></td>
                        <td class="font-11">Página N° {{ $pagina }}</td>
                    </tr>
                </table>
            </div>
        
            <div style="height:80%;">

                @php
                    $numero = 1;
                @endphp
                    
                @foreach ($item as $info)
                    @php
                        $work = $info->work;
                    @endphp
                    <table class="table-sm w-100 uppercase">
                        <thead class="bbt-1 bbb-1">
                            <tr>
                                <td class="py-0 font-11" colspan="2">N° {{ $numero++ }} CODIGO {{ $info->work_id }} </td>
                                <td class="py-0 font-11">PLAZA {{ $info->plaza }}</td>
                                <td class="py-0 font-11">DEDICACION</td>
                                <td class="py-0 font-11">AFP {{ $work->afp ? $work->afp->nombre : "" }}</td>
                                <td class="py-0 font-11" colspan="2">N° CUSPP: {{ $work->numero_de_cuspp }}</td>
                                <td class="py-0 font-11" colspan="2">N° ESSALUD: {{ $work->numero_de_essalud }}</td>
                                <td class="py-0 font-11" colspan="3">OBS: {{ $info->observacion }}</td>
                            </tr>
                            <tr>
                                <td class="py-0 font-11" colspan="4">
                                    APELLIDOS Y NOMBRES: {{ $work->profesion }} {{ $work->nombre_completo }}
                                </td>
                                <td class="py-0 font-11" colspan="3">
                                    COND.LABORAL: {{ $info->cargo ? $info->cargo->descripcion : "" }} - {{ $info->pap == 1 ? "Contratado" : "" }} {{ $info->pap == 0 ? "Nombrado" : "" }}
                                </td>
                                <td class="py-0 font-11" colspan="2">CARGO: {{ $info->perfil }}</td>
                                <td class="py-0 font-11" colspan="2">DNI/LE: {{ $work->numero_de_documento }}</td>
                                <td class="py-0 font-11" colspan="1">&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody class="bbb-1">
                            <tr>
                                @foreach ($info->remuneraciones as $columna)
                                    <td width="8%">
                                        @foreach ($columna as $remuneracion)
                                            <table class="w-100">
                                                @if (!$remuneracion->nivel)
                                                    <tr>
                                                        <td class="py-0 font-11">
                                                            {{   
                                                                $remuneracion->typeRemuneracion ?
                                                                    $remuneracion->typeRemuneracion->key
                                                                    : null
                                                            }}.-   
                                                        </td>
                                                        <td class="py-0 text-right font-11">
                                                            {{   
                                                                $remuneracion->monto
                                                            }}         
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td class="py-0">
                                                            <div class="font-11">{{ $remuneracion->key }}</div>
                                                        </td>
                                                        <td class="py-0 text-right">
                                                            <div class="bbt-1 font-11">{{  $remuneracion->monto }}</div>      
                                                        </td>
                                                    </tr>
                                                    @for ($i = 0; $i < 6 - $columna->count(); $i++)
                                                        <tr>
                                                            <td class="py-0 font-11">&nbsp;</td>
                                                            <td class="py-0 font-11 text-right">&nbsp;</td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            </table>
                                        @endforeach
                                    </td>
                                @endforeach
                                @foreach ($info->descuentos as $columna)
                                    <td width="8%">
                                        @foreach ($columna as $descuento)
                                            <table class="w-100">
                                                @if (!$descuento->nivel)
                                                    <tr>
                                                        <td class="py-0 font-11">
                                                            {{   
                                                                $descuento->typeDescuento ?
                                                                    $descuento->typeDescuento->key
                                                                    : null
                                                            }}.-   
                                                        </td>
                                                        <td class="py-0 text-right font-11">
                                                            {{   
                                                                $descuento->monto
                                                            }}         
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td class="py-0">
                                                            <div class="font-11">{{ $descuento->key }}</div>
                                                        </td>
                                                        <td class="py-0 text-right">
                                                            <div class="bbt-1 font-11">{{  $descuento->monto }}</div>      
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-0">
                                                            <div class="font-11">TOTAL</div>
                                                        </td>
                                                        <td class="py-0 text-right">
                                                            <div class="bbt-1 font-11">{{  $info->neto }}</div>      
                                                        </td>
                                                    </tr>
                                                    @for ($i = 0; $i < 5 - $columna->count(); $i++)
                                                        <tr>
                                                            <td class="py-0 font-11">&nbsp;</td>
                                                            <td class="py-0 font-11 text-right">&nbsp;</td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            </table>
                                        @endforeach
                                    </td>
                                @endforeach
                                <td width="8%">
                                    @foreach ($info->aportaciones as $iter => $aport)
                                        <table class="w-100">
                                            @if (!$aport->nivel)
                                                <tr>
                                                    <td class="py-0 font-11">
                                                        {{   
                                                            $aport->typeDescuento ?
                                                                $aport->typeDescuento->key
                                                                : null
                                                        }}.-   
                                                    </td>
                                                    <td class="py-0 text-right font-11">
                                                        {{   
                                                            $aport->monto
                                                        }}         
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="py-0">
                                                        <div class="font-11">{{ $aport->key }}</div>
                                                    </td>
                                                    <td class="py-0 text-right">
                                                        <div class="bbt-1 font-11">{{ $aport->monto }}</div>      
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="py-0 font-11">&nbsp;</td>
                                                    <td class="py-0 font-11 text-right">&nbsp;</td>
                                                </tr>
                                            @endif
                                        </table>
                                    @endforeach
                                </td>
                                <td width="12%">
                                    <table class="w-100">
                                        @for ($i = 0; $i < 4; $i++) 
                                            <tr>
                                                <td class="py-0" width="100%">
                                                    &nbsp;
                                                </td>
                                            </tr>
                                        @endfor
                                        <tr>
                                            <td class="py-0" width="100%">
                                                <div class="ml-3">------------------------------------------------</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-0" width="100% text-center">
                                                <div class="ml-3 font-11 text-center">FIRMA</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-0" width="100%">
                                                <div class="ml-3 font-11">
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
            </div>
        </div>
    @endforeach

</body>
</html>