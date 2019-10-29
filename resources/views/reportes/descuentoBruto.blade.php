@extends('layouts.reportes')

@section('content')
    
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
                <span style="padding-right: 2em;">MONTO {{ $neto ? 'NETO' : 'BRUTO' }}</span> <br>
                <span style="margin-left: 50px;">{{ $meses[$cronograma->mes - 1] }} - {{ $cronograma->año }}</span>
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
                                    <th class="font-12 py-0" width="{{ $content['size'] }}">
                                        <div class="bbb-1 bbl-1 bbr-1 pt-1 pb-1">{{ $item }}</div>
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

@endsection
