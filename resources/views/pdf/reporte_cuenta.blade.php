<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de cuentas {{ $cronograma->año }} - {{ $cronograma->mes }}</title>
    </head>

    @foreach ($bancos as $banco)

        @foreach ($banco->historial->chunk(23) as $historial)
            <body class="bg-white text-negro">
                        
                <table class="text-dark">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . "/img/logo.png" }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-12 text-negro">UNIVERSIDAD NACIONAL DE UCAYALI</b></div>
                                <div class="ml-1 font-12 text-negro">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 font-12 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-12 text-negro mt-2">
                    <h5><b>Planilla con Neto por Cuenta {{ $banco->nombre }}</b></h5>
                </div>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE: {{ $meses[$cronograma->mes - 1] }} {{ $cronograma->año }}</b>
                    <b style="float: right;">Página: {{ $num_page }}</b>
                </h5>

                <table class="table mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" with="40%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" with="5%"><b>Numero de cuenta</b></th>
                            <th class="py-0 text-center font-10"><b>Neto a Pagar</b></th>
                            <th class="py-0 text-center font-10" with="5%"><b>Firma</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historial as $iter => $history)
                            <tr>
                                <th class="py-2 font-10 text-center">{{ $num_work }}</th>
                                <th class="py-2 font-10 text-center">{{ $history->work ? $history->work->nombre_completo : '' }}</th>
                                <th class="py-2 font-10 text-center">{{ $history->numero_de_cuenta }}</th>
                                <th class="py-2 font-10 text-center">{{ $history->total_neto }}</th>
                                <th class="py-2 font-10 text-center" style="padding-top: 1em;">
                                    <b class="text-center" style="padding-top: 1em;">
                                        {{ $history->work ? $history->work->numero_de_documento : '' }}
                                    </b>
                                </th>
                            </tr>
                            @php
                                $num_work++;
                            @endphp
                        @endforeach
                        <tr>
                            <th class="py-1 text-center font-10" colspan="5">
                                @php
                                    $totales += $historial->sum('total_neto');
                                @endphp
                                <b class="font-12 text-center">Total S/. {{ $totales }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                @php
                    $num_page++;
                @endphp
            </body>
        @endforeach

    @endforeach
</html>