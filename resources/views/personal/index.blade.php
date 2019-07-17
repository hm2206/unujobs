@extends('layouts.app')

@section('content')
    
<div class="row">

    <div class="col-md-12 mb-3">
        <a href="{{ route('planilla') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <a href="{{ route('personal.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> nuevo</a>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Requerimiento de Personal
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numero de Requerimiento</th>
                                <th>Sede</th>
                                <th>Dependencia</th>
                                <th>Cargo</th>
                                <th class="text-center">Fecha de publicación</th>
                                <th class="text-center">Archivo</th>
                                <th class="text-center">Aceptado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        @foreach ($personals as $personal)
                            <tr>
                                <td>
                                    <a href="{{ route('personal.show', $personal->id) }}">{{ $personal->numero_de_requerimiento }}</a>
                                </td>
                                <td>{{ $personal->sede ? $personal->sede->descripcion : null }}</td>
                                <td>{{ $personal->dependencia_txt }}</td>
                                <td>{{ $personal->cargo_txt }}</td>
                                <td class="text-center">
                                    <span class="btn btn-dark btn-sm">{{ $personal->created_at }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('personal.pdf', $personal->id) }}" target="__blank" class="btn btn-sm btn-danger">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if ($personal->aceptado)
                                            <span class="btn btn-sm btn-success"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="btn btn-sm btn-danger"><i class="fas fa-times"></i></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form class="group" method="POST" action="{{ route('personal.aceptar', $personal->id) }}">
                                        @csrf
                                        @if ($personal->aceptado)
                                            <button href=""class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Rechazar</button>
                                        @else
                                            <button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Aceptar</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection