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
                <th class="font-12 bbr-1 text-center" width="4%">META</th>
                <th class="font-12 bbr-1 text-center" width="7%">ACTIVIDAD</th>
                @foreach ($cargos as $cargo)
                   <th class="py-0 text-center bbr-1 font-11" colspan="2">
                       <div class="text-center">{{ $cargo->ext_pptto }}</div>
                        <table class="w-100 py-0">
                            <tr class="py-0 w-100">
                                <th class="font-10 py-0 text-center" width="50%">
                                    <div class="bbr-1 bbt-1 w-100">
                                        Con cuenta
                                    </div>
                                </th>
                                <th class="font-10 py-0 text-center" width="50%">
                                    <div class="bbr-1 bbt-1 w-100">
                                        Sin cuenta
                                    </div>
                                </th>
                            </tr>
                        </table>
                   </th>
                @endforeach
                <th class="font-10 bbr-1 text-center py-0" colspan="2">
                    <div class="py-0">21.19.12 
                        <div class="font-9 py-0" style="margin: 0px !important; padding-top: 0px !important;">Aguinaldo Fiestas</div>
                    </div>
                    <table class="w-100 py-0">
                        <tr class="py-0 w-100">
                            <th class="font-10 py-0 text-center" width="50%">
                                <div class="bbr-1 bbt-1 w-100">
                                    Con cuenta
                                </div>
                            </th>
                            <th class="font-10 py-0 text-center" width="50%">
                                <div class="bbr-1 bbt-1 w-100">
                                    Sin cuenta
                                </div>
                            </th>
                        </tr>
                    </table>
                </th>
                <th class="font-10 bbr-1 text-center py-0" colspan="2">
                    <div class="py-0">21.19.13
                        <div class="font-9 py-0" style="margin: 0px;">Escolaridad</div>
                    </div>
                    <table class="w-100 py-0">
                        <tr class="py-0 w-100">
                            <th class="font-10 py-0 text-center" width="50%">
                                <div class="bbr-1 bbt-1 w-100 py-0">
                                    Con cuenta
                                </div>
                            </th>
                            <th class="font-10 py-0 text-center py-0" width="50%">
                                <div class="bbr-1 bbt-1 w-100 py-0">
                                    Sin cuenta
                                </div>
                            </th>
                        </tr>
                    </table>
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
