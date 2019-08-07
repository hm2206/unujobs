@extends('layouts.app')

@section('titulo')
    - Trabajador >> {{ strtoupper($work->nombre_completo) }}
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Trabajador <span class="text-danger">>></span>  <span class="uppercase">{{ $work->nombre_completo }}</span> </h1>    
</div>

<div class="col-md-12">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('job.show', $work->slug()) }}" class="btn btn-primary"><i class="fas fa-user"></i> Perfil</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12 mt-5">

    <!--
        <div class="card mb-2">
            <div class="card-header">
                Filtrar
            </div>
            <form action="" class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="fecha_inicio" placeholder="Fecha Inicio: 07/2019">
                    </div>
                    <div class="col-md-1">
                        <div class="text-center">
                            <i class="fas fa-arrow-right text-danger"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="fecha_final" placeholder="Fecha Final: 08/2019">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    -->

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Boletas</h4>
            <p class="card-category">Sub Módulo de boletas</p>
        </div>
        <div class="card-body">
            <form class="table-responsive" method="POST" action="{{ route('job.boleta.store', $work->id) }}">
                @csrf
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>Seleccionar</th>
                            <th>Planilla</th>
                            <th>Observación</th>
                            <th>Adicional</th>
                            <th>Mes</th>
                            <th>Año</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cronogramas as $key => $cronograma)
                            <tr>
                                <td>
                                    <input type="checkbox" name="cronogramas[]" value="{{ $cronograma->id }}"
                                        {!! isset(request()->cronogramas[$key]) == $cronograma->id ? 'checked' : null !!}
                                    >
                                </td>
                                <td>
                                    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}
                                    @if ($cronograma->adicional)
                                    <span class="text-danger"> >> </span>
                                    {{ $cronograma->numero }}
                                    @endif
                                </td>
                                <td>{{ $cronograma->observacion }}</td>
                                <td>
                                    <span class="btn btn-sm btn-{{ $cronograma->adicional ? 'success' : 'danger' }}">
                                        {{ $cronograma->adicional ? 'Si' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <b class="btn btn-sm btn-warning">
                                       
                                    </b>
                                </td>
                                <td>
                                    <b class="btn btn-sm btn-primary">
                                        {{ $cronograma->año }}
                                    </b>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button class="btn btn-success"><i class="fa fa-sync"></i> Generar</button>

            </form>
        </div>
    </div>        

</div>
@endsection