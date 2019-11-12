<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $config = App\Models\Config::first();
        @endphp
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/pdf.css") }}">
        <link rel="stylesheet" href="{{ asset("/css/print/A4.css") }}">
        <title>Reporte de Relacion General de Trabajadores</title>
    </head>


    <body class="bg-white text-negro">
        @foreach ($pages as $boletas)
            <div class="page-only pt-2">  
                <table class="text-dark">
                    <thead>
                        <tr>
                            <th>
                                <img src="{{ asset($config->logo) }}" width="50" alt="">
                            </th>
                            <th>
                                <div><b class="font-12 text-negro">{{ $config->nombre }}I</b></div>
                                <div class="ml-1 font-11 text-negro">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                                <div class="ml-1 font-11 text-negro">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                            </th>
                        </tr>
                    </thead>
                </table>

                <div class="ml-1 font-11 text-negro text-center mt-2 uppercase">
                    <h6><b>RELACIÓN GENERAL TRABAJADORES MES {{ $meses[$mes - 1] }} - {{ $year }}</b></h6>
                </div>

                <h5 class="font-11">
                    <b>MES DE {{ $meses[$mes - 1] }} - {{ $year}}</b>

                    <b class="font-10" style="float: right">Página N° {{ $num_page }}</b>
                </h5>

                <table class="table mt-2 table-bordered text-negro uppercase">
                    <thead>
                        <tr>
                            <th class="py-0 text-center font-10" width="5%"><b>N°</b></th>
                            <th class="py-0 text-center font-10"><b>Apellidos y Nombres</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>Condición Lab</b></th>
                            <th class="py-0 font-10 text-center pl-1 pr-1" width="7%"><b>Base Bruto</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>X43</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>S.N.P</b></th>
                            <th class="py-0 font-10 text-center" width="7%"><b>Imp. renta</b></th>
                            @foreach ($typeAportes as $typeAporte)
                                <th class="py-0 font-10 text-center" width="7%"><b>{{ $typeAporte->descripcion }}</b></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $iter = $last_page;
                        @endphp
                        @foreach ($boletas as $boleta)
                            @php
                                $work = $boleta->work;
                                $cargo = $boleta->cargo;
                            @endphp
                            <tr>
                                <td class="py-0 font-10 text-left pl-1">{{ $iter }}</td>
                                <td class="py-0 font-10 text-left pl-1">{{ str_limit($work->nombre_completo, 31) }}</td>
                                <td class="py-0 font-10 text-center pl-1 pr-1">{{ str_limit($cargo->descripcion, 20) }}</td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->monto_bruto) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->x43) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->snp) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->renta) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->essalud) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->ies) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->dlfp) }}
                                    </b>
                                </td>
                                <td class="py-0 font-10 text-center">
                                    <b class="text-center py-0">
                                        {{ $money->parseTo($boleta->accidentes) }}
                                    </b>
                                </td>
                            </tr>
                            @php
                                $iter++;
                                $last_page = $iter;
                            @endphp
                        @endforeach
                        <tr>
                            @php
                                $beforeBruto += $boletas->sum("monto_bruto");
                                $beforeX43 += $boletas->sum('x43');
                                $beforeSNP += $boletas->sum('snp');
                                $beforeRenta += $boletas->sum('renta');
                                $beforeEssalud += $boletas->sum('essalud');
                                $beforeIES += $boletas->sum('ies');
                                $beforeDLFP += $boletas->sum('dlfp');
                                $beforeACCIDENTES += $boletas->sum('accidentes');
                                $num_page++;
                            @endphp
                            <th class="py-0 text-right font-11" colspan="3">
                                <b class="font-11 text-right py-0 pr-1">Totales S/.</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeBruto) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeX43) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeSNP) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeRenta) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeEssalud) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeIES) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeDLFP) }}</b>
                            </th>
                            <th class="py-0 text-center font-11">
                                <b class="font-11 py-0 text-center">{{ $money->parseTo($beforeACCIDENTES) }}</b>
                            </th>
                        </tr>
                    </tbody>
                </table>
                    
            </div>      
        @endforeach
    </body>

</html>