<!DOCTYPE html>
<html lang="es_Es">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>Reporte Planillas Meta x Metas </title>
</head>

<style>

    html {
        padding: 0px;
        margin: 0px;
    }

    body {
        padding: 0px;
        margin: 1.5em;
    }

</style>

@foreach ($meta->cargos as $cargo)
    @foreach ($cargo->historial as $historial)
        <body class="bg-white text-negro" style="padding-top: 1em; margin-top: 0px;">
                <div>
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <img src="{{ public_path() . $config->logo }}" width="50" alt="">
                                    </th>
                                    <th>
                                        <div class="font-14" style="margin: 0px;"><b>{{ $config->nombre }}</b></div>
                                        <div style="margin: 0px;" class="font-12 ml-1 text-sm"><b>OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                                        <div  style="margin: 0px;" class="font-12 ml-1 text-sm"><b>OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
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
                                        PLANILLA UNICA DE PAGO: 
                                        @if ($cronograma->adicional)
                                            ADICIONAL >> {{ $cronograma->numero }}
                                        @else 
                                            {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                                        @endif
                                    </b></h5>
                                </th>
                                <th class="py-0 font-12" width="10%">SUB PROGRAMA</th>
                                <th class="py-0 font-12">{{ $meta->subProgramaID }} {{ $meta->sub_programa }}</th>
                            </tr>
                        </table>
                    
                        <table class="w-100 table-sm uppercase">
                            <tr>
                                <td class="py-0 font-12">OBS:</td>
                                <th class="font-12"><b>MES DE {{ $meses[$meta->mes - 1] }}-{{ $meta->year }}</b></th>
                                <td class="font-12">META SIAF: {{ $meta->metaID }}</td>
                                <td class="font-12"></td>
                                <td class="font-12"></td>
                                <td class="font-12">Página N° {{ $pagina++ }}</td>
                            </tr>
                        </table>
                    </div>
                
                    <div style="height:auto;">

                        @php
                            $numero = 1;
                        @endphp
                            
                        @foreach ($historial as $history)
                            @php
                                $work = $history->work;
                            @endphp
                            <table class="table-sm w-100 uppercase">
                                <thead class="bbt-1 bbb-1">
                                    <tr>
                                        <th class="py-0 font-12" colspan="2">N° {{ $history->id }} CODIGO {{ $history->info_id }} </th>
                                        <th class="py-0 font-12">PLAZA {{ $history->plaza }}</th>
                                        <th class="py-0 font-12">DEDICACION</th>
                                        <th class="py-0 font-12">AFP {{ $history->afp ? $history->afp->nombre : "" }}</th>
                                        <th class="py-0 font-12" colspan="2">N° CUSPP: {{ $history->numero_de_cuspp }}</th>
                                        <th class="py-0 font-12" colspan="2">N° ESSALUD: {{ $history->numero_de_essalud }}</th>
                                        <th class="py-0 font-12" colspan="3">OBS: {{ $history->observacion }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 font-12" colspan="5">
                                            APELLIDOS Y NOMBRES: {{ $work->profesion }} {{ $work->nombre_completo }}
                                        </th>
                                        <th class="py-0 font-12" colspan="4">
                                            COND.LABORAL: {{ $history->cargo ? $history->cargo->descripcion : "" }} - {{ $history->pap }} 
                                        </th>
                                        <th class="py-0 font-12" colspan="2">CARGO: {{ $history->perfil }}</th>
                                        <th class="py-0 font-12" colspan="1">DNI/LE: {{ $work->numero_de_documento }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bbb-1">
                                    <tr>
                                        @foreach ($history->remuneraciones as $columna)
                                            <td width="7%">
                                                @foreach ($columna as $remuneracion)
                                                    <table class="w-100">
                                                        @if (!$remuneracion->nivel)
                                                            @if ($remuneracion->empty)
                                                                <tr>
                                                                    <th class="py-0 font-12">&nbsp;</th>
                                                                    <th class="py-0 text-right font-12">&nbsp;</th>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <th class="py-0 font-12">
                                                                        {{   
                                                                            $remuneracion->typeRemuneracion ?
                                                                                $remuneracion->typeRemuneracion->key
                                                                                : ""
                                                                        }}.-   
                                                                    </th>
                                                                    <th class="py-0 text-right font-12">
                                                                        {{ $money->parseTo($remuneracion->monto) }}         
                                                                    </th>
                                                                </tr>
                                                            @endif
                                                        @else
                                                            <tr>
                                                                <th class="py-0">
                                                                    <div class="font-12">{{ $remuneracion->key }}</div>
                                                                </th>
                                                                <th class="py-0 text-right">
                                                                    <div class="bbt-1 font-12">{{ $money->parseTo($remuneracion->monto) }}</div>      
                                                                </th>
                                                            </tr>
                                                            @for ($i = 0; $i < 6 - $columna->count(); $i++)
                                                                <tr>
                                                                    <td class="py-0 font-12">&nbsp;</td>
                                                                    <td class="py-0 font-12 text-right">&nbsp;</td>
                                                                </tr>
                                                            @endfor
                                                        @endif
                                                    </table>
                                                @endforeach
                                            </td>
                                        @endforeach
                                        @foreach ($history->descuentos as $columna)
                                            <td width="7%">
                                                @foreach ($columna as $descuento)
                                                    <table class="w-100">
                                                        @if (!$descuento->nivel)
                                                            <tr>
                                                                <th class="py-0 font-12">
                                                                    {{   
                                                                        $descuento->typeDescuento ?
                                                                            $descuento->typeDescuento->key
                                                                            : ""
                                                                    }}.-   
                                                                </th>
                                                                <th class="py-0 text-right font-12">
                                                                    {{ $money->parseTo($descuento->monto) }}         
                                                                </th>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <th class="py-0">
                                                                    <div class="font-12">{{ $descuento->key }}</div>
                                                                </th>
                                                                <th class="py-0 text-right">
                                                                    <div class="bbt-1 font-12">
                                                                        {{ $money->parseTo($descuento->monto) }}
                                                                    </div>      
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th class="py-0">
                                                                    <div class="font-12">NETO</div>
                                                                </th>
                                                                <th class="py-0 text-right">
                                                                    <div class="bbt-1 font-12">
                                                                        {{ $money->parseTo($history->neto) }}
                                                                    </div>      
                                                                </th>
                                                            </tr>
                                                            @for ($i = 0; $i < 5 - $columna->count(); $i++)
                                                                <tr>
                                                                    <th class="py-0 font-12">&nbsp;</td>
                                                                    <th class="py-0 font-12 text-right">&nbsp;</td>
                                                                </tr>
                                                            @endfor
                                                        @endif
                                                    </table>
                                                @endforeach
                                            </td>
                                        @endforeach
                                        <td width="7%">
                                            @foreach ($history->aportaciones as $iter => $aport)
                                                <table class="w-100">
                                                    @if (!$aport->nivel)
                                                        <tr>
                                                            <th class="py-0 font-12">
                                                                {{   
                                                                    $aport->typeDescuento ?
                                                                        $aport->typeDescuento->key
                                                                        : ""
                                                                }}.-   
                                                            </th>
                                                            <th class="py-0 text-right font-13">
                                                                {{ $money->parseTo($aport->monto) }}         
                                                            </th>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <th class="py-0">
                                                                <div class="font-12">{{ $aport->key }}</div>
                                                            </th>
                                                            <th class="py-0 text-right">
                                                                <div class="bbt-1 font-12">
                                                                    {{ $money->parseTo($aport->monto) }}
                                                                </div>      
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="py-0 font-12">&nbsp;</th>
                                                            <th class="py-0 font-12 text-right">&nbsp;</th>
                                                        </tr>
                                                    @endif
                                                </table>
                                            @endforeach
                                        </td>
                                        <td width="12%">
                                            <table class="w-100">
                                                @for ($i = 0; $i < 3; $i++) 
                                                    <tr>
                                                        <th class="py-0" width="100%">
                                                            &nbsp;
                                                        </th>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td class="py-0 text-center" width="100%">
                                                        <div>------------------------------------------------</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="py-0" width="100% text-center">
                                                        <div class="ml-3 font-12 text-center">FIRMA</div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="py-0" width="100%">
                                                        <div class="ml-3 font-12">
                                                            DNI:
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
                
            </body>
    @endforeach
@endforeach
</html>