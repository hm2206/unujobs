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
<body class="bg-white text-negro h-100">

    <div class="text-center"></div>
        
    <table>
        <thead>
            <tr>
                <th>
                    <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                </th>
                <th>
                    <div><b class="font-14">UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                    <div class="ml-1 text-sm"><b class="font-13">OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                    <div class="ml-1 text-sm"><b class="font-13">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                </th>
            </tr>
        </thead>
    </table>

    <h6 class="mt-1 text-center mb-2 uppercase"><b class="font-13">{{ $sub_titulo }}</b></h6>

    <div class="text-md uppercase">
        <b class="font-13"> 
            RESUMEN DE PLANILLA UNICA DE PAGO : 
            {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
        </b>
    </div>

    <table width="100%" class="table-sm font-12">
        <thead>
            <tr>
                <th class="font-12">OBSERVACIONES: {{ $cronograma->observacion }}</th>
                <th class="uppercase font-12">MES DE: {{ $meses[$cronograma->mes - 1] }}</th>
                <th class="font-12">META SIAF: {{ $titulo }}</th>
                <th class="font-12">Página : 1</th>
            </tr>
        </thead>
    </table>


    <div class="boleta-header mb-0" style="width:100%;">
        <table class="table-boleta table-sm" style="width:100%;">
            <thead> 
                <tr>
                    <th class="py-0 pl-3 pt-0 font-11">5. GASTO CORRIENTE:</th>
                </tr>
                <tr>
                    <th class="py-0 pl-3 pt-0 font-11">1. PERSONAL Y OBLIGACIONES SOCIALES:</th>
                </tr>
                <tr>
                    <th class="py-0 pl-3 pt-0 font-11">11. APLICACIONES DIRECTAS:</th>
                </tr>
                <tr>
                    <th class="py-0 pl-3 pt-0 font-11">01. RETRIBUCIONES Y COMPLEMENTOS - LEY DE BASES DE LA CARRERA ADMINISTRATIVA:</th>
                </tr>
            </thead>
        </table>
    </div>

    <table width="100%" class="table-sm mt-0">
        <thead>
            <tr>
                <th width="55%">
                    <table width="100%">
                        <thead>
                            @foreach ($type_remuneraciones as $remuneracion)
                                <tr>
                                    <th width="80%" class="py-0 font-12">{{ $remuneracion->key }}.-{{ $remuneracion->descripcion }}</th>
                                    <th width="20%" class="py-0 text-right">
                                        <div class="font-13">{{ $remuneracion->monto }}</div>
                                    </th>
                                </tr>
                            @endforeach
                        </thead>
                        <tbody>
                            <tr>
                                <th width="80%" class="py-0">
                                    <div class="bbt-1 font-13">TOTAL REMUNERACION</div>
                                </th>
                                <th width="20%" class="py-0 text-right">
                                    <div class="bbt-1 font-13">{{ $money->parseTo($total_remuneracion) }}</div>    
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </th>
                @foreach ($only_descuentos->chunk($type_remuneraciones->count() + 1) as $count => $body)
                    <th width="20%">
                        <table width="100%">
                            <thead>
                                @foreach ($body as $descuento)
                                    <tr>
                                        <th width="80%" class="py-0 font-13">{{ $descuento->key }}.-{{ $descuento->descripcion }}</th>
                                        <th width="20%" class="py-0 text-right font-13">
                                            {{ $money->parseTo($descuento->monto) }}
                                        </th>
                                    </tr>
                                @endforeach
                            </thead>
                            <tbody>
                                @if ($only_descuentos->chunk($type_remuneraciones->count())->count() == $count + 1)
                                    <tr>
                                        <th width="80%" class="py-0 font-13">
                                            <div class="bbt-1">TOTAL DESCUENTOS</div>
                                        </th>
                                        <th width="20%" class="py-0 text-right font-13">
                                            <div class="bbt-1">
                                                {{ $money->parseTo($total_descuento) }}
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="80%" class="py-0 font-13">
                                            <div class="bbt-1 bbb-1">NETO A PAGAR</div>
                                        </th>
                                        <th width="20%" class="py-0 text-right font-13">
                                            <div class="bbt-1 bbb-1">
                                                {{ $money->parseTo($neto_remuneracion) }}
                                            </div>
                                        </th>
                                    </tr>
                                    @foreach ($aportaciones as $aport)
                                        <tr>
                                            <th width="80%" class="py-0 font-13">
                                                <b>{{ $aport->key }}.-{{ $aport->descripcion }}</b>
                                            </th>
                                            <th width="20%" class="py-0 text-right font-13">
                                                <b>{{ $money->parseTo($aport->monto) }}</b>
                                            </th>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th width="80%" class="py-0 font-13">
                                            <b class="bbt-1">TOTAL APORTACIONES</b>
                                        </th>
                                        <th width="20%" class="py-0 text-right font-13">
                                            <div class="bbt-1"><b>{{ $money->parseTo($total_aportacion) }}</b></div>
                                        </th>
                                    </tr>
                                    @for ($i = 0; $i <= $espacios + 2 ;$i++)
                                        <tr>
                                            <td width="80%" class="py-0 font-11">&nbsp;</td>
                                            <td width="20%" class="py-0 font-11">&nbsp;</td>
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

    <div class="boleta-header mb-0" style="width:100%; margin-top: 4em;">
        <table class="table-boleta table-sm" style="width:100%;">
            <thead> 
                <tr>
                    <th class="py-0 pl-3 pt-0 font-12">
                        LOS NUMEROS DE {{ $first_remuneracion->key }} AL {{ $remuneracion->key }} CORRESPONDEN  A REMUNERACIONES, DEL {{ $first_descuento->key }} AL {{ $descuento->key }} CORRESPONDEN A DESCUENTOS
                    </th>
                </tr>
                <tr>
                    <th class="py-0 pl-3 pt-0 font-12">
                        EL NUMERO 61 INDICA EL TOTAL DE DESCUENTOS Y EL NUMERO 62 INDICA EL NETO A PAGAR
                    </th>
                </tr>
                <tr>
                    <th class="py-0 pl-3 pt-0 font-12">
                        LOS NUMEROS DEL 80 AL 83 CORRESPONDEN A APORTACIONES Y EL NUMERO 84 EL TOTAL DE APORTACIONES
                    </th>
                </tr>
            </thead>
        </table>
    </div>

</body>
</html>