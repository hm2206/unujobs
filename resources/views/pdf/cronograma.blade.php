<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>Reporte </title>
</head>
<body class="bg-white">

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

    <h6 class="mt-1 text-center mb-2 uppercase">{{ $sub_titulo }}</h6>

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
            <td style="position:relative;">
                <table class="table-sm table table-bordered" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead class="table-thead text-center">
                        <tr>
                            <td></td>
                            @foreach ($type_categorias as $categoria)
                                <td>{{ $categoria->descripcion }} 
                                    <table width="100%">
                                        <tr>
                                            @foreach ($categoria->cargos as $cargo)
                                                <td class="small text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    {{ $cargo->tag }}
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
                            <td width="25%"><b>2.1 PERSONAL Y OBLIGACIONES SOCIALES</b></td>
                            @foreach ($resumen as $res)
                                <td>
                                    <table width="100%">
                                        <tr>
                                            @foreach ($res['cargos'] as $car)
                                                <td width="50%" class="text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                    <b>{{ $car['total'] }}</b>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                            @endforeach
                            <td class="text-center"><b>{{ $totales }}</b></td>
                        </tr>
                        @foreach ($results as $type)
                            <tr class="table-height">

                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                    {{ $type['id'] }}. {{ $type['descripcion'] }}
                                </td>

                                @foreach ($type['categorias'] as $categoria)
                                    <td class="table-height text-center" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                        <table width="100%">
                                            <tr>
                                                @foreach ($categoria['tags'] as $tag)
                                                    <td width="50%" style="height: 10px; border:0px;padding:0px;padding-left:0.3em;">
                                                        {{ $tag['monto'] }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </td>
                                @endforeach

                                <td style="height: 10px; border:0px;padding:0px;padding-left:0.3em;" class="text-center">
                                    {{ $type['total'] }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            <td style="position:relative;">
                <table class="table table-sm table-bordered" style="position:absolute;top:0px;left:0px;width:100%;">
                    <thead>
                        <tr>
                            <th colspan="2">DESCUENTOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($type_descuentos as $descuento)
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
        
</body>
</html>