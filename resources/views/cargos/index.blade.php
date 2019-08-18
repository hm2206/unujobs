@extends('layouts.app')

@section('titulo')
    - Cargos
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <btn-cargo
        theme="btn-primary"
        redirect="{{ route('cargo.index')}}"
    >
        <i class="fas fa-plus"></i> Nuevo
    </btn-cargo>
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
        <form class="card-header" method="POST" action="{{ route('export.cargo') }}">
            @csrf
            <h4 class="card-title">Lista de Cargos</h4>
            <p class="card-category">Sub Módulo de Gestión de cargos</p>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Cargos
            </button>
        </form>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Categorias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cargos as $iter => $cargo)
                            <tr>
                                <th>{{ $cargo->id}}</th>
                                <th class="uppercase">{{ $cargo->descripcion }}</th>
                                <th>
                                    <a href="{{ route('cargo.categoria', $cargo->slug()) }}" class="btn mt-1 btn-sm btn-primary"><i class="fas fa-plus"></i></a>
                                    @foreach ($cargo->categorias as $categoria)
                                        <a href="#" class="btn btn-sm btn-danger uppercase mt-1">
                                            {{ $categoria->nombre }}
                                        </a>
                                    @endforeach
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <btn-cargo
                                            theme="btn-warning btn-sm"
                                            redirect="{{ route('cargo.index')}}"
                                            :datos="{{ $cargo }}"
                                        >
                                            <i class="fas fa-pencil-alt"></i>
                                        </btn-cargo>

                                        <a href="{{ route('cargo.config', $cargo->slug()) }}" class="btn btn-sm btn-dark">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr>
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