<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte Mensual de boleta - {{ $cronograma->mes }}-{{ $cronograma->año }}</title>
    </head>

    @foreach ($infos->chunk(2) as $infos)
    <body class="bg-white text-negro" style="margin-top: -1.5em; padding: 0px; box-sizing:border-box; margin-bottom: -1.5em; height: 100%;">
        @foreach ($infos as $info)
            @php
                $work = $info->work;
            @endphp
                <div style="width: 105%; height: 50%; padding: 0px; margin-top: -0.8em; margin-left: -2em;">
                            
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                                </th>
                                <th>
                                    <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                    <div class="ml-1 text-sm"><b class="font-13">OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                                    <div class="ml-1 text-sm"><b class="font-13">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    
                    <div class="text-md uppercase mt-2 font-12">
                        <b class="13"> 
                            Actividad: {{ $info->meta ? $info->meta->actividadID : null }} {{ $info->meta ? $info->meta->actividad : null }}
                        </b>
                    </div>
                        
                    <div class="w-100 py-0">
                        <div class="boleta-header w-100 py-0">
                            <table class="table-boleta table-sm w-100">
                                <thead> 
                                    <tr>
                                        <th class="py-0 pl-3 font-12">Boleta de Pago N°:</th>
                                        <th class="py-0 font-12">{{ $info->num }}</th>
                                        <th class="py-0 font-12">Fecha de Ingreso:</th>
                                        <th class="py-0 font-12">{{ date($work->fecha_de_ingreso) }}</th>
                                        <th class="py-0 font-12" width="10%">D.N.I.</th>
                                        <th class="py-0 font-12">{{ $work->numero_de_documento }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 font-12">A.F.P:</th>
                                        <th class="py-0 font-12" colspan="3">{{ $work->afp ? $work->afp->nombre: null }}</th>
                                        <th class="py-0 font-12" width="10%">N° CUSSP</th>
                                        <th class="py-0 font-12">{{ $work->numero_de_cussp }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 font-12">Nombres y Apellidos</th>
                                        <th colspan="3" class="uppercase py-0 font-12">{{ $work->profesion }} {{ $work->nombre_completo }}</th>
                                        <th class="py-0 font-12" width="10%">N° ESSALUD</th>
                                        <th class="py-0 font-12">{{ $work->numero_de_essalud }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 font-12">Condición Laboral</th>
                                        <th colspan="3" class="uppercase py-0 font-12">{{ $info->cargo ? $info->cargo->descripcion : null }} - {{ $info->cargo ? $info->cargo->tag : '' }}</th>
                                        <th class="py-0 font-12" width="10%">Meta Siaf:</th>
                                        <th class="py-0 font-12">{{ $info->meta ? $info->meta->metaID : null }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 font-12 pl-3">Cargo</th>
                                        <th colspan="3" class="uppercase py-0 font-12">{{ $info->perfil }}</th>
                                        <th class="py-0 font-12" width="10%">Categoría</th>
                                        <th class="uppercase font-12 py-0">{{ $info->categoria ? $info->categoria->nombre : null }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
            
                        <table class="table-sm w-100">
                            <thead class="py-0 bbt-1 bbl-1 bbb-1">
                                <tr class="text-center py-0">
                                    <th class="py-0 font-13">
                                        <div class="py-0">
                                            INGRESOS
                                        </div>
                                    </th>
                                    <th class="py-0 bbl-1 font-13" colspan="2">
                                        <div class="py-0">
                                            RETENCIONES
                                        </div>
                                    </th>
                                    <th class="py-0 bbr-1 bbl-1 font-13">
                                        <div class="py-0">
                                            APORTES EMPLEADOR
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bbb-1 py-0">
                                <tr>
                                    <td class="bbr-1 p-relative bbb-1 py-0">
                                        <table class="w-100">
                                            <tbody>
                                                @foreach ($info->remuneraciones as $remuneracion)
                                                    @if (!$remuneracion->nivel)
                                                        <tr>
                                                            <th class="py-0 font-13 uppercase">
                                                                {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->key : null }}
                                                                <span>.-</span>
                                                                {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->alias : null }}
                                                            </th>
                                                            <th class="py-0 text-right font-13-5" width="5%">
                                                                {{ round($remuneracion->monto, 2) }}
                                                            </th>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <th class="py-0 font-13">{{ $remuneracion->nombre }}</th>
                                                            <th class="py-0 text-right font-13-5" width="5%">
                                                                <div class="bbt-1 font-13-5">{{ round($remuneracion->monto, 2) }}</div>
                                                            </th>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                @foreach ($info->descuentos as $body)
                                    <td class="p-relative bbb-1 py-0">
                                        <table class="w-100">
                                            <tbody class="py-0">
                                                @foreach ($body as $descuento)
                                                    @if (!$descuento->nivel)
                                                        <tr>
                                                            <th class="py-0 font-13">
                                                                {{ $descuento->typeDescuento ? $descuento->typeDescuento->key : null }}
                                                                <span>.-</span>
                                                                {{ $descuento->typeDescuento ? $descuento->typeDescuento->descripcion : null }}
                                                            </th>
                                                            <th class="py-0 text-right font-13-5" width="25%">{{ round($descuento->monto, 2) }}</th>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <th class="py-0 font-13">
                                                                {{ $descuento->nombre }}
                                                            </th>
                                                            <th class="py-0 text-right font-13-5" width="25%">
                                                                <div class="bbt-1 font-13-5">{{ round($descuento->monto, 2) }}</div>
                                                            </th>
                                                        </tr>
                                                        @for ($i = 0; $i < 24 - $body->count(); $i++)
                                                            <tr>
                                                                <td class="py-0 font-13">&nbsp;</td>
                                                                <td class="py-0 text-right font-13" width="25%">&nbsp;</td>
                                                            </tr>
                                                        @endfor
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                @endforeach
                                    <td class="bbl-1 p-relative py-0">
                                        <table class="p-absolute top-0 w-100">

                                            @foreach ($info->aportaciones as $aporte)
                                                @if (!$aporte->nivel)
                                                    <tr>
                                                        <th class="py-0 font-13">
                                                            {{ $aporte->typeDescuento ? $aporte->typeDescuento->key : '' }}
                                                            .-{{ $aporte->typeDescuento ? $aporte->TypeDescuento->descripcion : '' }}
                                                        </th>
                                                        <th class="py-0 text-right font-13-5">{{ round($aporte->monto, 2) }}</th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <th class="py-0 font-13">{{ $aporte->nombre }}</th>
                                                        <th class="py-0 text-right font-13-5">
                                                            <div class="bbt-1 font-13-5">
                                                                {{ round($aporte->monto, 2) }}
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-0 font-13">&nbsp;</td>
                                                        <td class="py-0 font-13">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-0 font-13">BASE IMPONIBLE</td>
                                                        <th class="py-0 font-13-5">{{ round($info->base, 2) }}</td>
                                                    </tr>
                                                    @for ($i = 0; $i < 16; $i++)
                                                        <tr>
                                                            <td class="font-13"></td>
                                                            <td class="font-13"></td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <th colspan="2" class="font-13 py-0 p-absolute bottom-0 text-center">
                                                            <div class="center">-----------------------------------</div>
                                                            <div class="center font-13">RECIBI CONFORME</div>
                                                        </th>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>  
            @endforeach
    </body>
    @endforeach
</html>