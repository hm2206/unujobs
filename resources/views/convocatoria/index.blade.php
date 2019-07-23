@extends('layouts.app')

@section('content')
    
<div class="row">

    <div class="col-md-12 mb-3">
        <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <a href="{{ route('convocatoria.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> nuevo</a>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Convocatorias
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numero de Convocatoria</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Final</th>
                                <th class="text-center">Etapas</th>
                                <th class="text-center">Archivo</th>
                                <th class="text-center">Aceptado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($convocatorias as $convocatoria)
                                <tr>
                                    <td>
                                        <a href="{{ route('convocatoria.edit', $convocatoria->id) }}">N° {{ $convocatoria->numero_de_convocatoria }}</a>
                                    </td>
                                    <td>
                                        <span class="btn btn-sm btn-dark">{{ $convocatoria->fecha_inicio }}</span>
                                    </td>
                                    <td>
                                        <span class="btn btn-sm btn-dark">{{ $convocatoria->fecha_final }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('convocatoria.etapas', $convocatoria->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a target="__blank" href="{{ route('convocatoria.pdf', $convocatoria->id) }}" class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if ($convocatoria->aceptado)
                                            <span class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @else
                                            <span class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('convocatoria.aceptar', $convocatoria->id) }}">
                                            @csrf
                                            @if ($convocatoria->aceptado)
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Aceptar
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection