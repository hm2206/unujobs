<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>Reporte Planilla </title>
</head>
<body class="bg-white text-negro mt-0 pt-0">

    <div class="text-center">{{ $titulo }}</div>
        
    <table>
        <thead>
            <tr>
                <th>
                    <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                </th>
                <th>
                    <div><b class="font-14">UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                    <div class="ml-1 font-13 text-sm"><b>OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                    <div class="ml-1 font-13 text-sm"><b>OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                </th>
            </tr>
        </thead>
    </table>

    <h6 class="mt-2 text-center uppercase"><b class="font-13">{{ $sub_titulo }}</b></h6>

    <div class="text-md uppercase font-13">
        <b> 
            RESUMEN DE PLANILLA UNICA DE PAGO : 
            @if ($cronograma->adicional)
                ADICIONAL <span class="text-danger">>></span> {{ $cronograma->numero }}
            @else
                <span class="ml-2">{{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}</span>
            @endif 
        </b>
    </div>
    <div class="text-md uppercase font-12"><b>OBSERVACIONES :{{ $cronograma->observacion }}</b></div>

    <table style="width:100%;" class="text-negro">
        <tr>
            <td style="position:relative;" class="py-0 font-12">
                <table class="table-sm" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead class="table-thead text-center bbb-1 bbl-1 bbt-1">
                        <tr>
                            <td class="py-0"></td>
                            @foreach ($type_categorias as $categoria)
                                <th class="py-0 font-12">{{ $categoria->descripcion }} 
                                    <table width="100%">
                                        <tr>
                                            @foreach ($categoria->cargos as $cargo)
                                                <th class="small text-center font-11 py-0" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    <b>{{ $cargo->tag }} </b><br>
                                                    <b>{{ $cargo->ext_pptto }}</b>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </table>
                                </th>
                            @endforeach
                            <td class="font-12"><b>TOTALES</b></td>
                        </tr>
                    </thead>
                    <tbody  class="bbl-1">
                        <tr>
                            <td width="25%" class="py-0 font-11 bbb-1 bbr-1"><b>2.1 PERSONAL Y OBLIGACIONES SOCIALES</b></td>
                            @foreach ($type_categorias as $type_categoria)
                                <td class="py-0 bbb-1">
                                    <table width="100%">
                                        <tr>
                                            @foreach ($type_categoria->cargos as $cargo)
                                                <td width="50%" class="text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    <b class="font-13">{{ $remuneraciones->where("cargo_id", $cargo->id)->sum("monto") }}</b>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                            @endforeach
                            <td class="text-center bbb-1 py-0"><b class="font-13">{{ $remuneraciones->sum("monto") }}</b></td>
                        </tr>
                        @foreach ($type_remuneraciones as $type)
                            <tr class="table-height">

                                <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-11">
                                    <b class="font-11">{{ $type->key }}. {{ $type->descripcion }}</b>
                                </th>

                                @foreach ($type->type_categorias as $type_categoria)
                                    <td class="table-height text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                        <table width="100%">
                                            <tr>
                                                @foreach ($type_categoria->cargos as $cargo)
                                                    <th width="50%" class="font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                        {{ 
                                                            $remuneraciones->where("cargo_id", $cargo->id)
                                                                ->where("type_remuneracion_id", $type->id)
                                                                ->sum('monto') 
                                                        }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                @endforeach

                                <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="bbr-1 font-13 text-center">
                                    {{ $type->total }}
                                </th>

                            </tr>
                        @endforeach
                        @for ($i = 0; $i < 4; $i++)
                            <tr>
                                <th colspan="{{ $type->type_categorias->count() }}" class="font-12">&nbsp;</th>
                            </tr>
                        @endfor
                        <tr>
                            <th colspan="{{ $type->type_categorias->count() }}" class="font-12 bbb-1"></th>
                            <th class="font-12 bbb-1" colspan="2">
                                <div class="bbt-1 font-13">
                                    TOTAL PLANILLA BRUTA: S/. {{ $total_bruto }}
                                </div>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </td>

            <td style="position:relative;" class="py-0 font-11">
                <table class="table-sm" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead class="bbr-1 bbt-1 bbb-1 bbl-1 bbr-1">
                        <tr>
                            <th colspan="2" class="py-2 font-12">DESCUENTOS</th>
                        </tr>
                    </thead>
                    <tbody class="bbl-1 bbr-1">
                        @foreach ($type_descuentos as $descuento)
                            <tr>
                                <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                    {{ $descuento->descripcion }}
                                </th>
                                <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $descuento->monto }}
                                </th>
                            </tr>
                        @endforeach

                        @foreach ($afps as $afp)
                            <tr>
                                <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                    AFP {{ $afp->nombre }}
                                </th>
                                <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $afp->monto }}
                                </th>
                            </tr>
                        @endforeach
                        <tr>
                            <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                TOTAL DESCUENTOS
                            </th>
                            <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-top:1px solid black;">
                                {{ $total_descuentos }}
                            </th>
                        </tr>
                        <tr>
                            <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                TOTAL LICUADO PERCIBIDO
                            </th>
                            <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-bottom:1px solid black;"
                                class="font-13 text-right"
                            >
                                {{ $total_liquido  }}
                            </th>
                        </tr>
                        <tr>
                            <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                TOTAL PLANILLA BRUTA
                            </th>
                            <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-bottom:2px solid black;">
                                {{ $total_bruto }}
                            </th>
                        </tr>
                        @foreach ($type_aportaciones as $aport)
                            <tr>
                                <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                    {{ $aport->key }}.- {{ $aport->descripcion }}
                                </th>
                                <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $aport->monto }}
                                </th>
                            </tr>
                        @endforeach
                        <tr>
                            <th style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="font-12">
                                <div class="bbb-1 font-12">84.-TOTAL APORTACIONES</div>
                            </th>
                            <th class="text-right font-13" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                <div class="bbb-1 font-13 bbt-1">{{ $total_aportaciones }}</div>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
        
</body>
</html>