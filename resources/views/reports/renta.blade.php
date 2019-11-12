<!DOCTYPE html>
<html lang="es">
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
        <title>Reporte Renta de {{ $work->nombre_completo }}</title>
    </head>

    <body class="bg-white text-negro">
        
        @foreach ($pages as $page)
                <div class="m-height-100 pt-2 page-only p-relative" style="min-height: 90vh;">
                            
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <img src="{{ asset($config->logo) }}" width="45">
                                </th>
                                <th>
                                    <div><b>{{ $config->nombre }}</b></div>
                                    <div class="ml-1 text-sm"><b class="font-12">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</b></div>
                                </th>
                            </tr>
                        </thead>
                    </table>

                    <br>
                        
                    <div class="w-100 py-0 uppercase" style="margin: 0px; padding: 0px;">

                        <div class="boleta-header w-100 py-0 border-bottom-left border-bottom-right"
                            style="margin: 0px; padding: 0px;"
                        >
                            <table class="table-boleta table-sm w-100 py-0">
                                <thead class="py-0"> 
                                    <tr>
                                        <th class="py-0 font-10"><span class="pl-1">00000</span></th>
                                        <th class="py-0 font-10">A.F.P:</th>
                                        <th class="py-0 font-10">{{ $work->afp ? $work->afp->nombre: null }}</th>
                                        <th class="py-0 font-10" width="10%">N° CUSSP</th>
                                        <th class="py-0 font-10">{{ $work->numero_de_cussp }}</th>
                                        <th class="py-0 font-10" width="10%">N° ESSALUD</th>
                                        <th class="py-0 font-10">{{ $work->numero_de_essalud }}</th>
                                        <th class="py-0 font-10" width="10%">D.N.I.</th>
                                        <th class="py-0 font-10">{{ $work->numero_de_documento }}</th>
                                        <th class="py-0 font-10">OBSERVACIONES</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="py-0 pb-1 font-10">
                                            <span class="pl-1 pr-2">Nombres y Apellidos:</span>
                                            {{ $work->profesion }} {{ $work->nombre_completo }}
                                        </th>
                                        <th class="py-0 pl-3 font-10"></th>
                                        <th colspan="2" class="uppercase py-0 pb-1 font-10"></th>
                                        <th class="py-0 font-10 pl-3"></th>
                                        <th colspan="2" class="uppercase py-0 pb-1 font-10"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="boleta-header boleta-header-0 w-100 py-0 border-top-left border-top-right border-bottom-left border-bottom-right"
                            style="margin: 0px; padding: 0px;">
                            <table class="w-100" style="padding: 0px; margin: 0px">
                                <thead class="py-0 w-100">
                                    <tr class="py-0">
                                        <th class="py-0 font-10 box-height">
                                            <div class="bbb-1 pl-2 pt-2 bb-1 bbl-1 bb-left">MES</div>
                                        </th>
                                        <th class="py-0 font-10 box-height">
                                            <div class="bbb-1 pt-2">AÑO</div>
                                        </th>
                                        <th class="py-0 font-10 box-height">
                                            <div class="bbb-1 pt-2">Categoría</div>
                                        </th>

                                        @foreach ($page['header'] as $type)
                                            <th class="py-0 font-10 text-center box-height">
                                                <div class="bbb-1 pt-2">
                                                    {{ $type->key }}.-
                                                </div>
                                            </th>
                                        @endforeach

                                        @if ($page['is_sub_total'])
                                            <th class="py-0 font-10 box-height">
                                                <div class="bbb-1 pt-2 pr-1">{{ $page['txt_sub_total'] }}</div>
                                            </th>
                                        @endif

                                        @if ($page['is_neto'])
                                            <th class="py-0 font-10 box-height">
                                                <div class="bbb-1 pt-2 pr-1">NETO RECIB.</div>
                                            </th>
                                        @endif

                                        @if ($page['is_child'])
                                            <th class="py-0 font-10 bbl-1 box-height my-0" colspan="{{ $page['children']['count'] }}">
                                                <div class="pr-1 py-0 text-center my-0">{{ $page['children']['titulo'] }}</div>
                                                <table class="w-100 py-0 my-0">
                                                    <tr class="py-0 my-0">
                                                        @foreach ($page['children']['body'] as $child)
                                                            <th class="py-0 my-0">
                                                                <div class="py-0 my-0 font-9 bbb-1">{{ $child }}</div>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </table>
                                            </th>
                                        @endif

                                        @if ($page['is_total'] || $page['is_child'])
                                            <th class="py-0 font-10 box-height">
                                                <div class="bbb-1 bbr-1 pt-2 pr-1 bb-right">TOTAL</div>
                                            </th>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody class="w-100">
                                    @foreach ($page['bodies'] as $body)
                                        <tr>
                                            @foreach ($body as $iter => $item)
                                                <th class="font-10 {{ $iter == 0 ? 'pl-1' : '' }}">{{ $item }}</th>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tr>
                                    <th class="font-10" colspan="3">
                                        <div class="bbb-1 bbt-1">{{ $page['txt_total'] }}</div>
                                    </th>
                                    @foreach ($page['totales'] as $total)
                                        <th class="font-10">
                                            <div class="bbb-1 bbt-1">{{ $total }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </table>
                        </div>

                    </div>

                    <br>

                    
                    <div class=" bbt-1 bbb-1 w-100" style="position: absolute; bottom: 0px;">
                        <table class="w-100">
                            <thead class="py-0">
                                <tr class="py-0">
                                    @foreach ($page['footer'] as $footer)
                                        <th class="py-0">
                                            <div class="">
                                                <table class="w-100 py-0">
                                                    @foreach ($footer as $type)
                                                        <tr>
                                                            <th class="font-10 py-0">{{ $type->key }} .- {{ $type->descripcion }}</th>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>  
        @endforeach
    </body>
</html>