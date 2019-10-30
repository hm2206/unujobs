<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title> {{ $titulo }} </title>
    </head>
    
    <style>
        
        html {
            margin: 0px;
            padding: 0px;
        }

        body {
            margin: 1.5em;
            padding: 0px;
        }

    </style>
    
    @foreach ($storage as $body)
        <body class="bg-white text-negro">
            @foreach ($body as $store)
                <div style="height: 48%" class="mb-3 mt-1">    
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                                </th>
                                <th>
                                    <div><b>UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
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
                            
                    <div class="w-100" style="margin-bottom: 2em;">
                        <div class="boleta-header" style="width:100%;">
                            <table class="table-boleta table-sm" style="width:100%;">
                                <thead> 
                                    <tr>
                                        <th class="py-0 pl-3 font-10 pt-1">Boleta de Pago N°:</th>
                                        <th class="py-0 pt-1 font-10">{{ $store['history']->id }}</th>
                                        <th class="py-0 pt-1 font-10">Fecha de Ingreso:</th>
                                        <th class="py-0 pt-1 font-10">{{ $store['history']->fecha_de_ingreso }}</th>
                                        <th class="py-0 pt-1 font-10" width="10%">D.N.I.</th>
                                        <th class="py-0 pt-1 font-10">{{ $store['work']->numero_de_documento }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 font-10">A.F.P:</th>
                                        <th class="py-0 font-10" colspan="3">{{ $store['afp'] ? $store['afp']->nombre : null }}</th>
                                        <th class="py-0 font-10" width="10%">N° CUSSP</th>
                                        <th class="py-0 font-10">{{ $store['history']->numero_de_cussp }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 font-10">Nombres y Apellidos</th>
                                        <th colspan="3" class="uppercase py-0 font-10">{{ $store['work']->profesion }} {{ $store['work']->nombre_completo }}</th>
                                        <th class="py-0 font-10" width="10%">N° ESSALUD</th>
                                        <th class="py-0 font-10">{{ $store['history']->numero_de_essalud }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 font-10 pl-3">Condición Laboral</th>
                                        <th colspan="3" class="uppercase py-0 font-10">{{ $store['cargo']->descripcion }} - {{ $store['history']->pap }}</th>
                                        <th class="py-0 font-10" width="10%">Meta Siaf:</th>
                                        <th class="py-0 font-10">{{ $store['meta']->metaID }}</th>
                                    </tr>
                                    <tr>
                                        <th class="py-0 pl-3 pb-1 font-10">Cargo</th>
                                        <th colspan="3" class="uppercase py-0 pb-1 font-10">{{ $store['history']->perfil }}</th>
                                        <th class="py-0 pb-1 font-10" width="10%">Categoría</td>
                                        <th class="uppercase py-0 pb-1 font-10">{{ $store['categoria'] ? $store['categoria']->nombre : null }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <table class="table-sm mt-2" style="width:100%;">
                            <thead class="py-0 bbt-1 bbl-1 bbb-1">
                                <tr class="text-center py-0" width="35%">
                                    <th class="py-0">
                                        <div class="py-0 font-10">
                                            INGRESOS
                                        </div>
                                    </th>
                                    <th class="py-0 bbl-1" width="45%">
                                        <div class="py-0 font-10">
                                            RETENCIONES
                                        </div>
                                    </th>
                                    <th class="py-0 bbr-1 bbl-1" width="20%">
                                        <div class="py-0 font-10">
                                            APORTES EMPLEADOR
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-relative">
                                        <table class="w-100 p-absolute top-0">
                                            <tbody>
                                                @foreach ($store['remuneraciones'] as $remuneracion)
                                                    <tr>
                                                        <th class="py-0 font-11">
                                                            {{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->key  : null }}
                                                            <span>.-</span>
                                                            {{ $remuneracion->typeRemuneracion ? 
                                                                str_limit($remuneracion->typeRemuneracion->descripcion, 45) 
                                                                : null 
                                                            }}
                                                        </th>
                                                        <th class="py-0 text-right font-12" width="5%">
                                                            {{ $money->parseTo(round($remuneracion->monto, 2)) }}
                                                        </th>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th class="font-11 py-0 pl-1">TOTAL BRUTO</th>
                                                    <th class="text-right font-12">
                                                        <div class="bbt-1">
                                                            {{ $money->parseTo(round($store['history']->total_bruto, 2)) }}
                                                        </div>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="py-0 bbr-1 bbl-1">
                                            <table class="w-100">
                                                <tbody>
                                                    @foreach ($store['descuentos']->chunk(2) as $body)
                                                        <tr>
                                                            @foreach ($body as $descuento)
                                                                <th class="py-0 font-11 ml-1" width="35%">
                                                                    {{ $descuento->typeDescuento ? $descuento->typeDescuento->key : null }}
                                                                    <span>.-</span>
                                                                    {{ $descuento->typeDescuento ? $descuento->typeDescuento->descripcion : null }}
                                                                </th>
                                                                <th class="py-0 font-12 text-right" style="padding-right: 0.5em;">
                                                                    {{ $money->parseTo(round($descuento->monto, 2)) }}
                                                                </th>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th></th>
                                                        <th colspan="3"></th>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-0 font-11 pl-1">TOTAL DSCTS.</th>
                                                        <th class="py-0 font-12" colspan="3">
                                                            <div class="bbt-1 text-center py-0">
                                                                {{ $money->parseTo(round($store['history']->total_desct, 2)) }}
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-0 font-11 pl-1">NETO A PAGAR</th>
                                                        <th class="py-0 font-12" colspan="3">
                                                            <div class="bbt-1 text-center text-center">
                                                                {{ $money->parseTo(round($store['history']->total_neto, 2)) }}
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                    </td>
                                    <th class="p-relative">
                                        <table class="p-absolute top-0 w-100">

                                            @foreach ($store['aportaciones'] as $aport)
                                                <tr>
                                                    <th class="py-0 font-11">
                                                        {{ $aport->typeDescuento ? $aport->typeDescuento->key : '' }}
                                                            .-
                                                        {{ $aport->typeDescuento ? $aport->typeDescuento->descripcion : '' }}</th>
                                                    <th class="py-0 font-12 text-right" width="20%">{{ $money->parseTo(round($aport->monto, 2)) }}</th>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <th class="py-0 font-12">TOTAL APORTE</th>
                                                <th class="py-0 font-12">
                                                    <div class="py-0 bbt-1 text-right">
                                                        {{ $money->parseTo(round($store['aportaciones']->sum('monto'), 2)) }}
                                                    </div>
                                                </th>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <th class="py-0 font-10">BASE IMPONIBLE</th>
                                                <th class="py-0 font-12 text-right">{{ round($store['history']->base, 2) }}</th>
                                            </tr>
                                            @for ($i = 0; $i < 15; $i++)
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            @endfor
                                            <tr>
                                                <th colspan="2" class="py-0 p-absolute bottom-0 text-center font-10">
                                                    <div class="center">--------------------------------</div>
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
        </body>
    @endforeach

</html>