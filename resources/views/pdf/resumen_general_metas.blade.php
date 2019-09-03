<!DOCTYPE html>
<html lang="es_Es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>
        Reporte General Planilla {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }} 
        {{ $titulo }}
    </title>
</head>
<body class="bg-white text-dark h-100">

    <div class="text-center"></div>
        
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
            {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
        </b>
    </div>

    <table width="100%" class="table-sm">
        <thead>
            <tr>
                <td>OBSERVACIONES: {{ $cronograma->observacion }}</td>
                <td class="uppercase">MES DE: {{ $meses[$cronograma->mes - 1] }}</td>
                <td>META SIAF: {{ $titulo }}</td>
                <td>Página : 1</td>
            </tr>
        </thead>
    </table>


    <div class="boleta-header mb-0" style="width:100%;">
        <table class="table-boleta table-sm" style="width:100%;">
            <thead> 
                <tr>
                    <td class="py-0 pl-3 pt-0">5. GASTO CORRIENTE:</td>
                </tr>
                <tr>
                    <td class="py-0 pl-3 pt-0">1. PERSONAL Y OBLIGACIONES SOCIALES:</td>
                </tr>
                <tr>
                    <td class="py-0 pl-3 pt-0">11. APLICACIONES DIRECTAS:</td>
                </tr>
                <tr>
                    <td class="py-0 pl-3 pt-0">01. RETRIBUCIONES Y COMPLEMENTOS - LEY DE BASES DE LA CARRERA ADMINISTRATIVA:</td>
                </tr>
            </thead>
        </table>
    </div>

    <table width="100%" class="table-sm mt-0">
        <thead>
            <tr>
                <td width="40%">
                    <table width="100%">
                        <thead>
                            @foreach ($type_remuneraciones as $remuneracion)
                                <tr>
                                    <td width="80%" class="py-0">{{ $remuneracion->key }}.-{{ $remuneracion->descripcion }}</td>
                                    <td width="20%" class="py-0 text-right">{{ $remuneracion->monto }}</td>
                                </tr>
                            @endforeach
                        </thead>
                        <tbody>
                            <tr>
                                <th width="80%" class="py-0 bbt-1">TOTAL REMUNERACION</th>
                                <th width="20%" class="py-0 text-right bbt-1">{{ $total_remuneracion }}</th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                @foreach ($only_descuentos->chunk($type_remuneraciones->count() + 1) as $count => $body)
                    <td width="30%">
                        <table width="100%">
                            <thead>
                                @foreach ($body as $descuento)
                                    <tr>
                                        <td width="80%" class="py-0">{{ $descuento->key }}.-{{ $descuento->descripcion }}</td>
                                        <td width="20%" class="py-0 text-right">{{ $descuento->monto }}</td>
                                    </tr>
                                @endforeach
                            </thead>
                            <tbody>
                                @if ($only_descuentos->chunk($type_remuneraciones->count())->count() == $count + 1)
                                    <tr>
                                        <th width="80%" class="py-0">
                                            <div class="bbt-1">TOTAL DESCUENTOS</div>
                                        </th>
                                        <th width="20%" class="py-0 text-right">
                                            <div class="bbt-1">{{ $total_descuento }}</div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="80%" class="py-0">
                                            <div class="bbt-1 bbb-1">NETO A PAGAR</div>
                                        </th>
                                        <th width="20%" class="py-0 text-right">
                                            <div class="bbt-1 bbb-1">{{ $neto_remuneracion }}</div>
                                        </th>
                                    </tr>
                                    @foreach ($aportaciones as $aport)
                                        <tr>
                                            <td width="80%" class="py-0">{{ $aport->key }}.-{{ $aport->descripcion }}</td>
                                            <td width="20%" class="py-0 text-right">{{ $aport->monto }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th width="80%" class="py-0">
                                            <div class="bbt-1">TOTAL APORTACIONES</div>
                                        </th>
                                        <th width="20%" class="py-0 text-right">
                                            <div class="bbt-1">{{ $total_aportacion }}</div>
                                        </th>
                                    </tr>
                                    @for ($i = 0; $i <= $espacios ;$i++)
                                        <tr>
                                            <td width="80%" class="py-0">&nbsp;</td>
                                            <td width="20%" class="py-0">&nbsp;</td>
                                        </tr>
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                    </td>
                @endforeach
            </tr>
        </thead>
    </table>

    <div class="boleta-header mb-0" style="width:100%;">
        <table class="table-boleta table-sm" style="width:100%;">
            <thead> 
                <tr>
                    <td class="py-0 pl-3 pt-0">
                        LOS NUMEROS DE {{ $first_remuneracion->key }} AL {{ $remuneracion->key }} CORRESPONDEN  A REMUNERACIONES, DEL {{ $first_descuento->key }} AL {{ $descuento->key }} CORRESPONDEN A DESCUENTOS
                    </td>
                </tr>
                <tr>
                    <td class="py-0 pl-3 pt-0">
                        EL NUMERO 61 INDICA EL TOTAL DE DESCUENTOS Y EL NUMERO 62 INDICA EL NETO A PAGAR
                    </td>
                </tr>
                <tr>
                    <td class="py-0 pl-3 pt-0">
                        LOS NUMEROS DEL 80 AL 83 CORRESPONDEN A APORTACIONES Y EL NUMERO 84 EL TOTAL DE APORTACIONES
                    </td>
                </tr>
            </thead>
        </table>
    </div>

</body>
</html>