@extends('layouts.app')

@section('titulo')
    - Descuentos
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <btn-remuneracion
        theme="btn-primary"
        redirect="{{ route('remuneracion.index')}}"
    >
        <i class="fas fa-plus"></i> Nuevo
    </btn-remuneracion>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif


<div class="col-md-12">

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Remuneraciones</h4>
            <p class="card-category">Sub Módulo de Gestión de remuneraciones</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th class="text-center">Activo</th>
                            <th class="text-center">Base Imponible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($remuneraciones as $remuneracion)
                            <tr id="categoria-{{ $remuneracion->id }}">
                                <th>{{ $remuneracion->key }}</th>
                                <th class="uppercase">{{ $remuneracion->descripcion }}</th>
                                <th>
                                    {{ $remuneracion->activo ? 'Si' : 'No' }}
                                </th>
                                <th class="text-center">
                                    {{ $remuneracion->base ? 'No' : 'Si' }}
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <btn-remuneracion
                                            theme="btn-warning btn-sm"
                                            redirect="{{ route('remuneracion.index')}}"
                                            :datos="{{ $remuneracion }}"
                                        >
                                            <i class="fas fa-pencil-alt"></i>
                                        </btn-remuneracion>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="4">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection