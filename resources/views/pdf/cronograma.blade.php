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
<body class="bg-white text-dark mt-0 pt-0">

    <div class="text-center">{{ $titulo }}</div>
        
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

    <h6 class="mt-1 text-center uppercase">{{ $sub_titulo }}</h6>

    <div class="text-md uppercase">
        <b> 
            RESUMEN DE PLANILLA UNICA DE PAGO : 
            @if ($cronograma->adicional)
                ADICIONAL <span class="text-danger">>></span> {{ $cronograma->numero }}
            @else
                <span class="ml-2">{{ $cronograma->planilla ? $cronograma->planilla->key : null }}</span>
            @endif 
        </b>
    </div>
    <div class="text-md uppercase"><b>OBSERVACIONES :{{ $cronograma->observacion }}</b></div>

    <table style="width:100%;">
        <tr>
            <td style="position:relative;" class="py-0">
                <table class="table-sm table table-bordered" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead class="table-thead text-center">
                        <tr>
                            <td class="py-0"></td>
                            @foreach ($type_categorias as $categoria)
                                <td class="py-0">{{ $categoria->descripcion }} 
                                    <table width="100%">
                                        <tr>
                                            @foreach ($categoria->cargos as $cargo)
                                                <td class="small text-center py-0" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    {{ $cargo->tag }} <br>
                                                    {{ $cargo->ext_pptto }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                            @endforeach
                            <td><b>TOTALES</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="25%" class="py-0"><b>2.1 PERSONAL Y OBLIGACIONES SOCIALES</b></td>
                            @foreach ($type_categorias as $type_categoria)
                                <td class="py-0">
                                    <table width="100%">
                                        <tr>
                                            @foreach ($type_categoria->cargos as $cargo)
                                                <td width="50%" class="text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    <b>{{ $remuneraciones->where("cargo_id", $cargo->id)->sum("monto") }}</b>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                            @endforeach
                            <td class="text-center py-0"><b>{{ $remuneraciones->sum("monto") }}</b></td>
                        </tr>
                        @foreach ($type_remuneraciones as $type)
                            <tr class="table-height">

                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $type->key }}. {{ $type->descripcion }}
                                </td>

                                @foreach ($type->type_categorias as $type_categoria)
                                    <td class="table-height text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                        <table width="100%">
                                            <tr>
                                                @foreach ($type_categoria->cargos as $cargo)
                                                    <td width="50%" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                        {{ 
                                                            $remuneraciones->where("cargo_id", $cargo->id)
                                                                ->where("type_remuneracion_id", $type->id)
                                                                ->sum('monto') 
                                                        }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                @endforeach

                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="text-center">
                                    {{ $type->total }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            <td style="position:relative;" class="py-0">
                <table class="table table-sm table-bordered" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead>
                        <tr>
                            <th colspan="2" class="py-0">DESCUENTOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($type_descuentos as $descuento)
                            <tr>
                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $descuento->descripcion }}
                                </td>
                                <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $descuento->monto }}
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($afps as $afp)
                            <tr>
                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    AFP {{ $afp->nombre }}
                                </td>
                                <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $afp->monto }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                TOTAL DESCUENTOS
                            </td>
                            <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-top:1px solid black;">
                                {{ $total_descuentos }}
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                TOTAL LICUADO PERCIBIDO
                            </td>
                            <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-bottom:1px solid black;">
                                {{ $total_liquido  }}
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                TOTAL PLANILLA BRUTA
                            </td>
                            <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;border-bottom:2px solid black;">
                                {{ $total_bruto }}
                            </td>
                        </tr>
                        @foreach ($type_aportaciones as $aport)
                            <tr>
                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $aport->key }}.- {{ $aport->descripcion }}
                                </td>
                                <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $aport->monto }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                84.-TOTAL APORTACIONES
                            </td>
                            <td class="text-right" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                {{ $total_aportaciones }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
        
</body>
</html>