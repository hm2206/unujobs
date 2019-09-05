<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte Mensual de boleta - {{ $cronograma->mes }}-{{ $cronograma->year }}</title>
    </head>

    @foreach ($infos as $info)
        @php
            $work = $info->work;
        @endphp
        <body class="bg-white text-negro">
                <div class="">
                            
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                                    </th>
                                    <th>
                                        <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                        <div class="ml-1 text-sm"><b class="font-11">OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                                        <div class="ml-1 text-sm"><b class="font-11">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    
                        <div class="text-md uppercase mt-2">
                            <b> 
                                Actividad: {{ $info->meta ? $info->meta->actividadID : null }} {{ $info->meta ? $info->meta->actividad : null }}
                            </b>
                        </div>
                        
                        <div class="w-100">
                            <div class="boleta-header w-100">
                                <table class="table-boleta table-sm w-100">
                                    <thead> 
                                        <tr>
                                            <td class="py-0 pl-3 pt-1 font-10">Boleta de Pago N°:</td>
                                            <td class="py-0 pt-1 font-10">{{ $info->num }}</td>
                                            <td class="py-0 pt-1 font-10">Fecha de Ingreso:</td>
                                            <td class="py-0 pt-1 font-10">{{ date($work->fecha_de_ingreso) }}</td>
                                            <td class="py-0 pt-1 font-10" width="10%">D.N.I.</td>
                                            <td class="py-0 pt-1 font-10">{{ $work->numero_de_documento }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 pl-3 font-10">A.F.P:</td>
                                            <td class="py-0 font-10" colspan="3">{{ $work->afp ? $work->afp->nombre: null }}</td>
                                            <td class="py-0 font-10" width="10%">N° CUSSP</td>
                                            <td class="py-0 font-10">{{ $work->numero_de_cussp }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 pl-3 font-10">Nombres y Apellidos</td>
                                            <td colspan="3" class="uppercase py-0 font-10">{{ $work->profesion }} {{ $work->nombre_completo }}</td>
                                            <td class="py-0 font-10" width="10%">N° ESSALUD</td>
                                            <td class="py-0 font-10">{{ $work->numero_de_essalud }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 pl-3 font-10">Condición Laboral</td>
                                            <td colspan="3" class="uppercase py-0 font-10">{{ $info->cargo ? $info->cargo->descripcion : null }} - {{ $info->cargo ? $info->cargo->tag : '' }}</td>
                                            <td class="py-0 font-10" width="10%">Meta Siaf:</td>
                                            <td class="py-0 font-10">{{ $info->meta ? $info->meta->metaID : null }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-0 font-10 pl-3 pb-1">Cargo</td>
                                            <td colspan="3" class="uppercase py-0 pb-1 font-10">{{ $info->perfil }}</td>
                                            <td class="py-0 pb-1 font-10" width="10%">Categoría</td>
                                            <td class="uppercase font-10 py-0 pb-1">{{ $info->categoria ? $info->categoria->nombre : null }}</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
            
                            <table class="table-sm mt-2 w-100">
                                <thead class="py-0 bbt-1 bbl-1 bbb-1">
                                    <tr class="text-center py-0">
                                        <th class="py-0 font-10">
                                            <div class="py-0">
                                                INGRESOS
                                            </div>
                                        </th>
                                        <th class="py-0 bbl-1 font-10" colspan="2">
                                            <div class="py-0">
                                                RETENCIONES
                                            </div>
                                        </th>
                                        <th class="py-0 bbr-1 bbl-1 font-10">
                                            <div class="py-0">
                                                APORTES EMPLEADOR
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bbb-1">
                                    <tr>
                                        <td class="bbr-1 p-relative bbb-1">
                                            <table class="w-100">
                                                <tbody>
                                                    @foreach ($info->remuneraciones as $remuneracion)
                                                        @if (!$remuneracion->nivel)
                                                            <tr>
                                                                <td class="py-0 font-10">
                                                                    {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->key : null }}
                                                                    <span>.-</span>
                                                                    {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->descripcion : null }}
                                                                </td>
                                                                <td class="py-0 text-right font-10" width="5%">
                                                                    {{ round($remuneracion->monto, 2) }}
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td class="py-0 font-10">{{ $remuneracion->nombre }}</td>
                                                                <td class="py-0 text-right font-10" width="5%">
                                                                    <div class="bbt-1">{{ round($remuneracion->monto, 2) }}</div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                        @foreach ($info->descuentos as $body)
                                            <td class="p-relative bbb-1">
                                                <table class="w-100">
                                                    <tbody>
                                                        @foreach ($body as $descuento)
                                                            @if (!$descuento->nivel)
                                                                <tr>
                                                                    <td class="py-0 font-10">
                                                                        {{ $descuento->typeDescuento ? $descuento->typeDescuento->key : null }}
                                                                        <span>.-</span>
                                                                        {{ $descuento->typeDescuento ? $descuento->typeDescuento->descripcion : null }}
                                                                    </td>
                                                                    <td class="py-0 text-right font-10" width="25%">{{ round($descuento->monto, 2) }}</td>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <td class="py-0 font-10">
                                                                        {{ $descuento->nombre }}
                                                                    </td>
                                                                    <td class="py-0 text-right font-10" width="25%">
                                                                        <div class="bbt-1">{{ round($descuento->monto, 2) }}</div>
                                                                    </td>
                                                                </tr>
                                                                @for ($i = 0; $i < 24 - $body->count(); $i++)
                                                                    <tr>
                                                                        <td class="py-0 font-10">&nbsp;</td>
                                                                        <td class="py-0 text-right font-10" width="25%">&nbsp;</td>
                                                                    </tr>
                                                                @endfor
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        @endforeach
                                        <td class="bbl-1 p-relative">
                                            <table class="p-absolute top-0 w-100">

                                                @foreach ($info->aportaciones as $aporte)
                                                    @if (!$aporte->nivel)
                                                        <tr>
                                                            <td class="py-0">
                                                                {{ $aporte->typeDescuento ? $aporte->typeDescuento->key : '' }}
                                                                .-{{ $aporte->typeDescuento ? $aporte->TypeDescuento->descripcion : '' }}
                                                            </td>
                                                            <td class="py-0 text-right">{{ round($aporte->monto, 2) }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td class="py-0">{{ $aporte->nombre }}</td>
                                                            <td class="py-0 text-right">
                                                                <div class="bbt-1">
                                                                    {{ round($aporte->monto, 2) }}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-0">&nbsp;</td>
                                                            <td class="py-0">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-0">BASE IMPONIBLE</td>
                                                            <td class="py-0">{{ round($info->base, 2) }}</td>
                                                        </tr>
                                                        @for ($i = 0; $i < 16; $i++)
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        @endfor
                                                        <tr>
                                                            <td colspan="2" class="py-0 p-absolute bottom-0 text-center">
                                                                <div class="center">--------------------------------</div>
                                                                <div class="center">RECIBI CONFORME</div>
                                                            </td>
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
                </body>
        @endforeach
</html>