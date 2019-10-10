<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de pago afp {{ $afp->nombre }} | {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    <style>
        * {
            padding: 0em;
            margin: 0px;
        }

        body {
            padding: 1.5em 1em;
        }
    </style>

        @foreach ($pages as $page => $works)
            <body class="bg-white text-negro" style="">
                        
                <table class="text-dark">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-12 text-negro">UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                <div class="ml-1 font-11 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h5><b>REPORTE DE PAGO AFP {{ $afp->nombre }}</b></h5>
                </div>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE: {{ $meses[$cronograma->mes - 1] }} - {{ $cronograma->año }}</b>

                    <b class="font-10" style="float: right">Página N° {{ $page + 1 }}</b>
                </h5>

                <table class="table mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" width="35%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>N° Cussp</b></th>
                            <th class="py-0 text-center font-10"><b>R. Bruta</b></th>
                            <th class="py-0 text-center font-10"><b>F. Pensión</b></th>
                            <th class="py-0 text-center font-10"><b>C.A.</b></th>
                            <th class="py-0 text-center font-10"><b>Prima Seg.</b></th>
                            <th class="py-0 text-center font-10"><b>Total Dscto.</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($works as $iter => $work)
                            <tr>
                                <td class="py-0 font-10 text-left pl-1">{{ $iter + 1 }}</td>
                                <td class="py-0 font-10 text-left pl-1">{{ $work->nombre_completo }}</td>
                                <td class="py-0 font-10 text-center pl-1 pr-1">{{ $work->numero_de_cussp }}</td>
                                <td class="py-0 font-10 text-center" style="padding-top: 1em;">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($work->bruta) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($work->pension) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($work->ca) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($work->prima_seg) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($work->total_dscto) }}
                                    </b>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="py-0 text-center font-11" colspan="2">&nbsp;</th>
                            <th class="py-0 text-center font-11" colspan="2">
                                <b class="font-11 text-center py-0">Totales S/. {{ $money->parseTo($works->sum("bruta")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 text-center py-0">{{ $money->parseTo($works->sum("pension")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($works->sum("ca")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 text-center py-0">{{ $money->parseTo($works->sum("prima_seg")) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($works->sum("total_dscto")) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                    
            </body>
        @endforeach

</html>