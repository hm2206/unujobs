<!DOCTYPE html>
<html lang="es_Es">
    
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset("/css/app.css") }}">
    <link rel="stylesheet" href="{{ asset("/css/pdf.css") }}">
    <link rel="stylesheet" href="{{ asset("/css/print/A3.css") }}">
    <title>
        @yield('titulo')
    </title>
</head>

    <body class="bg-white text-negro h-100">

        <div class="text-center"></div>
            
        <table>
            <thead>
                <tr>
                    <th>
                        <img src="{{ asset($config->logo) }}" width="45" alt="">
                    </th>
                    <th>
                        <div><b class="font-14 ml-1">{{ $config->nombre }}</b></div>
                        <div class="ml-1 text-sm"><b class="font-13">OFICINA GENERAL DE RECURSOS HUMANOS</b></div>
                        <div class="ml-1 text-sm"><b class="font-13">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                    </th>
                </tr>
            </thead>
        </table>

        <h6 class="mt-1 text-center mb-2 uppercase"><b class="font-13"></b></h6>

        <h6 class="mt-1 text-center mb-2 uppercase"><b class="font-13"></b></h6>

        <div class="text-md uppercase">
            <b class="font-13"> 
                RESUMEN DE EJECUCION DE TODAS LAS METAS DEL PERSONAL : 
                @if ($cronograma->adicional)
                    ADICIONAL >> {{ $cronograma->numero }}
                @else
                    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                @endif

                <div class="text-center">
                    <span>MONTO {{ $neto ? 'NETO' : 'BRUTO' }}</span> <br>
                    <span>{{ $mes }} - {{ $cronograma->año }}</span>
                </div>
            </b>
        </div>

        <h5 style="font-size: 14px; margin-top: 0.5em;" class="uppercase">
            <b>
                PLANILLA 
                @if ($cronograma->adicional)
                    ADICIONAL >> {{ $cronograma->numero }}
                @else
                    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}
                @endif
            </b>
        </h5>

        <table class="table-sm w-100">
            <thead class="bbt-1 bbb-1 bbl-1 bbr-1">
                <tr>
                    <th class="font-12 bbr-1 text-center" width="20%">DETALLE</th>
                    <th class="font-12 bbr-1 text-center" width="7%">ACTIVIDAD</th>
                    <th class="font-12 bbr-1 text-center" width="4%">META</th>
                    @foreach ($type_categorias as $categoria)
                    <th class="py-0 text-center bbr-1 font-11" colspan="{{ $categoria->cargos->count() }}">
                        <div class="">{{ $categoria->descripcion }}</div>
                            <table class="w-100 py-0">
                                <tr class="py-0 w-100">
                                    @foreach ($categoria->cargos as $iter => $cargo)
                                        @php
                                            $count = (100 * 1) / $categoria->cargos->count();
                                        @endphp
                                        <th class="font-10 py-0 text-center" width="{{ $count . "%" }}">
                                            <div class="bbr-1 bbt-1 w-100">
                                                {{ $cargo->tag }}
                                                <div class="py-0 w-100">
                                                    {{ $cargo->ext_pptto }}
                                                </div>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </table>
                    </th>
                    @endforeach
                    <th class="font-12 bbr-1 text-center">
                        AGUINALDO
                        <div>2.1.19.12</div>
                    </th>
                    <th class="font-12 bbr-1 text-center">
                        ESCOLARIDAD
                        <div>2.1.15.13</div>
                    </th>
                    <th class="font-12 text-center">TOTAL</th>
                </tr>
            </thead>
            <tbody class="text-center font-11 py-0">
                <tr class="py-0">
                    @foreach ($contenidos as $content)
                        <th class="py-0">
                            <table class="py-0 w-100">
                                @foreach ($content['content'] as $item)
                                    <tr>
                                        <th class="font-12 py-0" width="{{ $content['size'] . "%" }}">
                                            <div style="width: 100%;" class="bbb-1 bbl-1 bbr-1 pt-1 pb-1">
                                                {{ $item }}
                                            </div>
                                        </th>
                                    </tr>
                                @endforeach
                            </table>
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <th class="font-12 py-0 text-center" colspan="3">
                        <div class="bbr-1 bbt-1 bbb-1 bbl-1 w-100">
                            &nbsp;
                        </div>
                    </th>
                    @foreach ($footer as $pie)
                        <th class="font-12 py-0 text-center">
                            <div class="bbr-1 bbt-1 bbb-1 bbl-1 w-100">
                                {{ $pie }}
                            </div>
                        </th>
                    @endforeach
                </tr>
            </tbody>
        </table>

    </body>
</html>
    