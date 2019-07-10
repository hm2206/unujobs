@extends('layouts.app')

@section('titulo')
    - Trabajador >> {{ strtoupper($work->nombre_completo) }}
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('job.show', $work->id) }}" class="btn btn-primary"><i class="fas fa-user"></i> perfil</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12">
    <h3>Trabajador <span class="text-danger">>></span>  <span class="uppercase">{{ $work->nombre_completo }}</span></h3>
</div>

<div class="col-md-12 mt-5">

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
                                        {{ $meses[$cronograma->mes - 1] }}
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