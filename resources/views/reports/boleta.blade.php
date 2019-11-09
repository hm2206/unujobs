<!DOCTYPE html>
<html lang="es_Es">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset($styles['app']['css']) }}" media="{{ $styles['app']['media'] }}">
        <link rel="stylesheet" href="{{ asset($styles['pdf']['css']) }}" media="{{ $styles['pdf']['media'] }}">
        <link rel="stylesheet" href="{{ asset($styles['default']['css']) }}">
        <title> {{ $titulo }} </title>
    </head>
    
    <body class="bg-white text-negro">
        @forelse ($storage as $body)
            <div style="height: 100%;" class="page-only pt-2">
                @foreach ($body as $store)
                    <div class="pb-3 pb-1 boleta-size">    
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <img src="{{ asset($config->logo) }}" width="35" alt="">
                                    </th>
                                    <th>
                                        <div><b class="uppercase ml-1">{{ $config->nombre }}</b></div>
                                        <div class="ml-1 text-sm font-11"><b>OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                                        <div class="ml-1 text-sm font-11"><b>OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                            
                        <h6 class="mt-1 text-center mb-2 uppercase"></h6>
                            
                        <div class="text-md uppercase">
                            <b> 
                                Actividad: {{ $store['meta'] ? $store['meta']->actividadID : '' }} {{ $store['meta'] ? $store['meta']->meta : null }}
                            </b>
                            <b style="float: right; padding-right: 1em;"> 
                                Mes: {{ $store['mes'] }} - {{ $store['year'] }}
                            </b>
                        </div>
                                
                        <div class="w-100 bbb-1">
                            <div class="boleta-header border-bottom-right border-bottom-left">
                                <table class="table-boleta table-sm" style="width:100%;">
                                    <thead> 
                                        <tr>
                                            <th class="py-0 font-10" style="padding-top: 0.5em;">
                                                <div class="pl-2">Boleta de Pago N°:</div>
                                            </th>
                                            <th class="py-0 font-10" style="padding-top: 0.5em;">{{ $store['history']->id }}</th>
                                            <th class="py-0 font-10 text-center" style="padding-top: 0.5em;" colspan="2">
                                                Fecha de Ingreso: {{ $store['history']->fecha_de_ingreso }}
                                            </th>
                                            <th class="py-0 font-10" style="padding-top: 0.5em;" width="10%">D.N.I.</th>
                                            <th class="py-0 font-10" style="padding-top: 0.5em;">{{ $store['work']->numero_de_documento }}</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0 font-10">
                                                <div class="pl-2">A.F.P:</div>
                                            </th>
                                            <th class="py-0 font-10" colspan="3">{{ $store['afp'] ? $store['afp']->nombre : null }}</th>
                                            <th class="py-0 font-10" widtd="10%">N° CUSSP</th>
                                            <th class="py-0 font-10">{{ $store['history']->numero_de_cussp }}</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0 font-10">
                                                <div class="pl-2">Nombres y Apellidos</div>
                                            </th>
                                            <th colspan="3" class="uppercase py-0 font-10">{{ $store['work']->profesion }} {{ $store['work']->nombre_completo }}</th>
                                            <th class="py-0 font-10" width="10%">N° ESSALUD</th>
                                            <th class="py-0 font-10">{{ $store['history']->numero_de_essalud }}</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0 font-10 pl-2">
                                                <div class="pl-1">Condición Laboral</div>
                                            </th>
                                            <th colspan="3" class="uppercase py-0 font-10">{{ $store['cargo']->descripcion }} - {{ $store['history']->pap }}</th>
                                            <th class="py-0 font-10" width="10%">Meta Siaf:</th>
                                            <th class="py-0 font-10">{{ $store['meta']->metaID }}</th>
                                        </tr>
                                        <tr>
                                            <th class="py-0 pl-2 pb-1 font-10">
                                                <div class="pl-1">Cargo</div>
                                            </th>
                                            <th colspan="3" class="uppercase py-0 pb-1 font-10">{{ $store['history']->perfil }}</th>
                                            <th class="py-0 pb-1 font-10" width="10%">Categoría</th>
                                            <th class="uppercase py-0 pb-1 font-10">{{ $store['categoria'] ? $store['categoria']->nombre : null }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <table class="table-sm py-0" style="width:100%;">
                                <thead class="py-0 bbt-1 bbl-1 bbb-1">
                                    <tr class="text-center py-0" width="30%">
                                        <th class="py-0">
                                            <div class="py-0 font-9">
                                                INGRESOS
                                            </div>
                                        </th>
                                        <th class="py-0 bbl-1" width="50%" colspan="2">
                                            <div class="py-0 font-9">
                                                RETENCIONES
                                            </div>
                                        </th>
                                        <th class="py-0 bbr-1 bbl-1" width="20%">
                                            <div class="py-0 font-9">
                                                APORTES EMPLEADOR
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-relative bbl-1" style="border-left-radius: 1em;" width="30%">
                                            <table class="w-100 p-absolute top-0">
                                                <tbody>
                                                    @foreach ($store['remuneraciones'] as $remuneracion)
                                                        <tr>
                                                            <th class="py-0 font-9">
                                                                <div>
                                                                    {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->key  : null }}
                                                                    <span>.-</span>
                                                                    {{ $remuneracion->typeRemuneracion ? 
                                                                        str_limit($remuneracion->typeRemuneracion->descripcion, 25) 
                                                                        : null 
                                                                    }}
                                                                </div>
                                                            </th>
                                                            <th class="py-0 text-right font-10" width="5%">
                                                                <div class="pr-2">{{ $money->parseTo(round($remuneracion->monto, 2)) }}</div>
                                                            </th>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th class="font-10 pt-0 pl-1">TOTAL BRUTO</th>
                                                        <th class="text-right pt-0 font-10">
                                                            <div class="bbt-1">
                                                                {{ $money->parseTo(round($store['history']->total_bruto, 2)) }}
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        @php
                                            $newRows = $store['rows'] + 3;
                                            $collection = $store['descuentos']->chunk($newRows);
                                        @endphp
                                        @foreach ($collection as $iterBody => $body)
                                            <td class="py-0" width="25%">
                                                @php
                                                    $bb = $iterBody == 0 ? 'bbl-1' : 'bbr-1';
                                                    $iterador = 0;
                                                @endphp
                                                <div class="{{ $bb }} h-100 pr-1 pl-1">
                                                    <table class="w-100">
                                                        @foreach ($body as $iterDes => $descuento)
                                                            <tr>
                                                                <th class="py-0 font-9 ml-1" width="80%">
                                                                    {{ $descuento->typeDescuento ? $descuento->typeDescuento->key : null }}
                                                                    <span>.-</span>
                                                                    {{ $descuento->typeDescuento ? str_limit($descuento->typeDescuento->descripcion, 30) : null }}
                                                                </th>
                                                                <th class="py-0 font-10 text-right" style="padding-right: 0.5em;">
                                                                        {{ $money->parseTo(round($descuento->monto, 2)) }}
                                                                </th>
                                                            </tr>
                                                            @php
                                                                $iterador++;
                                                            @endphp
                                                        @endforeach
                                                        @if ($collection->count() == ($iterBody + 1))
                                                            @for ($i = 0; $i < $newRows - ($iterador + 2); $i++)
                                                                <tr>
                                                                    <th class="py-0 font-9">&nbsp;</th>
                                                                    <th class="py-0 font-10">&nbsp;</th>
                                                                </tr>
                                                            @endfor
                                                            <tr>
                                                                <th class="py-0 font-9 pl-1">TOTAL DSCTS. {{ $iterador }}</th>
                                                                <th class="py-0 font-10">
                                                                    <div class="bbt-1 text-center py-0">
                                                                        {{ $money->parseTo(round($store['history']->total_desct, 2)) }}
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th class="py-0 font-9 pl-1">NETO A PAGAR</th>
                                                                <th class="py-0 font-10" colspan="3">
                                                                    <div class="bbt-1 text-center text-center">
                                                                        <span class="pr-1">{{ $money->parseTo(round($store['history']->total_neto, 2)) }}</span>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </td>
                                        @endforeach
                                        <th class="p-relative bbr-1 pr-1">
                                            <table class="p-absolute top-0 w-100">

                                                @foreach ($store['aportaciones'] as $aport)
                                                    <tr>
                                                        <th class="py-0 font-9">
                                                            {{ $aport->typeDescuento ? $aport->typeDescuento->key : '' }}
                                                                .-
                                                            {{ $aport->typeDescuento ? $aport->typeDescuento->descripcion : '' }}</th>
                                                        <th class="pr-1 pt-0 pb-0 font-10 text-right" width="20%">
                                                            <div class="pr-1">{{ $money->parseTo(round($aport->monto, 2)) }}</div>
                                                        </th>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th class="py-0 font-9">TOTAL APORTE</th>
                                                    <th class="pt-0 pb-0 font-10">
                                                        <div class="bbt-1 pr-1 text-right">
                                                            <span>{{ $money->parseTo(round($store['aportaciones']->sum('monto'), 2)) }}</span>
                                                        </div>
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th class="py-0 font-9">BASE IMPONIBLE</th>
                                                    <th class="pt-0 pb-0 pr-2 font-10 text-right">{{ $money->parseTo(round($store['history']->base, 2)) }}</th>
                                                </tr>
                                                @for ($i = 0; $i < 15; $i++)
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <th colspan="2" class="py-0 text-center font-10">
                                                        <div class="text-center">--------------------------------</div>
                                                        <div class="center">RECIBI CONFORME</div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center">No hay boletas :(</div>
        @endforelse
    </body>

</html>