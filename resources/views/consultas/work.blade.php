@extends('layouts.bolsa')


@section('title', 'CONSULTAS')

@section('content')


<div class="row mt-3">
    
    <div class="col-md-12 mb-1">
        <a href="/consulta" class="btn btn-danger"><i class="fas fa-arrow-left"></i> OTRA CONSULTA</a>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                DATOS PERSONALES
            </div>
            <div class="card-body">
                <div class="row uppercase">
                    <div class="col-md-12">
                       <b>Apellidos y Nombres:</b>
                       <b class="text-primary">{{ $work->nombre_completo }}</b>
                    </div>
                    <div class="col-md-12">
                       <b>Numero de Identidad:</b>
                       <b class="text-primary">{{ $work->numero_de_documento }}</b>
                    </div>
                    <div class="col-md-12">
                       <b>Fecha de Nacimiento</b>
                       <b class="text-primary">{{ $work->fecha_de_nacimiento }}</b>
                    </div>
                    <div class="col-md-12">
                       <b>Dirección:</b>
                       <b class="text-primary">{{ $work->direccion }}</b>
                    </div>
                    <div class="col-md-12">
                       <b>Teléfono:</b>
                       <b class="text-primary">{{ $work->phone }}</b>
                    </div>
                    <div class="col-md-12">
                       <b>CORREO ELECTRÓNICO:</b>
                       <b class="text-primary">{{ $work->email }}</b>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                HISTORIAL DE BOLETAS INFORMATIVAS DESDE ENERO DEL 2019
            </div>
            <form class="card-body" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <input type="number" value="{{ $year }}" placeholder="AÑO" class="form-control" min="2019">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            <div class="card-footer">
                @forelse ($historial as $history)
                    <a href="{!! $history->pdf !!}" class="card" target="_blank">
                        <div class="card-body">
                            <i class="fas fa-file-pdf text-danger"></i>
                            {{ $history->categoria ? $history->categoria->nombre : '' }}
                            <i class="fas fa-arrow-right"></i>
                            @if ($history->cronograma)  
                                {{ $meses[$history->cronograma->mes - 1] }}
                                -
                                {{ $history->cronograma->año }}
                                @if ($history->cronograma->adicional)
                                    <span class="btn btn-sm btn-dark ml-1">
                                        ADICIONAL {{ $history->cronograma->numero }}
                                    </span>
                                @endif
                            @endif

                        </div>
                    </a>
                @empty
                    <b>No hay Boletas disponibles</b>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection