<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
        <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
        <title>Reporte de Relacion de Personal de l planilla {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</title>
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

        @foreach ($pages as $historial)
            <body class="bg-white text-negro" style="">
                        
                <table class="text-dark">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ public_path() . $config->logo }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-12 text-negro">{{ $config->nombre }}</b></div>
                                <div class="ml-1 font-11 text-negro">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 font-11 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h5><b>RELACIÓN DEL PERSONAL MES DE OCTUBRE - {{ $cronograma->año }}</b></h5>
                </div>


                <h5 class="font-11"><b>PLANILLA: {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</b></h5>
                <h5 class="font-11">
                    <b>MES DE {{ $meses[$cronograma->mes - 1] }} - {{ $cronograma->año }}</b>

                    <b class="font-10" style="float: right">Página N° {{ $num_page }}</b>
                </h5>

                <table class="table mt-2 table-bordered">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10"><b>N°</b></th>
                            <th class="py-0 text-center font-10" width="35%"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>Categoria</b></th>
                            <th class="py-0 font-10 text-center pl-1 pr-1" width="7%"><b>Dedicacion</b></th>
                            <th class="py-0 font-10 text-center" width="20%"><b>Cargo</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>N° DNI</b></th>
                            <th class="py-0 font-10 text-center"><b>Sueldo Bruto</b></th>
                            <th class="py-0 font-10 text-center"><b>Sueldo a Pagar</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $iter = $last_page;
                        @endphp
                        @foreach ($historial as $history)
                            @php
                                $work = $history->work;
                                $categoria = $history->categoria;
                            @endphp
                            <tr>
                                <td class="py-0 font-10 text-left pl-1">{{ $iter }}</td>
                                <td class="py-0 font-10 text-left pl-1">{{ $work->nombre_completo }}</td>
                                <td class="py-0 font-10 text-center pl-1 pr-1">{{ $categoria->descripcion }}</td>
                                <td class="py-0 font-10 text-center pl-1 pr-1">{{ $categoria->dedicacion }}</td>
                                <td class="py-0 font-10 text-center uppercase">{{ str_limit($history->perfil, 20) }}</td>
                                <td class="py-0 font-10 text-center">{{ $work->numero_de_documento }}</td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_bruto) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($history->total_neto) }}
                                    </b>
                                </td>
                            </tr>
                            @php
                                $iter++;
                                $last_page = $iter;
                            @endphp
                        @endforeach
                        <tr>
                            <th class="py-0 text-right font-11" colspan="7">
                                <b class="font-11 text-right py-0 pr-1">
                                    @php
                                        $beforeBruto += $historial->sum("total_bruto");
                                        $beforeNeto += $historial->sum("total_neto");
                                        $num_page++;
                                    @endphp
                                    Totales S/.&nbsp;&nbsp;&nbsp;&nbsp;{{ $money->parseTo($beforeBruto) }}
                                </b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeNeto) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                    
            </body>
        @endforeach

</html>